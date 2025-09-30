@extends('app.structure')

@section('content')
<div class="content-wrapper">
    <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Page Heading-->
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <!--begin::Page Title-->
                    <h5 class="text-dark font-weight-bold my-1 mr-5">{{ $data['subtitle'] }}</h5>
                    <!--end::Page Title-->
                </div>
                <!--end::Page Heading-->
            </div>
            <!--end::Info-->
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-tools">
                                <!-- <a href="{{ route('user-divisions.edit', $division->id) }}" class="btn btn-warning btn-sm">
                                    <i class="ki-outline ki-notepad-edit"></i> Edit
                                </a> -->
                                <a href="{{ route('user-divisions.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="ki-outline ki-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="250"><strong>Division Code:</strong></td>
                                            <td>
                                                <span class="badge badge-info">{{ $division->ud_code }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Division Name:</strong></td>
                                            <td>{{ $division->ud_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if($division->ud_status == 'active')
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created By:</strong></td>
                                            <td>{{ $division->created_by ?? 'System' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Updated By:</strong></td>
                                            <td>{{ $division->updated_by ?? 'System' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created At:</strong></td>
                                            <td>{{ $division->created_at ? date('d/m/Y H:i', strtotime($division->created_at)) : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Updated At:</strong></td>
                                            <td>{{ $division->updated_at ? date('d/m/Y H:i', strtotime($division->updated_at)) : '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Description:</strong></label>
                                        <div class="border p-3 rounded bg-light">
                                            {{ $division->ud_description ?: 'No description provided' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .content-wrapper {
        overflow-y: auto;
    }
</style>

<script>
$(document).ready(function() {
    loadStore();
    clockUpdate();
});
</script>
@endsection 