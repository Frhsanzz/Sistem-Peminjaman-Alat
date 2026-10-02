<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Dashboard Admin')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- ============================= -->
    <!-- STYLE SIDEBAR & CURSOR EFFECT -->
    <!-- ============================= -->
    <style>
        /* ============================= */
        /* MENU SIDEBAR */
        /* ============================= */

        .nav-item {
            position: relative;
            overflow: hidden;
            isolation: isolate;
        }

        /* Cahaya mengikuti cursor */
        .nav-item::before {
            content: "";
            position: absolute;

            width: 130px;
            height: 130px;

            left: var(--mouse-x, -100px);
            top: var(--mouse-y, -100px);

            transform: translate(-50%, -50%);

            background: radial-gradient(
                circle,
                rgba(255, 255, 255, 0.20) 0%,
                rgba(255, 255, 255, 0.10) 30%,
                rgba(255, 255, 255, 0.03) 55%,
                transparent 75%
            );

            pointer-events: none;
            opacity: 0;

            transition: opacity 0.2s ease;

            z-index: -1;
        }

        .nav-item:hover::before {
            opacity: 1;
        }

        /* Isi menu tetap di atas efek */
        .nav-item > * {
            position: relative;
            z-index: 2;
        }

        /* Transisi icon */
        .nav-icon {
            transition:
                transform 0.25s ease,
                opacity 0.25s ease;
        }

        .nav-item:hover .nav-icon {
            transform: translateX(3px) scale(1.08);
        }

        /* Menu aktif */
        .nav-active {
            box-shadow:
                0 4px 15px rgba(0, 0, 0, 0.15);
        }

        /* Scrollbar sidebar */
        aside::-webkit-scrollbar {
            width: 5px;
        }

        aside::-webkit-scrollbar-track {
            background: transparent;
        }

        aside::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
        }

        aside::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.25);
        }


        /* ============================= */
        /* LOGOUT BUTTON */
        /* ============================= */

        .logout-button {
            position: relative;
            overflow: hidden;
            isolation: isolate;

            transition:
                transform 0.25s ease,
                box-shadow 0.25s ease,
                background-color 0.25s ease,
                border-color 0.25s ease;
        }

        /* Cahaya merah */
        .logout-glow {
            position: absolute;

            width: 90px;
            height: 90px;

            border-radius: 50%;

            left: -50px;
            top: 50%;

            transform: translateY(-50%);

            background: radial-gradient(
                circle,
                rgba(239, 68, 68, 0.18),
                transparent 70%
            );

            opacity: 0;

            transition:
                left 0.45s ease,
                opacity 0.45s ease;

            pointer-events: none;

            z-index: 0;
        }

        .logout-button:hover {
            transform: translateY(-2px);

            box-shadow:
                0 8px 20px rgba(239, 68, 68, 0.15);
        }

        .logout-button:hover .logout-glow {
            left: 45%;
            opacity: 1;
        }

        .logout-button:active {
            transform: translateY(0) scale(0.97);
        }

        /* Kotak icon logout */
        .logout-icon-wrapper {
            transition:
                transform 0.3s ease,
                background-color 0.3s ease;
        }

        .logout-button:hover .logout-icon-wrapper {
            transform: rotate(-5deg) scale(1.05);
            background-color: #fee2e2;
        }

        /* Icon logout */
        .logout-icon {
            transition:
                transform 0.3s ease,
                color 0.3s ease;
        }

        .logout-button:hover .logout-icon {
            transform: translateX(3px);
            color: #dc2626;
        }

        /* Tulisan */
        .logout-text {
            transition:
                transform 0.3s ease,
                color 0.3s ease;
        }

        .logout-button:hover .logout-text {
            transform: translateX(2px);
            color: #dc2626;
        }

        /* Arrow */
        .logout-arrow {
            transition:
                opacity 0.3s ease,
                transform 0.3s ease;
        }

        .logout-button:hover .logout-arrow {
            opacity: 1;
            transform: translateX(2px);
        }
    </style>
</head>


<body class="bg-gray-100 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">

        <!-- ================================================= -->
        <!-- SIDEBAR -->
        <!-- ================================================= -->

        <aside class="w-64 bg-gray-900 text-white flex flex-col overflow-y-auto">

            <!-- ===================== -->
            <!-- JUDUL PANEL -->
            <!-- ===================== -->

            @if(auth()->user()->role === 'admin')

                <div class="p-5 text-xl font-bold tracking-wider border-b border-gray-800 flex items-center gap-3">

                    <!-- Icon Admin -->
                    <div class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center">

                        <svg
                            class="w-5 h-5 text-white"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 13h2v8H3v-8zm8-10h2v18h-2V3zm8 6h2v12h-2V9z" />

                        </svg>

                    </div>

                    <span>PANEL ADMIN</span>

                </div>

            @endif


            @if(auth()->user()->role === 'petugas')

                <div class="p-5 text-xl font-bold tracking-wider border-b border-gray-800 flex items-center gap-3">

                    <!-- Icon Petugas -->
                    <div class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center">

                        <svg
                            class="w-5 h-5 text-white"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />

                        </svg>

                    </div>

                    <span>PANEL PETUGAS</span>

                </div>

            @endif


            <!-- ================================================= -->
            <!-- NAVIGATION -->
            <!-- ================================================= -->

            <nav class="flex-1 p-4 space-y-2">

                <!-- ================================================= -->
                <!-- MENU ADMIN -->
                <!-- ================================================= -->

                @if(auth()->user()->role === 'admin')

                    <!-- DASHBOARD -->
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg transition
                        {{ request()->routeIs('admin.dashboard')
                            ? 'bg-gray-800 text-white font-medium nav-active'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        <svg
                            class="nav-icon w-5 h-5 flex-shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10" />

                        </svg>

                        <span>Dashboard</span>

                    </a>


                    <!-- KELOLA USER -->
                    <a href="{{ route('admin.user.index') }}"
                        class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg transition
                        {{ request()->routeIs('admin.user.*')
                            ? 'bg-gray-800 text-white font-medium nav-active'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        <svg
                            class="nav-icon w-5 h-5 flex-shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />

                        </svg>

                        <span>Kelola User</span>

                    </a>


                    <!-- KELOLA KATEGORI -->
                    <a href="{{ route('admin.kategori.index') }}"
                        class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg transition
                        {{ request()->routeIs('admin.kategori.*')
                            ? 'bg-gray-800 text-white font-medium nav-active'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        <svg
                            class="nav-icon w-5 h-5 flex-shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 7a2 2 0 012-2h5l2 2h7a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />

                        </svg>

                        <span>Kelola Kategori</span>

                    </a>


                    <!-- KELOLA ALAT -->
                    <a href="{{ route('admin.alat.index') }}"
                        class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg transition
                        {{ request()->routeIs('admin.alat.*')
                            ? 'bg-gray-800 text-white font-medium nav-active'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        <svg
                            class="nav-icon w-5 h-5 flex-shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M14.7 6.3a4 4 0 01-5.4 5.4L4 17v3h3l5.3-5.3a4 4 0 005.4-5.4l-3 3-2-2 2-3z" />

                        </svg>

                        <span>Kelola Alat</span>

                    </a>


                    <!-- KELOLA PEMINJAMAN -->
                    <a href="{{ route('admin.peminjaman.index') }}"
                        class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg transition
                        {{ request()->routeIs('admin.peminjaman.*')
                            ? 'bg-gray-800 text-white font-medium nav-active'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        <svg
                            class="nav-icon w-5 h-5 flex-shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5h6M9 3h6a1 1 0 011 1v1h1a2 2 0 012 2v13a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h1V4a1 1 0 011-1zM9 13h6M9 17h4" />

                        </svg>

                        <span>Kelola Peminjaman</span>

                    </a>


                    <!-- KELOLA PENGEMBALIAN -->
                    <a href="{{ route('admin.pengembalian.index') }}"
                        class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg transition
                        {{ request()->routeIs('admin.pengembalian.*')
                            ? 'bg-gray-800 text-white font-medium nav-active'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        <svg
                            class="nav-icon w-5 h-5 flex-shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 14l-5-5 5-5M4 9h10a5 5 0 015 5v1" />

                        </svg>

                        <span>Kelola Pengembalian</span>

                    </a>


                    <!-- LOG AKTIVITAS -->
                    <a href="{{ route('admin.log-aktivitas') }}"
                        class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg transition
                        {{ request()->routeIs('admin.log-aktivitas')
                            ? 'bg-gray-800 text-white font-medium nav-active'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        <svg
                            class="nav-icon w-5 h-5 flex-shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 12h4l3-8 4 16 3-8h4" />

                        </svg>

                        <span>Log Aktivitas</span>

                    </a>

                @endif


                <!-- ================================================= -->
                <!-- MENU PETUGAS -->
                <!-- ================================================= -->

                @if(auth()->user()->role === 'petugas')

                    <!-- PERSETUJUAN PEMINJAMAN -->
                    <a href="{{ route('petugas.peminjaman.index') }}"
                        class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg transition
                        {{ request()->routeIs('petugas.peminjaman.*')
                            ? 'bg-gray-800 text-white font-medium nav-active'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        <svg
                            class="nav-icon w-5 h-5 flex-shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5h6M9 3h6a1 1 0 011 1v1h1a2 2 0 012 2v13a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h1V4a1 1 0 011-1zM9 14l2 2 4-4" />

                        </svg>

                        <span>Persetujuan Peminjaman</span>

                    </a>


                    <!-- PEMANTAUAN PENGEMBALIAN -->
                    <a href="{{ route('petugas.pengembalian.index') }}"
                        class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg transition
                        {{ request()->routeIs('petugas.pengembalian.*')
                            ? 'bg-gray-800 text-white font-medium nav-active'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        <svg
                            class="nav-icon w-5 h-5 flex-shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 7h11M4 12h7M4 17h11M17 10l3 3-3 3" />

                        </svg>

                        <span>Pemantauan Pengembalian</span>

                    </a>


                    <!-- CETAK LAPORAN -->
                    <a href="{{ route('petugas.cetaklaporan.index') }}"
                        class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg transition
                        {{ request()->routeIs('petugas.cetaklaporan.*')
                            ? 'bg-gray-800 text-white font-medium nav-active'
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                        <svg
                            class="nav-icon w-5 h-5 flex-shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 2h9l5 5v15H6a2 2 0 01-2-2V4a2 2 0 012-2zM14 2v6h6M8 13h8M8 17h6" />

                        </svg>

                        <span>Cetak Laporan</span>

                    </a>

                @endif

            </nav>


            <!-- ================================================= -->
            <!-- USER LOGIN -->
            <!-- ================================================= -->

            <div class="p-4 border-t border-gray-800 text-sm text-gray-400">

                <div class="flex items-center gap-3">

                    <!-- User Avatar Icon -->
                    <div class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center">

                        <svg
                            class="w-5 h-5 text-gray-300"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8z" />

                        </svg>

                    </div>

                    <div>

                        <div class="text-xs text-gray-500">
                            Logged in as:
                        </div>

                        <div class="text-white font-semibold">
                            {{ auth()->user()->name }}
                        </div>

                    </div>

                </div>

            </div>

        </aside>


        <!-- ================================================= -->
        <!-- MAIN CONTENT -->
        <!-- ================================================= -->

        <div class="flex-1 flex flex-col overflow-y-auto">


            <!-- ================================================= -->
            <!-- NAVBAR ATAS -->
            <!-- ================================================= -->

            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6 z-10">

                <div class="text-lg font-semibold text-gray-800">
                    @yield('header-title', 'Dashboard')
                </div>


                <!-- ================================================= -->
                <!-- LOGOUT UNIK -->
                <!-- ================================================= -->

                <form action="{{ route('logout') }}" method="POST">

                    @csrf

                    <button
                        type="submit"
                        class="logout-button group relative flex items-center gap-3
                               px-3 py-2 rounded-xl
                               text-gray-600 bg-gray-100
                               border border-gray-200
                               hover:bg-red-50
                               hover:border-red-200
                               hover:text-red-600">

                        <!-- Glow -->
                        <span class="logout-glow"></span>


                        <!-- Icon -->
                        <span
                            class="logout-icon-wrapper relative z-10
                                   flex items-center justify-center
                                   w-8 h-8 rounded-lg bg-white">

                            <svg
                                class="logout-icon w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4" />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 17l5-5-5-5M15 12H3" />

                            </svg>

                        </span>


                        <!-- Text -->
                        <span
                            class="logout-text relative z-10
                                   font-semibold text-sm">

                            Keluar

                        </span>


                        <!-- Arrow -->
                        <svg
                            class="logout-arrow relative z-10
                                   w-4 h-4
                                   opacity-0 -translate-x-2"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7" />

                        </svg>

                    </button>

                </form>

            </header>


            <!-- ================================================= -->
            <!-- KONTEN UTAMA -->
            <!-- ================================================= -->

            <main class="flex-1 p-6">

                @yield('content')

            </main>

        </div>

    </div>


    <!-- ================================================= -->
    <!-- CURSOR FOLLOW EFFECT -->
    <!-- ================================================= -->

    <script>
        document.querySelectorAll('.nav-item').forEach(item => {

            item.addEventListener('mousemove', function (event) {

                const rect = this.getBoundingClientRect();

                const x = event.clientX - rect.left;
                const y = event.clientY - rect.top;

                this.style.setProperty('--mouse-x', `${x}px`);
                this.style.setProperty('--mouse-y', `${y}px`);

            });

            item.addEventListener('mouseleave', function () {

                this.style.setProperty('--mouse-x', '-100px');
                this.style.setProperty('--mouse-y', '-100px');

            });

        });
    </script>

</body>

</html>