@extends('layouts.app')

@section('title', 'Kelola Alat - Panel Admin')
@section('header-title', 'Manajemen Data Alat')

@section('content')
    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Daftar Alat Laboratorium</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola dan pantau kondisi alat laboratorium dengan mudah.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('admin.alat.index') }}" method="GET" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama alat, kategori..."
                       class="w-[280px] rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-900">Cari</button>
                @if(request('search'))
                    <a href="{{ route('admin.alat.index') }}" class="rounded-lg bg-slate-200 px-3 py-2.5 text-sm text-slate-700 hover:bg-slate-300">Reset</a>
                @endif
            </form>

            <a href="{{ route('admin.alat.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                + Tambah Alat
            </a>
        </div>
    </div>

    {{-- KARTU RINGKASAN --}}
    <div class="mt-5 flex flex-wrap gap-4">
        <div class="flex w-[249px] items-center justify-between rounded-lg bg-gradient-to-r from-green-700 to-green-500 px-5 py-3.5 font-semibold text-white shadow">
            <span class="flex items-center gap-3">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white text-green-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                </span> Baik
            </span>
            <span class="rounded-full bg-white/25 px-3 py-1 text-xs">{{ $totalBaik }} Unit</span>
        </div>
        <div class="flex w-[234px] items-center justify-between rounded-lg bg-gradient-to-r from-red-600 to-red-400 px-5 py-3.5 font-semibold text-white shadow">
            <span class="flex items-center gap-3">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white text-sm font-black text-red-600">!</span> Rusak
            </span>
            <span class="rounded-full bg-white/25 px-3 py-1 text-xs">{{ $totalRusak }} Unit</span>
        </div>
    </div>

    {{-- ============ TABEL KONDISI BAIK ============ --}}
    <section class="mt-6 overflow-hidden rounded-xl border border-green-200 bg-white shadow-sm">
        <div class="flex items-center justify-between bg-green-50 px-5 py-3.5">
            <h2 class="text-lg font-bold text-slate-900">Alat dalam Kondisi Baik</h2>
            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">Total: {{ $totalBaik }} Unit</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-700">
                    <tr>
                        <th class="px-5 py-3.5">No</th>
                        <th class="px-3 py-3.5">Gambar</th>
                        <th class="px-3 py-3.5">Nama Alat</th>
                        <th class="px-3 py-3.5">Kategori</th>
                        <th class="px-3 py-3.5">Stok Baik</th>
                        <th class="px-3 py-3.5">Kondisi</th>
                        <th class="px-3 py-3.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($alatBaik as $item)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3 font-medium">{{ $alatBaik->firstItem() + $loop->index }}</td>
                            <td class="px-3 py-3">
                                @if($item->gambar)
                                    <img src="{{ asset($item->gambar) }}" alt="{{ $item->nama_alat }}"
                                         class="h-[50px] w-[50px] rounded-md border border-slate-200 object-cover">
                                @else
                                    <span class="text-xs italic text-slate-400">Tidak Ada</span>
                                @endif
                            </td>
                            <td class="px-3 py-3">{{ $item->nama_alat }}</td>
                            <td class="px-3 py-3">{{ $item->kategori->nama_kategori ?? '-' }}</td>
                            <td class="px-3 py-3">
                                <span class="inline-block rounded-full bg-green-100 px-3.5 py-1 text-xs font-semibold text-green-700">{{ $item->stok_baik }} Unit</span>
                            </td>
                            <td class="px-3 py-3">
                                <span class="inline-block rounded-full bg-green-100 px-3.5 py-1 text-xs font-semibold text-green-700">Baik</span>
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.alat.edit', $item->id) }}"
                                       class="rounded-md bg-amber-500 px-4 py-1.5 text-xs font-semibold text-white hover:bg-amber-600">Edit</a>
                                    <form action="{{ route('admin.alat.destroy', $item->id) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus alat ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="rounded-md bg-red-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-red-700">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-slate-400">Belum ada alat dalam kondisi baik.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-5 py-4">
            <p class="text-xs text-slate-600">
                Menampilkan {{ $alatBaik->firstItem() ?? 0 }} - {{ $alatBaik->lastItem() ?? 0 }} dari {{ $alatBaik->total() }} data
            </p>
            {{ $alatBaik->links() }}
        </div>
    </section>

    {{-- ============ TABEL KONDISI RUSAK ============ --}}
    <section class="mt-6 overflow-hidden rounded-xl border border-red-200 bg-white shadow-sm">
        <div class="flex items-center justify-between bg-red-50 px-5 py-3.5">
            <h2 class="text-lg font-bold text-red-700">Alat dalam Kondisi Rusak</h2>
            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">Total: {{ $totalRusak }} Unit</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-700">
                    <tr>
                        <th class="px-5 py-3.5">No</th>
                        <th class="px-3 py-3.5">Gambar</th>
                        <th class="px-3 py-3.5">Nama Alat</th>
                        <th class="px-3 py-3.5">Kategori</th>
                        <th class="px-3 py-3.5">Jumlah Rusak</th>
                        <th class="px-3 py-3.5">Kondisi Rusak</th>
                        <th class="px-3 py-3.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($alatRusak as $item)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3 font-medium">{{ $loop->iteration }}</td>
                            <td class="px-3 py-3">
                                @if($item->gambar)
                                    <img src="{{ asset($item->gambar) }}" alt="{{ $item->nama_alat }}"
                                         class="h-[50px] w-[50px] rounded-md border border-slate-200 object-cover">
                                @else
                                    <span class="text-xs italic text-slate-400">Tidak Ada</span>
                                @endif
                            </td>
                            <td class="max-w-[200px] px-3 py-3">{{ $item->nama_alat }}</td>
                            <td class="px-3 py-3">{{ $item->kategori->nama_kategori ?? '-' }}</td>
                            <td class="px-3 py-3">
                                <span class="inline-block rounded-full bg-red-100 px-3.5 py-1 text-xs font-semibold text-red-600">{{ $item->jumlah_rusak }} Unit</span>
                            </td>
                            <td class="px-3 py-3">
                                @if(strtolower($item->status_kondisi) === 'rusak')
                                    <span class="inline-block rounded-full bg-red-100 px-3.5 py-1 text-xs font-semibold text-red-700">Rusak Total</span>
                                @else
                                    <span class="inline-block rounded-full bg-yellow-100 px-3.5 py-1 text-xs font-semibold text-yellow-700">Sebagian Rusak</span>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.alat.show', $item->id) }}"
                                       class="rounded-md bg-blue-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">Detail</a>
                                    <form action="{{ route('admin.alat.destroy', $item->id) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus alat ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="rounded-md bg-red-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-red-700">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-slate-400">Tidak ada alat yang rusak.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 px-5 py-4">
            <p class="text-xs text-slate-600">Menampilkan {{ $alatRusak->isEmpty() ? 0 : 1 }} - {{ $alatRusak->count() }} dari {{ $alatRusak->count() }} data</p>
        </div>
    </section>
@endsection