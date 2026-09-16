@extends('layouts.app')

@section('title', 'Log Aktivitas - Panel Admin')
@section('page-title', 'Log Aktivitas')
@section('page-subtitle', 'Riwayat lengkap aktivitas seluruh user di sistem')

@section('content')

    {{-- ================= Filter / pencarian (opsional) =================
         Form ini submit via GET ke route yang sama, jadi kalau controller kamu
         sudah menangani $request->query('q') untuk filter user/aktivitas, ini
         akan langsung jalan. Kalau belum, boleh dihapus dulu tidak masalah. --}}
    <form method="GET" class="flex items-center gap-3 mb-6">
        <div class="relative flex-1 max-w-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-subtle absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Cari nama user atau aktivitas..."
                class="w-full pl-10 pr-4 py-2.5 text-[14px] rounded-xl border border-black/10 bg-white
                       focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition"
            >
        </div>
        <button type="submit"
                class="text-[13px] font-semibold text-white bg-brand hover:bg-brand-dark px-5 py-2.5 rounded-xl transition">
            Cari
        </button>
        @if (request('q'))
            <a href="{{ route('admin.log.index') }}" class="text-[13px] font-medium text-subtle hover:text-ink transition">
                Reset
            </a>
        @endif
    </form>

    {{-- ================= Log aktivitas (gaya chat) ================= --}}
    <div class="bg-white rounded-2xl border border-black/5 p-6">
        @php
            $nameColors = ['text-emerald-600', 'text-blue-600', 'text-purple-600', 'text-orange-600', 'text-rose-600', 'text-cyan-600'];
            $lastDate = null;
        @endphp

        <div class="space-y-1">
            @forelse ($logs as $log)
                @php
                    $waktu = \Carbon\Carbon::parse($log->waktu);
                    $tanggalLabel = $waktu->isToday() ? 'Hari ini' : $waktu->translatedFormat('d F Y');
                    $color = $nameColors[crc32($log->user->name) % count($nameColors)];
                @endphp

                @if ($tanggalLabel !== $lastDate)
                    @php $lastDate = $tanggalLabel; @endphp
                    <div class="flex justify-center my-4">
                        <span class="text-[11.5px] font-medium text-subtle bg-canvas px-3 py-1 rounded-full">
                            {{ $tanggalLabel }}
                        </span>
                    </div>
                @endif

                <div class="flex items-start gap-3 py-2">
                    <div class="w-9 h-9 rounded-full bg-canvas text-ink text-[12px] font-semibold flex items-center justify-center shrink-0">
                        {{ strtoupper(substr($log->user->name, 0, 1)) }}
                    </div>
                    <div class="bg-canvas rounded-2xl rounded-tl-sm px-4 py-2.5 max-w-[70%]">
                        <p class="text-[13.5px] font-semibold {{ $color }} leading-tight">{{ $log->user->name }}</p>
                        <p class="text-[14.5px] text-ink leading-snug mt-1">{{ $log->aktivitas }}</p>
                        <p class="text-[11px] text-subtle text-right mt-1.5">{{ $waktu->format('H:i') }}</p>
                    </div>
                </div>
            @empty
                <p class="text-[14px] text-subtle text-center py-16">
                    @if (request('q'))
                        Tidak ada aktivitas yang cocok dengan pencarian "{{ request('q') }}".
                    @else
                        Belum ada aktivitas tercatat.
                    @endif
                </p>
            @endforelse
        </div>
    </div>

    {{-- ================= Pagination =================
         Asumsi $logs adalah hasil paginate() dari controller.
         Kalau $logs masih collection biasa (get()), baris ini bisa dihapus. --}}
    @if (method_exists($logs, 'links'))
        <div class="mt-6">
            {{ $logs->links() }}
        </div>
    @endif

@endsection