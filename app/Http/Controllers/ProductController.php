<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $products = Product::with('merchant')->paginate(20);
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $this->authorizeMerchant();
        $data = $request->validate([
            'name'=>'required',
            'price'=>'required|integer|min:0',
            'stock'=>'required|integer|min:0',
            'description'=>'nullable'
        ]);
        $data['merchant_id'] = auth()->id();
        $product = Product::create($data);
        return response()->json($product,201);
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
    public function update(Request $request, Product $product) {
        $this->authorizeMerchantOwns($product);
        $product->update($request->only(['name','price','stock','description']));
        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product) {
        $this->authorizeMerchantOwns($product);
        $product->delete();
        return response()->json(null,204);
    }

    private function authorizeMerchant() {
        if (auth()->user()->role !== 'merchant') abort(403,'Only merchants');
    }
    private function authorizeMerchantOwns(Product $product) {
        $this->authorizeMerchant();
        if ($product->merchant_id !== auth()->id()) abort(403,'You do not own this product');
    }
}
