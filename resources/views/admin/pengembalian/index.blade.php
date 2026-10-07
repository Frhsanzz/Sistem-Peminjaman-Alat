@extends('layouts.app')

@section('title', 'Kelola Pengembalian - Panel Admin')
@section('header-title', 'Manajemen Pengembalian Alat')

@section('content')

@if (session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200
                text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 bg-red-50 border border-red-200
                text-red-800 p-4 rounded-lg shadow-sm text-sm">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

    {{-- HEADER --}}
    <div class="p-5 border-b border-gray-200 bg-gray-50
                flex flex-col md:flex-row justify-between
                items-center gap-4">

        <h3 class="text-lg font-bold text-gray-800">
            Daftar Pengembalian Alat
        </h3>

        <div class="flex items-center gap-3 w-full md:w-auto">

            <form
                action="{{ route('admin.pengembalian.index') }}"
                method="GET"
                class="flex w-full md:w-80"
            >

                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari nama peminjam..."
                    class="w-full px-3 py-2 text-sm border
                           border-gray-300 rounded-l-lg
                           focus:outline-none focus:ring-2
                           focus:ring-blue-500"
                >

                <button
                    type="submit"
                    class="bg-gray-800 hover:bg-gray-900
                           text-white px-4 py-2 text-sm
                           font-semibold rounded-r-lg"
                >
                    Cari
                </button>

                @if (request('q'))
                    <a
                        href="{{ route('admin.pengembalian.index') }}"
                        class="ml-2 bg-gray-300 hover:bg-gray-400
                               text-gray-700 px-3 py-2 text-sm
                               rounded-lg flex items-center"
                    >
                        Reset
                    </a>
                @endif

            </form>

            <a
                href="{{ route('admin.pengembalian.create') }}"
                class="bg-blue-600 hover:bg-blue-700
                       text-white text-sm font-semibold
                       px-4 py-2 rounded-lg whitespace-nowrap"
            >
                + Tambah Pengembalian
            </a>

        </div>
    </div>

    {{-- TABEL --}}
    <div class="overflow-x-auto">

        <table class="w-full min-w-[1050px] text-left border-collapse">

            <thead>
                <tr class="bg-gray-100 text-gray-600 text-xs
                           uppercase tracking-wider">

                    <th class="py-3 px-4 border-b text-center">
                        No
                    </th>

                    <th class="py-3 px-4 border-b">
                        Peminjam
                    </th>

                    <th class="py-3 px-4 border-b">
                        Alat Dipinjam
                    </th>

                    <th class="py-3 px-4 border-b">
                        Tgl Rencana
                    </th>

                    <th class="py-3 px-4 border-b">
                        Tgl Kembali
                    </th>

                    <th class="py-3 px-4 border-b">
                        Kondisi Alat
                    </th>

                    <th class="py-3 px-4 border-b">
                        Denda
                    </th>

                    <th class="py-3 px-4 border-b">
                        Diverifikasi Oleh
                    </th>

                    <th class="py-3 px-4 border-b">
                        Aksi
                    </th>

                </tr>
            </thead>

            <tbody class="text-gray-700 text-sm divide-y">

                @forelse ($pengembalian as $index => $p)

                    @php
                        $kond = strtolower(
                            $p->kondisi_kembali ?? ''
                        );

                        $rencana =
                            $p->peminjaman?->tgl_kembali_plan;

                        /*
                        |--------------------------------------------------------------------------
                        | STATUS
                        |--------------------------------------------------------------------------
                        |
                        | Karena data ini berada di tabel PENGEMBALIAN,
                        | berarti barang SUDAH dikembalikan.
                        |
                        | Jadi jangan membandingkan tanggal kembali
                        | untuk menentukan status.
                        |
                        */

                        $status = 'dikembalikan';
                    @endphp

                    <tr class="hover:bg-gray-50 transition
                        {{ $loop->even ? 'bg-gray-50/60' : '' }}">

                        {{-- NO --}}
                        <td class="py-3 px-4 border-b text-center">
                            {{ $pengembalian->firstItem() + $index }}
                        </td>

                        {{-- PEMINJAM --}}
                        <td class="py-3 px-4 border-b
                                   font-medium text-gray-900">

                            {{ $p->peminjaman->user->name ?? '-' }}

                        </td>

                        {{-- ALAT --}}
                        <td class="py-3 px-4 border-b">

                            @forelse (
                                $p->peminjaman->detailPinjam ?? []
                                as $d
                            )

                                <div class="mb-1">

                                    {{ $d->alat->nama_alat ?? '-' }}

                                    <span class="text-gray-400">
                                        ({{ $d->jumlah }} unit)
                                    </span>

                                </div>

                            @empty

                                <span class="text-gray-400">
                                    -
                                </span>

                            @endforelse

                        </td>

                        {{-- TANGGAL RENCANA --}}
                        <td class="py-3 px-4 border-b whitespace-nowrap">

                            {{ $rencana?->format('d-m-Y') ?? '-' }}

                        </td>

                        {{-- TANGGAL KEMBALI + STATUS --}}
                        <td class="py-3 px-4 border-b whitespace-nowrap">

                            {{ $p->tgl_kembali?->format('d-m-Y') ?? '-' }}

                            <div class="mt-1">

                                <span
                                    class="inline-block px-3 py-1
                                           rounded-full text-xs
                                           font-semibold
                                           bg-emerald-100
                                           text-emerald-700"
                                >
                                    Dikembalikan
                                </span>

                            </div>

                        </td>

                        {{-- KONDISI --}}
                        <td class="py-3 px-4 border-b">

                            @if ($kond === 'baik')

                                <span
                                    class="inline-block px-3 py-1
                                           rounded-full text-xs
                                           font-semibold
                                           bg-emerald-100
                                           text-emerald-700"
                                >
                                    Baik
                                </span>

                            @elseif ($kond === 'sebagian_rusak')

                                <span
                                    class="inline-block px-3 py-1
                                           rounded-full text-xs
                                           font-semibold
                                           bg-amber-100
                                           text-amber-700"
                                >
                                    Sebagian rusak
                                </span>

                                <div class="text-xs text-red-600
                                            font-medium mt-1">
                                    {{ $p->total_rusak }}
                                    unit rusak
                                </div>

                            @elseif ($kond === 'rusak')

                                <span
                                    class="inline-block px-3 py-1
                                           rounded-full text-xs
                                           font-semibold
                                           bg-red-100
                                           text-red-700"
                                >
                                    Rusak
                                </span>

                                <div class="text-xs text-red-600
                                            font-medium mt-1">
                                    {{ $p->total_rusak }}
                                    unit rusak
                                </div>

                            @else

                                <span class="text-gray-700">
                                    {{ $p->kondisi_kembali ?? '-' }}
                                </span>

                            @endif

                        </td>

                        {{-- DENDA --}}
                        <td class="py-3 px-4 border-b whitespace-nowrap">

                            @if (($p->denda ?? 0) > 0)

                                <span class="font-bold text-red-600">
                                    Rp
                                    {{ number_format(
                                        $p->denda,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </span>

                                @if (($p->denda_kerusakan ?? 0) > 0)

                                    <div class="text-xs text-gray-400 mt-1">
                                        Denda kerusakan:
                                        Rp
                                        {{ number_format(
                                            $p->denda_kerusakan,
                                            0,
                                            ',',
                                            '.'
                                        ) }}
                                    </div>

                                @endif

                            @else

                                <span class="text-gray-400">
                                    Tidak ada denda
                                </span>

                            @endif

                        </td>

                        {{-- PETUGAS --}}
                        <td class="py-3 px-4 border-b">

                            {{ $p->petugas->name ?? '-' }}

                            @if ($p->petugas)

                                <div>

                                    <span
                                        class="inline-block mt-1 px-3 py-1
                                               rounded-full text-xs
                                               font-semibold
                                               {{ $p->petugas->role === 'admin'
                                                    ? 'bg-slate-800 text-white'
                                                    : 'bg-indigo-100 text-indigo-700' }}"
                                    >
                                        {{ ucfirst(
                                            $p->petugas->role
                                        ) }}
                                    </span>

                                </div>

                            @endif

                        </td>

                        {{-- AKSI --}}
                        <td class="py-3 px-4 border-b whitespace-nowrap">

                            <div class="flex items-center space-x-2">

                                <a
                                    href="{{ route(
                                        'admin.pengembalian.show',
                                        $p->id
                                    ) }}"
                                    class="bg-sky-500 hover:bg-sky-600
                                           text-white text-xs font-semibold
                                           px-3 py-1.5 rounded-md"
                                >
                                    Detail
                                </a>

                                <a
                                    href="{{ route(
                                        'admin.pengembalian.edit',
                                        $p->id
                                    ) }}"
                                    class="bg-amber-500 hover:bg-amber-600
                                           text-white text-xs font-semibold
                                           px-3 py-1.5 rounded-md"
                                >
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'admin.pengembalian.destroy',
                                        $p->id
                                    ) }}"
                                    class="inline"
                                    onsubmit="return confirm(
                                        'Yakin ingin menghapus data pengembalian ini?'
                                    )"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="bg-red-500 hover:bg-red-600
                                               text-white text-xs
                                               font-semibold px-3 py-1.5
                                               rounded-md"
                                    >
                                        Hapus
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td
                            colspan="9"
                            class="py-10 text-center text-gray-500"
                        >
                            Belum ada data pengembalian.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- PAGINATION --}}
    <div class="p-4 border-t border-gray-200 bg-gray-50">
        {{ $pengembalian->links() }}
    </div>

</div>

@endsection