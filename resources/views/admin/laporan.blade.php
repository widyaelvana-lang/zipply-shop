@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center no-print">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Laporan Otomatis Persediaan</h1>
            <p class="text-sm text-gray-500">Data rekap terisi otomatis berdasarkan mutasi log barang masuk/keluar.</p>
        </div>
        <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-semibold"><i class="fa-solid fa-print mr-2"></i>Cetak Laporan (Print)</button>
    </div>

    <div class="bg-white p-4 rounded-xl border shadow-sm no-print">
        <form action="{{ url('/admin/laporan') }}" method="GET" class="flex items-end gap-4">
            <div class="w-1/3">
                <label class="block text-xs font-medium text-gray-500 mb-1">Jenis Transaksi</label>
                <select name="type" class="w-full border rounded-lg p-2 text-sm outline-none">
                    <option value="Semua">Semua Mutasi</option>
                    <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Barang Masuk Only</option>
                    <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Barang Keluar Only</option>
                </select>
            </div>
            <button type="submit" class="bg-[#C29B78] text-white px-4 py-2 rounded-lg text-sm">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden p-4">
        <div class="text-center my-4 hidden print:block">
            <h2 class="text-2xl font-bold">LAPORAN MUTASI BARANG - ZIPPLY SHOP</h2>
            <p class="text-sm text-gray-500">Tanggal Cetak: {{ now()->format('d F Y') }}</p>
            <hr class="my-4">
        </div>

        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 font-semibold text-gray-600 border-b">
                <tr><th class="p-3">Waktu Transaksi</th><th class="p-3">Kode Barang</th><th class="p-3">Nama Produk</th><th class="p-3">Tipe</th><th class="p-3 text-center">Jumlah</th><th class="p-3">Keterangan</th></tr>
            </thead>
            <tbody class="divide-y text-gray-600">
                @foreach($transactions as $t)
                <tr>
                    <td class="p-3 text-xs">{{ $t->created_at }}</td>
                    <td class="p-3 font-mono text-xs">{{ $t->product->code ?? '-' }}</td>
                    <td class="p-4 font-semibold text-gray-800">{{ $t->product->name ?? 'Produk Dihapus' }}</td>
                    <td class="p-3 font-bold {{ $t->type == 'masuk' ? 'text-green-600' : 'text-red-600' }}">{{ strtoupper($t->type) }}</td>
                    <td class="p-3 text-center font-bold">{{ $t->quantity }} pcs</td>
                    <td class="p-3 text-gray-400 italic">{{ $t->notes }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        aside, header { display: none !important; }
    }
</style>
@endsection