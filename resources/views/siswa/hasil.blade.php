@extends('layouts.app')

@section('title', 'Hasil Ujian - ' . $ujianSiswa->mataPelajaran->nama_mapel)

@section('content')
<header class="bg-white border-b border-slate-200 px-4 py-3 sm:px-6 sm:py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
    <div class="font-bold text-lg text-indigo-600">UjianKu - Hasil Ujian</div>
    <div class="flex flex-wrap items-center gap-4">
        <a href="{{ route('siswa.kode') }}" class="text-sm text-indigo-600 hover:underline font-medium">Input Kode Lain</a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="text-sm text-rose-600 hover:underline font-medium">Logout</button>
        </form>
    </div>
</header>

<main class="max-w-4xl mx-auto p-4 sm:p-6">
    <div class="bg-white p-5 sm:p-8 rounded-2xl shadow-sm border border-slate-200 text-center mb-8">
        <div class="text-sm text-slate-500 mb-1">Nilai Akhir Mata Pelajaran {{ $ujianSiswa->mataPelajaran->nama_mapel }}</div>
        <div class="text-4xl sm:text-5xl font-extrabold {{ $ujianSiswa->nilai >= 75 ? 'text-emerald-600' : 'text-rose-600' }} my-3">
            {{ number_format($ujianSiswa->nilai, 0) }}
        </div>
        <div class="text-sm text-slate-600">
            Benar: <strong>{{ $ujianSiswa->jawabanSiswas->where('is_correct', true)->count() }}</strong> dari
            <strong>{{ $ujianSiswa->jawabanSiswas->count() }}</strong> soal
        </div>
    </div>

    <h2 class="text-lg font-bold mb-4">Evaluasi Jawaban</h2>

    <div class="space-y-4 pb-12">
        @foreach($ujianSiswa->jawabanSiswas as $index => $item)
            @php
                $soal = $item->soal;
                $bgClass = $item->is_correct
                    ? 'bg-emerald-100 border-emerald-400'
                    : 'bg-rose-100 border-rose-400';
            @endphp
            <div class="p-4 sm:p-6 rounded-xl border-2 {{ $bgClass }}">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-bold text-sm">Soal No. {{ $index + 1 }}</span>
                    <span class="text-xs font-bold uppercase px-2.5 py-1 rounded {{ $item->is_correct ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white' }}">
                        {{ $item->is_correct ? 'Benar' : 'Salah' }}
                    </span>
                </div>

                <div class="prose max-w-none text-slate-900 mb-4 break-words">{!! $soal->pertanyaan !!}</div>

                <div class="space-y-1.5 text-sm">
                    @foreach(['a', 'b', 'c', 'd', 'e'] as $huruf)
                        @php
                            $kolomOpsi = 'opsi_' . $huruf;
                        @endphp
                        @if($soal->$kolomOpsi)
                            <div class="p-2 rounded break-words {{ $item->jawaban_dipilih === $huruf ? 'font-bold underline' : '' }}">
                                <span class="uppercase">{{ $huruf }}.</span> {{ $soal->$kolomOpsi }}
                                @if($item->jawaban_dipilih === $huruf)
                                    <span class="ml-2 text-xs font-semibold">(Jawaban Anda)</span>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</main>
@endsection