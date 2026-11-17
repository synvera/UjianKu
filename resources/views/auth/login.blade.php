@extends('layouts.app')

@section('title', 'Login - UjianKu')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-5 sm:p-8 rounded-2xl shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold text-center text-slate-800 mb-2">UjianKu</h1>
        <p class="text-sm text-center text-slate-500 mb-6">Silakan masuk menggunakan akun Siswa atau Guru</p>

        @if($errors->any())
            <div class="mb-4 bg-rose-100 border border-rose-300 text-rose-700 px-4 py-3 rounded-lg text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" required autofocus class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg text-sm transition">
                Masuk
            </button>
        </form>
    </div>
</div>
@endsection