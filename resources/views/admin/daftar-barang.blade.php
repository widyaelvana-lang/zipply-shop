@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-amber-100 border border-amber-400 text-amber-700 px-4 py-3 rounded-lg relative">
            <strong class="font-bold">Periksa Kembali Isian Anda:</strong>
            <ul class="list-disc list-inside text-sm mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Barang</h1>
            <p class="text-sm text-gray-500">Master manajemen inventori barang jualan tas.</p>
        </div>
        <button onclick="toggleModal('addBarangModal')" class="bg-[#C29B78] hover:bg-[#b08a67] text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
            <i class="fa-solid fa-plus mr-2"></i>Tambah Barang
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b font-semibold text-gray-600">
                <tr>
                    <th class="p-4">Kode</th>
                    <th class="p-4">Nama Produk</th>
                    <th class="p-4">Kategori</th>
                    <th class="p-4 text-center">Stok</th>
                    <th class="p-4 text-center">Min Stok</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y text-gray-600">
                @forelse($products as $prod)
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-mono text-xs font-bold text-amber-800">{{ $prod->code }}</td>
                    <td class="p-4 font-semibold text-gray-800">{{ $prod->name }}</td>
                    <td class="p-4">{{ $prod->category->name ?? 'Tanpa Kategori' }}</td>
                    <td class="p-4 text-center font-bold {{ $prod->stock <= $prod->min_stock ? 'text-red-600 bg-red-50' : 'text-blue-600' }}">
                        {{ $prod->stock }} {{ $prod->unit ?? 'pcs' }}
                    </td>
                    <td class="p-4 text-center text-gray-400">{{ $prod->min_stock }}</td>
                    <td class="p-4 text-center space-x-1">
                        <a href="{{ url('/admin/daftar-barang/delete/'.$prod->id) }}" onclick="return confirm('Apakah Anda yakin ingin menghapus produk {{ $prod->name }}?')" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs transition-colors">Hapus</a>
                    </td>
                    
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-400 italic">Belum ada data barang jualan. Silakan klik tambah barang.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>




<div id="addBarangModal" class="{{ $errors->any() ? '' : 'hidden' }} fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl p-6 max-w-md w-full shadow-xl">
        <h3 class="text-lg font-bold mb-4">Tambah Barang Jualan Baru</h3>
        <form action="{{ url('/admin/daftar-barang/store') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium mb-1">Kode Barang</label>
                <input type="text" name="code" value="{{ old('code') }}" class="w-full border rounded-lg p-2" placeholder="Contoh: ZP-BKPK-01" required>
                @error('code')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Nama Barang</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded-lg p-2 @error('name') border-red-500 @enderror" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Kategori</label>
                <select name="category_id" class="w-full border rounded-lg p-2" required>
                    <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>-- Pilih Kategori Tas --</option>
                    @foreach($categories as $c) 
                        <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option> 
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Satuan</label>
                <select name="unit" class="w-full border rounded-lg p-2">
                    <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>Pcs</option>
                    <option value="pack" {{ old('unit') == 'pack' ? 'selected' : '' }}>Pack</option>
                    <option value="lusin" {{ old('unit') == 'lusin' ? 'selected' : '' }}>Lusin</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-sm font-medium mb-1">Stok Awal</label>
                    <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" class="w-full border rounded-lg p-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Stok Minimum</label>
                    <input type="number" name="min_stock" value="{{ old('min_stock', 5) }}" min="0" class="w-full border rounded-lg p-2">
                </div>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="toggleModal('addBarangModal')" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg text-sm transition-colors">Batal</button>
                <button type="submit" class="bg-[#C29B78] hover:bg-[#b08a67] text-white px-4 py-2 rounded-lg text-sm transition-colors">Simpan</button>
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