<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $fillable = [
        'product_id','type','quantity','notes','transaction_date'
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function storeTransaction(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|in:masuk,keluar',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($request->type === 'masuk') {
            $product->increment('stock', $request->quantity);
        } elseif ($request->type === 'keluar') {
            if ($product->stock < $request->quantity) {
                return back()->with('error', 'Stok tidak mencukupi!');
            }
            $product->decrement('stock', $request->quantity);
        }

        // Simpan transaksi ke tabel mutasi
        StockTransaction::create([
            'product_id' => $product->id,
            'type' => $request->type,
            'quantity' => $request->quantity,
            'transaction_date' => now(),
        ]);

        return back()->with('success', 'Transaksi stok berhasil dicatat!');
    }

}

