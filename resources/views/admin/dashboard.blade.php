@extends('layouts.app')

@section('title', 'Dashboard - Panel Admin')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan aktivitas sistem peminjaman alat')

@section('content')

    {{--
        ================= Hero / Welcome banner =================
        Background pakai asset('images/hero-banner.jpg') — taruh file hero-banner.jpg
        (sudah saya rotasi 90° jadi landscape) di folder public/images/.
        Overlay gradasi gelap ditambahkan supaya teks tetap terbaca di atas gambar.
    --}}
    <div
        class="relative overflow-hidden rounded-3xl mb-8 px-8 py-9 bg-cover bg-center"
        style="background-image: url('{{ asset('images/hero-banner.jpg') }}');"
    >
        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/20"></div>

        <div class="relative z-10">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-white tracking-tight">
                        Welcome back, {{ auth()->user()->name ?? 'Admin' }}
                    </h2>
                    <p class="text-[14px] text-white/70 mt-1">
                        Berikut ringkasan performa sistem peminjaman alat hari ini
                    </p>
                </div>
                <span class="text-[13px] font-medium text-white bg-white/15 px-4 py-1.5 rounded-full">
                    {{ now()->translatedFormat('l, d F Y') }}
                </span>
            </div>

            {{-- mini stat pills — sesuaikan variabel dengan yang dikirim controller --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-7">
                @php
                    $miniStats = [
                        ['label' => 'Peminjaman Hari Ini', 'value' => $peminjamanHariIni ?? 0],
                        ['label' => 'User Baru', 'value' => $userBaru ?? 0],
                        ['label' => 'Alat Sedang Dipinjam', 'value' => $alatDipinjam ?? 0],
                        ['label' => 'Denda Terkumpul', 'value' => 'Rp' . number_format($dendaTerkumpul ?? 0, 0, ',', '.')],
                    ];
                @endphp
                @foreach ($miniStats as $s)
                    <div class="bg-white/10 backdrop-blur rounded-2xl px-5 py-4 border border-white/10">
                        <p class="text-[12px] text-white/60 mb-1">{{ $s['label'] }}</p>
                        <p class="text-xl font-bold text-white">{{ $s['value'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ================= Stat cards ================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        @php
            $statCards = [
                ['label' => 'Total User', 'value' => $totalUser ?? 0, 'color' => 'blue', 'icon' => 'users'],
                ['label' => 'Total Alat', 'value' => $totalAlat ?? 0, 'color' => 'emerald', 'icon' => 'box'],
                ['label' => 'Total Peminjaman', 'value' => $totalPeminjaman ?? 0, 'color' => 'orange', 'icon' => 'cart'],
                ['label' => 'Total Kategori', 'value' => $totalKategori ?? 0, 'color' => 'purple', 'icon' => 'tag'],
            ];
            $colorMap = [
                'blue'    => ['bg-blue-50', 'text-blue-600'],
                'emerald' => ['bg-emerald-50', 'text-emerald-600'],
                'orange'  => ['bg-orange-50', 'text-orange-600'],
                'purple'  => ['bg-purple-50', 'text-purple-600'],
            ];
        @endphp
        @foreach ($statCards as $card)
            @php [$bgClass, $textClass] = $colorMap[$card['color']]; @endphp
            <div class="bg-white rounded-2xl border border-black/5 p-5">
                <div class="w-10 h-10 rounded-xl {{ $bgClass }} {{ $textClass }} flex items-center justify-center mb-4">
                    @switch($card['icon'])
                        @case('users')
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                            @break
                        @case('box')
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5V18a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18V7.5m18 0L13.5 3.75a2.25 2.25 0 0 0-3 0L3 7.5m18 0-9 5.25L3 7.5" /></svg>
                            @break
                        @case('cart')
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.899-4.653 2.32-7.005a1.5 1.5 0 0 0-1.478-1.745H5.106M7.5 14.25 5.106 5.25M7.5 14.25 5.106 5.25" /></svg>
                            @break
                        @case('tag')
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.31a11.15 11.15 0 0 0 3.844-3.844c.562-.827.39-1.908-.31-2.607L10.31 3.659A2.25 2.25 0 0 0 8.318 3H9.568Z" /></svg>
                    @endswitch
                </div>
                <p class="text-2xl font-bold tracking-tight">{{ $card['value'] }}</p>
                <p class="text-[13px] text-subtle mt-0.5">{{ $card['label'] }}</p>
            </div>
        @endforeach
    </div>
     {{-- Aksi Cepat --}}
        <div class="bg-white rounded-2xl border border-black/5 p-6">
            <h3 class="font-semibold text-[15px] mb-1">Aksi Cepat</h3>
            <p class="text-[12.5px] text-subtle mb-5">Tugas admin yang sering dipakai</p>

            <div class="grid grid-cols-2 gap-3">
                @php
                    $quickActions = [
                        ['label' => 'Tambah Alat', 'route' => 'admin.alat.create', 'color' => 'bg-blue-600'],
                        ['label' => 'Kelola User', 'route' => 'admin.user.index', 'color' => 'bg-emerald-600'],
                        ['label' => 'Kelola Kategori', 'route' => 'admin.kategori.index', 'color' => 'bg-orange-500'],
                        ['label' => 'Persetujuan Peminjaman', 'route' => 'admin.peminjaman.index', 'color' => 'bg-purple-600'],
                    ];
                @endphp
                @foreach ($quickActions as $action)
                    <a href="{{ Route::has($action['route']) ? route($action['route']) : '#' }}"
                       class="{{ $action['color'] }} text-white rounded-xl px-4 py-4 text-[13px] font-semibold hover:opacity-90 transition">
                        {{ $action['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection