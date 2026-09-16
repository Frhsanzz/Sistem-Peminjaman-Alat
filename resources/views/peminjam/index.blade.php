@extends('layouts.peminjam')

@section('title', 'Peminjaman Saya')

@section('content')

<section class="max-w-4xl mx-auto px-6 pt-16 pb-8 flex items-center justify-between">
    <div>
        <h1 class="text-4xl font-semibold tracking-tight">Peminjaman Saya</h1>
        <p class="mt-3 text-subtle">
            Pantau status pengajuan dan riwayat peminjaman alat kamu.
        </p>
    </div>

    <a href="{{ route('peminjam.katalog') }}"
        class="hidden md:inline-block bg-ink text-white text-[14px] font-medium px-6 py-3 rounded-full hover:bg-black transition whitespace-nowrap">
        + Ajukan Peminjaman
    </a>
</section>

<section class="max-w-4xl mx-auto px-6 pb-24 space-y-4">

    @forelse ($peminjaman as $item)

        @php
            $badge = $item->statusBadge();
        @endphp

        <div class="bg-white rounded-4xl border border-black/5 p-6 shadow-sm">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                <div>

                    <span class="px-3 py-1 rounded-full text-[12px] font-semibold {{ $badge['color'] }}">
                        {{ $badge['label'] }}
                    </span>

                    <p class="mt-3 font-medium text-[16px]">
                        @foreach ($item->detailPinjam as $detail)
                            {{ $detail->alat->nama_alat ?? '-' }}

                            <span class="text-subtle">
                                x{{ $detail->jumlah }}
                            </span>

                            @if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                    </p>

                    <p class="text-[13px] text-subtle mt-1">
                        Pinjam
                        {{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}

                        &middot;

                        Rencana kembali
                        {{ \Carbon\Carbon::parse($item->tgl_kembali_plan)->format('d M Y') }}
                    </p>

                    @if ($item->pengembalian)

                        <p class="text-[13px] text-subtle mt-1">

                            Dikembalikan
                            {{ \Carbon\Carbon::parse($item->pengembalian->tgl_kembali)->format('d M Y') }}

                            &middot;

                            Kondisi:
                            {{ $item->pengembalian->kondisi_kembali }}

                            @if ($item->pengembalian->denda > 0)

                                &middot;

                                Denda:
                                Rp {{ number_format($item->pengembalian->denda, 0, ',', '.') }}

                            @endif

                        </p>

                    @endif

                </div>

                <div class="flex items-center gap-2 shrink-0">

                    @if ($item->status === 'diajukan')

                        <form action="{{ route('peminjam.peminjaman.cancel', $item->id) }}"
                            method="POST"
                            onsubmit="return confirm('Batalkan pengajuan ini?');">

                            @csrf
                            @method('DELETE')

                            <button
                                class="text-[13px] font-medium text-red-500 hover:text-red-600 transition px-4 py-2 rounded-full border border-red-200 hover:bg-red-50">
                                Batalkan
                            </button>

                        </form>

                    @elseif (in_array($item->status, ['dipinjam', 'telat']))

                        @if ($item->minta_kembali)

                            <span
                                class="text-[13px] font-medium text-amber-600 px-4 py-2 rounded-full bg-amber-50 border border-amber-200">
                                Menunggu Verifikasi
                            </span>

                        @else

                            <form action="{{ route('peminjam.peminjaman.kembalikan', $item->id) }}"
                                method="POST"
                                onsubmit="return confirm('Ajukan pengembalian alat ini?');">

                                @csrf

                                <button
                                    class="text-[13px] font-medium text-white bg-ink hover:bg-black transition px-4 py-2 rounded-full">
                                    Ajukan Pengembalian
                                </button>

                            </form>

                        @endif

                    @endif

                </div>

            </div>

        </div>

    @empty

        <div class="text-center py-24">

            <p class="text-subtle">
                Kamu belum pernah mengajukan peminjaman.
            </p>

            <a href="{{ route('peminjam.alat.index') }}"
                class="inline-block mt-4 text-blue-600 hover:underline text-[14px]">
                Jelajahi katalog alat &rarr;
            </a>

        </div>

    @endforelse

</section>

@endsection