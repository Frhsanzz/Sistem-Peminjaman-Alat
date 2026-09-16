<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin')</title>
    <!-- Memuat Tailwind CSS melalui CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">

        <!-- SIDEBAR -->
        <aside class="w-64 bg-gray-900 text-white flex flex-col">
            @if(auth()->user()->role === 'admin')
            <div class="p-5 text-xl font-bold tracking-wider border-b border-gray-800">
                PANEL ADMIN
            </div>
            @endif

           @if(auth()->user()->role === 'petugas')
            <div class="p-5 text-xl font-bold tracking-wider border-b border-gray-800">
                PANEL PETUGAS
            </div>
            @endif

            <nav class="flex-1 p-4 space-y-2">
                <!-- Menu Khusus Admin -->
                 @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" 
                   class="block px-4 py-2 rounded-lg bg-gray-800 text-white font-medium">
                   Dashboard
                </a>
                <a href="{{ route('admin.user.index') }}" 
                   class="block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition">
                   Kelola User
                </a>
                <a href="{{ route('admin.kategori.index') }}" 
                   class="block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition">
                   Kelola Kategori
                </a>
                <a href="{{ route('admin.alat.index') }}" 
                   class="block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition">
                   Kelola Alat
                </a>
                <a href="{{ route('admin.peminjaman.index') }}" 
                   class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.peminjaman.index') ? 'bg-gray-800 text-white' : '' }} text-gray-400 hover:bg-gray-800 hover:text-white">
                   Kelola Peminjaman
                </a>
                <a href="{{ route('admin.pengembalian.index') }}" 
                    class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.pengembalian.*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    Kelola Pengembalian
                </a>
                @endif

                <!-- MENU KHUSUS PETUGAS -->
                @if(auth()->user()->role === 'petugas')
                <a href="{{ route('petugas.peminjaman.index') }}"
                   class="block px-4 py-2 rounded-lg transition {{ 
                    request()->routeIs('petugas.peminjaman.*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    Persetujuan Peminjaman
                </a>

                <a href="{{ route('petugas.pengembalian.index') }}"
                    class="block px-4 py-2 rounded-lg transition {{ 
                    request()->routeIs('petugas.pengembalian.*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    Pemantauan Pengembalian
                </a>

                <a href="{{ route('petugas.cetaklaporan.index') }}"
    class="block px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('petugas.cetaklaporan.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }} transition">
    Cetak Laporan
</a>
                @endif
            </nav>

            <div class="p-4 border-t border-gray-800 text-sm text-gray-400">
                Logged in as: <span class="text-white font-semibold">{{ auth()->user()->name }}</span>
            </div>
        </aside>

        <!-- MAIN CONTENT CONTAINER -->
        <div class="flex-1 flex flex-col overflow-y-auto">

            <!-- NAVBAR ATAS -->
            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6 z-10">
                <div class="text-lg font-semibold text-gray-800">
                    @yield('header-title', 'Dashboard')
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                        Logout
                    </button>
                </form>
            </header>

            <!-- KONTEN UTAMA HALAMAN -->
            <main class="flex-1 p-6">
                @yield('content')
            </main>

        </div>
    </div>
</body>
</html>