@extends('app.structure')
@section('content')

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <div class="container">
        <h4 class="mb-4">Detail Overtime Request</h4>

        <!-- Tombol Back -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('overtime.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <h4 class="mb-0">Detail Overtime Request</h4>
        </div>

        <!-- ========================= CARD DETAIL ========================= -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Header Information</h5>
                <div>
                    @php
                        $statusBadge = $detail->status ?? '-';
                    @endphp
                    <span class="badge bg-light text-dark">{{ $statusBadge }}</span>

{{--                    @if($isManager && empty($detail->approved_by))--}}
{{--                        <button id="btnApprove" class="btn btn-success btn-sm ms-2">--}}
{{--                            <i class="bi bi-check-circle"></i> Approve Overtime--}}
{{--                        </button>--}}
{{--                    @endif--}}

{{--                    @if($detail->status === 'HR Check' && $isHR)--}}
{{--                        <button id="btnApproveHR" class="btn btn-warning btn-sm ms-2">--}}
{{--                            <i class="bi bi-person-check"></i> Approve HR--}}
{{--                        </button>--}}
{{--                    @endif--}}
                    {{-- APPROVE BUTTONS --}}
                    @if($isManager && empty($detail->approved_by))
                        <div class="btn-group ms-2">
                            <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" id="ManagerAction" aria-expanded="false">
                                <i class="bi bi-check-circle"></i> Manager Action
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <button id="btnApprove" class="dropdown-item text-success">
                                        <i class="bi bi-check-circle"></i> Approve Overtime
                                    </button>
                                </li>
                                <li>
                                    <button id="btnReject" class="dropdown-item text-danger">
                                        <i class="bi bi-x-circle"></i> Reject Overtime
                                    </button>
                                </li>
                            </ul>
                        </div>
                    @endif

                    @if($detail->status === 'HR Check' && $isHR)
                        <div class="btn-group ms-2">
                            <button type="button" class="btn btn-warning btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-check"></i> HR Action
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <button id="btnApproveHR" class="dropdown-item text-success">
                                        <i class="bi bi-check2"></i> Approve HR
                                    </button>
                                </li>
                                <li>
                                    <button id="btnRejectHR" class="dropdown-item text-danger">
                                        <i class="bi bi-x-circle"></i> Reject HR
                                    </button>
                                </li>
                            </ul>
                        </div>
                    @endif

                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <!-- Kolom Kiri: Detail Utama -->
                    <div class="col-md-8">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <th>Department</th>
                                <td>{{ $detail->department_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Assigned Staff</th>
                                <td>
                                    @php
                                        $staff = is_array($detail->assigned_staff)
                                            ? $detail->assigned_staff
                                            : json_decode($detail->assigned_staff, true);
                                    @endphp

                                    @if(!empty($staff))
                                        @foreach($staff as $s)
                                            <span class="badge bg-success text-dark me-1 mb-1" style="font-size: 12px; padding: 6px 10px;">{{ $s }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Start</th>
                                <td>{{ \Carbon\Carbon::parse($detail->start)->translatedFormat('d F Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>End</th>
                                <td>{{ \Carbon\Carbon::parse($detail->end)->translatedFormat('d F Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Duration</th>
                                <td>
                                    @php
                                        $start = \Carbon\Carbon::parse($detail->start);
                                        $end = \Carbon\Carbon::parse($detail->end);
                                        $diffHours = $start->diffInHours($end);
                                        $diffMinutes = $start->diffInMinutes($end) % 60;
                                    @endphp
                                    <span class="badge bg-secondary">{{ $diffHours }} jam {{ $diffMinutes }} menit</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Claim</th>
                                <td>{{ $detail->claim }}</td>
                            </tr>
                            <tr>
                                <th>Desc Overtime</th>
                                <td>{{ $detail->details }}</td>
                            </tr>
                            <tr>
                                <th>Requested By</th>
                                <td>{{ $detail->request_by_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ \Carbon\Carbon::parse($detail->created_at)->translatedFormat('d F Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Kolom Kanan: Approval Info -->
                    <div class="col-md-4 border-start">
                        <h6 class="mb-3 text-primary"><i class="bi bi-clock-history"></i> Approval History</h6>
                        @if(!empty($detail->approval_logs))
                            <ul class="timeline">
                                @foreach($detail->approval_logs as $log)
                                    <li class="timeline-item text-success">
                                        <strong>{{ $log['role'] ?? '-' }}:</strong><br>
                                        {{ $log['name'] ?? '-' }}<br>
                                        <small>{{ $log['date'] ?? '-' }}</small>
                                        @if(!empty($log['note']))
                                            <br><small>{{ $log['note'] }}</small>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted mb-0">Belum ada riwayat approval.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================= CARD INPUT REPORT ========================= -->
        <!-- ========================= CARD INPUT / VIEW REPORT ========================= -->
        @if($statusBadge === 'Approved' || 'HR Check' || 'Done')
            <div class="card shadow-sm border-warning mb-4">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil-square"></i>
                        {{ !empty($detail->report_desc) ? 'View Report Overtime' : 'Input Report Overtime' }}
                    </h5>
                </div>

                <div class="card-body">
                    @if(empty($detail->report_desc))
                        {{-- =================== FORM INPUT REPORT =================== --}}
                        <form id="formReport" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="report_desc" class="form-label">Report Description</label>
                                <textarea name="report_desc" id="report_desc" class="form-control" rows="4" placeholder="Tuliskan hasil pekerjaan selama lembur..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="report_attachment" class="form-label">Attachment (optional)</label>
                                <input type="file" name="report_attachment" id="report_attachment" class="form-control">
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-warning text-dark">
                                    <i class="bi bi-save"></i> Simpan Report
                                </button>
                            </div>
                        </form>
                    @else
                        {{-- =================== VIEW REPORT =================== --}}
                        <div class="mb-3">
                            <h6 class="text-primary"><i class="bi bi-journal-text"></i> Deskripsi Report:</h6>
                            <p class="border rounded p-3 bg-light">{{ $detail->report_desc }}</p>
                        </div>

                        @if(!empty($detail->report_attachment))
                            <div class="mb-3">
                                <h6 class="text-primary"><i class="bi bi-paperclip"></i> Lampiran:</h6>
                                <a href="{{ asset('storage/'.$detail->report_attachment) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-file-earmark-arrow-down"></i> Lihat Lampiran
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @endif


    </div>

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
    </style>

    @include('app._partials.js')
    @include('app.overtime.overtime_js')

    <script>
        // ✅ Tombol Approve
        $(document).on('click', '#btnApprove', function() {
            Swal.fire({
                title: 'Approve Overtime?',
                text: 'Anda yakin ingin menyetujui lembur ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Approve',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('overtime.approve', $detail->id) }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            action: 'Approved'
                        },
                        success: function(res) {
                            if (res.success) {
                                swal('Berhasil', 'Lembur telah disetujui', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                swal('Gagal', 'Terjadi kesalahan saat approve', 'error');
                            }
                        },
                        error: function(err) {
                            console.error(err);
                            swal('Error', 'Terjadi kesalahan server', 'error');
                        }
                    });
                }
            });
        });

        $(document).on('click', '#btnReject', function() {
            Swal.fire({
                title: 'Reject Overtime?',
                text: 'Anda yakin ingin menolak lembur ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Approve',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('overtime.approve', $detail->id) }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            action: 'Rejected'
                        },
                        success: function(res) {
                            if (res.success) {
                                swal('Berhasil', 'Lembur telah disetujui', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                swal('Gagal', 'Terjadi kesalahan saat approve', 'error');
                            }
                        },
                        error: function(err) {
                            console.error(err);
                            swal('Error', 'Terjadi kesalahan server', 'error');
                        }
                    });
                }
            });
        });


        $('#formReport').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);
            $.ajax({
                url: "{{ route('overtime.report.submit', $detail->id) }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {
                    if (res.success) {
                        swal('Berhasil', 'Report lembur telah disimpan', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        swal('Gagal', res.message || 'Gagal menyimpan report', 'error');
                    }
                },
                error: function(err) {
                    console.error(err);
                    swal('Error', 'Terjadi kesalahan server', 'error');
                }
            });
        });

        $(document).on('click', '#btnApproveHR', function() {
            Swal.fire({
                title: 'Approve HR Overtime?',
                text: 'Apakah Anda yakin ingin menyetujui lembur ini sebagai HR?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Approve HR',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('overtime.approve.hr', $detail->id) }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            action: 'Done'
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire('Berhasil', 'Lembur disetujui oleh HR', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
                            }
                        },
                        error: function(err) {
                            console.error(err);
                            Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                        }
                    });
                }
            });
        });

        $(document).on('click', '#btnRejectHR', function() {
            Swal.fire({
                title: 'Rejected By HR?',
                text: 'Apakah Anda yakin ingin menolak lembur ini sebagai HR?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Approve HR',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('overtime.approve.hr', $detail->id) }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            action: 'Rejected'
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire('Berhasil', 'Lembur disetujui oleh HR', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
                            }
                        },
                        error: function(err) {
                            console.error(err);
                            Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                        }
                    });
                }
            });
        });


    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection
