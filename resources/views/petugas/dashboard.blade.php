@extends('layouts.app')

@section('title', 'Dashboard Petugas')
@section('header-title', 'Dashboard')

@section('content')

<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <p class="text-2xl font-bold text-amber-600">{{ $stats['menunggu_persetujuan'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Menunggu Persetujuan</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <p class="text-2xl font-bold text-blue-600">{{ $stats['sedang_dipinjam'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Sedang Dipinjam</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <p class="text-2xl font-bold text-purple-600">{{ $stats['minta_kembali'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Minta Pengembalian</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <p class="text-2xl font-bold text-red-600">{{ $stats['terlambat'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Terlambat</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <p class="text-2xl font-bold text-emerald-600">Rp {{ number_format($stats['total_denda_bulan_ini'], 0, ',', '.') }}</p>
        <p class="text-xs text-gray-500 mt-1">Denda Bulan Ini</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-sm">Menunggu Persetujuan</h3>
            <a href="{{ route('petugas.peminjaman.index') }}" class="text-xs text-blue-600 hover:underline">Lihat semua</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse ($antreanPersetujuan as $item)
                <div class="px-5 py-3 flex items-center justify-between text-sm">
                    <div>
                        <p class="font-medium text-gray-800">{{ $item->user->name ?? '-' }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $item->detailPinjam->pluck('alat.nama_alat')->filter()->join(', ') }}
                        </p>
                    </div>
                    <form action="{{ route('petugas.peminjaman.approve', $item->id) }}" method="POST">
                        @csrf
                        <button class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-3 py-1.5 rounded transition">
                            Setujui
                        </button>
                    </form>
                </div>
            @empty
                <p class="px-5 py-6 text-center text-sm text-gray-400">Tidak ada antrean.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-sm">Permintaan Pengembalian</h3>
            <a href="{{ route('admin.pengembalian.create') }}" class="text-xs text-blue-600 hover:underline">Proses</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse ($antreanPengembalian as $item)
                <div class="px-5 py-3 text-sm">
                    <p class="font-medium text-gray-800">{{ $item->user->name ?? '-' }}</p>
                    <p class="text-xs text-gray-500">
                        {{ $item->detailPinjam->pluck('alat.nama_alat')->filter()->join(', ') }}
                    </p>
                </div>
            @empty
                <p class="px-5 py-6 text-center text-sm text-gray-400">Tidak ada permintaan pengembalian.</p>
            @endforelse
        </div>
    </div>
</div>

@endsection
