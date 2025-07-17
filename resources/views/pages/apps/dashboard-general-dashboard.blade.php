@extends('layouts.app')

@section('title', 'Dashboard Utama')

@push('style')
    <style>
        .card-full-height {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .card-full-height .card-body {
            flex-grow: 1;
            overflow-y: auto;
        }

        .clickable-notification {
            cursor: pointer;
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Dashboard</h1>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Karyawan Sedang Cuti</h4>
                            </div>
                            <div class="card-body">
                                {{ $sedangCutiCount }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info">
                            <i class="far fa-calendar-check"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Pengajuan Cuti</h4>
                            </div>
                            <div class="card-body">
                                {{ $totalCutiCount }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Karyawan</h4>
                            </div>
                            <div class="card-body">
                                {{ $totalKaryawanCount }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Pengajuan Cuti Terbaru</h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-md">
                                    <thead>
                                        <tr>
                                            <th>Nama Karyawan</th>
                                            <th>Tanggal</th>
                                            <th>Progres</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($cutiTerbaru as $cuti)
                                            <tr>
                                                <td>{{ $cuti->karyawan->nama_karyawan ?? 'Karyawan Dihapus' }}</td>
                                                <td>{{ $cuti->tanggal_mulai_cuti->format('d M') }} -
                                                    {{ $cuti->tanggal_akhir_cuti->format('d M Y') }}</td>
                                                <td>
                                                    @php
                                                        $persentase = $cuti->progres_persentase;
                                                        $colorClass = 'bg-secondary';
                                                        if ($persentase > 0) {
                                                            $colorClass = 'bg-danger';
                                                            if ($persentase >= 34 && $persentase < 67) {
                                                                $colorClass = 'bg-warning';
                                                            } elseif ($persentase >= 67) {
                                                                $colorClass = 'bg-success';
                                                            }
                                                        }
                                                    @endphp
                                                    <div class="progress" style="height: 8px;" data-toggle="tooltip"
                                                        title="{{ $persentase > 0 ? $persentase . '% Selesai' : 'Belum Dimulai' }}">
                                                        <div class="progress-bar {{ $colorClass }}" role="progressbar"
                                                            style="width: {{ $persentase }}%;"
                                                            aria-valuenow="{{ $persentase }}" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">Tidak ada pengajuan cuti terbaru.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <a href="{{ route('cuti.index') }}" class="btn btn-primary">Lihat Semua Pengajuan</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12 col-12 col-sm-12">
                    <div class="card card-full-height">
                        <div class="card-header">
                            <h4>Notifikasi Terbaru</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled list-unstyled-border">
                                @forelse ($notifikasiTerbaru as $notification)
                                    @if (isset($notification->data['cuti_id']) && isset($cutisForNotifications[$notification->data['cuti_id']]))
                                        @php
                                            $cuti = $cutisForNotifications[$notification->data['cuti_id']];
                                        @endphp
                                        <li class="media clickable-notification" data-toggle="modal"
                                            data-target="#detailNotifikasiModal"
                                            data-deskripsi="{{ $notification->data['message'] ?? '' }}"
                                            data-nama="{{ $cuti->karyawan->nama_karyawan ?? 'N/A' }}"
                                            data-nik="{{ $cuti->karyawan->nik ?? '-' }}"
                                            data-ruangan="{{ $cuti->karyawan->ruangan->nama_ruangan ?? 'N/A' }}"
                                            data-sisa-cuti="{{ $cuti->karyawan->sisa_cuti ?? '0' }} hari"
                                            data-tanggal="{{ $cuti->tanggal_mulai_cuti->format('d M Y') }} - {{ $cuti->tanggal_akhir_cuti->format('d M Y') }}"
                                            data-progress="{{ $cuti->progres_persentase }}">
                                            <div class="media-body">
                                                <div class="float-right text-primary">
                                                    <small>{{ $notification->created_at->diffForHumans() }}</small></div>
                                                <div class="media-title">{{ $notification->data['title'] ?? 'Notifikasi' }}
                                                </div>
                                                <span
                                                    class="text-small text-muted">{{ Str::limit($notification->data['message'], 50) }}</span>
                                            </div>
                                        </li>
                                    @else
                                        <li class="media clickable-notification" data-toggle="modal"
                                            data-target="#generalNotifikasiModal"
                                            data-title="{{ $notification->data['title'] ?? 'Notifikasi Umum' }}"
                                            data-message="{{ $notification->data['message'] ?? 'Tidak ada detail.' }}">
                                            <div class="media-body">
                                                <div class="float-right text-primary">
                                                    <small>{{ $notification->created_at->diffForHumans() }}</small></div>
                                                <div class="media-title">
                                                    {{ $notification->data['title'] ?? 'Notifikasi Umum' }}</div>
                                                <span
                                                    class="text-small text-muted">{{ Str::limit($notification->data['message'], 50) }}</span>
                                            </div>
                                        </li>
                                    @endif
                                @empty
                                    <li class="media">
                                        <p class="text-center w-100">Tidak ada notifikasi terbaru.</p>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="card-footer text-center pt-1 pb-1">
                            <a href="{{ route('notifications.index') }}" class="btn btn-primary btn-lg btn-round">
                                Lihat Semua Notifikasi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="detailNotifikasiModal" tabindex="-1" role="dialog"
        aria-labelledby="detailNotifikasiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailNotifikasiModalLabel">Detail Notifikasi Cuti</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p><strong>Deskripsi Notifikasi:</strong> <span id="modalDeskripsi"></span></p>
                    <hr>
                    <h5>Detail Karyawan</h5>
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th style="width: 30%;">Nama Karyawan</th>
                            <td id="modalNama"></td>
                        </tr>
                        <tr>
                            <th>NIK</th>
                            <td id="modalNik"></td>
                        </tr>
                        <tr>
                            <th>Bagian / Ruangan</th>
                            <td id="modalRuangan"></td>
                        </tr>
                        <tr>
                            <th>Sisa Cuti</th>
                            <td id="modalSisaCuti"></td>
                        </tr>
                    </table>
                    <h5 class="mt-4">Detail Pengajuan</h5>
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th style="width: 30%;">Tanggal Cuti</th>
                            <td id="modalTanggal"></td>
                        </tr>
                        <tr>
                            <th>Progres Cuti</th>
                            <td>
                                <div class="progress" style="height: 15px;">
                                    <div id="modalProgressBar" class="progress-bar" role="progressbar"
                                        style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary"
                        data-dismiss="modal">Tutup</button></div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="generalNotifikasiModal" tabindex="-1" role="dialog"
        aria-labelledby="generalNotifikasiModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generalModalTitle">Detail Notifikasi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p id="generalModalMessage" style="white-space: pre-wrap;"></p>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary"
                        data-dismiss="modal">Tutup</button></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            $('#detailNotifikasiModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var deskripsi = button.data('deskripsi');
                var nama = button.data('nama');
                var nik = button.data('nik');
                var ruangan = button.data('ruangan');
                var sisaCuti = button.data('sisa-cuti');
                var tanggal = button.data('tanggal');
                var progress = button.data('progress');
                var modal = $(this);
                modal.find('#modalDeskripsi').text(deskripsi);
                modal.find('#modalNama').text(nama);
                modal.find('#modalNik').text(nik);
                modal.find('#modalRuangan').text(ruangan);
                modal.find('#modalSisaCuti').text(sisaCuti);
                modal.find('#modalTanggal').text(tanggal);
                var progressBar = modal.find('#modalProgressBar');
                progressBar.css('width', progress + '%').attr('aria-valuenow', progress).text(progress +
                    '%');
                progressBar.removeClass('bg-success bg-warning bg-danger bg-secondary').addClass(
                    progress >= 67 ? 'bg-success' :
                    progress >= 34 ? 'bg-warning' :
                    progress > 0 ? 'bg-danger' : 'bg-secondary'
                );
            });


            $('#generalNotifikasiModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var title = button.data('title');
                var message = button.data('message');

                var modal = $(this);
                modal.find('#generalModalTitle').text(title);
                modal.find('#generalModalMessage').text(message);
            });
        });
    </script>
@endpush
