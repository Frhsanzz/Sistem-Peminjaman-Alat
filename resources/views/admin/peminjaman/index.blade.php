@extends('layouts.app')

@section('title', 'Kelola Peminjaman - Panel Admin')
@section('header-title', 'Manajemen Peminjaman Alat')

@section('content')

<div class="space-y-4">

    {{-- NOTIFIKASI BERHASIL --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- NOTIFIKASI ERROR --}}
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- ERROR VALIDASI --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CARD UTAMA --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">

        {{-- HEADER --}}
        <div class="px-5 py-4 border-b border-gray-200">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <h3 class="text-base font-bold text-gray-800">
                    Daftar Peminjaman Alat
                </h3>

                <div class="flex flex-col sm:flex-row gap-2">

                    {{-- SEARCH --}}
                    <form
                        action="{{ route('admin.peminjaman.index') }}"
                        method="GET"
                        class="flex"
                    >

                        <input
                            type="text"
                            name="search"
                            value="{{ $search ?? request('search') }}"
                            placeholder="Cari nama peminjam..."
                            class="w-64 px-3 py-2 text-xs border border-gray-300 rounded-l-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                        >

                        <button
                            type="submit"
                            class="px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white text-xs font-semibold rounded-r-md"
                        >
                            Cari
                        </button>

                    </form>

                    {{-- RESET --}}
                    @if(request('search'))
                        <a
                            href="{{ route('admin.peminjaman.index') }}"
                            class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold rounded-md text-center"
                        >
                            Reset
                        </a>
                    @endif

                    {{-- TAMBAH PEMINJAMAN --}}
                    <a
                        href="{{ route('admin.peminjaman.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-xs font-semibold whitespace-nowrap text-center"
                    >
                        + Tambah Peminjaman
                    </a>

                </div>

            </div>

        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">

            <table class="w-full text-left">

                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase">

                        <th class="px-4 py-3 border-b text-center w-16">
                            No
                        </th>

                        <th class="px-4 py-3 border-b">
                            Peminjam
                        </th>

                        <th class="px-4 py-3 border-b">
                            Alat yang Dipinjam
                        </th>

                        <th class="px-4 py-3 border-b">
                            Tgl Pinjam
                        </th>

                        <th class="px-4 py-3 border-b">
                            Tgl Kembali
                        </th>

                        <th class="px-4 py-3 border-b">
                            Status
                        </th>

                        <th class="px-4 py-3 border-b text-center">
                            Aksi
                        </th>

                    </tr>
                </thead>

                <tbody class="text-gray-700 text-sm">

                    @forelse($peminjaman as $index => $item)

                        <tr class="hover:bg-gray-50">

                            {{-- NOMOR --}}
                            <td class="px-4 py-4 border-b text-center text-gray-500">
                                {{ $peminjaman->firstItem() + $index }}
                            </td>

                            {{-- PEMINJAM --}}
                            <td class="px-4 py-4 border-b">

                                <div class="font-semibold text-gray-800">
                                    {{ $item->user->name ?? '-' }}
                                </div>

                                @if($item->user && $item->user->email)
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $item->user->email }}
                                    </div>
                                @endif

                            </td>

                            {{-- ALAT --}}
                            <td class="px-4 py-4 border-b">

                                @forelse($item->detailPinjam as $detail)

                                    <div class="mb-1">

                                        <span class="font-medium text-gray-700">
                                            {{ $detail->alat->nama_alat ?? '-' }}
                                        </span>

                                        <span class="text-xs text-gray-400">
                                            ({{ $detail->jumlah ?? 0 }} unit)
                                        </span>

                                    </div>

                                @empty

                                    <span class="text-gray-400">
                                        Tidak ada alat
                                    </span>

                                @endforelse

                            </td>

                            {{-- TANGGAL PINJAM --}}
                            <td class="px-4 py-4 border-b whitespace-nowrap">

                                @if($item->tgl_pinjam)
                                    {{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d-m-Y') }}
                                @else
                                    -
                                @endif

                            </td>

                            {{-- TANGGAL KEMBALI --}}
                            <td class="px-4 py-4 border-b whitespace-nowrap">

                                @if($item->tgl_kembali_plan)
                                    {{ \Carbon\Carbon::parse($item->tgl_kembali_plan)->format('d-m-Y') }}
                                @else
                                    -
                                @endif

                            </td>

                            {{-- STATUS --}}
                            <td class="px-4 py-4 border-b">

                                @php
                                    $status = strtolower($item->status ?? '');

                                    $badge = match($status) {
                                        'diajukan' =>
                                            'bg-yellow-100 text-yellow-700',

                                        'disetujui' =>
                                            'bg-blue-100 text-blue-700',

                                        'dipinjam',
                                        'dipinjamkan' =>
                                            'bg-blue-100 text-blue-700',

                                        'dikembalikan',
                                        'selesai' =>
                                            'bg-emerald-100 text-emerald-700',

                                        'telat',
                                        'ditolak' =>
                                            'bg-red-100 text-red-700',

                                        default =>
                                            'bg-gray-100 text-gray-700',
                                    };
                                @endphp

                                <span
                                    class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}"
                                >
                                    {{ ucfirst($item->status ?? '-') }}
                                </span>

                            </td>

                            {{-- AKSI --}}
                            <td class="px-4 py-4 border-b">

                                <div class="flex items-center justify-center gap-2">

                                    {{-- EDIT --}}
                                    <a
                                        href="{{ route('admin.peminjaman.edit', $item->id) }}"
                                        class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold"
                                    >
                                        Edit
                                    </a>

                                    {{-- HAPUS --}}
                                    <form
                                        action="{{ route('admin.peminjaman.destroy', $item->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus data peminjaman ini?')"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold"
                                        >
                                            Hapus
                                        </button>

                                    </form>

                                </div>

                                                        </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                Belum ada data peminjaman.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- PAGINATION --}}
        @if($peminjaman->hasPages())

            <div class="px-5 py-4 border-t border-gray-200 bg-gray-50">

                {{ $peminjaman->links() }}

            </div>

        @endif

    </div>

</div>

@endsection