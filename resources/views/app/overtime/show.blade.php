@extends('app.structure')
@section('content')

    <div class="container">
        <h4 class="mb-4">Detail Overtime Request</h4>

        <!-- Card Header Information -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Header Information</h5>
                <div>
                    @php
                        $statusBadge = (!empty($detail->approved_by) && !empty($detail->approved_at))
                            ? 'Approved'
                            : ($detail->status ?? '-');
                    @endphp
                    <span class="badge bg-light text-dark">{{ $statusBadge }}</span>

                    {{-- ✅ Button muncul hanya jika MANAGER --}}
                    @if($isManager && empty($detail->approved_by))
                        <button id="btnApprove" class="btn btn-success btn-sm ms-2">
                            <i class="bi bi-check-circle"></i> Approve Overtime
                        </button>
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
                                <td>
                                    {{ \Carbon\Carbon::parse($detail->start)->translatedFormat('d F Y H:i') }}
                                </td>
                            </tr>
                            <tr>
                                <th>End</th>
                                <td>
                                    {{ \Carbon\Carbon::parse($detail->end)->translatedFormat('d F Y H:i') }}
                                </td>
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
                            _token: "{{ csrf_token() }}"
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
    </script>
@endsection
