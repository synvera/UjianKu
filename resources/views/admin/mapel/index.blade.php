@extends('layouts.admin')

@section('title', 'Daftar Mata Pelajaran & Kode Ujian')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <h1 class="text-xl sm:text-2xl font-bold">Daftar Mata Pelajaran</h1>

    <form action="{{ route('admin.mapel.store') }}" method="POST" class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:flex-wrap sm:items-center">
        @csrf
        <input type="text" name="nama_mapel" placeholder="Nama Mata Pelajaran Baru" required class="w-full min-w-0 border border-slate-300 rounded-lg px-3.5 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:w-auto">
        <input type="number" name="jumlah_soal_tampil" value="0" min="1" title="Jumlah Soal Tampil" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:w-20" required>
        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition sm:w-auto">
            + Tambah Mapel
        </button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full min-w-[720px] text-left border-collapse text-sm">
        <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
            <tr>
                <th class="py-3.5 px-6">No</th>
                <th class="py-3.5 px-6">Mata Pelajaran</th>
                <th class="py-3.5 px-6">Jumlah Soal</th>
                <th class="py-3.5 px-6">Kode Ujian Aktif (5 Menit)</th>
                <th class="py-3.5 px-6 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($mataPelajarans as $index => $mapel)
                @php
                    $kodeAktif = $mapel->kodeUjians->first();
                @endphp
                <tr>
                    <td class="py-4 px-6">{{ $index + 1 }}</td>
                    <td class="py-4 px-6 font-semibold text-slate-800">{{ $mapel->nama_mapel }}</td>
                    <td class="py-4 px-6">{{ $mapel->soals_count }} Soal</td>
                    <td class="py-4 px-6">
                        @if($kodeAktif)
                            <div class="inline-flex items-center gap-2 bg-amber-50 border border-amber-300 px-3 py-1 rounded-lg">
                                <span class="font-mono font-bold text-amber-800 tracking-wider text-base">{{ $kodeAktif->kode }}</span>
                                <span class="text-xs text-amber-600 countdown" data-expired="{{ $kodeAktif->expired_at->timestamp }}"></span>
                            </div>
                        @else
                            <span class="text-slate-400 text-xs">Tidak ada kode aktif</span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-right space-x-2">
                        <a href="{{ route('admin.soal.index', $mapel->id) }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-2 rounded-lg text-xs font-medium transition">
                            Input Soal
                        </a>
                        <form action="{{ route('admin.mapel.generate-kode', $mapel->id) }}" method="POST" class="inline-block form-generate-kode">
                            @csrf
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-lg text-xs font-medium transition">
                                Create Code Ujian
                            </button>
                        </form>
                        <form action="{{ route('admin.mapel.destroy', $mapel->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus mata pelajaran {{ $mapel->nama_mapel }}? Semua soal dan nilai terkait juga akan terhapus.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-3.5 py-2 rounded-lg text-xs font-medium transition">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-8 px-6 text-center text-slate-400">Belum ada mata pelajaran. Silakan tambahkan di atas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateCountdowns() {
        const now = Math.floor(Date.now() / 1000);
        document.querySelectorAll('.countdown').forEach(el => {
            const expired = parseInt(el.dataset.expired, 10);
            const diff = expired - now;
            if (diff <= 0) {
                el.textContent = '(Kedaluwarsa)';
                const row = el.closest('tr');
                const form = row.querySelector('.form-generate-kode');
                if (form && !form.dataset.submitted) {
                    form.dataset.submitted = 'true';
                    form.submit();
                }
            } else {
                const mins = Math.floor(diff / 60);
                const secs = diff % 60;
                el.textContent = `(${mins}:${secs.toString().padStart(2, '0')})`;
            }
        });
    }
    setInterval(updateCountdowns, 1000);
    updateCountdowns();
</script>
@endpush