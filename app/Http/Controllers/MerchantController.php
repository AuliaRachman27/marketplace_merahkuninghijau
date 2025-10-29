<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;

class MerchantController extends Controller
{
    public function buyers() {
        if (auth()->user()->role !== 'merchant') {
            return response()->json(['error'=>'Only merchant'],403);
        }

        $merchantId = auth()->id();
        $buyers = Transaction::where('merchant_id',$merchantId)
        ->with('customer')
        ->select('customer_id', DB::raw('count(*) as orders'), DB::raw('sum(total_after) as total_spent'))
        ->groupBy('customer_id')
        ->get()
        ->map(function($r){ return [
            'customer'=>$r->customer,
            'orders'=>$r->orders,
            'total_spent'=>$r->total_spent
        ];});
        
        return response()->json($buyers);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
