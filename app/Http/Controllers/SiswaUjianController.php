<?php

namespace App\Http\Controllers;

use App\Models\JawabanSiswa;
use App\Models\KodeUjian;
use App\Models\UjianSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaUjianController extends Controller
{
    public function showKodeForm()
    {
        return view('siswa.kode');
    }

    public function verifikasiKode(Request $request)
    {
        $request->validate([
            'kode' => ['required', 'string'],
        ]);

        $kodeUjian = KodeUjian::with('mataPelajaran')
            ->where('kode', strtoupper(trim($request->kode)))
            ->first();

        if (!$kodeUjian || $kodeUjian->expired_at->isPast()) {
            return back()->withErrors([
                'kode' => 'Kode ujian tidak valid atau sudah kedaluwarsa (lebih dari 5 menit).',
            ])->onlyInput('kode');
        }

        $mapel = $kodeUjian->mataPelajaran;
        $siswaId = Auth::id();

        $ujianAda = UjianSiswa::where('user_id', $siswaId)
            ->where('mata_pelajaran_id', $mapel->id)
            ->first();

        if ($ujianAda) {
            if ($ujianAda->status === 'selesai') {
                return redirect()->route('siswa.hasil', $ujianAda->id);
            }

            return redirect()->route('siswa.ujian', $ujianAda->id);
        }

        $soals = $mapel->soals()
            ->inRandomOrder()
            ->limit($mapel->jumlah_soal_tampil)
            ->get();

        if ($soals->isEmpty()) {
            return back()->withErrors([
                'kode' => 'Belum ada soal yang tersedia untuk mata pelajaran ini.',
            ]);
        }

        $ujianSiswa = UjianSiswa::create([
            'user_id' => $siswaId,
            'mata_pelajaran_id' => $mapel->id,
            'nilai' => 0,
            'status' => 'sedang_mengerjakan',
        ]);

        foreach ($soals as $soal) {
            JawabanSiswa::create([
                'ujian_siswa_id' => $ujianSiswa->id,
                'soal_id' => $soal->id,
                'jawaban_dipilih' => null,
                'is_correct' => false,
            ]);
        }

        return redirect()->route('siswa.ujian', $ujianSiswa->id);
    }

    public function halamanUjian(UjianSiswa $ujianSiswa)
    {
        if ($ujianSiswa->user_id !== Auth::id()) {
            abort(403);
        }

        if ($ujianSiswa->status === 'selesai') {
            return redirect()->route('siswa.hasil', $ujianSiswa->id);
        }

        $ujianSiswa->load(['mataPelajaran', 'jawabanSiswas.soal']);

        return view('siswa.ujian', compact('ujianSiswa'));
    }

    public function simpanJawaban(Request $request, UjianSiswa $ujianSiswa)
    {
        if ($ujianSiswa->user_id !== Auth::id() || $ujianSiswa->status === 'selesai') {
            return response()->json(['status' => 'ignored'], 403);
        }

        $soalId = $request->input('soal_id');
        $jawaban = $request->input('jawaban') ? strtolower($request->input('jawaban')) : null;

        JawabanSiswa::where('ujian_siswa_id', $ujianSiswa->id)
            ->where('soal_id', $soalId)
            ->update([
                'jawaban_dipilih' => $jawaban,
            ]);

        return response()->json(['status' => 'saved']);
    }

    public function submitUjian(Request $request, UjianSiswa $ujianSiswa)
    {
        if ($ujianSiswa->user_id !== Auth::id()) {
            abort(403);
        }

        if ($ujianSiswa->status === 'selesai') {
            return redirect()->route('siswa.hasil', $ujianSiswa->id);
        }

        $jawabanInput = $request->input('jawaban', []);
        $jawabanSiswas = $ujianSiswa->jawabanSiswas()->with('soal')->get();

        $totalSoal = $jawabanSiswas->count();
        $jumlahBenar = 0;

        foreach ($jawabanSiswas as $item) {
            $dipilih = isset($jawabanInput[$item->soal_id])
                ? strtolower($jawabanInput[$item->soal_id])
                : null;

            $benar = $dipilih !== null && $dipilih === strtolower($item->soal->kunci_jawaban);

            if ($benar) {
                $jumlahBenar++;
            }

            $item->update([
                'jawaban_dipilih' => $dipilih,
                'is_correct' => $benar,
            ]);
        }

        $nilaiAkhir = $totalSoal > 0 ? ($jumlahBenar / $totalSoal) * 100 : 0;

    $ujianSiswa->update([
            'nilai' => round($nilaiAkhir, 2),
            'status' => 'selesai',
            'pelanggaran' => (int) $request->input('pelanggaran', 0),
            'waktu_selesai' => now(),
        ]);

        return redirect()->route('siswa.hasil', $ujianSiswa->id);
    }

    public function halamanHasil(UjianSiswa $ujianSiswa)
    {
        if ($ujianSiswa->user_id !== Auth::id()) {
            abort(403);
        }

        if ($ujianSiswa->status !== 'selesai') {
            return redirect()->route('siswa.ujian', $ujianSiswa->id);
        }

        $ujianSiswa->load(['mataPelajaran', 'jawabanSiswas.soal']);

        return view('siswa.hasil', compact('ujianSiswa'));
    }
}