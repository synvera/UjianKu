@extends('layouts.admin')

@section('title', 'Dashboard Guru')

@section('content')
<h1 class="text-xl sm:text-2xl font-bold mb-6">Dashboard Guru</h1>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <div class="text-sm text-slate-500 mb-1">Total Mata Pelajaran Anda</div>
        <div class="text-3xl font-bold text-indigo-600">{{ $totalMapel }}</div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <div class="text-sm text-slate-500 mb-1">Mata Pelajaran Sudah Diselesaikan Siswa</div>
        <div class="text-3xl font-bold text-emerald-600">{{ $totalMapelSelesai }} / {{ $totalMapel }}</div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 font-semibold">
        Rekap Nilai Siswa Terbaru
    </div>
    <div class="overflow-x-auto">
    <table class="w-full min-w-[640px] text-left border-collapse text-sm">
        <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
            <tr>
                <th class="py-3 px-6">Nama Siswa</th>
                <th class="py-3 px-6">Kelas</th>
                <th class="py-3 px-6">Mata Pelajaran</th>
                <th class="py-3 px-6">Nilai</th>
                <th class="py-3 px-6">Waktu Selesai</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($riwayatUjian as $item)
                <tr>
                    <td class="py-3 px-6 font-medium">{{ $item->siswa->name }}</td>
                    <td class="py-3 px-6">{{ $item->siswa->kelas ?? '-' }}</td>
                    <td class="py-3 px-6">{{ $item->mataPelajaran->nama_mapel }}</td>
                    <td class="py-3 px-6 font-bold text-indigo-600">{{ number_format($item->nilai, 0) }}</td>
                    <td class="py-3 px-6 text-slate-500">{{ $item->waktu_selesai?->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-6 px-6 text-center text-slate-400">Belum ada siswa yang menyelesaikan ujian.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection