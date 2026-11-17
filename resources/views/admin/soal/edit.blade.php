@extends('layouts.admin')

@section('title', 'Edit Soal - ' . $mapel->nama_mapel)

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
<div class="mb-6">
    <a href="{{ route('admin.soal.index', $mapel->id) }}" class="text-sm text-indigo-600 hover:underline">&larr; Kembali ke Daftar Soal</a>
    <h1 class="text-2xl font-bold mt-1">Edit Soal: {{ $mapel->nama_mapel }}</h1>
</div>

<div class="w-full max-w-4xl bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-slate-200">
    <form action="{{ route('admin.soal.update', $soal->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium mb-1">Pertanyaan</label>
            <textarea id="editor-pertanyaan" name="pertanyaan">{!! old('pertanyaan', $soal->pertanyaan) !!}</textarea>
        </div>

        <div class="space-y-3">
            <label class="block text-sm font-medium text-slate-700">Pilihan Jawaban & Kunci (Klik huruf A-E untuk menandai kunci jawaban)</label>

            @foreach(['a' => true, 'b' => true, 'c' => true, 'd' => true, 'e' => false] as $huruf => $wajib)
                @php
                    $kolomOpsi = 'opsi_' . $huruf;
                @endphp
                <div class="flex items-center gap-3">
                    <label class="cursor-pointer shrink-0" title="Jadikan {{ strtoupper($huruf) }} sebagai Kunci Jawaban">
                        <input type="radio" name="kunci_jawaban" value="{{ $huruf }}" {{ old('kunci_jawaban', $soal->kunci_jawaban) === $huruf ? 'checked' : '' }} class="peer sr-only">
                        <span class="w-10 h-10 rounded-lg border-2 border-slate-300 bg-slate-50 text-slate-600 font-bold text-sm flex items-center justify-center transition peer-checked:bg-emerald-600 peer-checked:border-emerald-600 peer-checked:text-white hover:border-indigo-400">
                            {{ strtoupper($huruf) }}
                        </span>
                    </label>
                    <input type="text"
                           name="{{ $kolomOpsi }}"
                           value="{{ old($kolomOpsi, $soal->$kolomOpsi) }}"
                           placeholder="Isi pilihan {{ strtoupper($huruf) }}{{ !$wajib ? ' (Opsional)' : '' }}"
                           {{ $wajib ? 'required' : '' }}
                           class="flex-1 border border-slate-300 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            @endforeach
        </div>

        <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center">
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2.5 rounded-lg text-sm transition sm:w-auto">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.soal.index', $mapel->id) }}" class="w-full bg-slate-200 hover:bg-slate-300 text-slate-700 text-center font-medium px-5 py-2.5 rounded-lg text-sm transition sm:w-auto">
                Batal
            </a>
        </div>
    </form>
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