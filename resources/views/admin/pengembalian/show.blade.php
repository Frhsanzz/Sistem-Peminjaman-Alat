@extends('layouts.app')

@section('title', 'Detail Pengembalian - Panel Admin')
@section('header-title', 'Detail Pengembalian Alat')

@section('content')
<div class="max-w-3xl bg-white rounded-lg shadow-sm border border-gray-200">

    <div class="p-6 flex justify-between items-start border-b border-gray-200">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Detail Pengembalian</h3>
            <p class="text-sm text-gray-500 mt-1">Rincian transaksi pengembalian alat.</p>
        </div>
        <a href="{{ route('admin.pengembalian.index') }}"
           class="border border-gray-300 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-50 transition">Kembali</a>
    </div>

    <div class="p-6 text-sm space-y-6">

        <div>
            <div class="text-xs font-bold tracking-wider text-gray-500 mb-3">INFORMASI PEMINJAMAN</div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="text-xs text-gray-400">Peminjam</div>
                    <div class="font-semibold">{{ $pengembalian->peminjaman->user->name ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-400">Status Peminjaman</div>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                        {{ ucfirst($pengembalian->peminjaman->status ?? '-') }}
                    </span>
                </div>
                <div>
                    <div class="text-xs text-gray-400">Tanggal Pinjam</div>
                    {{ $pengembalian->peminjaman->tgl_pinjam?->format('d-m-Y') ?? '-' }}
                </div>
                <div>
                    <div class="text-xs text-gray-400">Rencana Kembali</div>
                    {{ $pengembalian->peminjaman->tgl_kembali_plan?->format('d-m-Y') ?? '-' }}
                </div>
            </div>
        </div>

        <div>
            <div class="text-xs font-bold tracking-wider text-gray-500 mb-3">ALAT YANG DIKEMBALIKAN</div>
            <table class="w-full border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-50 text-xs text-gray-500">
                    <tr>
                        <th class="p-3 text-left">Alat</th>
                        <th class="p-3">Dipinjam</th>
                        <th class="p-3">Baik</th>
                        <th class="p-3">Rusak</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pengembalian->peminjaman->detailPinjam as $dp)
                        @php
                            $rusak = $pengembalian->details->firstWhere('alat_id', $dp->alat_id)?->jumlah_rusak ?? 0;
                        @endphp
                        <tr class="border-t">
                            <td class="p-3">{{ $dp->alat->nama_alat ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $dp->jumlah }}</td>
                            <td class="p-3 text-center">{{ $dp->jumlah - $rusak }}</td>
                            <td class="p-3 text-center {{ $rusak > 0 ? 'text-red-600 font-bold' : '' }}">{{ $rusak }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($pengembalian->catatan_kerusakan)
                <p class="mt-2 text-gray-500">Catatan: {{ $pengembalian->catatan_kerusakan }}</p>
            @endif
        </div>

        <div>
    <div class="text-xs font-bold tracking-wider text-gray-500 mb-3">
        RINCIAN DENDA
    </div>

    <div class="rounded-lg bg-gray-50 border border-gray-200 p-4">

        {{-- Tanggal Dikembalikan --}}
        <div class="flex justify-between mb-3">
            <span class="text-gray-600">
                Tanggal Dikembalikan
            </span>

            <span class="font-medium">
                {{ $pengembalian->tgl_kembali?->format('d-m-Y') ?? '-' }}
            </span>
        </div>

        {{-- Kondisi Pengembalian --}}
        <div class="flex justify-between mb-4">
            <span class="text-gray-600">
                Kondisi Pengembalian
            </span>

            <span class="font-medium">
                {{ str_replace('_', ' ', ucfirst($pengembalian->kondisi_kembali ?? '-')) }}
            </span>
        </div>


        {{-- Denda Terlambat --}}
        <div class="flex justify-between py-2">
            <span class="text-gray-600">
                Denda Terlambat
            </span>

            <span class="font-medium">
                Rp {{ number_format($pengembalian->denda_terlambat ?? 0, 0, ',', '.') }}
            </span>
        </div>


        {{-- Denda Kerusakan --}}
        <div class="flex justify-between py-2">
            <span class="text-gray-600">
                Denda Kerusakan
            </span>

            <span class="font-medium">
                Rp {{ number_format($pengembalian->denda_kerusakan ?? 0, 0, ',', '.') }}
            </span>
        </div>


        {{-- Garis pemisah --}}
        <div class="border-t border-gray-300 my-2"></div>


        {{-- TOTAL DENDA --}}
        <div class="flex justify-between items-center pt-2">

            <span class="font-bold text-gray-800 text-base">
                Total Denda
            </span>

            <span class="font-bold text-red-600 text-lg">
                Rp {{ number_format($pengembalian->denda ?? 0, 0, ',', '.') }}
            </span>

        </div>

    </div>
        </div>

        <div class="text-gray-500">
            Diproses oleh:
            <b class="text-gray-800">{{ $pengembalian->petugas->name ?? '-' }}</b>
            @if ($pengembalian->petugas)
                <span class="inline-block ml-1 px-3 py-1 rounded-full text-xs font-semibold
                    {{ $pengembalian->petugas->role === 'admin' ? 'bg-slate-800 text-white' : 'bg-indigo-100 text-indigo-700' }}">
                    {{ ucfirst($pengembalian->petugas->role) }}
                </span>
            @endif
        </div>
    </div>
</div>
@endsection