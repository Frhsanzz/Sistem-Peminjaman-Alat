<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Peminjaman dan Pengembalian</title>

    {{-- Salin baris pemuat Tailwind dari <head> di layouts/app.blade.php --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @page { size: A4; margin: 15mm; }
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 text-xs print:bg-white">

    <div class="print:hidden sticky top-0 z-10 flex justify-center gap-2 bg-white border-b border-gray-200 py-3">
        <button onclick="window.print()"
                class="px-4 py-2 rounded-md bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
            Cetak / Simpan PDF
        </button>
        <button onclick="window.close()"
                class="px-4 py-2 rounded-md bg-gray-200 text-gray-800 text-sm font-semibold hover:bg-gray-300">
            Tutup
        </button>
    </div>

    <div class="mx-auto my-6 max-w-[210mm] bg-white p-10 shadow print:m-0 print:max-w-none print:p-0 print:shadow-none">

        <div class="text-center border-b-2 border-gray-900 pb-3 mb-4">
            <h1 class="text-base font-bold tracking-wide">LAPORAN PEMINJAMAN DAN PENGEMBALIAN</h1>
            <p class="mt-1">
                Periode: {{ \Carbon\Carbon::parse($dari)->format('d-m-Y') }}
                s/d {{ \Carbon\Carbon::parse($sampai)->format('d-m-Y') }}
            </p>
        </div>

        <div class="grid grid-cols-5 gap-2 mb-5">
            <div class="border border-gray-900 py-2 text-center">
                <div class="text-base font-bold">{{ $ringkasan['total_peminjaman'] ?? 0 }}</div>
                <div class="text-[11px]">Total pengajuan</div>
            </div>
            <div class="border border-gray-900 py-2 text-center">
                <div class="text-base font-bold">{{ $ringkasan['total_disetujui'] ?? 0 }}</div>
                <div class="text-[11px]">Disetujui</div>
            </div>
            <div class="border border-gray-900 py-2 text-center">
                <div class="text-base font-bold">{{ $ringkasan['total_ditolak'] ?? 0 }}</div>
                <div class="text-[11px]">Ditolak</div>
            </div>
            <div class="border border-gray-900 py-2 text-center">
                <div class="text-base font-bold">{{ $ringkasan['total_selesai'] ?? 0 }}</div>
                <div class="text-[11px]">Selesai kembali</div>
            </div>
            <div class="border border-gray-900 py-2 text-center">
                <div class="text-base font-bold">Rp {{ number_format($ringkasan['total_denda'] ?? 0, 0, ',', '.') }}</div>
                <div class="text-[11px]">Total denda</div>
            </div>
        </div>

        <h2 class="font-bold mb-1.5">Riwayat peminjaman</h2>
        <table class="w-full border-collapse mb-5">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-900 p-1.5 text-left w-9">No</th>
                    <th class="border border-gray-900 p-1.5 text-left">Peminjam</th>
                    <th class="border border-gray-900 p-1.5 text-left w-28">Tgl pinjam</th>
                    <th class="border border-gray-900 p-1.5 text-left w-32">Status</th>
                    <th class="border border-gray-900 p-1.5 text-left w-24">Denda</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $i => $r)
                    @php
                        $labelStatus = match ($r->status ?? '') {
                            'diajukan'     => 'Diajukan',
                            'disetujui'    => 'Disetujui',
                            'ditolak'      => 'Ditolak',
                            'dipinjam'     => 'Sedang dipinjam',
                            'selesai'      => 'Selesai',
                            'dikembalikan' => 'Dikembalikan',
                            default        => ucfirst($r->status ?: 'Tidak diketahui'),
                        };
                    @endphp
                    <tr class="break-inside-avoid">
                        <td class="border border-gray-900 p-1.5">{{ $i + 1 }}</td>
                        <td class="border border-gray-900 p-1.5">{{ $r->user->name ?? '-' }}</td>
                        <td class="border border-gray-900 p-1.5">
                            {{ $r->tgl_pinjam ? \Carbon\Carbon::parse($r->tgl_pinjam)->format('d-m-Y') : '-' }}
                        </td>
                        <td class="border border-gray-900 p-1.5">{{ $labelStatus }}</td>
                        <td class="border border-gray-900 p-1.5">
                            {{ ($r->pengembalian && $r->pengembalian->denda)
                                ? 'Rp '.number_format($r->pengembalian->denda, 0, ',', '.')
                                : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="border border-gray-900 p-3 text-center">Tidak ada data pada periode ini</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <h2 class="font-bold mb-1.5">Alat paling sering dipinjam</h2>
        <table class="w-3/5 border-collapse mb-5">
            <tbody>
                @forelse ($alatPalingSering as $i => $a)
                    <tr class="break-inside-avoid">
                        <td class="border border-gray-900 p-1.5 w-9">{{ $i + 1 }}</td>
                        <td class="border border-gray-900 p-1.5">{{ $a->nama_alat }}</td>
                        <td class="border border-gray-900 p-1.5 w-16 text-right">{{ $a->total_dipinjam }}x</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="border border-gray-900 p-3 text-center">Belum ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="ml-auto mt-7 w-48 text-center break-inside-avoid">
            <p>Dicetak: {{ now()->format('d-m-Y') }}</p>
            <div class="h-14"></div>
            <p class="font-bold underline">{{ auth()->user()->name }}</p>
            <p>Petugas</p>
        </div>

    </div>
</body>
</html>