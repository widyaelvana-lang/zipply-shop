<?php

namespace App\Http\Controllers;

// IMPORT UTAMA AGAR TIDAK ERROR "NOT FOUND" Lagi
use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    // --- 1. DASHBOARD ---
    public function dashboard()
    {
        $totalTransaksi = StockTransaction::whereDate('transaction_date', today())->count();

        $totalMasukHariIni = StockTransaction::where('type', 'masuk')
            ->whereDate('transaction_date', today())
            ->sum('quantity');

        $totalKeluarHariIni = StockTransaction::where('type', 'keluar')
            ->whereDate('transaction_date', today())
            ->sum('quantity');

        $produkBaruHariIni = Product::whereDate('created_at', today())->count();

        $transaksiBaruHariIni = StockTransaction::where('transaction_date', today())->count();

        $stokPerluDitambah = Product::whereColumn('stock', '<=', 'min_stock')->get();

        $stokTertinggi = Product::with('transactions')
            ->orderBy('stock', 'desc')
            ->orderBy('id', 'asc')
            ->take(5)
            ->get();

        $stokTerendah = Product::with('transactions')
            ->orderBy('stock', 'asc')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        // Tambahkan query transaksi bulanan di sini
        $transaksi = StockTransaction::selectRaw('DATE(transaction_date) as tanggal, COUNT(*) as jumlah')
            ->whereMonth('transaction_date', now()->month)
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Format tanggal ke lokal (Asia/Jakarta)
        $transaksi->map(function ($item) {
            $item->tanggal = \Carbon\Carbon::parse($item->tanggal)
                ->timezone('Asia/Jakarta')
                ->format('d-m-Y');
            return $item;
        });

        return view('admin.dashboard', compact(
            'totalTransaksi',
            'totalMasukHariIni',
            'totalKeluarHariIni',
            'produkBaruHariIni',
            'transaksiBaruHariIni',
            'stokPerluDitambah',
            'stokTertinggi',
            'stokTerendah',
            'transaksi'
        ));
    }

    // --- 2. KATEGORI (CRUD) ---
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

    // --- 3. DAFTAR BARANG (CRUD) ---
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
    public function deleteBarang($id) {
        Product::findOrFail($id)->delete();
        return back()->with('success', 'Barang Berhasil Dihapus!');
    }

    // --- 4. PERSEDIAAN (BARANG MASUK/KELUAR OTOMATIS) ---
    public function persediaan(Request $request)
{
    $tanggal = $request->tanggal ?? today();

    $transactions = StockTransaction::with('product')
        ->whereDate('created_at', $tanggal)
        ->orderBy('created_at','desc')
        ->get();

    $products = Product::all();

    return view('admin.persediaan', compact('transactions','products'));
}

    public function storePersediaan(Request $request) {
        $product = Product::findOrFail($request->product_id);
        
        if($request->type == 'keluar' && $product->stock < $request->quantity) {
            return back()->with('error', 'Stok di gudang tidak mencukupi untuk pengeluaran barang!');
        }

        StockTransaction::create([
            'product_id' => $request->product_id,
            'type' => $request->type,
            'quantity' => $request->quantity,
            'notes' => $request->notes ?? '-'
        ]);

        // Manipulasi Kuantitas Stok Secara Otomatis
        if($request->type == 'masuk') {
            $product->increment('stock', $request->quantity);
        } else {
            $product->decrement('stock', $request->quantity);
        }

        return back()->with('success', 'Mutasi Persediaan Berhasil Dicatat!');
    }

    // --- 5. MANAJEMEN PENGGUNA (ADMIN) ---
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

    // --- 6. LAPORAN OTOMATIS ---
    public function laporan(Request $request)
{
    $query = StockTransaction::with('product');

    // Filter tanggal kalau user pilih
    if ($request->filled('tanggal')) {
        $query->whereDate('created_at', $request->tanggal);
    }

    // Filter tipe transaksi
    if ($request->filled('type') && $request->type != 'Semua') {
        $filterType = strtolower($request->type);
        if ($filterType === 'masuk' || $filterType === 'masuk') {
            $query->where('type', 'masuk');
        } elseif ($filterType === 'keluar' || $filterType === 'keluar') {
            $query->where('type', 'keluar');
        }
    }

    $transactions = $query->orderBy('created_at','desc')->get();

    return view('admin.laporan', compact('transactions'));
}


}