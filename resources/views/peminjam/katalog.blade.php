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
        @if (session('error') || $errors->any())
        <div class="max-w-5xl mx-auto px-4 pt-6">
            <div class="rounded-2xl border border-red-500/20 bg-red-500/10
                        px-5 py-3 text-sm text-red-400">
                @if (session('error'))
                    <p>{{ session('error') }}</p>
                @endif
                @foreach ($errors->all() as $e)
                    <p>{{ $e }}</p>
                @endforeach
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

            {{-- ================= Pencarian Alat ================= --}}
            <div class="rounded-3xl border border-white/10 bg-white/5 p-4 sm:p-5 backdrop-blur-xl
                        shadow-xl shadow-black/20">

                <label for="cari_alat" class="sr-only">Cari alat</label>

                <div class="flex items-center gap-3">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/30 shrink-0"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 21l-4.35-4.35M17 10.5A6.5 6.5 0 114 10.5a6.5 6.5 0 0113 0z" />
                    </svg>

                    <input
                        type="text"
                        id="cari_alat"
                        placeholder='Cari alat, misalnya "kamera" atau "tang crimping"...'
                        class="w-full bg-transparent text-[15px] text-white placeholder-white/30
                               focus:outline-none"
                        autocomplete="off"
                    >

                </div>

            </div>


            {{-- ================= Grid kartu alat ================= --}}
            <div id="grid-alat" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                @forelse ($alat as $item)

                    <div
                        class="kartu-alat rounded-3xl border border-white/10 bg-white/5 p-3
                            backdrop-blur-xl shadow-xl shadow-black/20"
                        data-nama="{{ strtolower($item->nama_alat) }}"
                        data-kategori="{{ strtolower($item->kategori->nama_kategori ?? '') }}"
                    >

                        {{-- ================= Gambar + checkbox pilih (pojok kanan atas) ================= --}}
                        <div class="relative mb-3 w-full aspect-[4/3] rounded-2xl overflow-hidden
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

                            {{-- ===== Checkbox pilih alat, pojok kanan atas foto ===== --}}
                            <label
                                class="absolute right-2 top-2 flex items-center gap-1.5
                                       rounded-lg border border-white/10 bg-black/70 backdrop-blur-md
                                       px-2 py-1.5 cursor-pointer"
                            >
                                <input
                                    type="checkbox"
                                    name="alat_id[]"
                                    value="{{ $item->id }}"
                                    class="checkbox-alat w-[18px] h-[18px] rounded-md border-white/20
                                           bg-white/5 text-blue-500 accent-blue-500 cursor-pointer shrink-0"
                                    @disabled($item->stok < 1)
                                >
                                <span class="text-[11px] font-medium text-white/70">Pilih</span>
                            </label>

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


                        {{-- ================= Tanggal kembali (kiri bawah) + jumlah & tombol ajukan (kanan bawah) ================= --}}
                        <div class="mt-4 pt-4 border-t border-white/10 flex items-end justify-between gap-3">

                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] text-white/40">Tanggal Kembali</label>
                                <input
                                    type="date"
                                    min="{{ now()->toDateString }}"
                                    name="tanggal_kembali[{{ $item->id }}]"
                                    disabled
                                    class="tanggal-alat w-[140px] rounded-xl border border-white/10
                                           bg-white/5 px-3 py-2 text-[13px] text-white
                                           [color-scheme:dark]
                                           focus:outline-none focus:ring-4 focus:ring-blue-500/20
                                           focus:border-blue-400/40 transition
                                           disabled:opacity-40"
                                >
                            </div>

                            <div class="flex flex-col items-end gap-2">
                                <input
                                    type="number"
                                    name="jumlah[{{ $item->id }}]"
                                    value="1"
                                    min="1"
                                    max="{{ max(0, $item->stok - $item->jumlah_rusak }}"
                                    class="w-16 text-center rounded-xl border border-white/10
                                           bg-white/5 px-2 py-2 text-[15px] text-white
                                           [color-scheme:dark]
                                           focus:outline-none focus:ring-4
                                           focus:ring-blue-500/20
                                           focus:border-blue-400/40 transition"
                                >

                                <button
                                    type="submit"
                                    class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500
                                           hover:to-indigo-500 text-white text-[12.5px] font-semibold
                                           rounded-full px-4 py-2 shadow-lg shadow-blue-600/30 transition
                                           whitespace-nowrap">
                                    Ajukan Peminjaman
                                </button>
                            </div>

                        </div>

                    </div>


                @empty

                    <div class="sm:col-span-2 text-center text-white/40 rounded-3xl border
                                border-white/10 bg-white/5 backdrop-blur-xl px-7 py-16">
                        Belum ada alat yang tersedia.
                    </div>

                @endforelse

            </div>

            {{-- Pesan ketika hasil pencarian kosong --}}
            <p id="tidak-ditemukan" class="hidden text-center text-white/40 rounded-3xl border
                        border-white/10 bg-white/5 backdrop-blur-xl px-7 py-16">
                Alat tidak ditemukan. Coba kata kunci lain.
            </p>

        </form>

    </div>


    {{-- ================= Script: pencarian + sinkron checkbox & tanggal ================= --}}
    <script>
        // Saat checkbox alat dicentang, aktifkan input tanggal di kartu yang sama
        // (dan wajibkan diisi), saat tidak dicentang, nonaktifkan lagi.
        document.querySelectorAll('.kartu-alat').forEach(function (kartu) {
            const checkbox = kartu.querySelector('.checkbox-alat');
            const tanggal  = kartu.querySelector('.tanggal-alat');

            checkbox.addEventListener('change', function () {
                tanggal.disabled  = !checkbox.checked;
                tanggal.required  = checkbox.checked;
                if (!checkbox.checked) {
                    tanggal.value = '';
                }
            });
        });

        // Pencarian alat berdasarkan nama & kategori (client-side, tanpa reload)
        const inputCari   = document.getElementById('cari_alat');
        const semuaKartu  = document.querySelectorAll('.kartu-alat');
        const tidakDitemukan = document.getElementById('tidak-ditemukan');

        inputCari.addEventListener('input', function () {
            const kata = inputCari.value.trim().toLowerCase();
            let adaYangTampil = false;

            semuaKartu.forEach(function (kartu) {
                const cocok = kartu.dataset.nama.includes(kata) ||
                              kartu.dataset.kategori.includes(kata);

                kartu.style.display = cocok ? '' : 'none';
                if (cocok) adaYangTampil = true;
            });

            tidakDitemukan.classList.toggle('hidden', adaYangTampil || kata === '');
        });
    </script>

@endsection