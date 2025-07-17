@extends('layouts.app')

@section('title', 'Formulir Pengajuan Cuti')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Formulir Pengajuan Cuti</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('cuti.index') }}">Data Cuti</a></div>
                    <div class="breadcrumb-item">Ajukan Cuti</div>
                </div>
            </div>

            <div class="section-body">
                <div class="card">
                    <form action="{{ route('cuti.store') }}" method="POST">
                        @csrf
                        <div class="card-header">
                            <h4>Formulir Pengajuan</h4>
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
                                            {{ old('karyawan_id') == $karyawan->id ? 'selected' : '' }}>
                                            {{-- Teks (Sisa Cuti: ..) akan otomatis sesuai dengan data terbaru --}}
                                            {{ $karyawan->nama_karyawan }} (Sisa Cuti: {{ $karyawan->sisa_cuti }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Tanggal Mulai Cuti</label>
                                <input type="date" class="form-control" name="tanggal_mulai_cuti"
                                    value="{{ old('tanggal_mulai_cuti') }}" required>
                            </div>

                            <div class="form-group">
                                <label>Tanggal Akhir Cuti</label>
                                <input type="date" class="form-control" name="tanggal_akhir_cuti"
                                    value="{{ old('tanggal_akhir_cuti') }}" required>
                            </div>

                            <div class="form-group">
                                <label>Keperluan Cuti</label>
                                <textarea class="form-control" name="keperluan_cuti" rows="5" required>{{ old('keperluan_cuti') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea class="form-control" name="keterangan" rows="4">{{ old('keterangan') }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <a href="{{ route('cuti.index') }}" class="btn btn-secondary">Batal</a>
                            <button class="btn btn-primary">Ajukan</button>
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
