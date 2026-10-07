@extends('layouts.app')

@section('title', 'Tambah Alat - Panel Admin')
@section('header-title', 'Tambah Alat Baru')

@section('content')
@if (session('error'))
    <div class="mb-4 p-4 rounded-lg bg-red-100 border border-red-300 text-red-700 font-semibold">
        {{ session('error') }}
    </div>
@endif
<div class="max-w-xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">

    <form action="{{ route('admin.alat.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Nama Alat --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Nama Alat
            </label>

            <input
                type="text"
                name="nama_alat"
                value="{{ old('nama_alat') }}"
                required
                placeholder="Contoh: Multimeter Digital"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >

            @error('nama_alat')
            <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror

        {{-- Kategori --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Kategori
            </label>

            <select
                name="kategori_id"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                <option value="">-- Pilih Kategori --</option>

                @foreach($kategori as $kategori)
                    <option
                        value="{{ $kategori->id }}"
                        {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}
                    >
                        {{ $kategori->nama_kategori }}
                    </option>
                @endforeach
            </select>

            @error('kategori_id')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        {{-- Stok --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Stok
            </label>

            <input
                type="number"
                name="stok"
                value="{{ old('stok', 1) }}"
                min="0"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >

            @error('stok')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        {{-- Status Kondisi --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Status Kondisi
            </label>

            <input
                type="text"
                name="status_kondisi"
                value="{{ old('status_kondisi', 'Baik') }}"
                required
                placeholder="Contoh: Baik / Rusak Ringan"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >

            @error('status_kondisi')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        {{-- Deskripsi --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Deskripsi (Opsional)
            </label>

            <textarea
                name="deskripsi"
                rows="3"
                placeholder="Keterangan tambahan alat...."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >{{ old('deskripsi') }}</textarea>

            @error('deskripsi')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        {{-- Gambar --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Gambar (Opsional)
            </label>

            <input
                type="file"
                name="gambar"
                accept="image/*"
                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
            >

            @error('gambar')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        {{-- Tombol --}}
        <div class="flex justify-end space-x-2">
            <a
                href="{{ route('admin.alat.index') }}"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition"
            >
                Batal
            </a>

            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition"
            >
                Simpan
            </button>
        </div>

    </form>
</div>
@endsection