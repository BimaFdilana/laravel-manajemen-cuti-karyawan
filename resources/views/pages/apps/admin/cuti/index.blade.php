@extends('layouts.app')

@section('title', 'Daftar Pengajuan Cuti')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Data Cuti</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Cuti</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4>Data Cuti Karyawan</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('cuti.index') }}" method="GET">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Nama Karyawan</label>
                                                <input type="text" class="form-control" name="nama"
                                                    value="{{ request('nama') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Dari Tanggal</label>
                                                <input type="date" class="form-control" name="tanggal_mulai"
                                                    value="{{ request('tanggal_mulai') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Sampai Tanggal</label>
                                                <input type="date" class="form-control" name="tanggal_akhir"
                                                    value="{{ request('tanggal_akhir') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary">Filter</button>
                                                <a href="{{ route('cuti.index') }}" class="btn btn-secondary">Reset</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <a href="{{ route('cuti.create') }}" class="btn btn-primary">Tambah Data Cuti</a>
                                <a href="{{ route('cuti.export', request()->query()) }}" class="btn btn-success ml-2">
                                    <i class="fa fa-file-excel"></i> Export ke Excel
                                </a>
                            </div>
                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Karyawan</th>
                                                <th>Ringkasan Pengajuan</th>
                                                <th>Progres Jatah Cuti</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($karyawans as $karyawan)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <div>
                                                            <b>{{ $karyawan->nama_karyawan }}</b>
                                                        </div>
                                                        <div class="text-muted">
                                                            {{ $karyawan->ruangan->nama_ruangan ?? 'N/A' }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>Total Pengajuan: <b>{{ $karyawan->cutis->count() }} kali</b>
                                                        </div>
                                                        <button class="btn btn-sm btn-info mt-2" data-toggle="modal"
                                                            data-target="#detailCutiModal"
                                                            data-karyawan-nama="{{ $karyawan->nama_karyawan }}"
                                                            data-karyawan-nik="{{ $karyawan->nik }}"
                                                            data-karyawan-profesi="{{ $karyawan->profesi }}"
                                                            data-karyawan-ruangan="{{ $karyawan->ruangan->nama_ruangan ?? 'N/A' }}"
                                                            data-cutis="{{ $karyawan->cutis->toJson() }}">
                                                            <i class="fa fa-eye"></i> Lihat Detail Cuti
                                                        </button>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $cutiDiambil = $karyawan->cuti_diambil;
                                                            $sisaCuti = $karyawan->sisa_cuti;
                                                            $totalJatahCuti = $cutiDiambil + $sisaCuti;
                                                            $persentaseJatah =
                                                                $totalJatahCuti > 0
                                                                    ? ($cutiDiambil / $totalJatahCuti) * 100
                                                                    : 0;
                                                        @endphp
                                                        <div>
                                                            <div class="text-small">Terpakai:
                                                                <b>{{ $cutiDiambil }}</b> dari
                                                                <b>{{ $totalJatahCuti }}</b>
                                                            </div>
                                                            <div class="progress" data-height="6" data-toggle="tooltip"
                                                                title="Sisa Jatah Cuti: {{ $sisaCuti }}">
                                                                <div class="progress-bar bg-warning" role="progressbar"
                                                                    style="width: {{ $persentaseJatah }}%;"
                                                                    aria-valuenow="{{ $persentaseJatah }}"
                                                                    aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">Data tidak ditemukan.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="detailCutiModal">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Cuti: <span id="modal-nama-karyawan"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>NIK:</strong> <span id="modal-nik"></span></p>
                            <p class="mb-1"><strong>Profesi:</strong> <span id="modal-profesi"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Ruangan:</strong> <span id="modal-ruangan"></span></p>
                        </div>
                    </div>

                    <h6>Daftar Riwayat Pengajuan Cuti:</h6>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Cuti</th>
                                    <th>Jumlah Hari</th>
                                    <th>Keperluan</th>
                                    <th>Progres</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="modal-detail-cuti-body">
                                {{-- Konten diisi oleh JavaScript --}}
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });

        $('#detailCutiModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            var nama = button.data('karyawan-nama');
            var nik = button.data('karyawan-nik');
            var profesi = button.data('karyawan-profesi');
            var ruangan = button.data('karyawan-ruangan');
            var cutis = button.data('cutis');

            modal.find('#modal-nama-karyawan').text(nama);
            modal.find('#modal-nik').text(nik);
            modal.find('#modal-profesi').text(profesi);
            modal.find('#modal-ruangan').text(ruangan);

            var tableBody = modal.find('#modal-detail-cuti-body');
            tableBody.empty();

            if (cutis.length > 0) {
                $.each(cutis, function(index, cuti) {
                    let tglMulai = new Date(cuti.tanggal_mulai_cuti).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                    let tglAkhir = new Date(cuti.tanggal_akhir_cuti).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });

                    let persentase = cuti.progres_persentase;
                    let colorClass = persentase >= 67 ? 'bg-success' : (persentase >= 34 ? 'bg-warning' :
                        'bg-danger');
                    if (persentase <= 0) colorClass = 'bg-secondary';
                    let title = persentase > 0 ? persentase + '% Selesai' : 'Belum Dimulai';

                    let progressBarHtml = `
                    <div class="progress" style="height: 6px;" data-toggle="tooltip" title="${title}">
                        <div class="progress-bar ${colorClass}" role="progressbar" style="width: ${persentase}%;" aria-valuenow="${persentase}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                `;



                    let editUrlTemplate = '{{ route('cuti.edit', ['cuti' => 'PLACEHOLDER']) }}';
                    let deleteUrlTemplate = '{{ route('cuti.destroy', ['cuti' => 'PLACEHOLDER']) }}';

                    let editUrl = editUrlTemplate.replace('PLACEHOLDER', cuti.id);
                    let deleteUrl = deleteUrlTemplate.replace('PLACEHOLDER', cuti.id);

                    let csrfToken = `{{ csrf_token() }}`;

                    let row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${tglMulai} - ${tglAkhir}</td>
                        <td>${cuti.jumlah_cuti} hari</td>
                        <td>${cuti.keperluan_cuti}</td>
                        <td>${progressBarHtml}</td>
                        <td>
                            <a href="${editUrl}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="${deleteUrl}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini? Ini akan mengembalikan jatah cuti karyawan.');">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                `;
                    tableBody.append(row);
                });
            } else {
                tableBody.append('<tr><td colspan="6" class="text-center">Tidak ada data cuti.</td></tr>');
            }

            modal.find('[data-toggle="tooltip"]').tooltip();
        });

        $('#detailCutiModal').on('hidden.bs.modal', function() {
            $('.tooltip').remove();
        });
    </script>
@endpush
