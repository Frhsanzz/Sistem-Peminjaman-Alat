@extends('layouts.app')
@section('title', 'Pemantauan Pengembalian - Panel Petugas')
@section('header-title', 'Status Pengembalian')
@section('content')

<div class="p-6">

    <h1 class="text-2xl font-bold mb-6">
        Daftar Pengembalian
    </h1>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">

        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="p-3 text-left">No</th>
                    <th class="p-3 text-left">Peminjam</th>
                    <th class="p-3 text-left">Tanggal Pinjam</th>
                    <th class="p-3 text-left">Rencana Kembali</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($peminjaman as $item)
                    <tr class="border-b">

                        <td class="p-3">
                            {{ $loop->iteration }}
                        </td>

                        <td class="p-3">
                            {{ $item->user->name ?? '-' }}
                        </td>

                        <td class="p-3">
                            {{ $item->tgl_pinjam ?? '-' }}
                        </td>

                        <td class="p-3">
                            {{ $item->tgl_kembali_plan ?? '-' }}
                        </td>

                        <td class="p-3">
                            {{ $item->status ?? '-' }}
                        </td>

                        <td class="p-3">

                            <form
                                action="{{ route('petugas.pengembalian.proses', $item->id) }}"
                                method="POST"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="px-3 py-2 bg-green-600 text-white rounded"
                                >
                                    Proses Pengembalian
                                </button>
                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="p-6 text-center">
                            Tidak ada peminjaman yang sedang dipinjam.
                        </td>
                    </tr>

                @endforelse
            </tbody>

        </table>

    </div>

</div>

@endsection
