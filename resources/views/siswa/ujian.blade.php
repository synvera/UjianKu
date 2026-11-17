@extends('layouts.app')

@section('title', 'Ujian ' . $ujianSiswa->mataPelajaran->nama_mapel)

@section('content')
<style>
    html, body, * {
        -webkit-user-select: none !important;
        -moz-user-select: none !important;
        -ms-user-select: none !important;
        user-select: none !important;
        -webkit-touch-callout: none !important;
    }

    img {
        pointer-events: none !important;
        -webkit-user-drag: none !important;
    }
</style>

<div id="overlay-mulai" class="fixed inset-0 bg-slate-900/95 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full text-center shadow-2xl">
        <h2 class="text-xl font-bold text-slate-800 mb-2">Peraturan Ujian</h2>
        <p class="text-sm text-slate-600 mb-4">
            Ujian akan berjalan dalam mode <strong>Layar Penuh (Fullscreen)</strong>. Anda dilarang berpindah tab, membuka aplikasi lain, atau keluar dari mode layar penuh.
        </p>
        <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs p-3 rounded-lg mb-6">
            Pelanggaran lebih dari <strong>3 kali</strong> akan otomatis menghentikan ujian dan mengirimkan jawaban Anda saat ini.
        </div>
        <button type="button" id="btn-masuk-ujian" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl text-sm transition">
            Saya Mengerti & Mulai Kerjakan
        </button>
    </div>
</div>

<div id="modal-peringatan" class="hidden fixed inset-0 bg-rose-950/90 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full text-center shadow-2xl border-4 border-rose-600">
        <div class="text-rose-600 font-extrabold text-2xl mb-2">PERINGATAN PELANGGARAN!</div>
        <p class="text-sm text-slate-700 mb-4">
            Anda terdeteksi meninggalkan halaman ujian atau keluar dari layar penuh.
        </p>
        <div class="text-lg font-bold text-rose-600 mb-6">
            Pelanggaran: <span id="jumlah-pelanggaran">0</span> / 3
        </div>
        <button type="button" id="btn-kembali-ujian" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 rounded-xl text-sm transition">
            Kembali ke Layar Penuh Ujian
        </button>
    </div>
</div>

<header class="bg-white border-b border-slate-200 px-6 py-4 sticky top-0 z-10 flex justify-between items-center shadow-sm select-none">
    <div>
        <div class="text-xs text-slate-500">Mata Pelajaran</div>
        <div class="font-bold text-lg text-slate-800">{{ $ujianSiswa->mataPelajaran->nama_mapel }}</div>
    </div>
    <div class="text-right">
        <div class="text-sm font-medium">{{ Auth::user()->name }}</div>
        <div class="text-xs text-rose-600 font-semibold">Pelanggaran: <span id="header-pelanggaran">0</span>/3</div>
    </div>
</header>

<main class="max-w-4xl mx-auto p-6 select-none">
    <form id="form-ujian" action="{{ route('siswa.ujian.submit', $ujianSiswa->id) }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="pelanggaran" id="input-pelanggaran" value="0">

        @foreach($ujianSiswa->jawabanSiswas as $index => $item)
            @php
                $soal = $item->soal;
            @endphp
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <div class="font-bold text-indigo-600 text-sm mb-2">Soal No. {{ $index + 1 }}</div>
                <div class="prose max-w-none text-slate-800 mb-4">{!! $soal->pertanyaan !!}</div>

                <div class="space-y-2.5">
                    @foreach(['a', 'b', 'c', 'd', 'e'] as $huruf)
                        @php
                            $kolomOpsi = 'opsi_' . $huruf;
                        @endphp
                        @if($soal->$kolomOpsi)
                            <label class="flex items-start gap-3 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer">
                                <input type="radio"
                                       name="jawaban[{{ $soal->id }}]"
                                       value="{{ $huruf }}"
                                       data-soal-id="{{ $soal->id }}"
                                       {{ $item->jawaban_dipilih === $huruf ? 'checked' : '' }}
                                       class="opsi-radio mt-1 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm">
                                    <strong class="uppercase mr-1">{{ $huruf }}.</strong> {{ $soal->$kolomOpsi }}
                                </span>
                            </label>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="flex justify-end pt-4 pb-12">
            <button type="submit" id="btn-submit-manual" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-8 py-3 rounded-xl shadow-md transition">
                Selesai Ujian
            </button>
        </div>
    </form>
</main>

<script>
    let ujianAktif = false;
    let sedangSubmit = false;
    let pelanggaran = 0;
    const batasPelanggaran = 3;
    let cooldownDeteksi = false;

    const overlayMulai = document.getElementById('overlay-mulai');
    const modalPeringatan = document.getElementById('modal-peringatan');
    const formUjian = document.getElementById('form-ujian');
    const teksPelanggaran = document.getElementById('jumlah-pelanggaran');
    const headerPelanggaran = document.getElementById('header-pelanggaran');

    function masukFullscreen() {
        const elem = document.documentElement;
        if (elem.requestFullscreen) {
            elem.requestFullscreen().catch(() => {});
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }
    }

    function catatPelanggaran() {
        if (!ujianAktif || sedangSubmit || cooldownDeteksi) return;

        cooldownDeteksi = true;
        setTimeout(() => { cooldownDeteksi = false; }, 1000);

        pelanggaran++;
        teksPelanggaran.textContent = pelanggaran;
        headerPelanggaran.textContent = pelanggaran;
        document.getElementById('input-pelanggaran').value = pelanggaran;

        if (pelanggaran >= batasPelanggaran) {
            sedangSubmit = true;
            alert('Batas pelanggaran tercapai! Ujian otomatis diselesaikan.');
            formUjian.submit();
            return;
        }

        modalPeringatan.classList.remove('hidden');
    }

    document.querySelectorAll('.opsi-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            fetch('{{ route("siswa.ujian.simpan-jawaban", $ujianSiswa->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    soal_id: this.dataset.soalId,
                    jawaban: this.value
                })
            });
        });
    });

    document.getElementById('btn-masuk-ujian').addEventListener('click', () => {
        masukFullscreen();
        overlayMulai.classList.add('hidden');
        ujianAktif = true;
        history.pushState(null, null, location.href);
    });

    document.getElementById('btn-kembali-ujian').addEventListener('click', () => {
        masukFullscreen();
        modalPeringatan.classList.add('hidden');
    });

    document.getElementById('btn-submit-manual').addEventListener('click', (e) => {
        if (!confirm('Yakin ingin menyelesaikan ujian sekarang?')) {
            e.preventDefault();
            return;
        }
        sedangSubmit = true;
    });

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            catatPelanggaran();
        }
    });

    window.addEventListener('blur', () => {
        catatPelanggaran();
    });

    document.addEventListener('fullscreenchange', () => {
        if (!document.fullscreenElement && ujianAktif && !sedangSubmit) {
            catatPelanggaran();
        }
    });

    window.addEventListener('popstate', () => {
        if (ujianAktif && !sedangSubmit) {
            history.pushState(null, null, location.href);
            catatPelanggaran();
        }
    });

    window.addEventListener('beforeunload', (e) => {
        if (ujianAktif && !sedangSubmit) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    document.addEventListener('contextmenu', (e) => e.preventDefault());
    document.addEventListener('copy', (e) => e.preventDefault());
    document.addEventListener('cut', (e) => e.preventDefault());
    document.addEventListener('paste', (e) => e.preventDefault());

    document.addEventListener('keydown', (e) => {
        if (
            e.key === 'F12' ||
            e.key === 'Escape' ||
            (e.ctrlKey && ['c', 'v', 'x', 'u', 's', 'p', 'a', 't', 'n', 'w', 'r'].includes(e.key.toLowerCase())) ||
            (e.altKey && e.key === 'Tab')
        ) {
            e.preventDefault();
            if (ujianAktif && !sedangSubmit) {
                catatPelanggaran();
            }
        }
    });
</script>
@endsection