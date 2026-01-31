@extends('app.structure')
@section('content')
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
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
                <div class="d-flex align-items-center">
                    <a href="{{ route('staff-information.index') }}" class="btn btn-light-primary font-weight-bolder">
                        <i class="ki-outline ki-arrow-left"></i>Back
                    </a>
                </div>
            </div>
        </div>
        <!--end::Subheader-->
        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-lg-5">
                                <!--begin::Card-->
                                <div class="card card-custom gutter-b">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h3 class="card-label">Profile Photo</h3>
                                        </div>
                                    </div>
                                    <div class="card-body text-center">
                                        <img src="{{ $staff->u_photo ? Storage::disk('s3')->url($staff->u_photo) : asset('photos/no_image.png') }}"
                                            alt="Photo" class="img-fluid rounded" style="max-height: 300px;">
                                    </div>
                                </div>
                                <!--end::Card-->
                            </div>
                            <div class="col-lg-7">
                                <!--begin::Card-->
                                <div class="card card-custom gutter-b">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h3 class="card-label">Personal Information</h3>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label font-weight-bold">Name:</label>
                                            <div class="col-lg-8">
                                                <span class="form-control-plaintext">{{ $staff->u_name }}</span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label font-weight-bold">Birthday:</label>
                                            <div class="col-lg-8">
                                                <span class="form-control-plaintext">{{ $staff->u_birthday }}</span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label font-weight-bold">Address:</label>
                                            <div class="col-lg-8">
                                                <span class="form-control-plaintext">{{ $staff->u_address }}</span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label font-weight-bold">Position:</label>
                                            <div class="col-lg-8">
                                                <span class="form-control-plaintext">{{ $staff->up_name ?? '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-0">
                                            <label class="col-lg-4 col-form-label font-weight-bold">Division:</label>
                                            <div class="col-lg-8">
                                                <span class="form-control-plaintext">{{ $staff->ud_name ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Card-->
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <!--begin::Card KTP-->
                                <div class="card card-custom gutter-b">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h3 class="card-label">KTP</h3>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="font-weight-bold">KTP Number:</label>
                                            <p>{{ $staff->u_ktp ?? '-' }}</p>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label class="font-weight-bold">KTP Image:</label>
                                            <div class="mt-2">
                                                <img src="{{ $staff->u_ktp_image ? Storage::disk('s3')->url($staff->u_ktp_image) : asset('photos/no_image.png') }}"
                                                    alt="KTP" class="img-fluid rounded border"
                                                    style="max-height: 200px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Card-->
                            </div>
                            <div class="col-lg-6">
                                <!--begin::Card NPWP-->
                                <div class="card card-custom gutter-b">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h3 class="card-label">NPWP</h3>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="font-weight-bold">NPWP Number:</label>
                                            <p>{{ $staff->u_npwp ?? '-' }}</p>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label class="font-weight-bold">NPWP Image:</label>
                                            <div class="mt-2">
                                                <img src="{{ $staff->u_npwp_image ? Storage::disk('s3')->url($staff->u_npwp_image) : asset('photos/no_image.png') }}"
                                                    alt="NPWP" class="img-fluid rounded border"
                                                    style="max-height: 200px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Card-->
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <!--begin::Card BPJS Kesehatan-->
                                <div class="card card-custom gutter-b">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h3 class="card-label">BPJS Kesehatan</h3>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="font-weight-bold">BPJS Kesehatan Number:</label>
                                            <p>{{ $staff->u_bpjs_kes_number ?? '-' }}</p>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label class="font-weight-bold">BPJS Kesehatan Image:</label>
                                            <div class="mt-2">
                                                <img src="{{ $staff->u_bpjs_kes_image ? Storage::disk('s3')->url($staff->u_bpjs_kes_image) : asset('photos/no_image.png') }}"
                                                    alt="BPJS Kesehatan" class="img-fluid rounded border"
                                                    style="max-height: 200px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Card-->
                            </div>
                            <div class="col-lg-6">
                                <!--begin::Card BPJS Ketenagakerjaan-->
                                <div class="card card-custom gutter-b">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h3 class="card-label">BPJS Ketenagakerjaan</h3>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="font-weight-bold">BPJS Ketenagakerjaan Number:</label>
                                            <p>{{ $staff->u_bpjs_tk_number ?? '-' }}</p>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label class="font-weight-bold">BPJS Ketenagakerjaan Image:</label>
                                            <div class="mt-2">
                                                <img src="{{ $staff->u_bpjs_tk_image ? Storage::disk('s3')->url($staff->u_bpjs_tk_image) : asset('photos/no_image.png') }}"
                                                    alt="BPJS Ketenagakerjaan" class="img-fluid rounded border"
                                                    style="max-height: 200px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Card-->
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <!--begin::Card Bank-->
                                <div class="card card-custom gutter-b">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h3 class="card-label">Bank Information</h3>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Bank Name:</label>
                                                    <p>{{ $staff->u_bank_name ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Account Number:</label>
                                                    <p>{{ $staff->u_bank_account_number ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Account Holder:</label>
                                                    <p>{{ $staff->u_bank_account_holder ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Card-->
                            </div>
                        </div>
                    </div>

                    <!--begin::Comments Section-->
                    <div class="col-lg-3">
                        <div class="card card-custom gutter-b">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label">
                                        <i class="flaticon2-file mr-2"></i>PKWT Status
                                    </h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold  mb-2">PKWT Ke</label>
                                    <select name="contract_number" id="contract_number" class="form-control ">
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}"
                                                {{ ($staff->contract_number ?? '') == $i ? 'selected' : '' }}>
                                                {{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="form-group mb-0 mt-4">
                                    <label class="font-weight-bold  mb-2">Akhir Kontrak</label>
                                    <input type="date" name="contract_end_date" id="contract_end_date"
                                        class="form-control"
                                        value="{{ $staff->u_active ? \Carbon\Carbon::parse($staff->u_active)->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="card card-custom gutter-b">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label">
                                        <i class="flaticon2-chat-1 mr-2"></i>Comments
                                    </h3>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div id="commentList" class="p-5" style="max-height: 30vh; overflow-y: auto;">
                                    @forelse ($comments ?? [] as $comment)
                                        <div class="mb-5 pb-4" style="border-bottom: 1px solid #EBEDF3;">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="symbol symbol-35 mr-3">
                                                    <span class="symbol-label font-size-h5 font-weight-bold text-primary">
                                                        {{ substr($comment->user->u_name ?? 'U', 0, 1) }}
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="font-weight-bold text-dark-75 font-size-sm">
                                                        {{ $comment->user->u_name ?? 'Unknown' }}
                                                    </div>
                                                    <div class="text-muted font-size-xs">
                                                        {{ \Carbon\Carbon::parse($comment->created_at)->format('d/m/Y H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-dark-75 font-size-sm">
                                                {!! nl2br(e($comment->comment)) !!}
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted py-10 no-comment">
                                            <i class="flaticon2-chat-1 icon-3x mb-3"></i>
                                            <p class="font-size-sm">No comments yet.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            <div class="card-footer">
                                <form id="commentForm">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <textarea name="comment" id="commentInput" class="form-control form-control-sm" rows="3" placeholder="Add a comment..."
                                            required></textarea>
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm btn-block" id="btnAddComment">
                                        <i class="flaticon2-send"></i> Submit Comment
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!--end::Comments Section-->
                </div>
            </div>
            <!--end::Container-->
        </div>
        <!--end::Entry-->
    </div>
    <!--end::Content-->
    @include('app._partials.js')
    @include('app.staff_information.staff_information_js')

    <script>
        document.getElementById('contract_number').addEventListener('change', function() {
            var contractNumber = this.value;
            var staffId = '{{ $staff->id }}';
            
            fetch('/staff/' + staffId + '/change-contract-number', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    contract_number: contractNumber
                })
            })
            .then(res => res.json())
            .then(response => {
                toastr.success('Contract Number updated successfully.');
            })
            .catch(error => {
                toastr.error('Failed to update Contract Number.');
            });
        });

        document.getElementById('contract_end_date').addEventListener('change', function() {
            var contractEndDate = this.value;
            var staffId = '{{ $staff->id }}';
            
            fetch('/staff/' + staffId + '/change-contract-end', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    contract_end_date: contractEndDate
                })
            })
            .then(res => res.json())
            .then(response => {
                toastr.success('Contract End Date updated successfully.');
            })
            .catch(error => {
                toastr.error('Failed to update Contract End Date.');
            });
        });

        document.getElementById('btnAddComment').addEventListener('click', function() {
            let comment = document.getElementById('commentInput').value.trim();
            if (!comment) return;

            fetch('{{ route('comments.store', $staff->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    comment: comment,
                    identifier: 'staff-information',
                    key_id: {{ $staff->id }}
                })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    // hapus jika ada text kosong
                    let emptyText = document.querySelector('.no-comment');
                    if (emptyText) emptyText.remove();

                    // append komentar baru
                    document.getElementById('commentList').insertAdjacentHTML('afterbegin', `
                        <div class="mb-5 pb-4" style="border-bottom: 1px solid #EBEDF3;">
                            <div class="d-flex align-items-center mb-2">
                                <div class="symbol symbol-35 mr-3">
                                    <span class="symbol-label font-size-h5 font-weight-bold text-primary">
                                        ${res.data.name.charAt(0)}
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="font-weight-bold text-dark-75 font-size-sm">
                                        ${res.data.name}
                                    </div>
                                    <div class="text-muted font-size-xs">
                                        ${res.data.datetime}
                                    </div>
                                </div>
                            </div>
                            <div class="text-dark-75 font-size-sm">
                                ${res.data.comment.replace(/\n/g, '<br>')}
                            </div>
                        </div>
                    `);

                    document.getElementById('commentInput').value = '';
                    toastr.success('Comment added successfully.');
                }
            })
            .catch(error => {
                toastr.error('Failed to add comment.');
            });
        });
    </script>
@endSection
