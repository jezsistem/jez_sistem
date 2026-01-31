@extends('app.structure')
@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Edit External Assignment Request</h3>
                        </div>
                        <div class="card-body">
                            <form id="externalAssignmentFormUpdate" >
                                @csrf

                                <div class="row">
                                    <!-- External Assignment Type -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="ea_id">Assignment Type <span class="text-danger">*</span></label>
                                            <input type="hidden" id="external_assignment_request_id" name="external_assignment_request_id" value="{{ $externalAssignmentRequest->id }}">
                                            <select class="form-control @error('ea_id') is-invalid @enderror"
                                                    id="ea_id" name="ea_id" required>
                                                <option value="">Select Type</option>
                                                @foreach($types as $type)
                                                    <option value="{{ $type->id }}" {{ old('ea_id', $externalAssignmentRequest->ea_id) == $type->id ? 'selected' : '' }}>
                                                        {{ $type->ea_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('ea_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Cash Advance -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="ear_cash_advance">Cash Advance</label>
                                            <input type="number"
                                                   class="form-control @error('ear_cash_advance') is-invalid @enderror"
                                                   id="ear_cash_advance" name="ear_cash_advance"
                                                   value="{{ old('ear_cash_advance', $externalAssignmentRequest->ear_cash_advance) }}" placeholder="0">
                                            @error('ear_cash_advance')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Date Start -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="ear_date_start">Start Date <span class="text-danger">*</span></label>
                                            <input type="date"
                                                   class="form-control @error('ear_date_start') is-invalid @enderror"
                                                   id="ear_date_start" name="ear_date_start"
                                                   value="{{ old('ear_date_start', $externalAssignmentRequest->ear_date_start ? \Carbon\Carbon::parse($externalAssignmentRequest->ear_date_start)->format('Y-m-d') : '') }}" required>
                                            @error('ear_date_start')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Date End -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="ear_date_end">End Date</label>
                                            <input type="date"
                                                   class="form-control @error('ear_date_end') is-invalid @enderror"
                                                   id="ear_date_end" name="ear_date_end"
                                                   value="{{ old('ear_date_end', $externalAssignmentRequest->ear_date_end ? \Carbon\Carbon::parse($externalAssignmentRequest->ear_date_end)->format('Y-m-d') : '') }}">
                                            @error('ear_date_end')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Location -->
                                <div class="form-group">
                                    <label for="ear_locations">Location <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ear_locations') is-invalid @enderror"
                                           id="ear_locations" name="ear_locations" 
                                           value="{{ old('ear_locations', $externalAssignmentRequest->ear_locations) }}" required>
                                    @error('ear_locations')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="ear_note">Catatan / Deskripsi Kegiatan</label>
                                    <textarea name="ear_note" id="ear_note"
                                              class="form-control @error('ear_note') is-invalid @enderror"
                                              rows="3"
                                              placeholder="Tuliskan catatan atau deskripsi kegiatan...">{{ old('ear_note', $externalAssignmentRequest->ear_note) }}</textarea>
                                    @error('ear_note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Rundown Details -->
                                <h5 class="mt-4">Rundown Details</h5>
                                <table class="table table-bordered" id="detailsTable">
                                    <thead>
                                    <tr>
                                        <th>Activity</th>
                                        <th>Date</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Description</th>
                                        <th style="width: 50px;">#</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($externalAssignmentRequest->rundowns && $externalAssignmentRequest->rundowns->count() > 0)
                                        @foreach($externalAssignmentRequest->rundowns as $index => $detail)
                                        <tr>
                                            <td><input type="text" name="details[{{ $index }}][activity]" class="form-control" value="{{ old('rundowns.'.$index.'.activity', $detail->activity) }}" required></td>
                                            <td><input type="date" name="details[{{ $index }}][date]" class="form-control" value="{{ old('rundowns.'.$index.'.date', $detail->rundown_date ? \Carbon\Carbon::parse($detail->rundown_date)->format('Y-m-d') : '') }}"></td>
                                            <td><input type="time" name="details[{{ $index }}][start_time]" class="form-control" value="{{ old('rundowns.'.$index.'.start_time', $detail->start_time ? \Carbon\Carbon::parse($detail->start_time)->format('H:i') : '') }}"></td>
                                            <td><input type="time" name="details[{{ $index }}][end_time]" class="form-control" value="{{ old('rundowns.'.$index.'.end_time', $detail->end_time ? \Carbon\Carbon::parse($detail->end_time)->format('H:i') : '') }}"></td>
                                            <td><textarea name="details[{{ $index }}][description]" class="form-control" rows="1">{{ old('rundowns.'.$index.'.description', $detail->description) }}</textarea></td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm btn-remove-row">&times;</button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td><input type="text" name="details[0][activity]" class="form-control" required></td>
                                            <td><input type="date" name="details[0][date]" class="form-control"></td>
                                            <td><input type="time" name="details[0][start_time]" class="form-control"></td>
                                            <td><input type="time" name="details[0][end_time]" class="form-control"></td>
                                            <td><textarea name="details[0][description]" class="form-control" rows="1"></textarea></td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm btn-remove-row">&times;</button>
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>

                                <div class="text-right mb-3">
                                    <button type="button" class="btn btn-sm btn-success" id="addRow">
                                        <i class="fa fa-plus"></i> Add Rundown Detail
                                    </button>
                                </div>

                                <hr style="margin-top: 5rem;">
                                <h5>Cash Advance Detail</h5>

                                <table class="table table-bordered" id="cashDetailTable">
                                    <thead>
                                    <tr>
                                        <th>Cash Purpose</th>
                                        <th>Cash Amount</th>
                                        <th width="50">#</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($externalAssignmentRequest->cashDetails && $externalAssignmentRequest->cashDetails->count() > 0)
                                        @foreach($externalAssignmentRequest->cashDetails as $index => $cashDetail)
                                        <tr>
                                            <td>
                                                <input type="text" name="cash_details[{{ $index }}][cash_purpose]" class="form-control" placeholder="Purpose" value="{{ old('cash_details.'.$index.'.cash_purpose', $cashDetail->cash_purpose) }}">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="cash_details[{{ $index }}][cash_amount]" class="form-control cash-amount" placeholder="0.00" value="{{ old('cash_details.'.$index.'.cash_amount', $cashDetail->cash_amount) }}">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger removeCashDetailRow">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>
                                                <input type="text" name="cash_details[0][cash_purpose]" class="form-control" placeholder="Purpose">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="cash_details[0][cash_amount]" class="form-control cash-amount" placeholder="0.00">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger removeCashDetailRow">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>

                                <div class="text-right mb-3">
                                    <button type="button" class="btn btn-sm btn-success" id="addCashDetailRow">
                                        <i class="fa fa-plus"></i> Add Cash Detail
                                    </button>
                                </div>

                                <div class="form-group">
                                    <label>Total Cash Used</label>
                                    <input type="text" id="total_cash_used" class="form-control" readonly value="0.00">
                                </div>

                                <!-- Submit -->
                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Request
                                    </button>
                                    <a href="{{ route('external-assignments.index') }}" class="btn btn-secondary">
                                        <i class="ki-outline ki-left"></i> Back
                                    </a>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@include('app._partials.js')
@include('app.external_assignment_request.external_assignment_request_js')
