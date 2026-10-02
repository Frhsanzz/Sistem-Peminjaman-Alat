@extends('layouts.app')

@section('title', 'Laporan')
@section('header-title', 'Laporan Peminjaman & Pengembalian')

@section('content')

{{-- =========================
FILTER TANGGAL
========================= --}}

<form method="GET"
    action="{{ route('petugas.cetaklaporan.index') }}"
    class="flex flex-wrap items-end gap-3 mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">


<div>
    <label class="block text-xs font-semibold text-gray-600 mb-1">
        Dari Tanggal
    </label>

    <input
        type="date"
        name="dari"
        value="{{ $dari }}"
        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
</div>

<div>
    <label class="block text-xs font-semibold text-gray-600 mb-1">
        Sampai Tanggal
    </label>

    <input
        type="date"
        name="sampai"
        value="{{ $sampai }}"
        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
</div>

<button
    type="submit"
    class="bg-gray-900 hover:bg-black text-white px-5 py-2 rounded-lg text-sm font-semibold transition">
    Terapkan
</button>


</form>

{{-- =========================
TOMBOL CETAK
========================= --}}

<div class="flex justify-end mb-6">


<a href="{{ route('petugas.cetaklaporan.print', [
    'dari' => $dari,
    'sampai' => $sampai
]) }}"
    target="_blank"
    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-semibold transition">
    Cetak Laporan
</a>


</div>

{{-- =========================
RINGKASAN
========================= --}}

<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">


<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">

    <p class="text-2xl font-bold text-gray-900">
        {{ $ringkasan['total_peminjaman'] ?? 0 }}
    </p>

    <p class="text-xs text-gray-500 mt-1">
        Total Pengajuan
    </p>

</div>


<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">

    <p class="text-2xl font-bold text-blue-600">
        {{ $ringkasan['total_disetujui'] ?? 0 }}
    </p>

    <p class="text-xs text-gray-500 mt-1">
        Disetujui
    </p>

</div>


<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">

    <p class="text-2xl font-bold text-red-500">
        {{ $ringkasan['total_ditolak'] ?? 0 }}
    </p>

    <p class="text-xs text-gray-500 mt-1">
        Ditolak
    </p>

</div>


<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">

    <p class="text-2xl font-bold text-emerald-600">
        {{ $ringkasan['total_selesai'] ?? 0 }}
    </p>

    <p class="text-xs text-gray-500 mt-1">
        Selesai Dikembalikan
    </p>

</div>


<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">

    <p class="text-2xl font-bold text-amber-600">
        Rp {{ number_format($ringkasan['total_denda'] ?? 0, 0, ',', '.') }}
    </p>

    <p class="text-xs text-gray-500 mt-1">
        Total Denda
    </p>

</div>


</div>

{{-- =========================
DATA LAPORAN
========================= --}}

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">


{{-- ALAT PALING SERING DIPINJAM --}}
<div class="lg:col-span-1 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

    <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">

        <h3 class="font-bold text-gray-800 text-sm">
            Alat Paling Sering Dipinjam
        </h3>

    </div>


    <div class="divide-y divide-gray-100">

        @forelse ($alatPalingSering as $a)

            <div class="px-5 py-3 flex items-center justify-between text-sm">

                <span class="text-gray-700">
                    {{ $a->nama_alat }}
                </span>

                <span class="font-semibold text-gray-900">
                    {{ $a->total_dipinjam }}x
                </span>

            </div>

        @empty

            <p class="px-5 py-6 text-center text-sm text-gray-400">
                Belum ada data.
            </p>

        @endforelse

    </div>

</div>


{{-- RIWAYAT PEMINJAMAN --}}
<div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

    <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">

        <h3 class="font-bold text-gray-800 text-sm">
            Riwayat Peminjaman pada Periode Ini
        </h3>

    </div>


    <div class="overflow-x-auto">

        <table class="w-full text-left border-collapse text-sm">

            <thead>

                <tr class="bg-gray-50 text-gray-500 text-xs uppercase">

                    <th class="py-2 px-4 border-b">
                        Peminjam
                    </th>

                    <th class="py-2 px-4 border-b">
                        Tgl Pinjam
                    </th>

                    <th class="py-2 px-4 border-b">
                        Status
                    </th>

                    <th class="py-2 px-4 border-b">
                        Denda
                    </th>

                </tr>

            </thead>


            <tbody class="text-gray-700">

                @forelse ($riwayat as $r)

                    @php

                        $status = $r->status ?? '';

                        $badge = match ($status) {

                            'diajukan' => [
                                'label' => 'Diajukan',
                                'color' => 'bg-yellow-100 text-yellow-700'
                            ],

                            'disetujui' => [
                                'label' => 'Disetujui',
                                'color' => 'bg-blue-100 text-blue-700'
                            ],

                            'ditolak' => [
                                'label' => 'Ditolak',
                                'color' => 'bg-red-100 text-red-700'
                            ],

                            'dipinjam' => [
                                'label' => 'Sedang Dipinjam',
                                'color' => 'bg-purple-100 text-purple-700'
                            ],

                            'selesai' => [
                                'label' => 'Selesai',
                                'color' => 'bg-emerald-100 text-emerald-700'
                            ],

                            'dikembalikan' => [
                                'label' => 'Dikembalikan',
                                'color' => 'bg-green-100 text-green-700'
                            ],

                            default => [
                                'label' => ucfirst($status ?: 'Tidak Diketahui'),
                                'color' => 'bg-gray-100 text-gray-600'
                            ],

                        };

                    @endphp


                    <tr class="hover:bg-gray-50">

                        <td class="py-2 px-4 border-b">
                            {{ $r->user->name ?? '-' }}
                        </td>


                        <td class="py-2 px-4 border-b">

                            @if ($r->tgl_pinjam)

                                {{ \Carbon\Carbon::parse($r->tgl_pinjam)->format('d-m-Y') }}

                            @else

                                -

                            @endif

                        </td>


                        <td class="py-2 px-4 border-b">

                            <span
                                class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $badge['color'] }}">

                                {{ $badge['label'] }}

                            </span>

                        </td>


                        <td class="py-2 px-4 border-b">

                            @if ($r->pengembalian && $r->pengembalian->denda)

                                Rp {{ number_format($r->pengembalian->denda, 0, ',', '.') }}

                            @else

                                -

                            @endif

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td colspan="4"
                            class="py-6 text-center text-gray-400">

                            Tidak ada data pada periode ini.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- PAGINATION --}}
    <div class="p-4 border-t border-gray-200 bg-gray-50">

        {{ $riwayat->links() }}

    </div>

</div>


</div>

@endsection
