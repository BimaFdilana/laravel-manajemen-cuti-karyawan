@extends('layouts.app')

@section('title', 'Edit Data Cuti')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Data Cuti</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('cuti.index') }}">Data Cuti</a></div>
                    <div class="breadcrumb-item">Edit</div>
                </div>
            </div>

            <div class="section-body">
                <div class="card">
                    <form action="{{ route('cuti.update', $cuti->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-header">
                            <h4>Edit Formulir</h4>
                        </div>
                        <div class="card-body">
                            {{-- Menampilkan error validasi --}}
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-group">
                                <label>Nama Karyawan</label>
                                <select name="karyawan_id" class="form-control select2" required>
                                    <option value="">-- Pilih Karyawan --</option>
                                    @foreach ($karyawans as $karyawan)
                                        <option value="{{ $karyawan->id }}"
                                            {{ old('karyawan_id', $cuti->karyawan_id) == $karyawan->id ? 'selected' : '' }}>
                                            {{-- Teks (Sisa Cuti: ..) akan otomatis sesuai dengan data terbaru --}}
                                            {{ $karyawan->nama_karyawan }} (Sisa Cuti: {{ $karyawan->sisa_cuti }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Tanggal Mulai Cuti</label>
                                <input type="date" class="form-control" name="tanggal_mulai_cuti"
                                    value="{{ old('tanggal_mulai_cuti', $cuti->tanggal_mulai_cuti->format('Y-m-d')) }}"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>Tanggal Akhir Cuti</label>
                                <input type="date" class="form-control" name="tanggal_akhir_cuti"
                                    value="{{ old('tanggal_akhir_cuti', $cuti->tanggal_akhir_cuti->format('Y-m-d')) }}"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>Keperluan Cuti</label>
                                <textarea class="form-control" name="keperluan_cuti" rows="3" required>{{ old('keperluan_cuti', $cuti->keperluan_cuti) }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea class="form-control" name="keterangan" rows="2">{{ old('keterangan', $cuti->keterangan) }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <a href="{{ route('cuti.index') }}" class="btn btn-secondary">Batal</a>
                            <button class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    {{-- Tambahkan Select2 untuk dropdown yang lebih baik jika belum ada --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endpush
