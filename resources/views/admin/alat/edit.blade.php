@extends('layouts.app')

@section('title', 'Edit Alat - Panel Admin')

@section('header-title', 'Edit Data Alat')

@section('content')

<div class="max-w-xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">

    <form action="{{ route('admin.alat.update', $alat->id) }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')

        {{-- Nama Alat --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Nama Alat
            </label>

            <input type="text"
                   name="nama_alat"
                   value="{{ old('nama_alat', $alat->nama_alat) }}"
                   required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        {{-- Kategori --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Kategori
            </label>

            <select name="kategori_id"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

                @foreach($kategori as $item)

                    <option value="{{ $item->id }}"
                        {{ old('kategori_id', $alat->kategori_id) == $item->id ? 'selected' : '' }}>

                        {{ $item->nama_kategori }}

                    </option>

                @endforeach

            </select>
        </div>

        {{-- Stok & Status --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">
                    Stok
                </label>

                <input type="number"
                       name="stok"
                       value="{{ old('stok', $alat->stok) }}"
                       min="0"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
             {{-- Jumlah Rusak --}}
            <div>
                <label for="jumlah_rusak" class="block text-gray-700 text-sm font-semibold mb-2">
                    Jumlah Rusak
                </label>

                <input type="number"
                       name="jumlah_rusak"
                       id="jumlah_rusak"
                       min="0"
                       value="{{ old('jumlah_rusak', $alat->jumlah_rusak ?? 0) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

                @error('jumlah_rusak')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">
                    Status Kondisi
                </label>

                <input type="text"
                       name="status_kondisi"
                       value="{{ old('status_kondisi', $alat->status_kondisi) }}"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

        </div>

        {{-- Deskripsi --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Deskripsi
            </label>

            <textarea name="deskripsi"
                      rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('deskripsi', $alat->deskripsi) }}</textarea>
        </div>

        {{-- Gambar --}}
        <div class="mb-6">

            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Gambar Alat

                <span class="text-xs text-gray-400 font-normal">
                    (Biarkan kosong jika tidak ingin mengubah gambar)
                </span>
            </label>

            {{-- Gambar lama --}}
            @if($alat->gambar)
                <div class="mb-3">
                    <p class="text-xs text-gray-500 mb-1">
                        Gambar saat ini:
                    </p>

                    <img src="{{ asset($alat->gambar) }}"
                         alt="{{ $alat->nama_alat }}"
                         class="w-32 h-32 object-cover rounded-lg border">
                </div>
            @endif

            {{-- Upload gambar baru --}}
            <input type="file"
                   name="gambar"
                   accept="image/jpeg,image/png,image/jpg"
                   class="w-full text-sm text-gray-500
                          file:mr-4 file:py-2 file:px-4
                          file:rounded-lg file:border-0
                          file:text-sm file:font-semibold
                          file:bg-blue-50 file:text-blue-700
                          hover:file:bg-blue-100">

        </div>

        {{-- Tombol --}}
        <div class="flex justify-end space-x-2">

            <a href="{{ route('admin.alat.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">
                Batal
            </a>

            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Perbarui
            </button>

        </div>

    </form>

</div>

@endsection