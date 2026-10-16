<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> TOKO TAS </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">
        <aside class="w-64 bg-[#C29B78] text-white flex flex-col z-20 shadow-lg">
            <div class="p-5 flex flex-col items-center border-b border-[#B38A67]">
                <div class="bg-white p-3 rounded-xl shadow-inner mb-2">
                    <i class="fa-solid fa-bag-shopping text-3xl text-[#C29B78]"></i>
                </div>
                <span class="font-bold text-xl tracking-wider text-amber-950">Zipply Shop</span>
            </div>
            
            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <a href="{{ url('/admin/dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-[#B38A67] transition-all {{ Request::is('admin/dashboard') ? 'bg-[#B38A67] font-semibold' : '' }}">
                    <i class="fa-solid fa-chart-pie w-5"></i> <span>Dashboard</span>
                </a>
                <a href="{{ url('/admin/persediaan') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-[#B38A67] transition-all {{ Request::is('admin/persediaan') ? 'bg-[#B38A67] font-semibold' : '' }}">
                    <i class="fa-solid fa-boxes-stacked w-5"></i> <span>Persediaan Barang</span>
                </a>
                
                <div class="pt-2">
                    <span class="px-4 text-xs font-semibold text-amber-950 uppercase tracking-wider">Master Data</span>
                    <div class="mt-1 space-y-1 pl-2">
                        <a href="{{ url('/admin/kategori') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg hover:bg-[#B38A67] text-sm transition-all {{ Request::is('admin/kategori') ? 'bg-[#B38A67] font-semibold' : '' }}">
                            <i class="fa-solid fa-tags w-4"></i> <span>Kategori Barang</span>
                        </a>
                        <a href="{{ url('/admin/daftar-barang') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg hover:bg-[#B38A67] text-sm transition-all {{ Request::is('admin/daftar-barang') ? 'bg-[#B38A67] font-semibold' : '' }}">
                            <i class="fa-solid fa-list w-4"></i> <span>Daftar Barang</span>
                        </a>
                        <a href="{{ url('/admin/pengguna') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg hover:bg-[#B38A67] text-sm transition-all {{ Request::is('admin/pengguna') ? 'bg-[#B38A67] font-semibold' : '' }}">
                            <i class="fa-solid fa-users w-4"></i> <span>Manajemen Pengguna</span>
                        </a>
                    </div>
                </div>

                <a href="{{ url('/admin/laporan') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-[#B38A67] transition-all {{ Request::is('admin/laporan') ? 'bg-[#B38A67] font-semibold' : '' }}">
                    <i class="fa-solid fa-file-invoice w-5"></i> <span>Laporan</span>
                </a>
            </nav>

            <div class="p-4 border-t border-[#B38A67]">
                <a href="{{ url('/login') }}" 
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-red-700 hover:bg-red-800 transition-all text-center justify-center font-medium">
                    <i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span>
                </a>
                
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm border-b px-8 py-4 flex justify-between items-center z-10">
                <div class="relative w-64">
                    <input type="text" placeholder="Search..." class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#C29B78] text-sm">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400 text-sm"></i>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm font-semibold text-gray-700">Admin</span>
                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                        <i class="fa-solid fa-user text-gray-600"></i>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-8">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>