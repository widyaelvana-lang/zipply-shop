<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function handleLogin(Request $request) {
        // Simulasi login cepat untuk keperluan demo/sidang kuliah
        return redirect('/admin/dashboard');
    }

    public function handleLogout() {
        return redirect('/login');
    }

    public function dashboard() {
        // 1. Kalkulasi Otomatis untuk Mini Cards
        $totalTransaksi = StockTransaction::count();
        $totalMasuk = StockTransaction::where('type', 'masuk')->sum('quantity');
        $totalKeluar = StockTransaction::where('type', 'keluar')->sum('quantity');
        $totalBarangBaru = Product::where('created_at', '>=', now()->subDays(7))->count();

        // 2. Otomatisasi Stok Kritis (Stok <= Stok Minimum atau Stok == 0)
        $stokPerluDitambah = Product::where('stock', '<=', \DB::raw('min_stock'))
                                    ->orWhere('stock', 0)
                                    ->get();

        // 3. Data Grafik Otomatis (Stok Tertinggi & Terendah)
        $stokTertinggi = Product::orderBy('stock', 'desc')->take(5)->get();
        $stokTerendah = Product::orderBy('stock', 'asc')->take(5)->get();

        return view('admin.dashboard', compact(
            'totalTransaksi', 'totalMasuk', 'totalKeluar', 'totalBarangBaru',
            'stokPerluDitambah', 'stokTertinggi', 'stokTerendah'
        ));
    }

    // --- FITUR KATEGORI (CRUD) ---
    public function kategori() {
        $categories = Category::orderBy('created_at', 'desc')->get();
        return view('admin.kategori', compact('categories'));
    }
    public function storeKategori(Request $request) {
        Category::create([
            'name' => $request->name,
            'description' => $request->description ?? '-'
        ]);
        return back()->with('success', 'Kategori Berhasil Ditambahkan!');
    }
    public function updateKategori(Request $request, $id) {
        Category::findOrFail($id)->update([
            'name' => $request->name,
            'description' => $request->description
        ]);
        return back()->with('success', 'Kategori Berhasil Diperbarui!');
    }
    public function deleteKategori($id) {
        Category::findOrFail($id)->delete();
        return back()->with('success', 'Kategori Berhasil Dihapus!');
    }

    // --- FITUR DAFTAR BARANG / PRODUCT (CRUD) ---
    public function daftarBarang() {
        $products = Product::with('category')->get();
        $categories = Category::all();
        return view('admin.daftar-barang', compact('products', 'categories'));
    }
    public function storeBarang(Request $request) {
        Product::create([
            'code' => $request->code,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'unit' => $request->unit ?? 'pcs',
            'stock' => $request->stock ?? 0,
            'min_stock' => $request->min_stock ?? 5,
        ]);
        return back()->with('success', 'Barang Berhasil Ditambahkan!');
    }
    public function updateBarang(Request $request, $id) {
        Product::findOrFail($id)->update($request->all());
        return back()->with('success', 'Barang Berhasil Diperbarui!');
    }
    public function deleteBarang($id) {
        Product::findOrFail($id)->delete();
        return back()->with('success', 'Barang Berhasil Dihapus!');
    }

    // --- FITUR PERSEDIAAN (OTOMATISASI POTONG/TAMBAH STOK) ---
    public function persediaan() {
        $transactions = StockTransaction::with('product')->orderBy('created_at', 'desc')->get();
        $products = Product::all();
        return view('admin.persediaan', compact('transactions', 'products'));
    }
    public function storePersediaan(Request $request) {
        $product = Product::findOrFail($request->product_id);
        
        // Cek jika barang keluar melampaui sisa stok gudang
        if($request->type == 'keluar' && $product->stock < $request->quantity) {
            return back()->with('error', 'Stok tidak mencukupi untuk transaksi keluar!');
        }

        // Simpan Log Mutasi
        StockTransaction::create([
            'product_id' => $request->product_id,
            'type' => $request->type,
            'quantity' => $request->quantity,
            'notes' => $request->notes ?? '-'
        ]);

        // Manipulasi Nilai Stok Produk Secara Otomatis
        if($request->type == 'masuk') {
            $product->increment('stock', $request->quantity);
        } else {
            $product->decrement('stock', $request->quantity);
        }

        return back()->with('success', 'Transaksi Persediaan Berhasil Dicatat!');
    }

    // --- MANAJEMEN PENGGUNA ---
    public function pengguna() {
        $users = User::all();
        return view('admin.pengguna', compact('users'));
    }
    public function storePengguna(Request $request) {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return back()->with('success', 'Admin Baru Berhasil Ditambahkan!');
    }
    public function deletePengguna($id) {
        User::findOrFail($id)->delete();
        return back()->with('success', 'Admin Berhasil Dihapus!');
    }

    // --- LAPORAN otomatis ---
    public function laporan(Request $request) {
        $query = StockTransaction::with('product');
        if($request->type && $request->type != 'Semua') {
            $query->where('type', $request->type);
        }
        $transactions = $query->orderBy('created_at', 'desc')->get();
        return view('admin.laporan', compact('transactions'));
    }
}