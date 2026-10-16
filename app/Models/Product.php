<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['code','name','category_id','unit','stock','min_stock'];


    // Relasi ke transaksi stok
    public function transactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    // Relasi ke kategori (kalau belum ada)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public $timestamps = true;

}
