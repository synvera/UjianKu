@extends('layouts.admin')

@section('title', 'Input Soal - ' . $mapel->nama_mapel)

@push('head')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<style>
    .note-editor.note-frame {
        border-color: #cbd5e1;
        border-radius: 0.5rem;
        background: #ffffff;
    }
    .note-toolbar {
        overflow-x: auto;
    }
    .note-editable table {
        border-collapse: collapse;
        width: 100%;
    }
    .note-editable td, .note-editable th {
        border: 1px solid #94a3b8;
        padding: 6px;
    }
    .note-modal {
        z-index: 9999 !important;
    }
    .note-modal-backdrop {
        z-index: 9998 !important;
    }
    .note-modal .note-btn-primary {
        background-color: #4f46e5 !important;
        color: #ffffff !important;
        opacity: 1 !important;
        cursor: pointer !important;
    }
</style>
@endpush

@section('content')
<div class="flex items-start justify-between mb-6">
    <div>
        <a href="{{ route('admin.mapel.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Kembali ke Daftar Mapel</a>
        <h1 class="text-2xl font-bold mt-1">Input Soal: {{ $mapel->nama_mapel }}</h1>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 min-w-0 bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-slate-200">
        <h2 class="text-base sm:text-lg font-semibold mb-4">Input Soal Manual (Teks, Gambar, Tabel)</h2>
        <form action="{{ route('admin.soal.store', $mapel->id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Pertanyaan</label>
                <textarea id="editor-pertanyaan" name="pertanyaan"></textarea>
            </div>

            <div class="space-y-3">
                <label class="block text-sm font-medium text-slate-700">Pilihan Jawaban & Kunci (Klik huruf A-E untuk menandai kunci jawaban)</label>

                @foreach(['a' => true, 'b' => true, 'c' => true, 'd' => true, 'e' => false] as $huruf => $wajib)
                    <div class="flex items-center gap-3">
                        <label class="cursor-pointer shrink-0" title="Jadikan {{ strtoupper($huruf) }} sebagai Kunci Jawaban">
                            <input type="radio" name="kunci_jawaban" value="{{ $huruf }}" {{ $huruf === 'a' ? 'checked' : '' }} class="peer sr-only">
                            <span class="w-10 h-10 rounded-lg border-2 border-slate-300 bg-slate-50 text-slate-600 font-bold text-sm flex items-center justify-center transition peer-checked:bg-emerald-600 peer-checked:border-emerald-600 peer-checked:text-white hover:border-indigo-400">
                                {{ strtoupper($huruf) }}
                            </span>
                        </label>
                        <input type="text"
                               name="opsi_{{ $huruf }}"
                               placeholder="Isi pilihan {{ strtoupper($huruf) }}{{ !$wajib ? ' (Opsional)' : '' }}"
                               {{ $wajib ? 'required' : '' }}
                               class="flex-1 border border-slate-300 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                @endforeach
            </div>

            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2.5 rounded-lg text-sm transition">
                Simpan Soal
            </button>
        </form>
    </div>

    <div class="min-w-0 bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-slate-200 h-fit">
        <h2 class="text-lg font-semibold mb-2">Import dari File .TXT</h2>
        <p class="text-xs text-slate-500 mb-4">Unggah file .txt sesuai format standar untuk memasukkan banyak soal sekaligus.</p>

        <form action="{{ route('admin.soal.import-txt', $mapel->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <input type="file" name="file_txt" accept=".txt" required class="w-full text-sm border border-slate-300 rounded-lg p-2">
            </div>
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 rounded-lg text-sm transition">
                Convert & Import TXT
            </button>
        </form>

        <div class="mt-6 bg-slate-50 p-3 rounded-lg border border-slate-200 text-xs font-mono text-slate-600 whitespace-pre-line">1. Pertanyaan pertama?
A. Pilihan A
B. Pilihan B
C. Pilihan C
D. Pilihan D
E. Pilihan E
KUNCI: A

2. Pertanyaan kedua?
A. Pilihan A
B. Pilihan B
C. Pilihan C
D. Pilihan D
KUNCI: C</div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 sm:p-6">
    <h2 class="text-lg font-semibold mb-4">Daftar Soal Tersimpan ({{ $soals->count() }})</h2>
    <div class="space-y-4">
        @forelse($soals as $index => $soal)
            <div class="border border-slate-200 rounded-lg p-4">
                <div class="flex justify-between items-start gap-4 mb-2">
                    <div class="font-semibold text-sm text-indigo-600">Soal #{{ $index + 1 }}</div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.soal.edit', $soal->id) }}" class="text-xs text-amber-600 hover:underline font-medium">Edit</a>
                        <form action="{{ route('admin.soal.destroy', $soal->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus soal ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-rose-600 hover:underline font-medium">Hapus</button>
                        </form>
                    </div>
                </div>
                <div class="prose max-w-none text-sm mb-3 break-words">{!! $soal->pertanyaan !!}</div>
                <ul class="break-words text-sm space-y-1 text-slate-700">
                    <li class="{{ $soal->kunci_jawaban === 'a' ? 'font-bold text-emerald-600' : '' }}">A. {{ $soal->opsi_a }}</li>
                    <li class="{{ $soal->kunci_jawaban === 'b' ? 'font-bold text-emerald-600' : '' }}">B. {{ $soal->opsi_b }}</li>
                    <li class="{{ $soal->kunci_jawaban === 'c' ? 'font-bold text-emerald-600' : '' }}">C. {{ $soal->opsi_c }}</li>
                    <li class="{{ $soal->kunci_jawaban === 'd' ? 'font-bold text-emerald-600' : '' }}">D. {{ $soal->opsi_d }}</li>
                    @if($soal->opsi_e)
                        <li class="{{ $soal->kunci_jawaban === 'e' ? 'font-bold text-emerald-600' : '' }}">E. {{ $soal->opsi_e }}</li>
                    @endif
                </ul>
            </div>
        @empty
            <p class="text-sm text-slate-400 text-center py-4">Belum ada soal pada mata pelajaran ini.</p>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#editor-pertanyaan').summernote({
            height: 220,
            dialogsInBody: true,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['picture']],
                ['view', ['codeview']]
            ],
            callbacks: {
                onImageUpload: function(files) {
                    let formData = new FormData();
                    formData.append('file', files[0]);

                    fetch('{{ route("admin.soal.upload-gambar") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.location) {
                            $('#editor-pertanyaan').summernote('insertImage', data.location);
                        } else {
                            alert('Gagal mengunggah gambar.');
                        }
                    })
                    .catch(() => alert('Terjadi kesalahan saat mengunggah gambar.'));
                }
            }
        });
    });
</script>
@endpush