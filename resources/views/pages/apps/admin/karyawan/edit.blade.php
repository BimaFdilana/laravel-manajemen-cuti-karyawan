@extends('layouts.app')

@section('title', 'Edit Data Karyawan')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Data Karyawan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Data Karyawan</a></div>
                    <div class="breadcrumb-item">Edit</div>
                </div>
            </div>

            <div class="section-body">
                <div class="card">
                    <form action="{{ route('karyawan.update', $karyawan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-header">
                            <h4>Formulir Edit Karyawan</h4>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-group">
                                <label for="nik">NIK (Nomor Induk Karyawan)</label>
                                <input type="text" id="nik" class="form-control" name="nik"
                                    value="{{ old('nik', $karyawan->nik) }}" required>
                            </div>

                            <div class="form-group">
                                <label for="nama_karyawan">Nama Lengkap Karyawan</label>
                                <input type="text" id="nama_karyawan" class="form-control" name="nama_karyawan"
                                    value="{{ old('nama_karyawan', $karyawan->nama_karyawan) }}" required>
                            </div>

                            <div class="form-group">
                                <label for="profesi">Profesi</label>
                                <input type="text" id="profesi" class="form-control" name="profesi"
                                    value="{{ old('profesi', $karyawan->profesi) }}" required>
                            </div>

                            <div class="form-group">
                                <label for="tanggal_masuk">Tanggal Masuk</label>
                                <input type="date" id="tanggal_masuk" class="form-control" name="tanggal_masuk"
                                    value="{{ old('tanggal_masuk', $karyawan->tanggal_masuk) }}" required>
                            </div>

                            <div class="form-group">
                                <label for="ruangan_id">Ruangan / Departemen</label>
                                <select id="ruangan_id" name="ruangan_id" class="form-control select2" required>
                                    <option value="">-- Pilih Ruangan --</option>
                                    @foreach ($ruangans as $ruangan)
                                        <option value="{{ $ruangan->id }}"
                                            {{ old('ruangan_id', $karyawan->ruangan_id) == $ruangan->id ? 'selected' : '' }}>
                                            {{ $ruangan->nama_ruangan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="jatah_cuti">Jatah Cuti Tahunan</label>
                                <input type="number" id="jatah_cuti" class="form-control" name="jatah_cuti"
                                    value="{{ old('jatah_cuti', $karyawan->jatah_cuti) }}" min="0" required>
                                <small class="form-text text-muted">
                                    Jumlah hari cuti yang didapat karyawan dalam setahun.
                                </small>
                            </div>

                            <div class="form-group">
                                <label>Cuti yang Sudah Diambil</label>
                                <input type="number" class="form-control" value="{{ $karyawan->cuti_diambil }}" disabled>
                                <small class="form-text text-muted">
                                    Kolom ini diperbarui secara otomatis saat ada pengajuan cuti.
                                </small>
                            </div>

                        </div>
                        <div class="card-footer text-right">
                            <a href="{{ route('karyawan.index') }}" class="btn btn-secondary">Batal</a>
                            <button class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endpush
