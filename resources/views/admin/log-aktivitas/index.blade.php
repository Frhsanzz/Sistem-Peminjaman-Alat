@extends('layouts.app')

@section('title', 'Log Aktivitas - Panel Admin')
@section('header-title', 'Log Aktivitas')

@section('content')

@php
    $statusStyle = [
        'diajukan' => [
            'label' => 'Diajukan',
            'class' => 'bg-amber-100 text-amber-700 border-amber-200',
            'icon' => '⏳',
            'iconBg' => 'bg-amber-50',
        ],

        'dipinjamkan' => [
            'label' => 'Dipinjamkan',
            'class' => 'bg-blue-100 text-blue-700 border-blue-200',
            'icon' => '📦',
            'iconBg' => 'bg-blue-50',
        ],

        'dikembalikan' => [
            'label' => 'Dikembalikan',
            'class' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'icon' => '✓',
            'iconBg' => 'bg-emerald-50',
        ],

        'telat' => [
            'label' => 'Telat',
            'class' => 'bg-red-100 text-red-700 border-red-200',
            'icon' => '!',
            'iconBg' => 'bg-red-50',
        ],
    ];
@endphp


<div class="space-y-6">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <div class="flex items-center gap-3">

                <div class="w-11 h-11 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6 text-white"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                <div>
                    <h2 class="text-2xl font-extrabold text-gray-800">
                        Log Aktivitas
                    </h2>

                    <p class="text-gray-500 text-sm mt-0.5">
                        Pantau riwayat aktivitas dan status peminjaman pengguna.
                    </p>
                </div>

            </div>
        </div>

        {{-- Total aktivitas --}}
        <div class="bg-white border border-gray-200 rounded-2xl px-5 py-3 shadow-sm">
            <p class="text-xs text-gray-400 font-medium">
                TOTAL AKTIVITAS
            </p>

            <p class="text-xl font-extrabold text-gray-800">
                {{ $logs->total() }}
            </p>
        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- STATUS CARDS --}}
    {{-- ========================================================= --}}

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        @foreach ($statusStyle as $key => $s)

            <div class="group bg-white rounded-2xl border border-gray-200 p-5 shadow-sm
                        hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">

                <div class="flex items-center justify-between">

                    <div class="flex items-center gap-3">

                        <div class="w-11 h-11 rounded-xl {{ $s['iconBg'] }}
                                    flex items-center justify-center">

                            <span class="text-lg">
                                {{ $s['icon'] }}
                            </span>

                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                {{ $s['label'] }}
                            </p>

                            <p class="text-2xl font-extrabold text-gray-800">
                                {{ $counts[$key] ?? 0 }}
                            </p>
                        </div>

                    </div>

                    <div class="w-2 h-2 rounded-full
                        {{ $key === 'diajukan' ? 'bg-amber-400' : '' }}
                        {{ $key === 'dipinjamkan' ? 'bg-blue-500' : '' }}
                        {{ $key === 'dikembalikan' ? 'bg-emerald-500' : '' }}
                        {{ $key === 'telat' ? 'bg-red-500' : '' }}">
                    </div>

                </div>

            </div>

        @endforeach

    </div>


    {{-- ========================================================= --}}
    {{-- FILTER --}}
    {{-- ========================================================= --}}

    <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">

        <form method="GET"
              class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div>
                <h3 class="font-bold text-gray-800">
                    Riwayat Aktivitas
                </h3>

                <p class="text-xs text-gray-400 mt-1">
                    Gunakan filter untuk melihat aktivitas berdasarkan status.
                </p>
            </div>

            <div class="relative">

                <select
                    name="status"
                    onchange="this.form.submit()"
                    class="appearance-none w-full sm:w-52
                           bg-gray-50
                           border border-gray-200
                           rounded-xl
                           px-4 py-2.5 pr-10
                           text-sm font-medium text-gray-700
                           focus:outline-none
                           focus:ring-2
                           focus:ring-indigo-100
                           focus:border-indigo-400
                           cursor-pointer">

                    <option value="all"
                        {{ $status == 'all' ? 'selected' : '' }}>
                        Semua Status
                    </option>

                    @foreach ($statusStyle as $key => $s)

                        <option value="{{ $key }}"
                            {{ $status == $key ? 'selected' : '' }}>
                            {{ $s['label'] }}
                        </option>

                    @endforeach

                </select>

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M19 9l-7 7-7-7"/>
                </svg>

            </div>

        </form>

    </div>


    {{-- ========================================================= --}}
    {{-- ACTIVITY LIST --}}
    {{-- ========================================================= --}}

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Table Header --}}
        <div class="hidden md:grid grid-cols-12 gap-4
                    px-6 py-4
                    bg-gray-50
                    border-b border-gray-200
                    text-[11px]
                    font-bold
                    text-gray-400
                    uppercase
                    tracking-wider">

            <div class="col-span-3">
                Pengguna
            </div>

            <div class="col-span-4">
                Aktivitas
            </div>

            <div class="col-span-2">
                Status
            </div>

            <div class="col-span-3">
                Waktu
            </div>

        </div>


        {{-- Activity Rows --}}
        <div class="divide-y divide-gray-100">

            @forelse ($logs as $log)

                <div class="group
                            px-6 py-5
                            hover:bg-gray-50
                            transition-colors duration-200">

                    <div class="grid grid-cols-1 md:grid-cols-12
                                gap-4 md:gap-6
                                items-center">


                        {{-- USER --}}
                        <div class="md:col-span-3 flex items-center gap-3">

                            <div class="relative flex-shrink-0">

                                <div class="w-11 h-11 rounded-full
                                            bg-gradient-to-br from-indigo-500 to-violet-600
                                            flex items-center justify-center
                                            shadow-sm">

                                    <span class="text-white font-bold">
                                        {{ strtoupper(substr($log->user_name ?? 'U', 0, 1)) }}
                                    </span>

                                </div>

                                <span class="absolute -right-0.5 -bottom-0.5
                                             w-3.5 h-3.5
                                             bg-emerald-500
                                             border-2 border-white
                                             rounded-full">
                                </span>

                            </div>


                            <div class="min-w-0">

                                <p class="font-bold text-gray-800 truncate">
                                    {{ $log->user_name ?? 'User' }}
                                </p>

                                <p class="text-xs text-gray-400 mt-0.5">
                                    Pengguna
                                </p>

                            </div>

                        </div>


                        {{-- AKTIVITAS --}}
                        <div class="md:col-span-4">

                            <div class="flex items-start gap-2">

                                <div class="hidden md:flex
                                            w-8 h-8
                                            rounded-lg
                                            bg-indigo-50
                                            items-center
                                            justify-center
                                            flex-shrink-0">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="w-4 h-4 text-indigo-500"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor">

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>

                                    </svg>

                                </div>

                                <p class="text-sm text-gray-600 leading-6">
                                    {{ $log->aktivitas }}
                                </p>

                            </div>

                        </div>


                        {{-- STATUS --}}
                        <div class="md:col-span-2">

                            @if ($log->status && isset($statusStyle[$log->status]))

                                <span class="inline-flex items-center gap-1.5
                                             px-3 py-1.5
                                             rounded-full
                                             text-xs font-bold
                                             border
                                             {{ $statusStyle[$log->status]['class'] }}">

                                    <span class="w-1.5 h-1.5 rounded-full
                                        {{ $log->status === 'diajukan' ? 'bg-amber-500' : '' }}
                                        {{ $log->status === 'dipinjamkan' ? 'bg-blue-500' : '' }}
                                        {{ $log->status === 'dikembalikan' ? 'bg-emerald-500' : '' }}
                                        {{ $log->status === 'telat' ? 'bg-red-500' : '' }}">
                                    </span>

                                    {{ $statusStyle[$log->status]['label'] }}

                                </span>

                            @else

                                <span class="inline-flex items-center
                                             px-3 py-1.5
                                             rounded-full
                                             bg-gray-100
                                             text-gray-400
                                             text-xs font-medium">

                                    Aktivitas Sistem

                                </span>

                            @endif

                        </div>


                        {{-- WAKTU --}}
                        <div class="md:col-span-3">

                            <div class="flex items-center gap-2 text-gray-500">

                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="w-4 h-4 text-gray-400 flex-shrink-0"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>

                                </svg>

                                <div>

                                    <p class="text-sm font-medium text-gray-600">
                                        {{ $log->created_at
                                            ? \Carbon\Carbon::parse($log->created_at)->format('d M Y')
                                            : '-' }}
                                    </p>

                                    <p class="text-xs text-gray-400">
                                        {{ $log->created_at
                                            ? \Carbon\Carbon::parse($log->created_at)->format('H:i') . ' WIB'
                                            : '-' }}
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            @empty

                <div class="py-16 text-center">

                    <div class="w-16 h-16
                                mx-auto
                                rounded-2xl
                                bg-gray-100
                                flex items-center justify-center
                                mb-4">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-8 h-8 text-gray-400"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="1.5"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>

                        </svg>

                    </div>

                    <h3 class="font-bold text-gray-700">
                        Belum Ada Aktivitas
                    </h3>

                    <p class="text-sm text-gray-400 mt-1">
                        Belum terdapat riwayat aktivitas pengguna.
                    </p>

                </div>

            @endforelse

        </div>


        {{-- Pagination --}}
        @if ($logs->hasPages())

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">

                {{ $logs->links() }}

            </div>

        @endif

    </div>

</div>

@endsection