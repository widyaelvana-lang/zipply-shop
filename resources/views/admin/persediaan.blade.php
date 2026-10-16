@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Data Persediaan Barang</h1>
            <p class="text-sm text-gray-500">Tabel transaksi keluar masuk barang Zipply Shop.</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="toggleModal('trxModal')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center shadow-sm transition">
                <i class="fa-solid fa-arrow-down mr-2"></i> Barang Masuk
            </button>
            <button onclick="toggleModal('trxModal')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center shadow-sm transition">
                <i class="fa-solid fa-arrow-up mr-2"></i> Barang Keluar
            </button>
            <input type="date" name="transaction_date" value="{{ now()->toDateString() }}" required>

        </div>
    </div>

    @if(session('error')) 
        <div class="bg-red-100 border border-red-200 text-red-700 p-3 rounded-lg text-sm flex items-center shadow-sm">
            <i class="fa-solid fa-triangle-exclamation mr-2 text-base"></i>
            {{ session('error') }}
        </div> 
    @endif

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <span>Show</span>
                <select class="border rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option>5</option>
                    <option>10</option>
                    <option>25</option>
                </select>
                <span>entries</span>
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                
                
            </div>
        </div>

        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-gray-50 font-bold text-gray-400 border-b text-xs tracking-wider uppercase">
                <tr>
                    <th class="p-4 w-12 text-center">No</th>
                    <th class="p-4">Tanggal</th>
                    <th class="p-4 text-center">Kode Barang</th>
                    <th class="p-4">Nama Barang</th>
                    <th class="p-4 text-center">Masuk</th>
                    <th class="p-4 text-center">Keluar</th>
                    <th class="p-4 text-center">Stok Saat Ini</th>
                    <th class="p-4 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y text-gray-600 font-medium">
                @foreach($products as $index => $p)
<tr class="hover:bg-gray-50/70 transition-colors">
    <td class="p-4 text-center text-gray-400 text-xs">{{ $index + 1 }}</td>
    <td class="p-4 text-xs text-gray-500">{{ now()->format('d/m/Y') }}</td>
    <td class="p-4 text-center">
        <span class="px-2 py-1 bg-blue-50 text-blue-600 text-[11px] font-bold rounded border border-blue-100 uppercase tracking-wide">
            {{ $p->code }}
        </span>
    </td>
    <td class="p-4 font-bold text-gray-800">{{ $p->name }}</td>

    <!-- Total Masuk Hari Ini -->
    <td class="p-4 text-center text-green-600 font-bold font-mono">
        {{ $p->transactions->where('type','masuk')->sum('quantity') }} pcs
    </td>

    <!-- Total Keluar Hari Ini -->
    <td class="p-4 text-center text-red-500 font-bold font-mono">
        {{ $p->transactions->where('type','keluar')->sum('quantity') }} pcs
    </td>


    <!-- Stok Saat Ini -->
    <td class="p-4 text-center font-bold text-gray-800 font-mono">
        {{ $p->stock }} pcs
    </td>

    <!-- Status sesuai min_stock -->
    <td class="p-4 text-center">
        @if($p->stock <= $p->min_stock)
            <span class="px-2 py-0.5 bg-red-100 text-red-800 text-[10px] rounded font-bold uppercase tracking-wider">Menipis</span>
        @else
            <span class="px-2 py-0.5 bg-green-100 text-green-800 text-[10px] rounded font-bold uppercase tracking-wider">Tersedia</span>
        @endif
    </td>
</tr>
@endforeach

            </tbody>
        </table>

        <div class="p-4 bg-gray-50 border-t flex justify-between items-center text-xs text-gray-500">
            <div>Showing 1 to 5 of 5 entries</div>
            <div class="flex space-x-1">
                <button class="px-3 py-1 border rounded bg-white text-gray-400 cursor-not-allowed">Previous</button>
                <button class="px-3 py-1 border rounded bg-blue-600 text-white font-bold shadow-sm">1</button>
                <button class="px-3 py-1 border rounded bg-white hover:bg-gray-50">Next</button>
            </div>
        </div>
    </div>
</div>

<div id="trxModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-all">
    <div class="bg-white rounded-xl p-6 max-w-md w-full shadow-2xl border transform transition-all scale-100 animate-fade-in">
        <div class="flex justify-between items-center mb-4 pb-2 border-b">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <i class="fa-solid fa-boxes-stacked text-blue-500 mr-2 text-xl"></i> Form Mutasi Persediaan
            </h3>
            <button onclick="toggleModal('trxModal')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        
        <form action="{{ url('/admin/persediaan/store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Pilih Produk Bag</label>
                <select name="product_id" class="form-select">
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">
                            {{ $p->name }} (Sisa Stok: {{ $p->stock }} pcs)
                        </option>
                    @endforeach
                </select>

            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Jenis Operasi Persediaan</label>
                <select name="type" class="w-full border rounded-lg p-2.5 bg-gray-50 text-sm font-medium" required>
                    
                    <option value="masuk" class="text-green-600 font-bold">Barang Masuk (+ Pcs)</option>
                    <option value="keluar" class="text-red-600 font-bold">Barang Keluar (- Pcs)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Jumlah Kuantitas (Qty)</label>
                <input type="number" name="quantity" min="1" class="w-full border rounded-lg p-2.5 text-sm font-bold font-mono" placeholder="0" required>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Catatan Keterangan</label>
                <input type="text" name="notes" placeholder="Contoh: Pengisian Vendor / Restock / Barang Rusak" class="w-full border rounded-lg p-2.5 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Tanggal Transaksi</label>
                <input type="date" name="transaction_date" 
                    class="w-full border rounded-lg p-2.5 text-sm" 
                    value="{{ now()->toDateString() }}" required>
            </div>

            
            <div class="flex justify-end space-x-2 pt-2 border-t">
                <button type="button" onclick="toggleModal('trxModal')" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-semibold transition">Batal</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-semibold shadow-sm transition">Proses Mutasi</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(id) { 
        document.getElementById(id).classList.toggle('hidden'); 
    }
</script>
@endsection