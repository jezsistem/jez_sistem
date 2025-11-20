@extends('app.structure')

@section('content')
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">

        <div class="card shadow-sm mt-5">
            <div class="card-header">
                <h3 class="card-title">
                    WhatsApp Connection
                </h3>
            </div>

            <div class="card-body text-center">

                <div id="wa-status-section">
                    <p class="text-muted">Checking WhatsApp status...</p>
                </div>

                <div id="wa-qr-section" style="display:none;">
                    <h5 class="mb-3">Silakan Scan QR WhatsApp</h5>
                    <img id="wa-qr-image" src="" style="width: 300px; height: 300px;">
                    <p class="mt-3 text-muted">QR akan otomatis refresh jika berubah.</p>
                </div>

                <div id="wa-connected-section" style="display:none;">
                    <h4 class="text-success">✓ WhatsApp Sudah Terhubung</h4>
                    <p class="text-muted">Device sudah aktif dan siap mengirim pesan.</p>
                </div>

            </div>
        </div>

    </div>
    <!--end::Content-->

    @include('app.whatsapp.whatsapp_modal')
    @include('app._partials.js')
    @include('app.whatsapp.whatsapp_js')

@endsection
