<?php

namespace App\Http\Controllers;

use App\Models\KodeUjian;
use App\Models\MataPelajaran;
use App\Models\UjianSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $guruId = Auth::id();

        $totalMapel = MataPelajaran::where('user_id', $guruId)->count();

        $riwayatUjian = UjianSiswa::with(['siswa', 'mataPelajaran'])
            ->whereHas('mataPelajaran', function ($query) use ($guruId) {
                $query->where('user_id', $guruId);
            })
            ->where('status', 'selesai')
            ->latest('waktu_selesai')
            ->get();

        $totalMapelSelesai = $riwayatUjian->unique('mata_pelajaran_id')->count();

        return view('admin.dashboard', compact(
            'totalMapel',
            'totalMapelSelesai',
            'riwayatUjian'
        ));
    }

    public function cekNilai()
    {
        $guruId = Auth::id();

        $daftarNilai = UjianSiswa::with(['siswa', 'mataPelajaran'])
            ->whereHas('mataPelajaran', function ($query) use ($guruId) {
                $query->where('user_id', $guruId);
            })
            ->where('status', 'selesai')
            ->latest('waktu_selesai')
            ->get();

        return view('admin.cek-nilai', compact('daftarNilai'));
    }

    public function daftarMapel()
    {
        $guruId = Auth::id();

        $mataPelajarans = MataPelajaran::where('user_id', $guruId)
            ->withCount('soals')
            ->with(['kodeUjians' => function ($query) {
                $query->where('expired_at', '>', now())->latest();
            }])
            ->get();

        return view('admin.mapel.index', compact('mataPelajarans'));
    }

    public function generateKode(MataPelajaran $mapel)
    {
        if ($mapel->user_id !== Auth::id()) {
            abort(403);
        }

        KodeUjian::where('mata_pelajaran_id', $mapel->id)->delete();

        UjianSiswa::where('mata_pelajaran_id', $mapel->id)
            ->where('pelanggaran', '>=', 3)
            ->update([
                'status' => 'sedang_mengerjakan',
                'pelanggaran' => 0,
                'waktu_selesai' => null,
            ]);

        $kodeBaru = strtoupper(Str::random(6));

        while (KodeUjian::where('kode', $kodeBaru)->exists()) {
            $kodeBaru = strtoupper(Str::random(6));
        }

        $kodeUjian = KodeUjian::create([
            'mata_pelajaran_id' => $mapel->id,
            'kode' => $kodeBaru,
            'expired_at' => now()->addMinutes(5),
        ]);

        return back()->with('success', "Kode ujian baru untuk {$mapel->nama_mapel}: {$kodeUjian->kode} (Berlaku 5 menit & pelanggaran siswa telah direset)");
    }

    public function storeMapel(Request $request)
    {
        $validated = $request->validate([
            'nama_mapel' => ['required', 'string', 'max:255'],
            'jumlah_soal_tampil' => ['nullable', 'integer', 'min:1'],
        ]);

        MataPelajaran::create([
            'user_id' => Auth::id(),
            'nama_mapel' => $validated['nama_mapel'],
            'jumlah_soal_tampil' => $validated['jumlah_soal_tampil'] ?? 25,
        ]);

        return back()->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function destroyMapel(MataPelajaran $mapel)
    {
        if ($mapel->user_id !== Auth::id()) {
            abort(403);
        }

        $mapel->delete();

        return back()->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}