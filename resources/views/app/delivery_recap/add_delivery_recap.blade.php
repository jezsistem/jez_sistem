<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
@include('app._partials.head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Style for the signature pad */
    .signature-pad-container {
        border: 2px solid #ddd;
        border-radius: 5px;
        width: 100%;
        height: 200px;
        background-color: #fff;
        position: relative;
    }
    .signature-pad-container canvas {
        width: 100%;
        height: 100%;
    }
</style>
<!--end::Head-->

<body id="device_background_panel">
<div class="d-flex flex-column-fluid flex-center" id="device_background">
    <form id="f_rating" enctype="multipart/form-data">
        @csrf
        <!-- Courier Name -->
        <h3 class="font-weight-bolder text-white font-size-h1-lg" style="font-size:22px;">Add New Recap</h3><hr>
        <div class="form-group">
            <label class="font-size-h6 font-weight-bolder text-white float-left">Courier Name</label>
            <input type="text" class="form-control form-control-solid h-auto py-5 px-5 rounded-lg mb-2" name="courier_name" id="courier_name" placeholder="Enter Courier Name">
        </div>

        <!-- Expeditions -->
        <div class="form-group">
            <label class="font-size-h6 font-weight-bolder text-white float-left">Expeditions</label>
            <select class="form-control form-control-solid h-auto py-5 px-5 rounded-lg mb-2" name="expeditions" id="expeditions">
                <option value="">Select an Expedition</option>
                <option value="jne">JNE</option>
                <option value="jnt">J&T</option>
                <option value="sicepat">SiCepat</option>
                <option value="pos">POS Indonesia</option>
                <option value="anteraja">Anteraja</option>
            </select>
        </div>

        <!-- Import File -->
        <div class="form-group">
            <label class="font-size-h6 font-weight-bolder text-white float-left">Import File</label>
            <input type="file" class="form-control form-control-solid h-auto py-5 px-5 rounded-lg mb-2" name="import_file" id="import_file">
        </div>

        <!-- Signature Pad -->
        <div class="form-group">
            <label style="color: white; font-size: 15px; font-weight: bold; display: block; margin-bottom: 5px;">
                Signature
            </label>
            <div class="signature-pad-container">
                <canvas id="signature-pad"></canvas>
            </div>
            <button type="button" id="clear-signature" class="btn btn-danger mt-2">Clear Signature</button>
            <input type="hidden" name="signature" id="signature">
        </div>

        <button type="submit" id="kt_login_signin_submit" class="btn btn-warning font-weight-bolder font-size-h3 px-8 py-4 my-3 mr-2 col-12" style="background-color:#F2C94C;">Kirim</button>
    </form>
</div>

@include('app.rating_by_customer.rating_by_customer_modal')
@include('app._partials.js')

<!-- External JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    $(document).ready(function () {
        // Initialize Select2
        $('#expeditions').select2({
            placeholder: "Select an Expedition",
            allowClear: true,
            width: '100%'
        });

        // Initialize Signature Pad
        const canvas = document.getElementById('signature-pad');
        const signaturePad = new SignaturePad(canvas);

        // Resize the canvas to fit its container
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear(); // Reset the canvas after resizing
        }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        // Clear Signature
        $('#clear-signature').on('click', function () {
            signaturePad.clear();
        });

        // Save Signature as Base64
        $('#f_rating').on('submit', function (e) {
            if (!signaturePad.isEmpty()) {
                $('#signature').val(signaturePad.toDataURL('image/png'));
            } else {
                alert('Please provide your signature.');
                e.preventDefault(); // Prevent form submission if no signature
            }
        });
    });
</script>
@include('app.delivery_recap.add_delivery_recap_js')
</body>
</html>
