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
                                <!-- <a href="{{ route('user-positions.edit', $position->id) }}" class="btn btn-warning btn-sm">
                                    <i class="ki-outline ki-notepad-edit"></i> Edit
                                </a> -->
                                <a href="{{ route('user-positions.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="ki-outline ki-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="250"><strong>Position Code:</strong></td>
                                            <td>
                                                <span class="badge" style="background-color: {{ $position->up_color }}; color: white;">
                                                    {{ $position->up_code }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Position Name:</strong></td>
                                            <td>{{ $position->up_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Level:</strong></td>
                                            <td>{{ $position->up_level }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Can Approve Leave:</strong></td>
                                            <td>
                                                @if($position->up_can_approve_leave)
                                                    <span class="badge badge-success">Yes</span>
                                                @else
                                                    <span class="badge badge-secondary">No</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if($position->up_is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <!-- <tr>
                                            <td><strong>Color:</strong></td>
                                            <td>
                                                <div style="width: 30px; height: 20px; background-color: {{ $position->up_color }}; border: 1px solid #ccc;"></div>
                                                <small>{{ $position->up_color }}</small>
                                            </td>
                                        </tr> -->
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Description:</strong></label>
                                        <div class="border p-3 rounded bg-light">
                                            {{ $position->up_description ?: 'No description provided' }}
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