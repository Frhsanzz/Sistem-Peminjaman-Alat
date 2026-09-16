@extends('layouts.app')

@section('title', 'Tambah Pengembalian - Panel Admin')
@section('header-title', 'Tambah Pengembalian Baru')

@section('content')
<div class="max-w-xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <form action="{{ route('admin.pengembalian.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Peminjaman</label>
            <select name="peminjaman_id" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Peminjaman --</option>
                @foreach ($peminjaman as $p)
                    <option value="{{ $p->id }}" @selected(old('peminjaman_id') == $p->id)>
                        #{{ $p->id }} - {{ $p->user->name ?? '-' }}
                        (pinjam: {{ \Carbon\Carbon::parse($p->tgl_pinjam)->format('d-m-Y') }})
                    </option>
                @endforeach
            </select>
            <p class="text-gray-400 text-xs mt-1">Hanya menampilkan peminjaman berstatus "dipinjam"/"telat" yang belum ada pengembaliannya.</p>
            @error('peminjaman_id')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Kembali</label>
                <input type="date" name="tgl_kembali" value="{{ old('tgl_kembali') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('tgl_kembali')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Denda (Rp)</label>
                <input type="number" name="denda" value="{{ old('denda', 0) }}" min="0" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('denda')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Kondisi Alat Saat Kembali</label>
            <input type="text" name="kondisi_kembali" value="{{ old('kondisi_kembali') }}" required
                placeholder="cth: Baik / Rusak Ringan / Hilang"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('kondisi_kembali')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Diverifikasi Oleh (Petugas)</label>
            <select name="petugas_id" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Petugas --</option>
                @foreach ($petugas as $pt)
                    <option value="{{ $pt->id }}" @selected(old('petugas_id') == $pt->id)>
                        {{ $pt->name }} ({{ $pt->role }})
                    </option>
                @endforeach
            </select>
            @error('petugas_id')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.pengembalian.index') }}"
            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Simpan</button>
        </div>
    </form>
</div>
@endsection
