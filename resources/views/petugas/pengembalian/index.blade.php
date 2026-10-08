@extends('layouts.app')

@section('title', 'Pemantauan Pengembalian - Panel Petugas')
@section('header-title', 'Status Pengembalian')

@section('content')

@if(session('success'))
    <div class="mb-4 p-3 rounded bg-emerald-50 text-emerald-700 text-sm">{{ session('success') }}</div>
@endif
@if(session('error') || $errors->any())
    <div class="mb-4 p-3 rounded bg-red-50 text-red-700 text-sm">
        {{ session('error') }}
        @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
    </div>
@endif

<div class="p-6">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Pemantauan Pengembalian
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Pantau peminjaman yang sedang berjalan dan proses pengembaliannya.
            </p>
        </div>

        <div class="text-sm text-gray-500">
            Hari ini:
            <span class="font-semibold text-gray-800">
                {{ now('Asia/Jakarta')->format('d-m-Y') }}
            </span>
        </div>
    </div>

    {{-- Notifikasi sukses --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Notifikasi error --}}
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif


    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

        <div class="p-5 border-b border-gray-200 bg-gray-50">

            <h3 class="text-lg font-bold text-gray-800">
                Daftar Peminjaman
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Hanya peminjaman yang telah disetujui dan belum dikembalikan.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full min-w-[1000px] text-left border-collapse">

                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider">

                        <th class="py-3 px-4 border-b">
                            No
                        </th>

                        <th class="py-3 px-4 border-b">
                            Peminjam
                        </th>

                        <th class="py-3 px-4 border-b">
                            Tanggal Pinjam
                        </th>

                        <th class="py-3 px-4 border-b">
                            Rencana Kembali
                        </th>

                        <th class="py-3 px-4 border-b">
                            Status
                        </th>

                        <th class="py-3 px-4 border-b">
                            Aksi
                        </th>

                    </tr>
                </thead>


                <tbody class="text-gray-700 text-sm">

                    @forelse($peminjaman as $item)

                        @php

                            $hariIni = now('Asia/Jakarta')->startOfDay();

                            $rencana = $item->tgl_kembali_plan
                                ? \Carbon\Carbon::parse($item->tgl_kembali_plan)->startOfDay()
                                : null;

                            /*
                             * Tentukan status tampilan.
                             *
                             * Kalau sudah dikembalikan:
                             * Dikembalikan
                             *
                             * Kalau belum dikembalikan:
                             * tanggal rencana < hari ini = Terlambat
                             * selain itu = Dipinjamkan
                             */

                            if ($item->pengembalian) {

                                $statusTampilan = 'dikembalikan';

                            } elseif ($rencana && $rencana->lt($hariIni)) {

                                $statusTampilan = 'telat';

                            } else {

                                $statusTampilan = 'dipinjamkan';

                            }

                        @endphp


                        <tr class="hover:bg-gray-50 transition">

                            {{-- NO --}}
                            <td class="py-4 px-4 border-b">
                                {{ $loop->iteration }}
                            </td>


                            {{-- PEMINJAM --}}
                            <td class="py-4 px-4 border-b">

                                <div class="font-semibold text-gray-900">
                                    {{ $item->user->name ?? '-' }}
                                </div>

                            </td>


                            {{-- TANGGAL PINJAM --}}
                            <td class="py-4 px-4 border-b whitespace-nowrap">

                                {{ $item->tgl_pinjam
                                    ? \Carbon\Carbon::parse($item->tgl_pinjam)->format('d-m-Y')
                                    : '-'
                                }}

                            </td>


                            {{-- RENCANA KEMBALI --}}
                            <td class="py-4 px-4 border-b whitespace-nowrap">

                                {{ $item->tgl_kembali_plan
                                    ? \Carbon\Carbon::parse($item->tgl_kembali_plan)->format('d-m-Y')
                                    : '-'
                                }}

                            </td>


                            {{-- STATUS --}}
                            <td class="py-4 px-4 border-b">

                                @if($statusTampilan === 'dipinjamkan')

                                    <span class="inline-flex items-center px-3 py-1 rounded-full
                                                 text-xs font-semibold
                                                 bg-blue-100 text-blue-700">

                                        Dipinjamkan

                                    </span>

                                    <div class="text-xs text-gray-400 mt-1">
                                        Belum jatuh tempo
                                    </div>


                                @elseif($statusTampilan === 'telat')

                                    <span class="inline-flex items-center px-3 py-1 rounded-full
                                                 text-xs font-semibold
                                                 bg-red-100 text-red-700">

                                        Terlambat

                                    </span>

                                    <div class="text-xs text-red-500 mt-1">
                                        Melewati tanggal kembali
                                    </div>


                                @elseif($statusTampilan === 'dikembalikan')

                                    <span class="inline-flex items-center px-3 py-1 rounded-full
                                                 text-xs font-semibold
                                                 bg-emerald-100 text-emerald-700">

                                        Dikembalikan

                                    </span>

                                @endif

                            </td>


                            {{-- AKSI --}}
                            <td class="py-4 px-4 border-b">

                                @if($statusTampilan === 'dipinjamkan' || $statusTampilan === 'telat')
                                    <a href="{{ route('petugas.pengembalian.form', $item->id) }}"
   class="inline-block bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-xs font-semibold">
    Proses Pengembalian
</a>

                                @else

                                    <span class="text-xs text-gray-400">
                                        Sudah diproses
                                    </span>

                                @endif

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="py-10 text-center text-gray-500"
                            >

                                <div class="font-semibold">
                                    Tidak ada peminjaman yang sedang dipinjam.
                                </div>

                                <div class="text-xs mt-1">
                                    Semua peminjaman sudah dikembalikan atau belum disetujui.
                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection