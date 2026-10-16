@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kategori Barang</h1>
            <p class="text-sm text-gray-500">Kelola kategori produk Zipply Shop.</p>
        </div>
        <button onclick="toggleModal('addModal')" class="bg-[#C29B78] text-white px-4 py-2 rounded-lg text-sm font-semibold"><i class="fa-solid fa-plus mr-2"></i>Tambah Kategori</button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b font-semibold text-gray-600">
                <tr><th class="p-4 w-12 text-center">No</th><th class="p-4">Nama Kategori</th><th class="p-4">Deskripsi</th><th class="p-4 text-center">Aksi</th></tr>
            </thead>
            <tbody class="divide-y text-gray-600">
                @foreach($categories->sortBy('id') as $index => $cat)
                <tr class="hover:bg-gray-50">
                    <td class="p-4 text-center">{{ $index+1 }}</td>
                    <td class="p-4 font-semibold text-gray-800">{{ $cat->name }}</td>
                    <td class="p-4">{{ $cat->description }}</td>
                    <td class="p-4 text-center space-x-2">
                        <button onclick="openEditModal('{{ $cat->id }}', '{{ $cat->name }}', '{{ $cat->description }}')" class="bg-amber-500 text-white px-2.5 py-1 rounded text-xs">Edit</button>
                        <a href="{{ url('/admin/kategori/delete/'.$cat->id) }}" onclick="return confirm('Hapus kategori ini?')" class="bg-red-600 text-white px-2.5 py-1 rounded text-xs">Hapus</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div id="addModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl p-6 max-w-md w-full shadow-xl">
        <h3 class="text-lg font-bold mb-4">Tambah Kategori Baru</h3>
        <form action="{{ url('/admin/kategori/store') }}" method="POST" class="space-y-4">
            @csrf
            <div><label class="block text-sm font-medium mb-1">Nama Kategori</label><input type="text" name="name" class="w-full border rounded-lg p-2" required></div>
            <div><label class="block text-sm font-medium mb-1">Deskripsi</label><textarea name="description" class="w-full border rounded-lg p-2"></textarea></div>
            <div class="flex justify-end space-x-2"><button type="button" onclick="toggleModal('addModal')" class="bg-gray-200 px-4 py-2 rounded-lg text-sm">Batal</button><button type="submit" class="bg-[#C29B78] text-white px-4 py-2 rounded-lg text-sm">Simpan</button></div>
        </form>
    </div>
</div>

<div id="editModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl p-6 max-w-md w-full shadow-xl">
        <h3 class="text-lg font-bold mb-4">Ubah Data Kategori</h3>
        <form id="editForm" method="POST" class="space-y-4">
            @csrf
            <div><label class="block text-sm font-medium mb-1">Nama Kategori</label><input type="text" name="name" id="editName" class="w-full border rounded-lg p-2" required></div>
            <div><label class="block text-sm font-medium mb-1">Deskripsi</label><textarea name="description" id="editDesc" class="w-full border rounded-lg p-2"></textarea></div>
            <div class="flex justify-end space-x-2"><button type="button" onclick="toggleModal('editModal')" class="bg-gray-200 px-4 py-2 rounded-lg text-sm">Batal</button><button type="submit" class="bg-amber-500 text-white px-4 py-2 rounded-lg text-sm">Update</button></div>
        </form>
    </div>
</div>

<script>
    function toggleModal(id) { document.getElementById(id).classList.toggle('hidden'); }
    function openEditModal(id, name, desc) {
        document.getElementById('editForm').action = "/admin/kategori/update/" + id;
        document.getElementById('editName').value = name;
        document.getElementById('editDesc').value = desc;
        toggleModal('editModal');
    }
</script>
@endsection