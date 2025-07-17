@extends('layouts.app')

@section('title', 'Data Karyawan')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Data Karyawan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Karyawan</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Daftar Karyawan</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('karyawan.index') }}" method="GET" class="mb-3">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <input type="text" class="form-control"
                                                    placeholder="Cari berdasarkan nama..." name="nama"
                                                    value="{{ request('nama') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <input type="text" class="form-control"
                                                    placeholder="Cari berdasarkan NIK..." name="nik"
                                                    value="{{ request('nik') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i>
                                                Filter</button>
                                            <a href="{{ route('karyawan.index') }}" class="btn btn-secondary">Reset</a>
                                        </div>
                                    </div>
                                </form>

                                <a href="{{ route('karyawan.create') }}" class="btn btn-primary mb-3">
                                    <i class="fas fa-plus"></i> Tambah Karyawan Baru
                                </a>
                                <button class="btn btn-success mb-3" data-toggle="modal" data-target="#importModal">
                                    <i class="fas fa-file-excel"></i> Import dari Excel
                                </button>
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                @if (session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                @if (session('import_errors'))
                                    <div class="alert alert-danger">
                                        <strong>Gagal mengimpor data. Periksa kesalahan berikut:</strong>
                                        <ul>
                                            @foreach (session('import_errors') as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>NIK</th>
                                                <th>Nama Karyawan</th>
                                                <th>Profesi</th>
                                                <th>Sisa Cuti</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($karyawans as $karyawan)
                                                <tr>
                                                    <td>{{ $loop->iteration + $karyawans->firstItem() - 1 }}</td>
                                                    <td>{{ $karyawan->nik }}</td>
                                                    <td>{{ $karyawan->nama_karyawan }}</td>
                                                    <td>{{ $karyawan->profesi }}</td>
                                                    <td>
                                                        <span
                                                            class="badge {{ $karyawan->sisa_cuti > 5 ? 'badge-success' : ($karyawan->sisa_cuti > 0 ? 'badge-warning' : 'badge-danger') }}">
                                                            {{ $karyawan->sisa_cuti }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info detail-btn" data-toggle="modal"
                                                            data-target="#detailKaryawanModal"
                                                            data-nik="{{ $karyawan->nik }}"
                                                            data-nama="{{ $karyawan->nama_karyawan }}"
                                                            data-profesi="{{ $karyawan->profesi }}"
                                                            data-tanggal_masuk="{{ \Carbon\Carbon::parse($karyawan->tanggal_masuk)->isoFormat('D MMMM YYYY') }}"
                                                            data-ruangan="{{ $karyawan->ruangan->nama_ruangan ?? 'N/A' }}"
                                                            data-jatah_cuti="{{ $karyawan->jatah_cuti }}"
                                                            data-cuti_diambil="{{ $karyawan->cuti_diambil }}"
                                                            data-sisa_cuti="{{ $karyawan->sisa_cuti }}">
                                                            <i class="fas fa-eye"></i> Detail
                                                        </button>
                                                        <a href="{{ route('karyawan.edit', $karyawan->id) }}"
                                                            class="btn btn-sm btn-warning"><i class="fas fa-edit"></i>
                                                            Edit</a>
                                                        <form action="{{ route('karyawan.destroy', $karyawan->id) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Yakin ingin menghapus data karyawan ini? Semua data cuti yang terkait juga akan terhapus.');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"><i
                                                                    class="fas fa-trash"></i> Hapus</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">Data karyawan tidak ditemukan.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="float-right">
                                    {{ $karyawans->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="detailKaryawanModal" tabindex="-1" role="dialog"
        aria-labelledby="detailKaryawanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailKaryawanModalLabel">Detail Data Karyawan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th style="width: 30%;">NIK</th>
                                <th style="width: 0.5%;">:</th>
                                <td id="modalNik"></td>
                            </tr>
                            <tr>
                                <th style="width: 30%;">Nama Lengkap</th>
                                <th style="">:</th>
                                <td id="modalNama"></td>
                            </tr>
                            <tr>
                                <th style="width: 30%;">Profesi</th>
                                <th style="">:</th>
                                <td id="modalProfesi"></td>
                            </tr>
                            <tr>
                                <th style="width: 30%;">Tanggal Masuk</th>
                                <th style="">:</th>
                                <td id="modalTanggalMasuk"></td>
                            </tr>
                            <tr>
                                <th style="width: 30%;">Ruangan</th>
                                <th style="">:</th>
                                <td id="modalRuangan"></td>
                            </tr>
                            <tr>
                                <th style="width: 30%;">Jatah Cuti Tahunan</th>
                                <th style="">:</th>
                                <td id="modalJatahCuti"></td>
                            </tr>
                            <tr>
                                <th style="width: 30%;">Cuti Sudah Diambil</th>
                                <th style="">:</th>
                                <td id="modalCutiDiambil"></td>
                            </tr>
                            <tr>
                                <th style="width: 30%;">Sisa Cuti</th>
                                <th style="">:</th>
                                <td id="modalSisaCuti"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data Karyawan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('karyawan.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>File Excel</label>
                            <input type="file" name="file" class="form-control" required accept=".xlsx, .xls">
                            <small class="form-text text-muted">
                                Pastikan file sesuai dengan template.
                                <a href="{{ route('karyawan.template') }}">Unduh Template Disini</a>.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#detailKaryawanModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var nik = button.data('nik');
                var nama = button.data('nama');
                var profesi = button.data('profesi');
                var tanggal_masuk = button.data('tanggal_masuk');
                var ruangan = button.data('ruangan');
                var jatah_cuti = button.data('jatah_cuti');
                var cuti_diambil = button.data('cuti_diambil');
                var sisa_cuti = button.data('sisa_cuti');

                var modal = $(this);
                modal.find('#modalNik').text(nik);
                modal.find('#modalNama').text(nama);
                modal.find('#modalProfesi').text(profesi);
                modal.find('#modalTanggalMasuk').text(tanggal_masuk);
                modal.find('#modalRuangan').text(ruangan);
                modal.find('#modalJatahCuti').text(jatah_cuti);
                modal.find('#modalCutiDiambil').text(cuti_diambil);
                modal.find('#modalSisaCuti').text(sisa_cuti);
            });
        });
    </script>
@endpush
