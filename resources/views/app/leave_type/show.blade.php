@extends('app.structure')
@section('title', $data['title'])
@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-2">
                <!--begin::Page Title-->
                <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5 fs-3">{{ $data['subtitle'] }}</h5>
                <!--end::Page Title-->
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Subheader-->
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools">
                        <a href="{{ route('leave-types.index') }}" class="btn btn-secondary btn-sm">
                            <i class="ki-outline ki-left"></i> Back
                        </a>
                    
                    </div>
                </div>
                <div class="card-body">
                <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Code:</strong></td>
                                    <td>{{ $leaveType->lt_code }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $leaveType->lt_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Description:</strong></td>
                                    <td>{{ $leaveType->lt_description ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Default Days:</strong></td>
                                    <td>{{ $leaveType->lt_default_days }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Default Hours:</strong></td>
                                    <td>{{ $leaveType->lt_default_hours }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Unit:</strong></td>
                                    <td>{{ ucfirst($leaveType->lt_unit) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <!-- <tr>
                                    <td width="150"><strong>Color:</strong></td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $leaveType->lt_color }}; color: white;">
                                            {{ $leaveType->lt_color }}
                                        </span>
                                    </td>
                                </tr> -->
                                <tr>
                                    <td><strong>Requires Approval:</strong></td>
                                    <td>
                                        @if($leaveType->lt_requires_approval)
                                            <span class="badge badge-success">Yes</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($leaveType->lt_is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <!-- <tr>
                                    <td><strong>Created By:</strong></td>
                                    <td>{{ $leaveType->created_by }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created At:</strong></td>
                                    <td>{{ $leaveType->created_at }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Updated At:</strong></td>
                                    <td>{{ $leaveType->updated_at }}</td>
                                </tr> -->
                            </table>
                        </div>
                    </div>
                
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->

@endsection 