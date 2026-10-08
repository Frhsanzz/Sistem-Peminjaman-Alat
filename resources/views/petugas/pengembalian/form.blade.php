@extends('layouts.app')

@section('title', 'Proses Pengembalian - Panel Petugas')
@section('header-title', 'Proses Pengembalian')

@section('content')

<div class="p-6 max-w-3xl">

    @if(session('error') || $errors->any())
        <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
            {{ session('error') }}
            @foreach($errors->all() as $e)
                <p>{{ $e }}</p>
            @endforeach
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">

        <div class="mb-5">
            <p class="text-xs text-slate-400">Peminjaman #{{ $peminjaman->id }}</p>
            <h2 class="text-lg font-bold text-slate-800">{{ $peminjaman->user->name ?? '-' }}</h2>
            <p class="text-xs text-slate-500">
                Pinjam {{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('d-m-Y') }}
            </p>
        </div>

        <form action="{{ route('petugas.pengembalian.proses', $peminjaman->id) }}"
              method="POST"
              onsubmit="return confirm('Yakin data pengembalian sudah benar?')">
            @csrf

            {{-- Tanggal --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Rencana Kembali</label>
                    <input type="text" disabled
                           value="{{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->format('d-m-Y') }}"
                           class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-600">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Dikembalikan</label>
                    <input type="date" name="tgl_kembali"
                           value="{{ old('tgl_kembali', now()->toDateString()) }}" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                </div>
            </div>

            {{-- Alat --}}
            <p class="text-sm font-medium text-slate-700 mb-2">Alat yang Dikembalikan</p>
            <div class="rounded-lg border border-slate-200 overflow-hidden mb-6">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-left">Alat</th>
                            <th class="px-4 py-3 text-center">Dipinjam</th>
                            <th class="px-4 py-3 text-center">Jumlah Rusak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($peminjaman->detailPinjam as $detail)
                            <tr>
                                <td class="px-4 py-3 text-slate-700">{{ $detail->alat->nama_alat ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">{{ $detail->jumlah }}</td>
                                <td class="px-4 py-3 text-center">
                                    <input type="number"
                                           name="jumlah_rusak[{{ $detail->alat_id }}]"
                                           value="{{ old('jumlah_rusak.' . $detail->alat_id, 0) }}"
                                           min="0" max="{{ $detail->jumlah }}"
                                           class="w-20 rounded-lg border border-slate-300 px-2 py-1.5 text-center text-sm">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Denda --}}
            <p class="text-xs font-bold uppercase tracking-wide text-slate-600 mb-3">Rincian Denda</p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Denda Terlambat (Rp)
                    <span class="text-xs font-normal text-slate-400">diisi oleh petugas</span>
                </label>
                <input type="number" id="denda_terlambat" name="denda_terlambat" min="0"
                       value="{{ old('denda_terlambat', 0) }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Denda Kerusakan (Rp)
                    <span class="text-xs font-normal text-slate-400">diisi oleh petugas</span>
                </label>
                <input type="number" id="denda_kerusakan" name="denda_kerusakan" min="0"
                       value="{{ old('denda_kerusakan', 0) }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
            </div>

            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 mb-6 flex items-start justify-between">
                <div>
                    <p class="font-semibold text-slate-800">Total Denda</p>
                    <p class="text-xs text-slate-500">Total = Denda Terlambat + Denda Kerusakan</p>
                </div>
                <p id="total_denda" class="text-lg font-bold text-red-600">Rp 0</p>
            </div>

            {{-- Catatan --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Kerusakan</label>
                <textarea name="catatan_kerusakan" rows="3"
                          placeholder="Contoh: lensa tergores"
                          class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('catatan_kerusakan') }}</textarea>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('petugas.pengembalian.index') }}"
                   class="px-4 py-2 rounded-lg bg-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-300">
                    Batal
                </a>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const dTerlambat = document.getElementById('denda_terlambat');
    const dKerusakan = document.getElementById('denda_kerusakan');
    const totalEl    = document.getElementById('total_denda');

    function hitungTotal() {
        const total = (parseInt(dTerlambat.value) || 0) + (parseInt(dKerusakan.value) || 0);
        totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    dTerlambat.addEventListener('input', hitungTotal);
    dKerusakan.addEventListener('input', hitungTotal);
    hitungTotal();
</script>

@endsection