@extends('app.structure')
@section('content')

    <div class="container">
        <h4 class="mb-4">Detail External Assignment Request</h4>

        <!-- Card Gabungan Header Information + History -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Header Information</h5>
                <span class="badge bg-light text-dark">{{ $detail->ear_status }}</span>
            </div>

            <div class="card-body">
                <div class="row">
                    <!-- Kolom Kiri: Informasi Utama -->
                    <div class="col-md-8">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <th>Staff</th>
                                <td>{{ $detail->requester->u_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Division</th>
                                <td>{{ $detail->requester->division->ud_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Assignment Type</th>
                                <td>{{ $detail->type->ea_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Start</th>
                                <td>{{ $detail->ear_date_start }} {{ $detail->ear_time_start }}</td>
                            </tr>
                            <tr>
                                <th>End</th>
                                <td>{{ $detail->ear_date_end }} {{ $detail->ear_time_end }}</td>
                            </tr>
                            <tr>
                                <th>Location</th>
                                <td>{{ $detail->ear_locations ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Cash Advance</th>
                                <td>Rp {{ number_format($detail->ear_cash_advance, 0, ',', '.') }}</td>
                            </tr>
                        </table>

                        <!-- Tombol Approval Dinamis -->
                        <hr>
                        @if ($canApprove)
                            @if ($approvalStep === 'hr')
                                <!-- Dropdown Tombol HR -->
                                <div class="dropdown mt-3">
                                    <button class="btn btn-success dropdown-toggle" type="button" id="hrActionDropdown"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Action by HR
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="hrActionDropdown">
                                        <li>
                                            <a class="dropdown-item text-success" href="#" data-bs-toggle="modal"
                                                data-bs-target="#hrApproveModal">Approve</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal"
                                                data-bs-target="#hrRejectModal">Reject</a>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Modal Approve HR -->
                                <div class="modal fade" id="hrApproveModal" tabindex="-1"
                                    aria-labelledby="hrApproveModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form method="POST" action="{{ route('ear.approve', $detail->id) }}">
                                            @csrf
                                            <input type="hidden" name="action" value="Finance Process">
                                            <div class="modal-content">
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title">Approve by HR</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Catatan HR</label>
                                                        <textarea name="ear_hr_note" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success">Submit Approval</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Modal Reject HR -->
                                <div class="modal fade" id="hrRejectModal" tabindex="-1"
                                    aria-labelledby="hrRejectModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form method="POST" action="{{ route('ear.approve', $detail->id) }}">
                                            @csrf
                                            <input type="hidden" name="action" value="Rejected">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Reject by HR</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Reason</label>
                                                        <textarea name="ear_hr_note" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">Submit Reject</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @elseif($approvalStep === 'finance')
                                <!-- Dropdown Tombol Finance -->
                                <div class="dropdown mt-3">
                                    <button class="btn btn-success dropdown-toggle" type="button"
                                        id="financeActionDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Action by Finance
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="financeActionDropdown">
                                        <li><a class="dropdown-item text-success" href="#" data-bs-toggle="modal"
                                                data-bs-target="#financeApproveModal">Approve</a></li>
                                        <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal"
                                                data-bs-target="#financeRejectModal">Reject</a></li>
                                    </ul>
                                </div>

                                <!-- Modal Approve Finance -->
                                <div class="modal fade" id="financeApproveModal" tabindex="-1"
                                    aria-labelledby="financeApproveModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ route('ear.approve', $detail->id) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="action" value="Done">
                                            <div class="modal-content">
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title">Finance Approval</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="ear_finance_note" class="form-label">Catatan
                                                            Finance</label>
                                                        <textarea class="form-control" name="ear_finance_note" id="ear_finance_note" rows="3"></textarea>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="ear_finance_uploads" class="form-label">Upload Bukti
                                                            Transfer</label>
                                                        <input type="file" class="form-control"
                                                            name="ear_finance_uploads" id="ear_finance_uploads"
                                                            accept="image/*" required>
                                                        <small class="text-muted">Format: JPG, PNG, atau JPEG. Maksimal
                                                            2MB.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">Submit
                                                        Approval</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Modal Reject Finance -->
                                <div class="modal fade" id="financeRejectModal" tabindex="-1"
                                    aria-labelledby="financeRejectModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ route('ear.approve', $detail->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="Rejected">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Reject by Finance</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="ear_finance_note_reject"
                                                            class="form-label">Reason</label>
                                                        <textarea class="form-control" name="ear_finance_note" id="ear_finance_note_reject" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-danger">Submit Reject</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <!-- Dropdown Tombol Supervisor / Manager -->
                                <div class="dropdown mt-3">
                                    <button class="btn btn-success dropdown-toggle" type="button"
                                        id="defaultActionDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Action by {{ ucfirst($approvalStep) }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="defaultActionDropdown">
                                        <li>
                                            <form action="{{ route('ear.approve', $detail->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <input type="hidden" name="action" value="Approved">
                                                <button type="submit" class="dropdown-item text-success"
                                                    onclick="return confirm('Yakin ingin melanjutkan ke tahap berikutnya?')">Approve</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('ear.approve', $detail->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <input type="hidden" name="action" value="Rejected">
                                                <button type="submit" class="dropdown-item text-danger"
                                                    onclick="return confirm('Yakin ingin menolak request ini?')">Reject</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        @elseif($detail->ear_status === 'Completed')
                            <div class="alert alert-success mt-3 mb-0">
                                <i class="bi bi-check-circle"></i> Request ini sudah <strong>Completed</strong>.
                            </div>
                        @else
                            <div class="alert alert-secondary mt-3 mb-0">
                                <i class="bi bi-hourglass-split"></i> Menunggu proses approval berikutnya.
                            </div>
                        @endif
                    </div>

                    <!-- Kolom Kanan: Timeline History -->
                    <div class="col-md-4 border-start">
                        <h6 class="mb-3 text-primary"><i class="bi bi-clock-history"></i> Approval History</h6>
                        <ul class="timeline">
                            <li class="timeline-item {{ $detail->ear_approved_by ? 'text-success' : 'text-muted' }}">
                                <strong>Supervisor / Manager:</strong><br>
                                @if ($detail->ear_approved_by)
                                    {{ $detail->approver->u_name ?? '-' }}<br>
                                    <small>{{ $detail->ear_approved_at ? \Carbon\Carbon::parse($detail->ear_approved_at)->format('d M Y H:i') : '-' }}</small>
                                @else
                                    <em>Menunggu persetujuan</em>
                                @endif
                            </li>

                            <li class="timeline-item {{ $detail->ear_hr_checked_by ? 'text-success' : 'text-muted' }}">
                                <strong>HR Checked:</strong><br>
                                @if ($detail->ear_hr_checked_by)
                                    {{ $detail->hrChecker->u_name ?? '-' }}<br>
                                    <small>{{ $detail->ear_hr_checked_at ? \Carbon\Carbon::parse($detail->ear_hr_checked_at)->format('d M Y H:i') : '-' }}</small>
                                    <br>
                                    <small>{{ $detail->ear_hr_note }}</small>
                                @else
                                    <em>Menunggu pemeriksaan HR</em>
                                @endif
                            </li>

                            <li class="timeline-item {{ $detail->ear_finance_by ? 'text-success' : 'text-muted' }}">
                                <strong>Finance Processed:</strong><br>
                                @if ($detail->ear_finance_by)
                                    {{ $detail->financeProcessor->u_name ?? '-' }}<br>
                                    <small>{{ $detail->ear_finance_at ? \Carbon\Carbon::parse($detail->ear_finance_at)->format('d M Y H:i') : '-' }}</small>
                                    <br>
                                    <small>{{ $detail->ear_finance_note }}</small>
                                    @if ($detail->ear_finance_uploads)
                                        <div class="mt-2">
                                            <a href="#" class="text-primary" data-bs-toggle="modal"
                                                data-bs-target="#financeProofModal">
                                                <i class="fa fa-image"></i> Lihat Bukti Transfer
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    <em>Menunggu verifikasi Finance</em>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rundown -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Rundown</h5>
            </div>
            <div class="card-body">
                @if ($detail->rundowns && $detail->rundowns->count() > 0)
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Activity</th>
                                <th>Date</th>
                                <th>Start</th>
                                <th>End</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detail->rundowns as $r)
                                <tr>
                                    <td>{{ $r->activity }}</td>
                                    <td>{{ $r->rundown_date }}</td>
                                    <td>{{ $r->start_time }}</td>
                                    <td>{{ $r->end_time }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted mb-0">Tidak ada rundown.</p>
                @endif
            </div>
        </div>

        <!-- Cash Details -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Cash Details</h5>

            </div>
            <div class="card-body">
                @if ($detail->cashDetails && $detail->cashDetails->count() > 0)
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Purpose</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detail->cashDetails as $c)
                                <tr>
                                    <td>{{ $c->cash_purpose }}</td>
                                    <td>Rp {{ number_format($c->cash_amount, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted mb-0">Tidak ada cash details.</p>
                @endif
            </div>
        </div>

        <!-- Reports -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Reports</h5>
            </div>

            <div class="card-body">
                @if (
                    ($detail->ear_status === 'Approved' ||
                        $detail->ear_status === 'HR Check' ||
                        $detail->ear_status === 'Finance Process') &&
                        Auth::id() === $detail->request_by)
                    <!-- Form Input Report -->
                    <form action="{{ route('ear.report.update', $detail->id) }}" method="POST" id="reportForm">
                        @csrf

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle" id="reportTable">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 120px;">Tanggal</th>
                                        <th style="width: 90px;">Start</th>
                                        <th style="width: 90px;">End</th>
                                        <th>Detail Kegiatan</th>
                                        <th style="width: 140px;">Cash (Rp)</th>
                                        <th style="width: 50px;">#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($detail->reports && $detail->reports->count() > 0)
                                        @foreach ($detail->reports as $index => $report)
                                            <tr>
                                                <td><input type="date" name="reports[{{ $index }}][earr_date]"
                                                        class="form-control form-control-sm"
                                                        value="{{ $report->earr_date }}" required></td>
                                                <td><input type="time"
                                                        name="reports[{{ $index }}][earr_time_start]"
                                                        class="form-control form-control-sm"
                                                        value="{{ $report->earr_time_start }}" required></td>
                                                <td><input type="time"
                                                        name="reports[{{ $index }}][earr_time_end]"
                                                        class="form-control form-control-sm"
                                                        value="{{ $report->earr_time_end }}" required></td>
                                                <td>
                                                    <textarea name="reports[{{ $index }}][earr_detail]" class="form-control form-control-sm" rows="1"
                                                        required>{{ $report->earr_detail }}</textarea>
                                                </td>
                                                <td><input type="number"
                                                        name="reports[{{ $index }}][cash_amount]"
                                                        class="form-control form-control-sm text-end" placeholder="0"
                                                        value="{{ $report->cash_amount }}"></td>
                                                <td class="text-center">
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm btn-remove-row">&times;</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td><input type="date" name="reports[0][earr_date]"
                                                    class="form-control form-control-sm" required></td>
                                            <td><input type="time" name="reports[0][earr_time_start]"
                                                    class="form-control form-control-sm" required></td>
                                            <td><input type="time" name="reports[0][earr_time_end]"
                                                    class="form-control form-control-sm" required></td>
                                            <td>
                                                <textarea name="reports[0][earr_detail]" class="form-control form-control-sm" rows="1" required></textarea>
                                            </td>
                                            <td><input type="number" name="reports[0][cash_amount]"
                                                    class="form-control form-control-sm text-end" placeholder="0"></td>
                                            <td class="text-center">
                                                <button type="button"
                                                    class="btn btn-danger btn-sm btn-remove-row">&times;</button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="d-flex">
                                <button type="submit" class="btn btn-success btn-sm px-4" id="save_data">
                                    <i class="fa fa-save"></i> Simpan Report
                                </button>
                                @if ($detail->ear_status === 'Approved')
                                    <button type="submit" class="btn btn-info btn-sm px-4 ml-4" id="send_data_to_hr">
                                        <i class="fa fa-paper-plane"></i> Simpan & Kirim Report ke HR
                                    </button>
                                @endif
                            </div>


                            <button type="button" class="btn btn-primary btn-sm" id="addRowBtn">
                                <i class="fa fa-plus"></i> Tambah Baris
                            </button>
                        </div>
                    </form>

                    {{-- Jika sudah ada report sebelumnya, tampilkan di bawah form --}}
                    @if ($detail->reports && $detail->reports->count() > 0 && Auth::id() != $detail->request_by)
                        <hr class="my-4">
                        <h6 class="fw-bold mb-2">Report Dinas Sebelumnya:</h6>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle mb-0">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 120px;">Tanggal</th>
                                        <th style="width: 90px;">Start</th>
                                        <th style="width: 90px;">End</th>
                                        <th>Detail</th>
                                        <th style="width: 140px;" class="text-end">Cash (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($detail->reports as $rep)
                                        <tr>
                                            <td>{{ $rep->earr_date }}</td>
                                            <td>{{ $rep->earr_time_start }}</td>
                                            <td>{{ $rep->earr_time_end }}</td>
                                            <td>{{ $rep->earr_detail }}</td>
                                            <td class="text-end">Rp {{ number_format($rep->cash_amount, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th colspan="4" style="text-align: right;">Total Cash:</th>
                                        <th style="text-align: right;">
                                            Rp {{ number_format($detail->reports->sum('cash_amount'), 0, ',', '.') }}
                                        </th>
                                    </tr>
                                    <tr></tr>
                                    <tr>
                                        <th colspan="4" style="text-align: right;">Plus Minus Cash:</th>
                                        <th style="text-align: right;">
                                            Rp
                                            {{ number_format($detail->ear_cash_advance - $detail->reports->sum('cash_amount'), 0, ',', '.') }}
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                @else
                    <!-- View Only -->
                    @if ($detail->reports && $detail->reports->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle mb-0">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 120px;">Tanggal</th>
                                        <th style="width: 90px;">Start</th>
                                        <th style="width: 90px;">End</th>
                                        <th>Detail</th>
                                        <th style="width: 140px;" class="text-end">Cash (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($detail->reports as $rep)
                                        <tr>
                                            <td>{{ $rep->earr_date }}</td>
                                            <td>{{ $rep->earr_time_start }}</td>
                                            <td>{{ $rep->earr_time_end }}</td>
                                            <td>{{ $rep->earr_detail }}</td>
                                            <td class="text-end">Rp {{ number_format($rep->cash_amount, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th colspan="4" style="text-align: right;">Total Cash:</th>
                                        <th style="text-align: left;">
                                            Rp {{ number_format($detail->reports->sum('cash_amount'), 0, ',', '.') }}
                                        </th>
                                    </tr>
                                    <tr></tr>
                                    <tr>
                                        <th colspan="4" style="text-align: right;">Plus Minus Cash:</th>
                                        <th style="text-align: left;">
                                            Rp
                                            {{ number_format($detail->ear_cash_advance - $detail->reports->sum('cash_amount'), 0, ',', '.') }}
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">Belum ada laporan dinas.</p>
                    @endif
                @endif
            </div>
        </div>

    </div>

    @if ($detail->ear_finance_uploads)
        <!-- Modal Preview Bukti Transfer -->
        <div class="modal fade" id="financeProofModal" tabindex="-1" aria-labelledby="financeProofModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="financeProofModalLabel">Bukti Transfer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ asset('storage/' . $detail->ear_finance_uploads) }}" alt="Bukti Transfer"
                            class="img-fluid rounded shadow">
                    </div>
                </div>
            </div>
        </div>
    @endif


    <style>
        .timeline {
            position: relative;
            list-style: none;
            padding-left: 20px;
            margin: 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            left: 10px;
            width: 2px;
            height: 100%;
            background: #0d6efd;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
            padding-left: 25px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 3px;
            top: 5px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #0d6efd;
        }

        .timeline-item strong {
            color: #212529;
        }

        .timeline-item small {
            color: #6c757d;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reportForm = document.getElementById('reportForm');
            const sendToHrBtn = document.getElementById('send_data_to_hr');

            if (sendToHrBtn) {
                sendToHrBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    reportForm.action = "{{ route('ear.report.store', $detail->id) }}";
                    reportForm.submit();
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize rowIndex based on existing reports count
            let rowIndex = {{ $detail->reports && $detail->reports->count() > 0 ? $detail->reports->count() : 1 }};

            // Tambah baris
            document.getElementById('addRowBtn').addEventListener('click', function() {
                let newRow = `
            <tr>
                <td><input type="date" name="reports[${rowIndex}][earr_date]" class="form-control form-control-sm" required></td>
                <td><input type="time" name="reports[${rowIndex}][earr_time_start]" class="form-control form-control-sm" required></td>
                <td><input type="time" name="reports[${rowIndex}][earr_time_end]" class="form-control form-control-sm" required></td>
                <td><textarea name="reports[${rowIndex}][earr_detail]" class="form-control form-control-sm" rows="1" required></textarea></td>
                <td><input type="number" name="reports[${rowIndex}][cash_amount]" class="form-control form-control-sm text-end" placeholder="0"></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remove-row">&times;</button></td>
            </tr>
        `;
                $('#reportTable tbody').append(newRow);
                rowIndex++;
            });

            // Hapus baris
            $(document).on('click', '.btn-remove-row', function() {
                $(this).closest('tr').remove();
            });
        });
    </script>

    <!-- Bootstrap JS -->
    {{--    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> --}}

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection

@include('app.external_assignment_request.modal')
@include('app._partials.js')
