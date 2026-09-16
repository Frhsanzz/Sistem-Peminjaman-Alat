@extends('layouts.peminjam')

@section('title', 'Katalog Alat - Peminjam')

@section('content')

    {{-- ================= Flash message ================= --}}
    @if (session('success'))
        <div class="max-w-5xl mx-auto px-4 pt-6">
            <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10
                        px-5 py-3 text-sm text-emerald-400">
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- ================= Page header ================= --}}
    <div class="max-w-5xl mx-auto px-4 pt-8 pb-6 flex items-end justify-between gap-6 flex-wrap">

        <div>
            <h1 class="text-3xl font-bold tracking-tight text-white">
                Katalog Alat Tersedia
            </h1>
            <p class="text-sm text-white/40 mt-1">
                Pilih alat yang ingin kamu pinjam
            </p>
        </div>

        <a href="{{ route('peminjam.peminjaman') }}"
           class="text-[13px] font-medium text-white/60 hover:text-white transition whitespace-nowrap
                  rounded-full border border-white/10 bg-white/5 px-4 py-2 backdrop-blur-xl">
            Lihat Peminjaman Saya &rarr;
        </a>

    </div>


    <div class="max-w-5xl mx-auto px-4 pb-20">

        <form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST" class="space-y-6">
            @csrf

            {{-- ================= Rencana Pengembalian ================= --}}
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl
                        shadow-xl shadow-black/20 flex flex-wrap items-center justify-between gap-6">

                <div>
                    <label for="tanggal_kembali" class="block text-[15px] font-semibold text-white mb-1">
                        Rencana Pengembalian
                    </label>
                    <p class="text-sm text-white/40 max-w-sm">
                        Tentukan tanggal ketika seluruh alat akan dikembalikan.
                    </p>
                </div>

                <input
                    type="date"
                    id="tanggal_kembali"
                    name="tanggal_kembali"
                    required
                    class="w-full sm:w-64 rounded-xl border border-white/10 bg-white/5 px-4 py-3
                           text-[15px] text-white placeholder-white/30
                           [color-scheme:dark]
                           focus:outline-none focus:ring-4 focus:ring-blue-500/20
                           focus:border-blue-400/40 transition"
                >

            </div>


            {{-- ================= Grid kartu alat ================= --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                @forelse ($alat as $item)

                    <div class="rounded-3xl border border-white/10 bg-white/5 p-3
                        backdrop-blur-xl shadow-xl shadow-black/20">

                        {{-- ================= Gambar ================= --}}
                        <div class="mb-3 w-full aspect-[4/3] rounded-2xl overflow-hidden
                            bg-white/[0.04] flex items-center justify-center">

                            @if ($item->gambar)

    @php
        $gambar = $item->gambar;

        // Hilangkan "storage/" jika tersimpan di database
        $gambar = preg_replace('#^storage/#', '', $gambar);

        // Jika belum ada folder alat/, tambahkan
        if (!str_starts_with($gambar, 'alat/')) {
            $gambar = 'alat/' . ltrim($gambar, '/');
        }
    @endphp

    <img
        src="{{ asset('storage/' . $gambar) }}"
        alt="{{ $item->nama_alat }}"
        class="h-full w-full object-contain"
        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
    >

    <div
        style="display:none"
        class="h-full w-full items-center justify-center"
    >
        <svg xmlns="http://www.w3.org/2000/svg"
             class="h-10 w-10 text-white/20"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor"
             stroke-width="1.5">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M9 3h6m-7 4h8M7 7v10a5 5 0 005 5 5 5 0 005-5V7M8 7h8" />
        </svg>
    </div>

@else

                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white/20"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M9 3h6m-7 4h8M7 7v10a5 5 0 005 5 5 5 0 005-5V7M8 7h8" />
                                </svg>

                            @endif

                        </div>


                        {{-- ================= Nama & kategori ================= --}}
                        <h3 class="text-[17px] font-bold text-white truncate">
                            {{ $item->nama_alat }}
                        </h3>

                        <p class="text-sm text-white/40 mt-0.5">
                            {{ $item->kategori->nama_kategori ?? '-' }}
                        </p>


                        {{-- ================= Status & stok ================= --}}
                        <div class="flex items-center justify-between mt-4">

                            @if ($item->stok > 0)

                                <span class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-400">
                                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                    Tersedia
                                </span>

                            @else

                                <span class="inline-flex items-center gap-1.5 text-sm font-medium text-red-400">
                                    <span class="h-2 w-2 rounded-full bg-red-400"></span>
                                    Habis
                                </span>

                            @endif

                            <span class="text-sm text-white/50">
                                Stok: <span class="font-semibold text-white">{{ $item->stok }}</span>
                            </span>

                        </div>


                        {{-- ================= Pilih alat & jumlah pinjam ================= --}}
                        <div class="mt-4 pt-4 border-t border-white/10 flex items-center justify-between gap-3">

                            <label class="flex items-center gap-2 text-sm text-white/60 cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="alat_id[]"
                                    value="{{ $item->id }}"
                                    class="w-5 h-5 rounded-md border-white/20 bg-white/5
                                           text-blue-500 accent-blue-500 cursor-pointer"
                                >
                                Pilih alat
                            </label>

                            <input
                                type="number"
                                name="jumlah[{{ $item->id }}]"
                                value="1"
                                min="1"
                                max="{{ $item->stok }}"
                                class="w-16 text-center rounded-xl border border-white/10
                                       bg-white/5 px-2 py-2 text-[15px] text-white
                                       [color-scheme:dark]
                                       focus:outline-none focus:ring-4
                                       focus:ring-blue-500/20
                                       focus:border-blue-400/40 transition"
                            >

                        </div>

                    </div>


                @empty

                    <div class="sm:col-span-2 text-center text-white/40 rounded-3xl border
                                border-white/10 bg-white/5 backdrop-blur-xl px-7 py-16">
                        Belum ada alat yang tersedia.
                    </div>

                @endforelse

            </div>


            {{-- ================= Action bar ================= --}}
            <div class="flex justify-end pt-2">

                <button
                    type="submit"
                    class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500
                           hover:to-indigo-500 text-white text-[15px] font-semibold
                           rounded-full px-8 py-3.5 shadow-lg shadow-blue-600/30 transition">

                    Ajukan Peminjaman

                </button>

            </div>

        </form>

    </div>

@endsection