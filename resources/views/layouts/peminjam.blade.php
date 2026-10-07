<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Peminjam')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    {{-- CSS/config kamu yang lain --}}
    @stack('styles')
</head>

<body class="min-h-screen bg-neutral-950 text-white antialiased relative overflow-x-hidden">

    {{-- Background ambience: soft dark gradient blobs, seperti foto referensi --}}
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -left-40 h-96 w-96 rounded-full bg-blue-600/20 blur-3xl"></div>
        <div class="absolute top-1/3 -right-32 h-96 w-96 rounded-full bg-purple-600/20 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/4 h-96 w-96 rounded-full bg-cyan-500/10 blur-3xl"></div>
        <div class="absolute inset-0 bg-neutral-950/60"></div>
    </div>

    {{-- NAVBAR --}}
    <nav class="sticky top-4 z-50 mx-auto max-w-5xl px-4">
        <div class="flex items-center justify-between gap-4 rounded-full border border-white/10
                    bg-white/5 px-4 py-2.5 shadow-2xl shadow-black/40 backdrop-blur-xl">

            <a href="{{ route('peminjam.katalog') }}" class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-full
                            bg-gradient-to-br from-blue-500 to-indigo-600
                            text-white shadow-lg shadow-blue-500/30">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="1.8">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 3h6m-7 4h8M7 7v10a5 5 0 005 5 5 5 0 005-5V7M8 7h8" />
                    </svg>
                </div>

                <div class="hidden sm:block">
                    <h1 class="text-sm font-bold leading-tight text-white">
                        Peminjaman Alat
                    </h1>
                    <p class="text-[11px] leading-tight text-white/40">
                        Sistem Peminjaman
                    </p>
                </div>

            </a>

            {{-- NAVIGATION --}}
            <div class="hidden items-center gap-1 rounded-full bg-white/5 p-1 md:flex">


                <a href="{{ route('peminjam.katalog') }}"
                   class="rounded-full px-4 py-2 text-sm font-medium text-white
                          bg-white/10 transition hover:bg-white/15">
                    Katalog Alat
                </a>

                <a href="{{ route('peminjam.peminjaman') }}"
                   class="rounded-full px-4 py-2 text-sm font-medium text-white/60
                          transition hover:bg-white/10 hover:text-white">
                    Peminjaman Saya
                </a>

            </div>

            {{-- USER --}}
            <div class="flex items-center gap-2">

                <div class="hidden text-right sm:block">
                    <p class="text-xs font-semibold leading-tight text-white">
                        {{ auth()->user()->name ?? 'Peminjam' }}
                    </p>
                    <p class="text-[11px] leading-tight text-white/40">
                        Peminjam
                    </p>
                </div>

                <div class="flex h-9 w-9 items-center justify-center rounded-full
                            bg-gradient-to-br from-fuchsia-500 to-indigo-600
                            text-xs font-bold text-white ring-2 ring-white/10">

                    {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)) }}

                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf

                    <button type="submit"
                            title="Keluar"
                            class="hidden h-9 w-9 items-center justify-center rounded-full
                                   border border-white/10 bg-white/5 text-white/60
                                   transition hover:border-red-400/30 hover:bg-red-500/10
                                   hover:text-red-400 sm:flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>

            </div>

        </div>
    </nav>

    {{-- CONTENT --}}
    <main class="relative z-10">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="relative z-10 mt-10 border-t border-white/10">
        <div class="mx-auto max-w-6xl px-5 py-6 text-center
                    text-sm text-white/30 lg:px-8">

            © {{ date('Y') }} Sistem Peminjaman Alat.
            Semua hak dilindungi.

        </div>
    </footer>

    @stack('scripts')

</body>
</html>