@extends('layouts.peminjam')

@section('title', 'Riwayat Peminjaman - SIPPSD')
@section('header-title', 'Riwayat Peminjaman')

@section('content')

    {{-- =====================================================
         ALERT
    ====================================================== --}}
    @if(session('success'))

        <div
            class="mb-5 p-4 rounded-xl
                   bg-emerald-50 border border-emerald-200
                   text-emerald-700">

            <div class="flex items-center gap-2">

                <i class="bi bi-check-circle-fill"></i>

                <span>
                    {{ session('success') }}
                </span>

            </div>

        </div>

    @endif


    @if(session('error'))

        <div
            class="mb-5 p-4 rounded-xl
                   bg-red-50 border border-red-200
                   text-red-700">

            <div class="flex items-center gap-2">

                <i class="bi bi-exclamation-circle-fill"></i>

                <span>
                    {{ session('error') }}
                </span>

            </div>

        </div>

    @endif


    {{-- =====================================================
        HERO RIWAYAT
    ====================================================== --}}
    <section
        class="relative overflow-hidden
            rounded-3xl
            mb-8
            border border-indigo-200
            shadow-sm">

        {{-- Background --}}
        <div
            class="absolute inset-0
                bg-gradient-to-r
                from-indigo-700
                via-purple-700
                to-indigo-600">
        </div>


        {{-- Dekorasi --}}
        <div
            class="absolute -right-16 -top-20
                w-72 h-72
                rounded-full
                bg-white/10">
        </div>

        <div
            class="absolute -right-5 -bottom-28
                w-80 h-80
                rounded-full
                bg-white/5">
        </div>


        {{-- Icon dekorasi --}}
        <div
            class="absolute right-10 top-1/2
                -translate-y-1/2
                hidden md:flex
                w-32 h-32
                rounded-full
                bg-white/10
                items-center
                justify-center">

            <i
                class="bi bi-clock-history
                    text-7xl
                    text-white/20">
            </i>

        </div>


        {{-- Konten --}}
        <div
            class="relative z-10
                px-8 py-8
                md:px-10 md:py-10">

            <div class="flex items-center gap-4">

                <div
                    class="w-14 h-14
                        rounded-2xl
                        bg-white/15
                        border border-white/20
                        flex items-center
                        justify-center
                        shrink-0">

                    <i
                        class="bi bi-clock-history
                            text-2xl
                            text-white">
                    </i>

                </div>


                <div>

                    <p
                        class="text-sm
                            font-semibold
                            text-indigo-100">

                        SIPPSD

                    </p>

                    <h1
                        class="mt-1
                            text-2xl md:text-3xl
                            font-extrabold
                            text-white">

                        Riwayat Peminjaman

                    </h1>

                    <p
                        class="mt-2
                            text-sm
                            text-indigo-100
                            max-w-xl">

                        Pantau pengajuan, peminjaman,
                        dan proses pengembalian alatmu.

                    </p>

                </div>

            </div>

        </div>

    </section>

    {{-- =====================================================
        STATISTIK RIWAYAT
    ====================================================== --}}
    <div
        class="grid
            grid-cols-1
            sm:grid-cols-3
            gap-4
            mb-8">


        {{-- TOTAL --}}
        <div
            class="bg-white
                rounded-2xl
                border border-slate-200
                shadow-sm
                p-5
                hover:shadow-md
                transition">

            <div
                class="flex items-center
                    justify-between">

                <div>

                    <p
                        class="text-sm
                            text-slate-500">

                        Total Peminjaman

                    </p>

                    <p
                        class="mt-1
                            text-3xl
                            font-extrabold
                            text-slate-800">

                        {{ $peminjaman->count() }}

                    </p>

                </div>


                <div
                    class="w-11 h-11
                        rounded-xl
                        bg-indigo-100
                        flex items-center
                        justify-center">

                    <i
                        class="bi bi-clipboard-check
                            text-xl
                            text-indigo-600">
                    </i>

                </div>

            </div>

        </div>


        {{-- AKTIF --}}
        <div
            class="bg-white
                rounded-2xl
                border border-slate-200
                shadow-sm
                p-5
                hover:shadow-md
                transition">

            <div
                class="flex items-center
                    justify-between">

                <div>

                    <p
                        class="text-sm
                            text-slate-500">

                        Peminjaman Aktif

                    </p>

                    <p
                        class="mt-1
                            text-3xl
                            font-extrabold
                            text-slate-800">

                       {{ $peminjaman->whereIn('status', ['dipinjamkan', 'telat'])->count() }}

                    </p>

                </div>


                <div
                    class="w-11 h-11
                        rounded-xl
                        bg-amber-100
                        flex items-center
                        justify-center">

                    <i
                        class="bi bi-box-seam
                            text-xl
                            text-amber-600">
                    </i>

                </div>

            </div>

        </div>


        {{-- SELESAI --}}
        <div
            class="bg-white
                rounded-2xl
                border border-slate-200
                shadow-sm
                p-5
                hover:shadow-md
                transition">

            <div
                class="flex items-center
                    justify-between">

                <div>

                    <p
                        class="text-sm
                            text-slate-500">

                        Peminjaman Selesai

                    </p>

                    <p
                        class="mt-1
                            text-3xl
                            font-extrabold
                            text-slate-800">

                        {{ $peminjaman->where('status', 'dikembalikan')->count() }}

                    </p>

                </div>


                <div
                    class="w-11 h-11
                        rounded-xl
                        bg-emerald-100
                        flex items-center
                        justify-center">

                    <i
                        class="bi bi-check-circle
                            text-xl
                            text-emerald-600">
                    </i>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
        JUDUL DAFTAR
    ====================================================== --}}
    <div class="mb-5">

        <div class="flex items-center gap-2">

            <div
                class="w-1 h-7
                    rounded-full
                    bg-indigo-600">
            </div>

            <h2
                class="text-2xl
                    font-bold
                    text-slate-800">

                Riwayat Peminjaman Saya

            </h2>

        </div>

        <p
            class="mt-1 ml-3
                text-sm
                text-slate-500">

            Pantau seluruh pengajuan dan peminjaman alatmu.

        </p>

    </div>


    {{-- =====================================================
         DAFTAR PEMINJAMAN
    ====================================================== --}}
    @forelse($peminjaman as $peminjam)
    @php
        $status = $peminjam->status;

        // Otomatis "telat" jika sudah lewat tanggal rencana kembali
        if ($status === 'dipinjamkan'
            && \Carbon\Carbon::parse($peminjam->tgl_kembali_plan)->lt(today())) {
            $status = 'telat';
        }
    @endphp
        <div
            class="group
                bg-white
                rounded-2xl
                border border-slate-200
                shadow-sm
                overflow-hidden
                mb-6
                hover:shadow-lg
                hover:border-indigo-200
                transition-all
                duration-300">

            <div
                class="h-1
                    bg-gradient-to-r
                    from-indigo-500
                    via-purple-500
                    to-indigo-500">
            </div>


            {{-- =================================================
                 HEADER CARD
            ================================================== --}}
            <div
                class="px-5 py-4
                       border-b border-slate-100
                       flex flex-col
                       sm:flex-row
                       sm:items-center
                       sm:justify-between
                       gap-3">

                <div>

                    <p
                        class="text-xs
                               text-slate-400
                               font-medium">

                        ID PEMINJAMAN

                    </p>

                    <h3
                        class="mt-0.5
                               font-bold
                               text-slate-800">

                        Peminjaman #{{ $peminjam->id }}

                    </h3>

                </div>


                {{-- STATUS --}}
                @if($status === 'diajukan')

                    <span
                        class="inline-flex
                               items-center
                               gap-1.5
                               px-3 py-1.5
                               rounded-full
                               bg-amber-100
                               text-amber-700
                               text-xs
                               font-semibold">

                        <span
                            class="w-1.5 h-1.5
                                   rounded-full
                                   bg-amber-500">
                        </span>

                        Menunggu Persetujuan

                    </span>


                @elseif($status === 'dipinjamkan')

                    <span
                        class="inline-flex
                               items-center
                               gap-1.5
                               px-3 py-1.5
                               rounded-full
                               bg-indigo-100
                               text-indigo-700
                               text-xs
                               font-semibold">

                        <span
                            class="w-1.5 h-1.5
                                   rounded-full
                                   bg-indigo-500">
                        </span>

                        Sedang Dipinjam

                    </span>


                @elseif($status === 'telat')

                    <span
                        class="inline-flex
                               items-center
                               gap-1.5
                               px-3 py-1.5
                               rounded-full
                               bg-red-100
                               text-red-700
                               text-xs
                               font-semibold">

                        <span
                            class="w-1.5 h-1.5
                                   rounded-full
                                   bg-red-500">
                        </span>

                        Terlambat

                    </span>


              @elseif($status === 'dikembalikan')
                    <span
                        class="inline-flex
                               items-center
                               gap-1.5
                               px-3 py-1.5
                               rounded-full
                               bg-emerald-100
                               text-emerald-700
                               text-xs
                               font-semibold">

                        <span
                            class="w-1.5 h-1.5
                                   rounded-full
                                   bg-emerald-500">
                        </span>

                        Dikembalikan

                    </span>

                @elseif($status === 'ditolak')

                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                 bg-slate-100 text-slate-600 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                        Ditolak
                    </span>
                @else

                    <span
                        class="inline-flex
                               items-center
                               gap-1.5
                               px-3 py-1.5
                               rounded-full
                               bg-slate-100
                               text-slate-600
                               text-xs
                               font-semibold">

                        {{ ucfirst($status) }}

                    </span>

                @endif

            </div>


            {{-- =================================================
                 INFORMASI PEMINJAMAN
            ================================================== --}}
            <div class="p-5">


                <div
                    class="grid
                           grid-cols-1
                           sm:grid-cols-2
                           lg:grid-cols-3
                           gap-4
                           mb-6">


                    {{-- Tanggal Pinjam --}}
                    <div
                        class="rounded-xl
                               bg-slate-50
                               border border-slate-100
                               p-4">

                        <div
                            class="flex items-center
                                   gap-2
                                   text-slate-400">

                            <i class="bi bi-calendar-event"></i>

                            <span class="text-xs font-medium">
                                Tanggal Pinjam
                            </span>

                        </div>

                        <p
                            class="mt-2
                                   text-sm
                                   font-semibold
                                   text-slate-700">

                            {{ \Carbon\Carbon::parse($peminjam->tgl_pinjam)->format('d-m-Y H:i') }}

                        </p>

                    </div>


                    {{-- Rencana Kembali --}}
                    <div
                        class="rounded-xl
                               bg-slate-50
                               border border-slate-100
                               p-4">

                        <div
                            class="flex items-center
                                   gap-2
                                   text-slate-400">

                            <i class="bi bi-calendar-check"></i>

                            <span class="text-xs font-medium">
                                Rencana Kembali
                            </span>

                        </div>

                        <p
                            class="mt-2
                                   text-sm
                                   font-semibold
                                   text-slate-700">

                            {{ \Carbon\Carbon::parse($peminjam->tgl_kembali_plan)->format('d-m-Y') }}

                        </p>

                    </div>


                    {{-- Status --}}
                    <div
                        class="rounded-xl
                               bg-slate-50
                               border border-slate-100
                               p-4">

                        <div
                            class="flex items-center
                                   gap-2
                                   text-slate-400">

                            <i class="bi bi-info-circle"></i>

                            <span class="text-xs font-medium">
                                Status
                            </span>

                        </div>

                        <p
                            class="mt-2
                                   text-sm
                                   font-semibold
                                   text-slate-700">

                            @if($status === 'diajukan')
                                Menunggu Persetujuan
                            @elseif($status === 'dipinjamkan')
                                Sedang Dipinjam
                            @elseif($status === 'telat')
                                Terlambat
                           @elseif($status === 'dikembalikan')
                                Dikembalikan
                            @elseif($status === 'ditolak') 
                                Ditolak   
                            @else
                                {{ ucfirst($status) }}
                                
                            @endif

                        </p>

                    </div>

                </div>


                {{-- =================================================
                     DAFTAR ALAT
                ================================================== --}}
                <div>

                    <div
                        class="flex items-center
                               gap-2
                               mb-3">

                        <i
                            class="bi bi-box-seam
                                   text-indigo-600">
                        </i>

                        <h4
                            class="font-semibold
                                   text-slate-800">

                            Alat yang Dipinjam

                        </h4>

                    </div>


                    <div
                        class="border border-slate-200
                               rounded-xl
                               overflow-hidden">

                        <div
                            class="overflow-x-auto">

                            <table
                                class="w-full
                                       text-sm">

                                <thead
                                    class="bg-slate-50/80
                                           border-b
                                           border-slate-200">

                                    <tr class="hover:bg-indigo-50/40 transition">

                                        <th
                                            class="px-4 py-3
                                                   text-left
                                                   font-semibold
                                                   text-slate-600">

                                            Nama Alat

                                        </th>

                                        <th
                                            class="px-4 py-3
                                                   text-left
                                                   font-semibold
                                                   text-slate-600">

                                            Kategori

                                        </th>

                                        <th
                                            class="px-4 py-3
                                                   text-center
                                                   font-semibold
                                                   text-slate-600">

                                            Jumlah

                                        </th>

                                    </tr>

                                </thead>


                                <tbody
                                    class="divide-y
                                           divide-slate-100">

                                    @foreach($peminjam->detailPinjam as $detail)

                                        <tr
                                            class="hover:bg-indigo-50/40 transition">

                                            <td
                                                class="px-4 py-3
                                                       font-medium
                                                       text-slate-700">

                                                {{ $detail->alat->nama_alat ?? '-' }}

                                            </td>

                                            <td
                                                class="px-4 py-3
                                                       text-slate-500">

                                                {{ $detail->alat->kategori->nama_kategori ?? '-' }}

                                            </td>

                                            <td
                                                class="px-4 py-3
                                                       text-center
                                                       font-semibold
                                                       text-slate-700">

                                                {{ $detail->jumlah }}

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>


                {{-- ================= DIAJUKAN: tombol batal ================= --}}
@if($status === 'diajukan')

    <div class="mt-5 pt-5 border-t border-slate-100 flex items-center justify-between gap-3">
        <p class="text-sm text-amber-700">Menunggu persetujuan petugas.</p>

        <form action="{{ route('peminjam.peminjaman.cancel', $peminjam->id) }}"
              method="POST"
              onsubmit="return confirm('Batalkan pengajuan ini?');">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="px-4 py-2 rounded-xl border border-red-200 text-red-600
                           text-sm font-medium hover:bg-red-50 transition">
                Batalkan Pengajuan
            </button>
        </form>
    </div>

@endif


{{-- ================= DITOLAK ================= --}}
@if($status === 'ditolak')

    <div class="mt-5 p-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 text-sm">
        Pengajuan ini ditolak oleh petugas.
    </div>

@endif


{{-- ================= DIPINJAM / TELAT: ajukan pengembalian ================= --}}
@if(in_array($status, ['dipinjamkan', 'telat']))

    @if(!$peminjam->permintaan_pengembalian)

        <div class="mt-5 pt-5 border-t border-slate-100 flex justify-end">
            <form action="{{ route('peminjam.peminjaman.kembalikan', $peminjam->id) }}"
                  method="POST">
                @csrf
                <button type="submit"
                        onclick="return confirm('Yakin ingin mengajukan pengembalian alat ini?')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                               bg-amber-500 text-white font-semibold text-sm
                               hover:bg-amber-600 hover:shadow-md transition duration-200">
                    <i class="bi bi-arrow-return-left"></i>
                    Ajukan Pengembalian
                </button>
            </form>
        </div>

    @else

        <div class="mt-5 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-700">
            <div class="flex items-start gap-3">
                <i class="bi bi-hourglass-split mt-0.5"></i>
                <div>
                    <p class="font-semibold text-sm">Menunggu Pemeriksaan Petugas</p>
                    <p class="mt-1 text-xs text-amber-600">
                        Permintaan pengembalian telah dikirim dan sedang menunggu pemeriksaan Petugas.
                    </p>
                </div>
            </div>
        </div>

    @endif

@endif


{{-- ================= SELESAI ================= --}}
@if($status === 'dikembalikan')

    <div class="mt-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700">
        <div class="flex items-center gap-3">
            <i class="bi bi-check-circle-fill"></i>
            <div>
                <p class="font-semibold text-sm">Peminjaman Selesai</p>
                <p class="mt-1 text-xs text-emerald-600">
    Pengembalian telah diverifikasi petugas
    @if($peminjam->pengembalian)
        pada {{ \Carbon\Carbon::parse($peminjam->pengembalian->tgl_kembali)->format('d-m-Y') }}
        &middot; Kondisi: {{ ucfirst($peminjam->pengembalian->kondisi_kembali) }}
    @endif
</p>
            </div>
        </div>
    </div>

@endif

            </div>

        </div>

    @empty

        {{-- =====================================================
             EMPTY STATE
        ====================================================== --}}
        <div
            class="bg-white
                   rounded-2xl
                   border border-slate-200
                   shadow-sm
                   p-12
                   text-center">

            <div
                class="w-16 h-16
                       mx-auto
                       rounded-full
                       bg-slate-100
                       flex items-center
                       justify-center">

                <i
                    class="bi bi-clock-history
                           text-3xl
                           text-slate-400">
                </i>

            </div>


            <h3
                class="mt-4
                       font-semibold
                       text-slate-700">

                Belum Ada Riwayat Peminjaman

            </h3>


            <p
                class="mt-1
                       text-sm
                       text-slate-400
                       max-w-md
                       mx-auto">

                Kamu belum memiliki pengajuan atau
                riwayat peminjaman alat.

            </p>


            <a
                href="{{ route('peminjam.katalog') }}"
                class="inline-flex
                       items-center
                       gap-2
                       mt-6
                       px-5 py-2.5
                       rounded-xl
                       bg-indigo-600
                       text-white
                       font-semibold
                       text-sm
                       hover:bg-indigo-700
                       transition
                       shadow-sm">

                <i class="bi bi-grid-1x2-fill"></i>

                Lihat Katalog Alat

            </a>

        </div>

    @endforelse

@endsection
