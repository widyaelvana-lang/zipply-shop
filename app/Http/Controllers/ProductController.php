<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('transactions')->get();
        $categories = Category::all(); // Untuk opsi dropdown modal tambah/edit
        return view('admin.daftar-barang', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|unique:products,sku',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string'
        ]);

        Product::create($request->all());
        return redirect()->route('products.index')->with('success', 'Produk berhasil disimpan!');
    }

    public function update(Request $request, Product $product)
    {
            
            $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'nullable|string|max:20',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'description' => 'nullable|string'
        ]);

        $product->update($request->only([
            'code','name','category_id','unit','stock','min_stock'
        ]));

        return redirect()->route('products.index')->with('success', 'Produk berhasil diubah!');
    }


    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil didelete!');
    }

    

}