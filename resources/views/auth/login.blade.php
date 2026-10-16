<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Zipply Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden max-w-4xl w-full flex flex-col md:flex-row">
        
        <div class="w-full md:w-1/2 p-12 flex flex-col justify-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Login Admin</h2>
            <p class="text-gray-500 mb-6 text-sm">Silakan masuk menggunakan akun admin Anda.</p>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm font-medium flex items-center">
                    <i class="fa-solid fa-circle-check mr-2 text-base"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm font-medium flex items-center">
                    <i class="fa-solid fa-circle-exclamation mr-2 text-base"></i>
                    {{ session('error') }}
                </div>
            @endif
            
            <form action="{{ url('/login') }}" method="POST" class="space-y-6">
                @csrf {{-- Proteksi Token Keamanan Laravel --}}
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" 
                           name="email" {{-- Atribut Name Wajib Ada --}}
                           value="{{ old('email') }}"
                           placeholder="admin@tokotas.com" 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#C29B78] focus:outline-none transition" 
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" 
                           name="password" {{-- Atribut Name Wajib Ada --}}
                           placeholder="••••••••" 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#C29B78] focus:outline-none transition" 
                           required>
                </div>
                <button type="submit" class="w-full bg-[#C29B78] hover:bg-[#B38A67] text-white font-semibold py-3 rounded-lg shadow-md transition-all">
                    Login
                </button>
            </form>
        </div>
        
        <div class="w-full md:w-1/2 bg-[#C29B78] flex flex-col items-center justify-center p-12 text-white">
            <div class="bg-white p-6 rounded-2xl shadow-lg mb-4 text-[#C29B78]">
                <i class="fa-solid fa-bag-shopping text-6xl"></i>
            </div>
            <h1 class="text-4xl font-extrabold tracking-wider">Zipply Shop</h1>
            <p class="text-amber-100 mt-2 text-center text-sm">Sistem Informasi Manajemen Inventaris & Stok Tas</p>
        </div>
    </div>
</body>
</html>