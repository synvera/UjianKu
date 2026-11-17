@extends('layouts.admin')

@section('title', 'Cek Nilai Siswa')

@section('content')
<h1 class="text-xl sm:text-2xl font-bold mb-6">Cek Nilai Siswa</h1>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full min-w-[720px] text-left border-collapse text-sm">
        <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
            <tr>
                <th class="py-3.5 px-6">No</th>
                <th class="py-3.5 px-6">Nama Siswa</th>
                <th class="py-3.5 px-6">Kelas</th>
                <th class="py-3.5 px-6">Mata Pelajaran</th>
                <th class="py-3.5 px-6">Nilai Akhir</th>
                <th class="py-3.5 px-6">Waktu Selesai</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($daftarNilai as $index => $nilai)
                <tr>
                    <td class="py-3.5 px-6">{{ $index + 1 }}</td>
                    <td class="py-3.5 px-6 font-medium">{{ $nilai->siswa->name }}</td>
                    <td class="py-3.5 px-6">{{ $nilai->siswa->kelas ?? '-' }}</td>
                    <td class="py-3.5 px-6">{{ $nilai->mataPelajaran->nama_mapel }}</td>
                    <td class="py-3.5 px-6">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $nilai->nilai >= 75 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                            {{ number_format($nilai->nilai, 0) }}
                        </span>
                    </td>
                    <td class="py-3.5 px-6 text-slate-500">{{ $nilai->waktu_selesai?->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-8 px-6 text-center text-slate-400">Belum ada data nilai ujian.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection