@extends('app.structure')
@section('content')
    <div class="container">
        <h2>Overtime Requests</h2>
        <a href="{{ route('overtime.create') }}" class="btn btn-primary mb-3">+ New Request</a>

        <table id="overtimeTable" class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Request By</th>
                <th>Submission Date</th>
                <th>Department</th>
                <th>Assigned Staff</th>
                <th>Start</th>
                <th>End</th>
                <th>Claim</th>
                <th>Attachment</th>
            </tr>
            </thead>
        </table>
    </div>

    <!-- Include DataTables JS -->
{{--    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>--}}
{{--    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>--}}

    <script>
        $(function() {
            $('#overtimeTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('overtime.data') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'request_by', name: 'request_by' },
                    { data: 'submission_date', name: 'submission_date' },
                    { data: 'department', name: 'department' },
                    { data: 'assigned_staff', name: 'assigned_staff', orderable: false, searchable: false },
                    { data: 'start_date', render: function(data, type, row){
                            return row.start_date + ' ' + row.start_time;
                        }},
                    { data: 'end_date', render: function(data, type, row){
                            return row.end_date + ' ' + row.end_time;
                        }},
                    { data: 'claim', name: 'claim' },
                    { data: 'attachment', name: 'attachment', orderable: false, searchable: false },
                ],
                order: [[2, 'desc']],
            });
        });
    </script>
@endsection



@include('app._partials.js')
