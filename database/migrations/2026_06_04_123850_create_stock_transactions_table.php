<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel products
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            // Tipe mutasi
            $table->enum('type', ['masuk', 'keluar']);

            // Jumlah barang
            $table->unsignedInteger('quantity');

            // Catatan tambahan
            $table->text('notes')->nullable();

            // Tambahkan langsung di sini
            $table->dateTime('transaction_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
