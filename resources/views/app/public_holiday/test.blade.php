<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Test Shift Codes</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Test Shift Codes</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="shiftCodeTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>Shift Name</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- DataTables will populate this -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script>
        // Function to handle delete button
        function deleteShiftCode(id) {
            if (confirm('Are you sure you want to delete this shift code?')) {
                console.log('Deleting shift code with ID:', id);
                // Here you can add AJAX call to delete the record
                alert('Delete function would be called for ID: ' + id);
            }
        }

        $(function() {
            console.log('Initializing DataTables for test shift codes...');
            
            // Setup CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $('#shiftCodeTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('hr-shift-codes.datatables') }}',
                    error: function(xhr, error, thrown) {
                        console.error('DataTables AJAX error:', error);
                        console.error('Response:', xhr.responseText);
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
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"
                }
            });
            
            console.log('DataTables initialization completed');
        });
    </script>
</body>
</html> 