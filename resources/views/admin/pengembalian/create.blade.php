@extends('layouts.app')

@section('title', 'Tambah Pengembalian - Panel Admin')
@section('header-title', 'Tambah Pengembalian Baru')

@section('content')

@if (session('error'))
    <div class="mb-4 max-w-2xl bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
        {{ session('error') }}
    </div>
@endif

<div class="max-w-2xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">

    {{-- Langkah 1: pilih peminjaman --}}
    <form action="{{ route('admin.pengembalian.create') }}" method="GET" class="mb-6 flex gap-2">
        <select name="peminjaman_id"
            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">-- Pilih Peminjaman --</option>
            @foreach ($daftar as $p)
                <option value="{{ $p->id }}" @selected($dipilih && $dipilih->id == $p->id)>
                    #{{ $p->id }} - {{ $p->user->name ?? '-' }}
                    (pinjam: {{ \Carbon\Carbon::parse($p->tgl_pinjam)->format('d-m-Y') }})
                </option>
            @endforeach
        </select>
        <button type="submit"
            class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-lg transition">
            Pilih
        </button>
    </form>
    <p class="text-gray-400 text-xs -mt-4 mb-6">Hanya menampilkan peminjaman berstatus "dipinjamkan" atau "telat" yang belum dikembalikan.</p>

    {{-- Langkah 2: form pengembalian --}}
    @if ($dipilih)
    <form action="{{ route('admin.pengembalian.store') }}" method="POST">
        @csrf
        <input type="hidden" name="peminjaman_id" value="{{ $dipilih->id }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Rencana Kembali</label>
                <div class="w-full px-3 py-2 border border-gray-200 bg-gray-100 text-gray-600 rounded-lg">
                    {{ $dipilih->tgl_kembali_plan->format('d-m-Y') }}
                </div>
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Dikembalikan</label>
                <input type="date" name="tgl_kembali" id="tgl_kembali"
                    value="{{ old('tgl_kembali', now()->toDateString()) }}"
                    data-rencana="{{ $dipilih->tgl_kembali_plan->toDateString() }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('tgl_kembali')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Alat yang Dikembalikan</label>
            <table class="w-full border border-gray-200 rounded-lg overflow-hidden text-sm">
                <thead class="bg-gray-100 text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="p-3 text-left">Alat</th>
                        <th class="p-3 text-center">Dipinjam</th>
                        <th class="p-3 text-center">Jumlah Rusak</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dipilih->detailPinjam as $d)
                        <tr class="border-t">
                            <td class="p-3">{{ $d->alat->nama_alat ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $d->jumlah }}</td>
                            <td class="p-3 text-center">
                                <input type="number" name="items[{{ $d->id }}]" min="0" max="{{ $d->jumlah }}"
                                    value="{{ old('items.' . $d->id, 0) }}"
                                    class="rusak-in w-20 px-2 py-1 border border-gray-300 rounded-lg text-center">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div id="kondisi" class="text-xs font-semibold mt-2"></div>
        </div>

     {{-- RINCIAN DENDA --}}
<div class="mb-6">

    <div class="text-sm font-bold text-gray-700 mb-3">
        RINCIAN DENDA
    </div>

    {{-- Denda Terlambat --}}
    <div class="mb-4">
        <label for="denda_terlambat"
               class="block text-gray-700 text-sm font-semibold mb-2">
            Denda Terlambat (Rp)
            <span class="text-xs font-normal text-gray-400">
                diisi oleh petugas
            </span>
        </label>

        <input
            type="number"
            name="denda_terlambat"
            id="denda_terlambat"
            min="0"
            value="{{ old('denda_terlambat', 0) }}"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg
                   focus:outline-none focus:ring-2 focus:ring-blue-500"
        >

        @error('denda_terlambat')
            <span class="text-red-500 text-xs">
                {{ $message }}
            </span>
        @enderror
    </div>


    {{-- Denda Kerusakan --}}
    <div class="mb-4">
        <label for="denda_kerusakan"
               class="block text-gray-700 text-sm font-semibold mb-2">
            Denda Kerusakan (Rp)
            <span class="text-xs font-normal text-gray-400">
                diisi oleh petugas
            </span>
        </label>

        <input
            type="number"
            name="denda_kerusakan"
            id="denda_kerusakan"
            min="0"
            value="{{ old('denda_kerusakan', 0) }}"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg
                   focus:outline-none focus:ring-2 focus:ring-blue-500"
        >

        @error('denda_kerusakan')
            <span class="text-red-500 text-xs">
                {{ $message }}
            </span>
        @enderror
    </div>


    {{-- Total Denda --}}
    <div class="rounded-lg bg-gray-50 border border-gray-200 p-4">

        <div class="flex justify-between items-center">

            <span class="font-bold text-gray-800">
                Total Denda
            </span>

            <span class="font-bold text-red-600 text-lg">
                Rp <span id="d-total">0</span>
            </span>

        </div>

        <div class="text-xs text-gray-500 mt-2">
            Total = Denda Terlambat + Denda Kerusakan
        </div>

    </div>

</div>


{{-- CATATAN KERUSAKAN --}}
<div class="mb-6">

    <label class="block text-gray-700 text-sm font-semibold mb-2">
        Catatan Kerusakan
    </label>

    <textarea
        name="catatan_kerusakan"
        rows="2"
        placeholder="Contoh: lensa tergores"
        class="w-full px-3 py-2 border border-gray-300 rounded-lg
               focus:outline-none focus:ring-2 focus:ring-blue-500"
    >{{ old('catatan_kerusakan') }}</textarea>

</div>


<script>
    function hitungTotalDenda() {

        const dendaTerlambat =
            parseInt(
                document.getElementById('denda_terlambat').value
            ) || 0;

        const dendaKerusakan =
            parseInt(
                document.getElementById('denda_kerusakan').value
            ) || 0;

        const total =
            dendaTerlambat + dendaKerusakan;

        document.getElementById('d-total').textContent =
            new Intl.NumberFormat('id-ID').format(total);
    }


    document.addEventListener('DOMContentLoaded', function () {

        const dendaTerlambat =
            document.getElementById('denda_terlambat');

        const dendaKerusakan =
            document.getElementById('denda_kerusakan');

        dendaTerlambat.addEventListener(
            'input',
            hitungTotalDenda
        );

        dendaKerusakan.addEventListener(
            'input',
            hitungTotalDenda
        );

        hitungTotalDenda();
    });
</script>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.pengembalian.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
            <button type="submit"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Simpan</button>
        </div>
    </form>

    <script>
        (function () {
            var DENDA_TELAT = {{ \App\Models\Pengembalian::DENDA_TERLAMBAT }};
            var tgl = document.getElementById('tgl_kembali');
            var dk = document.getElementById('denda_kerusakan');
            var rp = function (n) { return 'Rp ' + n.toLocaleString('id-ID'); };

            function hitung() {
                var telat = new Date(tgl.value) > new Date(tgl.dataset.rencana) ? DENDA_TELAT : 0;

                var rusak = 0;
                document.querySelectorAll('.rusak-in').forEach(function (i) { rusak += parseInt(i.value) || 0; });

                document.getElementById('box-rusak').style.display = rusak > 0 ? 'block' : 'none';
                var k = rusak > 0 ? (parseInt(dk.value) || 0) : 0;

                document.getElementById('d-telat').textContent = rp(telat);
                document.getElementById('d-rusak').textContent = rp(k);
                document.getElementById('d-total').textContent = rp(telat + k);

                var el = document.getElementById('kondisi');
                el.textContent = rusak > 0 ? '● Ada alat rusak (' + rusak + ' unit)' : '● Semua alat baik';
                el.className = 'text-xs font-semibold mt-2 ' + (rusak > 0 ? 'text-red-600' : 'text-emerald-600');
            }

            tgl.addEventListener('input', hitung);
            dk.addEventListener('input', hitung);
            document.querySelectorAll('.rusak-in').forEach(function (i) { i.addEventListener('input', hitung); });
            hitung();
        })();
    </script>
    @endif
</div>
@endsection