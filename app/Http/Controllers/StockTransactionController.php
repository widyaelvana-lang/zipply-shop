<?php

namespace App\Http\Controllers;

// 1. IMPORT UTAMA AGAR TIDAK ERROR "NOT FOUND" LAGI
use App\Http\Controllers\Controller; 
use App\Models\StockTransaction;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

class StockTransactionController extends Controller
{
    // --- DESKTOP 2: DASHBOARD (GRAFIK & KELOLA OTOMATIS) ---
    public function dashboard()
{
    // Total transaksi semua waktu
    $totalTransaksi = StockTransaction::count();

    // Barang masuk hari ini
    $totalMasukHariIni = StockTransaction::where('type', 'masuk')
        ->whereDate('created_at', today())
        ->sum('quantity');

    // Barang keluar hari ini
    $totalKeluarHariIni = StockTransaction::where('type', 'keluar')
        ->whereDate('created_at', today())
        ->sum('quantity');

    // Produk baru hari ini
    $produkBaruHariIni = Product::whereDate('created_at', today())->count();

    // Transaksi baru 7 hari terakhir
    $transaksiBaruHariIni = StockTransaction::where('transaction_date', today())->count();

    // Barang dengan stok menipis
    $stokPerluDitambah = Product::whereColumn('stock', '<=', 'min_stock')
                                ->orWhere('stock', 0)
                                ->get();

    // Stok tertinggi & terendah
    $stokTertinggi = Product::orderBy('stock', 'desc')->take(2)->get();
    $stokTerendah  = Product::orderBy('stock', 'asc')->take(2)->get();

    return view('admin.dashboard', compact(
        'totalTransaksi','totalMasukHariIni','totalKeluarHariIni',
        'produkBaruHariIni','transaksiBaruHariIni',
        'stokPerluDitambah','stokTertinggi','stokTerendah'
    ));
}


    // --- DESKTOP 3 & 7: DATA PERSEDIAAN & LAPORAN MUTASI ---
    public function index()
{
    $products = Product::with(['transactions' => function($q){
        $q->select('product_id','type','quantity');
    }])->get();

    return view('admin.persediaan', compact('products'));
}

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type'       => 'required|in:masuk,keluar,in,out', // Diperluas agar aman menerima 'masuk'/'keluar'
            'quantity'   => 'required|integer|min:1',
            'notes'      => 'nullable|string'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $product = Product::findOrFail($request->product_id);

                // Normalisasi tipe input agar sinkron jika ada perbedaan istilah di UI
                $type = ($request->type === 'in' || $request->type === 'masuk') ? 'in' : 'out';

                if ($type === 'out' && $product->stock < $request->quantity) {
                    throw new \Exception('Stok tidak mencukupi untuk melakukan transaksi keluar.');
                }

                StockTransaction::create([
                    'product_id'       => $request->product_id,
                    'type'             => $request->type, // gunakan hasil normalisasi
                    'quantity'         => $request->quantity,
                    'notes'            => $request->notes,
                    'transaction_date' => $request->transaction_date ?? now()->toDateString(), 
                ]);



                // Otomatisasi penambahan / pemotongan jumlah stok barang riil
                if ($type === 'in') {
                    $product->increment('stock', $request->quantity);
                } else {
                    $product->decrement('stock', $request->quantity);
                }
            });

            return back()->with('success', 'Transaksi mutasi persediaan berhasil dicatat!');

        } catch (\Exception $e) { // Ditambahkan backslash (\) agar tidak crash
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    // --- DESKTOP 4: MASTER KATEGORI (CRUD) ---
    /**
     * Menampilkan halaman kategori
     */
    public function kategori() {
        // PERBAIKAN URUTAN: Ubah latest() menjadi oldest() agar produk baru otomatis di bawah
        $categories = Category::oldest()->get();
        
        return view('admin.kategori', compact('categories'));
    }

    /**
     * Menyimpan data kategori baru
     */
    public function storeKategori(Request $request) {
        // PERBAIKAN ERROR UNIQUE: Tambahkan validasi agar sistem menolak jika nama sudah ada
        $request->validate([
            'name' => 'required|unique:categories,name',
            'description' => 'nullable'
        ], [
            // Pesan kustom bahasa Indonesia jika nama kategori sudah ada
            'name.unique' => 'Nama kategori ini sudah terdaftar! Gunakan nama lain.'
        ]);

        // Jika lolos validasi, data baru disimpan
        Category::create([
            'name' => $request->name,
            'description' => $request->description ?? null
        ]);

        return back()->with('success', 'Kategori Berhasil Ditambahkan!');
    }
    public function updateKategori(Request $request, $id) {
        Category::findOrFail($id)->update(['name' => $request->name, 'description' => $request->description]);
        return back()->with('success', 'Kategori Berhasil Diperbarui!');
    }
    public function deleteKategori($id) {
        Category::findOrFail($id)->delete();
        return back()->with('success', 'Kategori Berhasil Dihapus!');
    }

    // --- DESKTOP 5: MASTER DAFTAR BARANG (CRUD) ---
    public function daftarBarang() {
        $products = Product::with('category')->get();
        $categories = Category::all();
        return view('admin.daftar-barang', compact('products', 'categories'));
    }
    public function storeBarang(Request $request)
    {
        // 1. Validasi input agar wajib diisi dan mencegah kode barang kembar
        $request->validate([
            'code'        => 'required|string|max:50|unique:products,code',
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit'        => 'nullable|string|max:20',
            'stock'       => 'nullable|numeric|min:0',
            'min_stock'   => 'nullable|numeric|min:0',
        ]);

        try {
            // 2. Jika validasi lolos, simpan data ke database
            Product::create([
                'code'        => $request->code,
                'name'        => $request->name,
                'category_id' => $request->category_id,
                'unit'        => $request->unit ?? 'pcs',
                'stock'       => $request->stock ?? 0,
                'min_stock'   => $request->min_stock ?? 5,
            ]);

            return back()->with('success', 'Barang Jualan Berhasil Ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan barang: ' . $e->getMessage())->withInput();
        }
    }
    public function deleteBarang($id) {
        Product::findOrFail($id)->delete();
        return back()->with('success', 'Barang Jualan Berhasil Dihapus!');
    }

    // --- DESKTOP 6: MANAJEMEN PENGGUNA ADMIN (CRUD) ---
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

    // --- TABEL LAPORAN (AUTOMATION PRINT) ---
    public function laporan(Request $request) {
        $tanggal = $request->tanggal ?? today();

        $query = StockTransaction::with('product')
            ->whereDate('created_at', $tanggal);

        if ($request->type && $request->type != 'Semua') {
            $query->where('type', $request->type);
        }

        $transactions = $query->orderBy('created_at','desc')->get();

        return view('admin.laporan', compact('transactions'));
    }

}