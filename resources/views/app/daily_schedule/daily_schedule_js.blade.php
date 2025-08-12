<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        var dailyScheduleTable = $('#dailyScheduleTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"pagination-class"ip>',
            ajax: {
                url : "{{ url('daily-schedules/datatables') }}",
                data : function (d) {
                    d.search = $('#daily_schedule_search').val();
                    d.employee_filter = $('#employee_filter').val();
                    d.division_filter = $('#division_filter').val();
                    d.shift_filter = $('#shift_filter').val();
                    d.status_filter = $('#status_filter').val();
                    d.start_date_filter = $('#start_date_filter').val();
                    d.end_date_filter = $('#end_date_filter').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                { data: 'ds_date', name: 'ds_date', orderable: false, width: '10%' },
                { data: 'employee_name', name: 'employee_name', orderable: false, width: '20%' },
                { data: 'division_name', name: 'division_name', orderable: false, width: '15%' },
                { data: 'shift_name', name: 'shift_name', orderable: false, width: '10%' },
                { data: 'ds_start_time', name: 'ds_start_time', orderable: false, width: '10%' },
                { data: 'ds_end_time', name: 'ds_end_time', orderable: false, width: '10%' },
                { data: 'ds_status', name: 'ds_status', orderable: false, width: '10%' },
                { data: 'action', name: 'action', orderable: false, searchable: false, width: '10%' },
            ],
            columnDefs: [
                {
                    "targets": 0,
                    "className": "text-center",
                    "width": "5%"
                },
                {
                    "targets": 3,
                    "className": "text-center font-weight-bold",
                    "render": function(data, type, row) {
                        if (type === 'display') {
                            return '<span class="badge badge-info">' + data + '</span>';
                        }
                        return data;
                    }
                },
                {
                    "targets": 5,
                    "className": "text-center"
                },
                {
                    "targets": 6,
                    "className": "text-center"
                },
                {
                    "targets": 7,
                    "className": "text-center"
                },
                {
                    "targets": 8,
                    "className": "text-center"
                }
            ],
            order: [], // No sorting
            pageLength: 25,
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
            },
            "drawCallback": function(settings) {
                updateStatistics();
                createDivisionGroups();
            }
        });
        
        // Search functionality with debounce
        var searchTimeout;
        $('#daily_schedule_search').on('keyup input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                dailyScheduleTable.draw(false);
            }, 300);
        });
    });

    // Apply filters function
    function applyFilters() {
        $('#dailyScheduleTable').DataTable().ajax.reload();
    }

    // Update statistics function
    function updateStatistics() {
        $.ajax({
            url: "{{ url('daily-schedules/statistics') }}",
            type: 'GET',
            data: {
                employee_filter: $('#employee_filter').val(),
                division_filter: $('#division_filter').val(),
                shift_filter: $('#shift_filter').val(),
                status_filter: $('#status_filter').val(),
                start_date_filter: $('#start_date_filter').val(),
                end_date_filter: $('#end_date_filter').val()
            },
            success: function(response) {
                $('#totalSchedules').text(response.total || 0);
                $('#scheduledSchedules').text(response.scheduled || 0);
                $('#completedSchedules').text(response.completed || 0);
            },
            error: function() {
                console.log('Error loading statistics');
            }
        });
    }

    // Auto-refresh every 30 seconds
    setInterval(function() {
        $('#dailyScheduleTable').DataTable().ajax.reload(null, false);
    }, 30000);

    // Create division groups function
    function createDivisionGroups() {
        var table = $('#dailyScheduleTable').DataTable();
        var currentDivision = '';
        var rowCount = 0;
        var isFirstRow = true;
        
        console.log('Creating division groups...');
        
        // Clear existing dividers
        $('.division-subheader').remove();
        
        table.rows().every(function() {
            var data = this.data();
            var division = data.division_name || 'Tidak Ada Divisi';
            
            console.log('Row division:', division, 'Current division:', currentDivision, 'Is first:', isFirstRow);
            
            if (division !== currentDivision || isFirstRow) {
                // Add sub-header row for new division at the beginning
                var subheaderRow = '<tr class="division-subheader"><td colspan="9" class="division-header-cell">DIVISI: ' + division + '</td></tr>';
                $(this.node()).before(subheaderRow);
                console.log('Added sub-header for division:', division);
                currentDivision = division;
                rowCount = 0;
                isFirstRow = false;
            }
            
            rowCount++;
            
            // Add alternating row colors within division
            if (rowCount % 2 === 0) {
                $(this.node()).addClass('table-secondary');
            } else {
                $(this.node()).removeClass('table-secondary');
            }
        });
        
        console.log('Division groups created successfully');
    }

    // Delete schedule function
    function deleteSchedule(id) {
        if (confirm('Are you sure you want to delete this schedule?')) {
            $.ajax({
                url: "{{ url('daily-schedules') }}/" + id,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#dailyScheduleTable').DataTable().ajax.reload();
                        alert('Schedule deleted successfully');
                    } else {
                        alert('Failed to delete schedule');
                    }
                },
                error: function() {
                    alert('Error occurred while deleting schedule');
                }
            });
        }
    }
</script> 