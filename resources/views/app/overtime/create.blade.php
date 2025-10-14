@extends('app.structure')

@section('content')

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form id="overtimeRequestForm" enctype="multipart/form-data">
                                @csrf

                                <!-- Submission Date -->
                                <div class="form-group">
                                    <label for="submission_date">Submission Date <span class="text-danger">*</span></label>
                                    <input type="date" id="submission_date" name="submission_date" class="form-control" required>
                                </div>

                                <!-- Department (Auto-filled) -->
                                <div class="form-group">
                                    <label for="department">Department</label>
                                    <input type="text" id="department_name" class="form-control"
                                           value="{{ $data['user']->ud_name }}" readonly>
                                    <input type="hidden" name="department" value="{{ $data['user']->ud_id }}">
                                </div>

                                <!-- Assigned Staff -->
                                <div class="form-group">
                                    <label for="assigned_staff">Assigned Staff <span class="text-danger">*</span></label>
                                    <select id="assigned_staff" name="assigned_staff[]" class="form-control select2" multiple required>
                                        @foreach($staff as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Start Date & Time -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Start Date</label>
                                        <input type="date" name="start_date" class="form-control" required>
                                        <label class="mt-2">Start Time</label>
                                        <input type="time" name="start_time" class="form-control" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label>End Date</label>
                                        <input type="date" name="end_date" class="form-control" required>
                                        <label class="mt-2">End Time</label>
                                        <input type="time" name="end_time" class="form-control" required>
                                    </div>
                                </div>

                                <!-- Details -->
                                <div class="form-group mt-3">
                                    <label for="details">Overtime Details <span class="text-danger">*</span></label>
                                    <textarea id="details" name="details" class="form-control" rows="3" placeholder="Describe the overtime work..." required></textarea>
                                </div>

                                <!-- Attachment -->
                                <div class="form-group">
                                    <label for="attachment">Attachment</label>
                                    <input type="file" id="attachment" name="attachment" class="form-control">
                                </div>

                                <!-- Claim -->
                                <div class="form-group">
                                    <label for="claim">Overtime Claim</label>
{{--                                    <input type="number" step="0.01" id="claim" name="claim" class="form-control" placeholder="0.00">--}}
                                    <select name="claim" id="claim" class="form-control">
                                        <option value="">-- Select Overtime Claim --</option>
                                        @foreach($overtime_types as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Submit -->
                                <div class="form-group text-right mt-4">
                                    <button type="submit" class="btn btn-primary">Submit Overtime Request</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

{{--    <!-- JS Section -->--}}
{{--    @push('scripts')--}}
{{--        <script>--}}
{{--            $(document).ready(function () {--}}
{{--                $('#assigned_staff').select2({--}}
{{--                    placeholder: "Select staff from your department",--}}
{{--                    width: '100%'--}}
{{--                });--}}
{{--            });--}}
{{--        </script>--}}
{{--    @endpush--}}



    @include('app._partials.js')
    @include('app.overtime.overtime_js')
@endsection


