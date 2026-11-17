@extends('layouts.app')

@section('title', 'Input Kode Ujian - UjianKu')

@section('content')
<div class="min-h-screen flex flex-col">
    <header class="bg-white border-b border-slate-200 px-4 py-3 sm:px-6 sm:py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div class="font-bold text-lg text-indigo-600">UjianKu</div>
        <div class="flex flex-wrap items-center gap-4">
            <span class="break-words text-sm text-slate-600">{{ Auth::user()->name }} ({{ Auth::user()->kelas }})</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-rose-600 hover:underline font-medium">Logout</button>
            </form>
        </div>
    </header>

    <div class="flex-1 flex items-center justify-center p-4">
        <div class="bg-white p-5 sm:p-8 rounded-2xl shadow-md w-full max-w-md text-center">
            <h1 class="text-xl font-bold mb-2">Masukkan Kode Ujian</h1>
            <p class="text-sm text-slate-500 mb-6">Minta kode token ujian kepada guru pengawas (berlaku 5 menit).</p>

            @if($errors->any())
                <div class="mb-4 bg-rose-100 border border-rose-300 text-rose-700 px-4 py-3 rounded-lg text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('siswa.kode.verifikasi') }}" method="POST" class="space-y-4">
                @csrf
                <input type="text" name="kode" value="{{ old('kode') }}" placeholder="CONTOH: AB12CD" required autofocus class="w-full text-center font-mono text-xl uppercase tracking-widest border border-slate-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl text-sm transition">
                    Mulai Ujian
                </button>
            </form>
        </div>
    </div>
</div>
@endsection