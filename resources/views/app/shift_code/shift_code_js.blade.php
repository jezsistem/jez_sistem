<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        var shiftCodeTable = $('#shiftCodeTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"pagination-class"ip>',
            ajax: {
                url : "{{ url('shift-codes/datatables') }}",
                data : function (d) {
                    d.search = $('#shift_code_search').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'sc_code', name: 'sc_code' },
                { data: 'sc_description', name: 'sc_description' },
                { data: 'sc_shift_name', name: 'sc_shift_name' },
                { data: 'sc_start_time', name: 'sc_start_time' },
                { data: 'sc_end_time', name: 'sc_end_time' },
                { data: 'sc_type', name: 'sc_type' },
                { data: 'sc_status', name: 'sc_status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            columnDefs: [
                {
                    "targets": 0,
                    "className": "text-center",
                    "width": "0%"
                }
            ],
            order: [[0, 'desc']],
            language: {
                "sProcessing":   "Loading...",
                "sLengthMenu":   "Tampilkan _MENU_ entri",
                "sZeroRecords":  "Tidak ditemukan data yang sesuai",
                "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
                "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                "sInfoPostFix":  "",
                "sSearch":       "Cari:",
                "sUrl":          "",
                // "oPaginate": {
                //     "sFirst":    "Pertama",
                //     "sPrevious": "Sebelumnya",
                //     "sNext":     "Selanjutnya",
                //     "sLast":     "Terakhir"
                // }
            }
        });
        
        // Search functionality with debounce
        var searchTimeout;
        $('#shift_code_search').on('keyup input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                shiftCodeTable.draw(false);
            }, 300);
        });
    });
</script> 