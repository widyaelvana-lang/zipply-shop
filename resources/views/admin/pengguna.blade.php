@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Pengguna</h1>
            <p class="text-sm text-gray-500">Master data akun administrator kendali sistem.</p>
        </div>
        <button onclick="toggleModal('addPenggunaModal')" class="bg-[#C29B78] hover:bg-[#b08a67] text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
            <i class="fa-solid fa-plus mr-2"></i>Tambah Pengguna
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-gray-50 border-b font-semibold text-gray-600">
                <tr>
                    <th class="p-4 w-12 text-center">No</th>
                    <th class="p-4">Nama Lengkap</th>
                    <th class="p-4">Alamat Email</th>
                    <th class="p-4 text-center">Status Sesi</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y text-gray-600">
                @forelse($users as $index => $user)
                <tr class="hover:bg-gray-50 {{ auth()->id() == $user->id ? 'bg-blue-50/40' : '' }}">
                    <td class="p-4 text-center text-gray-400">{{ $index + 1 }}</td>
                    <td class="p-4 font-semibold text-gray-800">
                        {{ $user->name }}
                        @if(auth()->id() == $user->id)
                            <span class="text-xs font-normal text-blue-600 ltr:ml-2 rtl:mr-2">(Anda)</span>
                        @endif
                    </td>
                    <td class="p-4 font-mono text-xs">{{ $user->email }}</td>
                    
                    <!-- INDIKATOR STATUS AKTIF / TIDAK AKTIF -->
                    <td class="p-4 text-center">
                        @if(auth()->id() == $user->id)
                            <span class="px-2.5 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full inline-flex items-center">
                                <span class="w-2 h-2 rounded-full bg-green-500 mr-1.5 animate-pulse"></span>
                                Aktif
                            </span>
                        @else
                            <span class="px-2.5 py-1 bg-gray-100 text-gray-500 text-xs font-bold rounded-full inline-flex items-center">
                                <span class="w-2 h-2 rounded-full bg-gray-400 mr-1.5"></span>
                                Tidak Aktif
                            </span>
                        @endif
                    </td>
                    
                    <!-- AKSI DENGAN VALIDASI KEAMANAN -->
                    <td class="p-4 text-center">
                        @if(auth()->id() == $user->id)
                            <span class="text-xs text-gray-400 italic">Sedang Digunakan</span>
                        @else
                            <a href="{{ url('/admin/pengguna/delete/'.$user->id) }}" 
                               onclick="return confirm('Apakah Anda yakin ingin menghapus akun admin {{ $user->name }}?')" 
                               class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-medium transition-colors shadow-sm">
                                Hapus
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-gray-400 italic">Belum ada admin terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL TAMBAH PENGGUNA -->
<div id="addPenggunaModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl p-6 max-w-md w-full shadow-xl">
        <h3 class="text-lg font-bold mb-4">Tambah Admin Baru</h3>
        <form action="{{ url('/admin/pengguna/store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                <input type="text" name="name" class="w-full border rounded-lg p-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" class="w-full border rounded-lg p-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" placeholder="Minimal 6 karakter" class="w-full border rounded-lg p-2" required>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="toggleModal('addPenggunaModal')" class="bg-gray-200 px-4 py-2 rounded-lg text-sm">Batal</button>
                <button type="submit" class="bg-[#C29B78] text-white px-4 py-2 rounded-lg text-sm">Simpan Akun</button>
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