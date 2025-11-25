<script>

    $(document).ready(function() {
        // Inisialisasi DataTable
        const table = $('#overtimeTable').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                url: "{{ route('overtime.index.data') }}",
                data: function (d) {
                    d.status = $('#status').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.division = $('#division').val();
                    d.staff = $('#staff').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'submission_date', name: 'submission_date' },
                { data: 'department_name', name: 'department_name' },
                {
                    data: 'assigned_staff',
                    name: 'assigned_staff',
                    render: function(data) {
                        console.log("Raw staff data:", data);

                        let staffArray = [];
                        try {
                            staffArray = JSON.parse(data);
                        } catch (e) {
                            staffArray = data ? [data] : [];
                        }

                        if (!staffArray.length) {
                            return `<span class="text-muted">-</span>`;
                        }

                        return staffArray.map(name =>
                            `<span class="badge bg-success text-dark me-1 mb-1" style="font-size: 12px; padding: 6px 10px;">${name}</span>`
                        ).join('<br>');
                    }
                },
                {
                    data: 'start',
                    name: 'start',
                    render: function(data) {
                        if (!data) return '-';
                        const [datePart, timePart] = data.split(' ');
                        const date = new Date(`${datePart}T${timePart}`);
                        return date.toLocaleString('id-ID', {
                            day: '2-digit', month: 'long', year: 'numeric',
                            hour: '2-digit', minute: '2-digit'
                        });
                    }
                },
                {
                    data: 'end',
                    name: 'end',
                    render: function(data) {
                        if (!data) return '-';
                        const [datePart, timePart] = data.split(' ');
                        const date = new Date(`${datePart}T${timePart}`);
                        return date.toLocaleString('id-ID', {
                            day: '2-digit', month: 'long', year: 'numeric',
                            hour: '2-digit', minute: '2-digit'
                        });
                    }
                },
                {
                    data: null,
                    name: 'duration',
                    render: function(row) {
                        if (!row.start_date || !row.start_time || !row.end_date || !row.end_time) return '-';

                        const start = new Date(`${row.start_date}T${row.start_time}`);
                        const end = new Date(`${row.end_date}T${row.end_time}`);
                        const diffMs = end - start;

                        if (isNaN(diffMs) || diffMs <= 0) return '-';

                        const diffHrs = Math.floor(diffMs / (1000 * 60 * 60));
                        const diffMins = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

                        return `<span class="badge bg-secondary">${diffHrs} jam ${diffMins} menit</span>`;
                    }
                },
                { data: 'claim', name: 'claim' },
                { data: 'request_by_name', name: 'request_by_name' },
                { data: 'approved_info', name: 'approved_info' },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']],
            columnDefs: [
                { width: '150rem', targets: [4, 5] }
            ]
        });

        // ==========================
        // 🔍 Event Filter
        // ==========================

        // Ketika user submit filter form
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            table.ajax.reload();
        });

        // Atau otomatis reload saat status diubah
        $('#status, #division, #start_date, #end_date, #staff').on('change', function() {
            table.ajax.reload();
        });

        // Optional: Tombol reset filter
        $('#resetFilter').on('click', function() {
            $('#status').val('');
            $('#start_date').val('');
            $('#end_date').val('');
            table.ajax.reload();
        });
    });



    $(document).ready(function() {
        console.log('Select2 init...');

        $('#assigned_staff').select2({
            // placeholder: "Select staff from your department",
            width: '100%'
        });
    });


    $(document).ready(function() {
        console.log('Select2 init...');
        $('#assigned_staff').select2({
            width: '100%'
        });

        // 🔹 Handle submit via AJAX
        $('#overtimeRequestForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const formData = new FormData(this);

            console.log('Submitting form...'); // Debug

            $.ajax({
                url: "{{ route('overtime.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    console.log('Sending AJAX request...');
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil', response.message, 'success');
                        form[0].reset();
                        $('#assigned_staff').val(null).trigger('change');
                        window.location.href = "{{ url('overtime') }}";
                    } else {
                        Swal.fire('Gagal', response.message || 'Terjadi kesalahan', 'error');
                    }
                },
                error: function(xhr) {
                    console.log("xhr.responseJSON:", xhr.responseJSON);

                    if (xhr.status === 422) {

                        // Jika respons berisi message (BUKAN errors)
                        if (xhr.responseJSON.message) {
                            Swal.fire('Validasi Gagal', xhr.responseJSON.message, 'warning');
                            return;
                        }

                        // Jika respons bentuknya errors
                        let errors = xhr.responseJSON.errors;
                        let messages = Object.values(errors).flat().join('\n');
                        Swal.fire('Validasi Gagal', messages, 'warning');

                    } else {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan server', 'error');
                    }
                }
            });
        });
    });
</script>