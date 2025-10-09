@extends('app.structure')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form id="overtimeRequestForm" enctype="multipart/form-data">
                                @csrf

                                <!-- Main Form -->
                                <div class="row">
                                    <!-- Submission Date -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="submission_date">Submission Date <span class="text-danger">*</span></label>
                                                <input type="date" id="submission_date" name="submission_date" class="form-control" required>
                                            </div>
                                        </div>

                                    <!-- Department -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="department">Department <span class="text-danger">*</span></label>
                                            <select id="department" name="department" class="form-control" required>
                                                <option value="">Select Department</option>
                                                @foreach($departments as $dept)
                                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Assigned Staff -->
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="assigned_staff">Assigned Staff <span class="text-danger">*</span></label>
                                            <select id="assigned_staff" name="assigned_staff[]" class="form-control select2" multiple required>
                                                @foreach($users as $id => $name)
                                                    <option value="{{ $name }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Hold CTRL / CMD to select multiple staff</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Start Date & Time -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                            <input type="date" id="start_date" name="start_date" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="start_time">Start Time <span class="text-danger">*</span></label>
                                            <input type="time" id="start_time" name="start_time" class="form-control" required>
                                        </div>
                                    </div>

                                    <!-- End Date & Time -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="end_date">End Date <span class="text-danger">*</span></label>
                                            <input type="date" id="end_date" name="end_date" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="end_time">End Time <span class="text-danger">*</span></label>
                                            <input type="time" id="end_time" name="end_time" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Details -->
                                <div class="form-group">
                                    <label for="details">Overtime Details <span class="text-danger">*</span></label>
                                    <textarea id="details" name="details" class="form-control" rows="3" placeholder="Describe the overtime work..." required></textarea>
                                </div>

                                <!-- Attachment -->
                                <div class="form-group">
                                    <label for="attachment">Attachment</label>
                                    <input type="file" id="attachment" name="attachment" class="form-control">
                                    <small class="text-muted">Optional — upload supporting file (PDF, JPG, etc.)</small>
                                </div>

                                <!-- Claim -->
                                <div class="form-group">
                                    <label for="claim">Overtime Claim (Rp)</label>
                                    <input type="number" step="0.01" id="claim" name="claim" class="form-control" placeholder="0.00">
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
@endsection

{{--@include('app._partials.js')--}}
{{--@include('app.overtime_request.overtime_request_js')--}}
