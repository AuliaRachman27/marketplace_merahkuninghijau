<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'customer') {
            $transactions = Transaction::with('items.product')
                ->where('customer_id', $user->id)
                ->orderByDesc('created_at')
                ->get();
        } else if ($user->role === 'merchant') {
            $transactions = Transaction::with('items.product')
                ->where('merchant_id', $user->id)
                ->orderByDesc('created_at')
                ->get();
        } else {
            $transactions = Transaction::with(['items.product', 'customer', 'merchant'])
                ->orderByDesc('created_at')
                ->where('merchant_id',$user->id)
                ->get();
        }

        return response()->json($transactions);
    }

    /**
     * Store a newly created transaction.
     */
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'customer') {
            return response()->json(['error' => 'Only customers can create transactions'], 403);
        }

        $payload = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1'
        ]);

        $totalBefore = 0;
        $merchantId = null;
        $itemsData = [];

        foreach ($payload['items'] as $it) {
            $product = Product::find($it['product_id']);

            if (!$merchantId) {
                $merchantId = $product->merchant_id;
            }

            if ($product->merchant_id != $merchantId) {
                return response()->json(['error' => 'All products must belong to the same merchant'], 422);
            }

            if ($product->stock < $it['qty']) {
                return response()->json(['error' => "Insufficient stock for product {$product->id}"], 422);
            }

            $subtotal = $product->price * $it['qty'];
            $totalBefore += $subtotal;

            $itemsData[] = [
                'product' => $product,
                'qty' => $it['qty'],
                'unit_price' => $product->price,
                'subtotal' => $subtotal
            ];
        }

        $shipping = ($totalBefore >= 15000) ? 0 : 10000;
        $discountAmount = ($totalBefore >= 50000) ? intval(round($totalBefore * 0.10)) : 0;
        $totalAfter = $totalBefore - $discountAmount + $shipping;

        DB::beginTransaction();
        try {
            $tx = Transaction::create([
                'customer_id' => Auth::id(),
                'merchant_id' => $merchantId,
                'total_before_discount' => $totalBefore,
                'discount' => $discountAmount,
                'shipping_cost' => $shipping,
                'total_after' => $totalAfter
            ]);

            foreach ($itemsData as $d) {
                TransactionItem::create([
                    'transaction_id' => $tx->id,
                    'product_id' => $d['product']->id,
                    'qty' => $d['qty'],
                    'unit_price' => $d['unit_price'],
                    'subtotal' => $d['subtotal']
                ]);

                $d['product']->decrement('stock', $d['qty']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Transaction created successfully',
                'transaction' => $tx->load('items.product')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Transaction failed',
                'detail' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a specific transaction.
     */
    public function show(string $id)
    {
        $tx = Transaction::with('items.product', 'merchant', 'customer')->findOrFail($id);
        return response()->json($tx);
    }

    /**
     * Remove a transaction (optional).
     */
    public function destroy(string $id)
    {
        $tx = Transaction::findOrFail($id);
        $tx->delete();
        return response()->json(['message' => 'Transaction deleted']);
    }
}
