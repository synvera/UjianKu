<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use App\Models\Soal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SoalController extends Controller
{
    public function index(MataPelajaran $mapel)
    {
        if ($mapel->user_id !== Auth::id()) {
            abort(403);
        }

        $soals = $mapel->soals()->latest()->get();

        return view('admin.soal.index', compact('mapel', 'soals'));
    }

    public function store(Request $request, MataPelajaran $mapel)
    {
        if ($mapel->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'pertanyaan' => ['required', 'string'],
            'opsi_a' => ['required', 'string'],
            'opsi_b' => ['required', 'string'],
            'opsi_c' => ['required', 'string'],
            'opsi_d' => ['required', 'string'],
            'opsi_e' => ['nullable', 'string'],
            'kunci_jawaban' => ['required', 'in:a,b,c,d,e'],
        ]);

        $mapel->soals()->create($validated);

        return back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function uploadGambar(Request $request)
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $folderTujuan = public_path('soal-images');
        if (!file_exists($folderTujuan)) {
            mkdir($folderTujuan, 0755, true);
        }

        $file = $request->file('file');
        $namaFile = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
        $file->move($folderTujuan, $namaFile);

        return response()->json([
            'location' => asset('soal-images/' . $namaFile),
        ]);
    }

    public function importTxt(Request $request, MataPelajaran $mapel)
    {
        if ($mapel->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'file_txt' => ['required', 'file', 'mimes:txt', 'max:2048'],
        ]);

        $content = file_get_contents($request->file('file_txt')->getRealPath());
        $content = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $content);
        $content = str_replace(["\r\n", "\r"], "\n", trim($content));

        $blocks = preg_split('/\n\s*\n/', $content);
        $berhasil = 0;

        foreach ($blocks as $block) {
            $lines = explode("\n", trim($block));
            $pertanyaan = [];
            $opsi = [
                'a' => null,
                'b' => null,
                'c' => null,
                'd' => null,
                'e' => null,
            ];
            $kunci = null;

            foreach ($lines as $index => $line) {
                $line = trim($line);

                if (preg_match('/^A\.\s*(.+)$/i', $line, $m)) {
                    $opsi['a'] = trim($m[1]);
                } elseif (preg_match('/^B\.\s*(.+)$/i', $line, $m)) {
                    $opsi['b'] = trim($m[1]);
                } elseif (preg_match('/^C\.\s*(.+)$/i', $line, $m)) {
                    $opsi['c'] = trim($m[1]);
                } elseif (preg_match('/^D\.\s*(.+)$/i', $line, $m)) {
                    $opsi['d'] = trim($m[1]);
                } elseif (preg_match('/^E\.\s*(.+)$/i', $line, $m)) {
                    $opsi['e'] = trim($m[1]);
                } elseif (preg_match('/^KUNCI\s*:\s*([a-e])(?:\..*|\s.*)?$/i', $line, $m)) {
                    $kunci = strtolower(trim($m[1]));
                } elseif (preg_match('/^Link\s*Foto\s*:\s*(https?:\/\/\S+)$/i', $line, $m)) {
                    $urlFoto = htmlspecialchars(trim($m[1]), ENT_QUOTES, 'UTF-8');
                    $pertanyaan[] = '<img src="' . $urlFoto . '" alt="Gambar Soal" class="my-3 max-w-full h-auto rounded-lg border border-slate-200">';
                } else {
                    if ($index === 0) {
                        $line = preg_replace('/^\d+\.\s*/', '', $line);
                    }
                    $pertanyaan[] = $line;
                }
            }

            $teksPertanyaan = implode("<br>\n", $pertanyaan);

            if ($teksPertanyaan && $opsi['a'] && $opsi['b'] && $opsi['c'] && $opsi['d'] && $kunci) {
                $mapel->soals()->create([
                    'pertanyaan' => $teksPertanyaan,
                    'opsi_a' => $opsi['a'],
                    'opsi_b' => $opsi['b'],
                    'opsi_c' => $opsi['c'],
                    'opsi_d' => $opsi['d'],
                    'opsi_e' => $opsi['e'],
                    'kunci_jawaban' => $kunci,
                ]);
                $berhasil++;
            }
        }

        return back()->with('success', "{$berhasil} soal berhasil diimpor dari file TXT.");
    }

    public function edit(Soal $soal)
    {
        if ($soal->mataPelajaran->user_id !== Auth::id()) {
            abort(403);
        }

        $mapel = $soal->mataPelajaran;

        return view('admin.soal.edit', compact('soal', 'mapel'));
    }

    public function update(Request $request, Soal $soal)
    {
        if ($soal->mataPelajaran->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'pertanyaan' => ['required', 'string'],
            'opsi_a' => ['required', 'string'],
            'opsi_b' => ['required', 'string'],
            'opsi_c' => ['required', 'string'],
            'opsi_d' => ['required', 'string'],
            'opsi_e' => ['nullable', 'string'],
            'kunci_jawaban' => ['required', 'in:a,b,c,d,e'],
        ]);

        $soal->update($validated);

        return redirect()->route('admin.soal.index', $soal->mata_pelajaran_id)
            ->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Soal $soal)
    {
        if ($soal->mataPelajaran->user_id !== Auth::id()) {
            abort(403);
        }

        $soal->delete();

        return back()->with('success', 'Soal berhasil dihapus.');
    }
}