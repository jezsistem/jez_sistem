<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Manager Action Menu Toggle
    $('#managerActionBtn').on('click', function(e) {
        e.stopPropagation();
        $('#managerActionMenu').toggleClass('hidden');
        $('#hrActionMenu').addClass('hidden');
    });

    // HR Action Menu Toggle
    $('#hrActionBtn').on('click', function(e) {
        e.stopPropagation();
        $('#hrActionMenu').toggleClass('hidden');
        $('#managerActionMenu').addClass('hidden');
    });

    // Close menus when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#managerActionBtn, #managerActionMenu').length) {
            $('#managerActionMenu').addClass('hidden');
        }
        if (!$(e.target).closest('#hrActionBtn, #hrActionMenu').length) {
            $('#hrActionMenu').addClass('hidden');
        }
    });

    // Approve Button
    $(document).on('click', '#btnApprove', function() {
        Swal.fire({
            title: 'Approve Overtime?',
            text: 'Anda yakin ingin menyetujui lembur ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Approve',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280'
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
                            Swal.fire('Berhasil', 'Lembur telah disetujui', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            Swal.fire('Gagal', 'Terjadi kesalahan saat approve', 'error');
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

    // Reject Button
    $(document).on('click', '#btnReject', function() {
        Swal.fire({
            title: 'Reject Overtime?',
            text: 'Anda yakin ingin menolak lembur ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Reject',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280'
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
                            Swal.fire('Berhasil', 'Lembur telah ditolak', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            Swal.fire('Gagal', 'Terjadi kesalahan saat reject', 'error');
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

    // Approve HR Button
    $(document).on('click', '#btnApproveHR', function() {
        Swal.fire({
            title: 'Approve HR Overtime?',
            text: 'Apakah Anda yakin ingin menyetujui lembur ini sebagai HR?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Approve HR',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280'
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

    // Reject HR Button
    $(document).on('click', '#btnRejectHR', function() {
        Swal.fire({
            title: 'Reject By HR?',
            text: 'Apakah Anda yakin ingin menolak lembur ini sebagai HR?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Reject HR',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280'
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
                            Swal.fire('Berhasil', 'Lembur ditolak oleh HR', 'success');
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

    // Form Report Submit
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
                    Swal.fire('Berhasil', 'Report lembur telah disimpan', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire('Gagal', res.message || 'Gagal menyimpan report', 'error');
                }
            },
            error: function(err) {
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan server', 'error');
            }
        });
    });
});
</script>
