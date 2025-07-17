@extends('layouts.app')

@section('title', 'Semua Notifikasi')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Semua Notifikasi</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Notifikasi</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Daftar Notifikasi</h4>
                                <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">Tandai Semua Dibaca</button>
                                </form>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled list-unstyled-border">
                                    @forelse ($notifications as $notification)
                                        @if (isset($notification->data['cuti_id']) && isset($cutis[$notification->data['cuti_id']]))
                                            @php
                                                $cuti = $cutis[$notification->data['cuti_id']];
                                            @endphp
                                            <li id="notification-{{ $notification->id }}"
                                                class="media {{ !$notification->read_at ? 'bg-light' : '' }} p-3 rounded mb-2 align-items-center"
                                                style="cursor: pointer;" data-toggle="modal"
                                                data-target="#detailNotifikasiModal" data-id="{{ $notification->id }}"
                                                data-deskripsi="{{ $notification->data['message'] ?? '' }}"
                                                data-nama="{{ $cuti->karyawan->nama_karyawan ?? 'N/A' }}"
                                                data-nik="{{ $cuti->karyawan->nik ?? '-' }}"
                                                data-ruangan="{{ $cuti->karyawan->ruangan->nama_ruangan ?? 'N/A' }}"
                                                data-sisa-cuti="{{ $cuti->karyawan->sisa_cuti ?? '0' }} hari"
                                                data-tanggal="{{ $cuti->tanggal_mulai_cuti->format('d M Y') }} - {{ $cuti->tanggal_akhir_cuti->format('d M Y') }}"
                                                data-progress="{{ $cuti->progres_persentase }}">
                                                <div class="mr-3">
                                                    <i
                                                        class="{{ $notification->data['icon'] ?? 'fas fa-bell' }} fa-2x text-primary"></i>
                                                </div>
                                                <div class="media-body">
                                                    <div class="float-right text-primary">
                                                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                                                    </div>
                                                    <div
                                                        class="media-title {{ !$notification->read_at ? 'font-weight-bold' : '' }}">
                                                        {{ $notification->data['title'] ?? 'Notifikasi' }}
                                                    </div>
                                                    <span class="text-small text-muted">
                                                        {{ Str::limit($notification->data['message'], 100) }}
                                                    </span>
                                                </div>
                                                <form action="{{ route('notifications.destroy', $notification->id) }}"
                                                    method="POST" class="ml-3"
                                                    onsubmit="event.stopPropagation(); return confirm('Yakin ingin menghapus notifikasi ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-sm btn-danger"
                                                        data-toggle="tooltip" title="Hapus Notifikasi"><i
                                                            class="fas fa-times"></i></button>
                                                </form>
                                            </li>
                                        @else
                                            <li id="notification-{{ $notification->id }}"
                                                class="media {{ !$notification->read_at ? 'bg-light' : '' }} p-3 rounded mb-2 align-items-center"
                                                style="cursor: pointer;" data-toggle="modal"
                                                data-target="#generalNotifikasiModal" data-id="{{ $notification->id }}"
                                                data-title="{{ $notification->data['title'] ?? 'Notifikasi Umum' }}"
                                                data-message="{{ $notification->data['message'] ?? 'Tidak ada detail.' }}">
                                                <div class="mr-3">
                                                    <i
                                                        class="{{ $notification->data['icon'] ?? 'fas fa-bell' }} fa-2x text-info"></i>
                                                </div>
                                                <div class="media-body">
                                                    <div class="float-right text-primary">
                                                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                                                    </div>
                                                    <div
                                                        class="media-title {{ !$notification->read_at ? 'font-weight-bold' : '' }}">
                                                        {{ $notification->data['title'] ?? 'Notifikasi Umum' }}
                                                    </div>
                                                    <span class="text-small text-muted">
                                                        {{ Str::limit($notification->data['message'], 100) }}
                                                    </span>
                                                </div>
                                                <form action="{{ route('notifications.destroy', $notification->id) }}"
                                                    method="POST" class="ml-3"
                                                    onsubmit="event.stopPropagation(); return confirm('Yakin ingin menghapus notifikasi ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-sm btn-danger"
                                                        data-toggle="tooltip" title="Hapus Notifikasi"><i
                                                            class="fas fa-times"></i></button>
                                                </form>
                                            </li>
                                        @endif
                                    @empty
                                        <li class="media p-3">
                                            <p class="text-center w-100 my-2">Tidak ada notifikasi.</p>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                            <div class="card-footer text-right">
                                {{ $notifications->links() }}
                            </div>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="generalNotifikasiModal" tabindex="-1" role="dialog"
        aria-labelledby="generalNotifikasiModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generalModalTitle">Detail Notifikasi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="generalModalMessage" style="white-space: pre-wrap;"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            function markNotificationAsRead(id) {
                let notificationElement = $('#notification-' + id);

                if (id && notificationElement.hasClass('bg-light')) {
                    $.ajax({


                        url: `{{ url('notifications') }}/${id}/mark-as-read`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {

                                notificationElement.removeClass('bg-light');
                                notificationElement.find('.media-title').removeClass(
                                'font-weight-bold');

                            }
                        },
                        error: function() {
                            console.log('Gagal menandai notifikasi sebagai sudah dibaca.');
                        }
                    });
                }
            }


            $('#detailNotifikasiModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                markNotificationAsRead(button.data('id'));

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
                markNotificationAsRead(button.data('id'));

                var title = button.data('title');
                var message = button.data('message');

                var modal = $(this);
                modal.find('#generalModalTitle').text(title);
                modal.find('#generalModalMessage').text(message);
            });
        });
    </script>
@endpush
