@extends('layouts.app')

@section('title', 'Edit Pengembalian - Panel Admin')
@section('header-title', 'Edit Pengembalian Alat')

@section('content')
<div class="max-w-xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="mb-4">
        <label class="block text-gray-700 text-sm font-semibold mb-2">Peminjaman</label>
        <input type="text" disabled
            value="#{{ $pengembalian->peminjaman->id }} - {{ $pengembalian->peminjaman->user->name ?? '-' }}"
            class="w-full px-3 py-2 border border-gray-200 bg-gray-100 text-gray-500 rounded-lg">
        <p class="text-gray-400 text-xs mt-1">Peminjaman terkait tidak dapat diubah setelah data pengembalian dibuat.</p>
    </div>

    <form action="{{ route('admin.pengembalian.update', $pengembalian->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Kembali</label>
                <input type="date" name="tgl_kembali" value="{{ old('tgl_kembali', $pengembalian->tgl_kembali) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('tgl_kembali')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Denda (Rp)</label>
                <input type="number" name="denda" value="{{ old('denda', $pengembalian->denda) }}" min="0" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('denda')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Kondisi Alat Saat Kembali</label>
            <input type="text" name="kondisi_kembali" value="{{ old('kondisi_kembali', $pengembalian->kondisi_kembali) }}" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('kondisi_kembali')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Diverifikasi Oleh (Petugas)</label>
            <select name="petugas_id" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach ($petugas as $pt)
                    <option value="{{ $pt->id }}" @selected(old('petugas_id', $pengembalian->petugas_id) == $pt->id)>
                        {{ $pt->name }} ({{ $pt->role }})
                    </option>
                @endforeach
            </select>
            @error('petugas_id')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.pengembalian.index') }}"
            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Perbarui</button>
        </div>
    </form>
</div>
@endsection
