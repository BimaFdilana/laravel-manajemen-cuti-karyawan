<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Cuti Karyawan</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo3.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/karyawan.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="header">
            <a href="/" class="page-logo">
                <img src="{{ asset('img/logo3.png') }}" alt="Logo Perusahaan">
            </a>
            <div class="header-text">
                <h1>📅 Jadwal Cuti Karyawan</h1>
                <p>Dashboard publik untuk memantau jadwal cuti</p>
            </div>
        </div>
        <div class="controls">
            <div class="filter-group">
                <label for="nameFilter">Nama</label>
                <input type="text" id="nameFilter" class="filter-input" placeholder="Cari berdasarkan nama...">
            </div>
            <br>
            <div class="filter-group">
                <label for="monthFilter">Bulan</label>
                <select id="monthFilter" class="filter-input">
                    <option value="">Semua Bulan</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}">{{ \Carbon\Carbon::create()->month($i)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="filter-group">
                <label for="yearFilter">Tahun</label>
                <select id="yearFilter" class="filter-input">
                    <option value="">Semua Tahun</option>
                    @for ($i = date('Y'); $i >= 2020; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <button class="reset-btn" onclick="resetFilters()">Reset Filter</button>
        </div>

        <div class="content">
            <div class="leave-grid" id="leaveGrid">
            </div>
        </div>
    </div>

    <div class="modal fade" id="leaveDetailModal" tabindex="-1" aria-labelledby="leaveDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: #667eea; color: white;">
                    <h5 class="modal-title" id="leaveDetailModalLabel"><i class="fas fa-user-clock me-2"></i>Detail Cuti
                        Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="filter: invert(1) grayscale(100%) brightness(200%);"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <p><strong><i class="fas fa-user fa-fw me-2"></i>Nama:</strong> <span
                                    id="modalEmployeeName"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-building fa-fw me-2"></i>Bagian:</strong> <span
                                    id="modalDepartment"></span></p>
                        </div>
                    </div>

                    <h6 class="mt-4"><i class="fas fa-chart-pie fa-fw me-2"></i>Progres Jatah Cuti Tahunan</h6>
                    <span id="modalAllowanceText" class="text-muted small d-block mb-1"></span>
                    <div class="progress" style="height: 20px;">
                        <div id="modalAllowanceProgressBar"
                            class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                            style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <hr class="my-4">

                    <h6><i class="fas fa-history fa-fw me-2"></i>Riwayat Pengajuan Cuti</h6>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Rentang Tanggal</th>
                                    <th>Total Hari</th>
                                    <th>Keperluan</th>
                                </tr>
                            </thead>
                            <tbody id="modalLeaveHistoryBody">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const employeeData = @json($cutiDataForJs);
        let filteredData = [...employeeData];
        const leaveDetailModal = new bootstrap.Modal(document.getElementById('leaveDetailModal'));

        function renderEmployeeCards() {
            const grid = document.getElementById('leaveGrid');
            grid.innerHTML = '';

            if (filteredData.length === 0) {
                grid.innerHTML =
                    `<div class="no-data"><div class="no-data-icon">📭</div><p>Tidak ada karyawan yang mengambil cuti sesuai filter.</p></div>`;
                return;
            }

            grid.innerHTML = filteredData.map((employee, index) => {
                return `
                <div class="leave-card" onclick="showDetails(${index})">
                    <div class="employee-name">${employee.employeeName}</div>
                    <div class="department">${employee.department}</div>
                    <div class="leave-info">
                        <div class="info-row">
                            <span class="info-label">Total Pengajuan</span>
                            <span class="info-value">${employee.leaves.length} kali</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Jatah Terpakai</span>
                            <span class="info-value">${employee.leaveTaken} / ${employee.totalAllowance} hari</span>
                        </div>
                    </div>
                </div>
                `;
            }).join('');
        }

        function showDetails(index) {
            const employee = filteredData[index];

            document.getElementById('modalEmployeeName').textContent = employee.employeeName;
            document.getElementById('modalDepartment').textContent = employee.department;

            const allowanceUsedPercentage = employee.totalAllowance > 0 ? (employee.leaveTaken / employee.totalAllowance) *
                100 : 0;
            let allowanceColorClass = 'bg-success';
            if (allowanceUsedPercentage >= 90) allowanceColorClass = 'bg-danger';
            else if (allowanceUsedPercentage >= 50) allowanceColorClass = 'bg-warning';

            document.getElementById('modalAllowanceText').textContent =
                `Terpakai ${employee.leaveTaken} dari ${employee.totalAllowance} hari`;
            const progressBar = document.getElementById('modalAllowanceProgressBar');
            progressBar.style.width = allowanceUsedPercentage + '%';
            progressBar.setAttribute('aria-valuenow', allowanceUsedPercentage);
            progressBar.className = `progress-bar progress-bar-striped progress-bar-animated ${allowanceColorClass}`;
            progressBar.textContent = Math.round(allowanceUsedPercentage) + '%';

            const historyBody = document.getElementById('modalLeaveHistoryBody');
            historyBody.innerHTML = '';
            if (employee.leaves && employee.leaves.length > 0) {
                employee.leaves.forEach((leave, leaveIndex) => {
                    const allDatesHtml = leave.allDates.join('<br>');
                    const row = `
                        <tr>
                            <td>${leaveIndex + 1}</td>
                            <td>${leave.startDate} - ${leave.endDate}</td>
                            <td>${leave.totalDays}</td>
                            <td>${leave.purpose || '-'}</td>
                        </tr>
                    `;
                    historyBody.insertAdjacentHTML('beforeend', row);
                });
            }

            const tooltipTriggerList = [].slice.call(historyBody.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            leaveDetailModal.show();
        }

        function applyFilters() {
            const nameFilter = document.getElementById('nameFilter').value.toLowerCase();
            const monthFilter = document.getElementById('monthFilter').value;
            const yearFilter = document.getElementById('yearFilter').value;

            filteredData = employeeData.filter(employee => {
                const nameMatch = !nameFilter || employee.employeeName.toLowerCase().includes(nameFilter);

                if (!monthFilter && !yearFilter) {
                    return nameMatch;
                }

                const dateMatch = employee.leaves.some(leave => {
                    const leaveDate = new Date(leave.rawStartDate);
                    const leaveMonth = leaveDate.getMonth() + 1;
                    const leaveYear = leaveDate.getFullYear();
                    const monthMatch = !monthFilter || leaveMonth.toString() === monthFilter;
                    const yearMatch = !yearFilter || leaveYear.toString() === yearFilter;
                    return monthMatch && yearMatch;
                });

                return nameMatch && dateMatch;
            });
            renderEmployeeCards();
        }

        function resetFilters() {
            document.getElementById('nameFilter').value = '';
            document.getElementById('monthFilter').value = '';
            document.getElementById('yearFilter').value = '';
            filteredData = [...employeeData];
            renderEmployeeCards();
        }

        document.getElementById('nameFilter').addEventListener('input', applyFilters);
        document.getElementById('monthFilter').addEventListener('change', applyFilters);
        document.getElementById('yearFilter').addEventListener('change', applyFilters);

        document.addEventListener('DOMContentLoaded', function() {
            renderEmployeeCards();
        });
    </script>
</body>

</html>
