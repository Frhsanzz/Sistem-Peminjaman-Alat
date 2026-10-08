@extends('layouts.app')

@section('title', 'Detail Alat Rusak - Panel Admin')
@section('header-title', 'Manajemen Data Alat')

@section('content')
@if(session('error'))
    <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">{{ session('error') }}</div>
@endif
    <a href="{{ route('admin.alat.index') }}" class="text-sm font-medium text-blue-600 hover:underline">← Kembali ke Daftar Alat</a>

    <div class="mt-2 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Detail Alat Rusak</h1>
            <p class="mt-1 text-sm text-slate-500">Informasi kerusakan dan kondisi stok alat.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.alat.edit', $alat->id) }}"
               class="rounded-lg bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-600">Edit</a>
            <form action="{{ route('admin.alat.destroy', $alat->id) }}" method="POST"
                  onsubmit="return confirm('Yakin ingin menghapus alat ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Hapus</button>
            </form>
        </div>
    </div>

    <section class="mt-5 max-w-3xl rounded-xl border border-red-200 bg-white p-5 shadow-sm">
        <div class="flex items-start gap-5">
            @if($alat->gambar)
                <img src="{{ asset($alat->gambar) }}" alt="{{ $alat->nama_alat }}"
                     class="h-28 w-28 rounded-xl border border-slate-200 object-cover">
            @else
                <div class="flex h-28 w-28 items-center justify-center rounded-xl border border-slate-200 bg-slate-100 text-xs italic text-slate-400">Tidak Ada</div>
            @endif

            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ $alat->nama_alat }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $alat->kategori->nama_kategori ?? '-' }}</p>
                <div class="mt-3">
                    @if(strtolower($alat->status_kondisi) === 'rusak')
                        <span class="rounded-full bg-red-100 px-3.5 py-1 text-xs font-semibold text-red-700">Rusak Total</span>
                    @elseif(strtolower($alat->status_kondisi) === 'sebagian_rusak')
                        <span class="rounded-full bg-yellow-100 px-3.5 py-1 text-xs font-semibold text-yellow-700">Sebagian Rusak</span>
                    @else
                        <span class="rounded-full bg-green-100 px-3.5 py-1 text-xs font-semibold text-green-700">Baik</span>
                    @endif
                </div>
            </div>
        </div>

        <dl class="mt-6 grid grid-cols-2 gap-x-6 gap-y-4 border-t border-slate-100 pt-5 text-sm sm:grid-cols-3">
            <div><dt class="text-slate-500">Total stok</dt><dd class="mt-0.5 font-semibold">{{ $alat->stok }} Unit</dd></div>
            <div><dt class="text-slate-500">Jumlah rusak</dt><dd class="mt-0.5 font-semibold text-red-600">{{ $alat->jumlah_rusak }} Unit</dd></div>
            <div><dt class="text-slate-500">Masih baik</dt><dd class="mt-0.5 font-semibold text-green-700">{{ $alat->stok_baik }} Unit</dd></div>

            <div class="col-span-2 sm:col-span-3">
                <dt class="text-slate-500">Tanggal dilaporkan rusak</dt>
                <dd class="mt-0.5 font-semibold">{{ $alat->tanggal_rusak ? $alat->tanggal_rusak->translatedFormat('d F Y') : '-' }}</dd>
            </div>
            <div class="col-span-2 sm:col-span-3">
                <dt class="text-slate-500">Keterangan kerusakan</dt>
                <dd class="mt-0.5 rounded-lg bg-red-50 p-3 text-slate-700">{{ $alat->keterangan_rusak ?: 'Belum ada keterangan.' }}</dd>
            </div>
            <div class="col-span-2 sm:col-span-3">
                <dt class="text-slate-500">Deskripsi alat</dt>
                <dd class="mt-0.5 text-slate-700">{{ $alat->deskripsi ?: '-' }}</dd>
                @if(($alat->jumlah_rusak ?? 0) > 0)
    <div class="mt-6 pt-5 border-t border-slate-100">
        <p class="text-sm font-semibold text-slate-700 mb-1">Perbaikan Alat</p>
        <p class="text-xs text-slate-500 mb-3">
            Isi jumlah unit yang sudah selesai diperbaiki. Unit tersebut kembali menjadi stok baik.
        </p>

        <form action="{{ route('admin.alat.perbaiki', $alat->id) }}"
              method="POST"
              onsubmit="return confirm('Yakin unit ini sudah selesai diperbaiki?')"
              class="flex items-center gap-3">
            @csrf

            <input type="number"
                   name="jumlah_diperbaiki"
                   value="{{ $alat->jumlah_rusak }}"
                   min="1"
                   max="{{ $alat->jumlah_rusak }}"
                   class="w-24 rounded-lg border border-slate-300 px-3 py-2 text-sm text-center">

            <span class="text-sm text-slate-500">dari {{ $alat->jumlah_rusak }} unit rusak</span>

            <button type="submit"
                    class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700
                           text-white text-sm font-semibold">
                Selesai Diperbaiki
            </button>
        </form>
    </div>
@endif
            </div>
        </dl>
    </section>
@endsection