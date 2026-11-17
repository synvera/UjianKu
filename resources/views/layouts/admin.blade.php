<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Guru - UjianKu')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        img, video {
            max-width: 100%;
            height: auto;
        }
        .prose {
            overflow-wrap: anywhere;
        }
        .prose table {
            display: block;
            max-width: 100%;
            overflow-x: auto;
        }
        .note-toolbar {
            max-width: 100%;
            overflow-x: auto;
        }
    </style>
    @stack('head')
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen lg:flex">
    <input type="checkbox" id="admin-menu" class="peer sr-only">

    <aside class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full bg-slate-900 text-white min-h-screen flex flex-col justify-between p-5 shrink-0 transition-transform peer-checked:translate-x-0 lg:static lg:translate-x-0">
        <div>
            <div class="text-xl font-bold tracking-wide border-b border-slate-700 pb-4 mb-6">
                UjianKu <span class="text-xs font-normal text-slate-400 block">Panel Guru / Admin</span>
            </div>
            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.mapel.index') }}" class="block px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.mapel.*') || request()->routeIs('admin.soal.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    Input Soal & Kode
                </a>
                <a href="{{ route('admin.cek-nilai') }}" class="block px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.cek-nilai') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    Cek Nilai
                </a>
            </nav>
        </div>

        <div class="border-t border-slate-700 pt-4">
            <div class="text-sm text-slate-300 mb-3">{{ Auth::user()->name }}</div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium py-2 rounded-lg transition">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <label for="admin-menu" class="fixed inset-0 z-30 hidden bg-slate-950/50 peer-checked:block lg:hidden" aria-label="Tutup menu"></label>

    <div class="flex min-h-screen min-w-0 flex-1 flex-col">
        <header class="flex items-center gap-3 border-b border-slate-200 bg-white px-4 py-3 lg:hidden">
            <label for="admin-menu" class="inline-flex h-10 w-10 cursor-pointer items-center justify-center rounded-lg text-slate-700 hover:bg-slate-100" aria-label="Buka menu">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </label>
            <span class="font-bold tracking-wide text-slate-900">UjianKu</span>
            <span class="text-xs text-slate-500">Panel Guru / Admin</span>
        </header>

        <main class="min-w-0 flex-1 p-4 sm:p-6 lg:p-8 lg:overflow-y-auto">
            @if(session('success'))
                <div class="mb-6 bg-emerald-100 border border-emerald-400 text-emerald-800 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-rose-100 border border-rose-400 text-rose-800 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>