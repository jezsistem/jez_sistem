<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
@include('app._partials.head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<style>

    .swal2-center-icon .swal2-icon {
        margin: 0 auto 1rem auto !important;
    }

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
<!--end::Head hehehhehehehe-->

<body id="device_background_panel">
<div class="d-flex flex-column-fluid flex-center" id="device_background">
    <form id="f_manifest" enctype="multipart/form-data">
        @csrf
        <!-- Courier Name -->
        <h3 class="font-weight-bolder text-white font-size-h1-lg text-center" style="font-size:22px; margin-top: 20px;">Add New Recap Manifest</h3><hr>

        <div class="form-group">
            <label class="font-size-h6 font-weight-bolder text-white float-left">Order Type</label>'
            <select name="order_type" id="order_type" class="form-control">
                <option value="Reguler">Reguler</option>
                <option value="Instan">Instan</option>
            </select>
        </div>

        <div class="form-group">
            <label class="font-size-h6 font-weight-bolder text-white float-left">Courier Name</label>
            <input type="text" class="form-control form-control-solid h-auto py-5 px-5 rounded-lg mb-2" name="courier_name" id="courier_name" placeholder="Enter Courier Name">
        </div>

        <div class="form-group">
            <label class="font-size-h6 font-weight-bolder text-white float-left">Courier Phone Number</label>
            <input type="text" class="form-control form-control-solid h-auto py-5 px-5 rounded-lg mb-2" name="courier_phone" id="courier_phone" placeholder="Enter courier phone number">
        </div>

        <!-- Expeditions -->
        <div class="form-group">
            <label class="font-size-h6 font-weight-bolder text-white float-left">Expeditions</label>
            <select class="form-control form-control-solid h-auto py-5 px-5 rounded-lg mb-2"
                    name="expeditions" id="expeditions">
                <option value="">Select an Expedition</option>
                @foreach($data['expeditions'] as $expedition)
                    <option value="{{ $expedition->id }}">{{ $expedition->cr_name }}</option>
                @endforeach
            </select>
        </div>


        <!-- Import File -->
        <div class="form-group">
            <label class="font-size-h6 font-weight-bolder text-white float-left">Import File</label>
            <input type="file" class="form-control form-control-solid h-auto py-5 px-5 rounded-lg mb-2" name="import_file" id="import_file">
        </div>

        <!-- Input Resi (muncul hanya jika Instan) -->
        <div class="form-group" id="resi-field" style="display:none;">
            <label class="font-size-h6 font-weight-bolder text-white float-left">Nomor Resi / Nomor Order</label>
            <input type="text" class="form-control form-control-solid h-auto py-5 px-5 rounded-lg mb-2"
                   name="resi_number" id="resi_number" placeholder="Masukkan nomor resi">
        </div>

        <!-- Signature Pad: PIC -->
        <div class="form-group">
            <label style="color: white; font-size: 15px; font-weight: bold; display: block; margin-bottom: 5px;">
                Signature (PIC Penyerah Barang)
            </label>
            <div class="signature-pad-container">
                <canvas id="signature-pad-pic"></canvas>
            </div>
            <button type="button" id="clear-signature-pic" class="btn btn-danger mt-2">Clear Signature</button>
            <button type="button" id="download-signature-pic" class="btn btn-success mt-2 ml-2">Download Signature</button>
            <input type="hidden" name="signature_pic" id="signature_pic">
            <input type="hidden" name="signature_pic_name" id="signature_pic_name">
        </div>

        <!-- Signature Pad: Kurir -->
        <div class="form-group">
            <label style="color: white; font-size: 15px; font-weight: bold; display: block; margin-bottom: 5px;">
                Signature (Kurir Penerima)
            </label>
            <div class="signature-pad-container">
                <canvas id="signature-pad-kurir"></canvas>
            </div>
            <button type="button" id="clear-signature-kurir" class="btn btn-danger mt-2">Clear Signature</button>
            <button type="button" id="download-signature-kurir" class="btn btn-success mt-2 ml-2">Download Signature</button>
            <input type="hidden" name="signature_kurir" id="signature_kurir">
            <input type="hidden" name="signature_kurir_name" id="signature_kurir_name">
        </div>

        <button type="submit" id="kt_login_signin_submit" class="btn btn-warning font-weight-bolder font-size-h3 px-8 py-4 my-3 mr-2 col-12" style="background-color:#F2C94C;">Kirim</button>
    </form>
</div>

@include('app.rating_by_customer.rating_by_customer_modal')
@include('app._partials.js')

<!-- External JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    $(document).ready(function () {
        function toggleFields() {
            var orderType = $('#order_type').val();

            if (orderType === 'Reguler') {
                // tampilkan input file
                $('#import_file').closest('.form-group').show();
                // sembunyikan input resi
                $('#resi-field').hide();
            } else if (orderType === 'Instan') {
                // tampilkan input resi
                $('#resi-field').show();
                // sembunyikan input file
                $('#import_file').closest('.form-group').hide();
            }
        }
        toggleFields();

        $('#order_type').on('change', toggleFields);

        const canvasPic = document.getElementById('signature-pad-pic');
        const canvasKurir = document.getElementById('signature-pad-kurir');
        const signaturePic = new SignaturePad(canvasPic);
        const signatureKurir = new SignaturePad(canvasKurir);

        function resizeCanvas(canvas, signaturePad) {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
        }

        window.addEventListener('resize', () => {
            resizeCanvas(canvasPic, signaturePic);
            resizeCanvas(canvasKurir, signatureKurir);
        });

        resizeCanvas(canvasPic, signaturePic);
        resizeCanvas(canvasKurir, signatureKurir);

        $('#clear-signature-pic').on('click', function () {
            signaturePic.clear();
        });
        $('#clear-signature-kurir').on('click', function () {
            signatureKurir.clear();
        });

        $('#download-signature-pic').on('click', function () {
            if (signaturePic.isEmpty()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'PIC Signature Missing!',
                    text: 'Please draw the PIC signature first.',
                });
                return;
            }
            downloadSignature(signaturePic, 'pic_signature', '#signature_pic_name');
        });

        $('#download-signature-kurir').on('click', function () {
            if (signatureKurir.isEmpty()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Kurir Signature Missing!',
                    text: 'Please draw the Kurir signature first.',
                });
                return;
            }
            downloadSignature(signatureKurir, 'kurir_signature', '#signature_kurir_name');
        });

        function downloadSignature(signaturePad, namePrefix, inputTarget) {
            const courierName = $('#courier_name').val().trim() || 'signature';
            const date = new Date().toISOString().slice(0, 19).replace(/[:T]/g, '-');
            const fileName = `${courierName}_${namePrefix}_${date}.png`;

            // Simpan nama file ke input hidden
            $(inputTarget).val(fileName);

            const dataURL = signaturePad.toDataURL('image/png');
            const link = document.createElement('a');
            link.href = dataURL;
            link.download = fileName;
            link.click();
        }

        $('#f_manifest').on('submit', function (e) {
            e.preventDefault();

            if (signaturePic.isEmpty() || signatureKurir.isEmpty()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Signatures Required',
                    text: 'Please provide both signatures (PIC & Kurir).',
                    customClass: {
                        popup: 'swal2-center-icon'
                    }
                });
                return;
            }

            const courierName = $('#courier_name').val().trim() || 'signature';
            const date = new Date().toISOString().slice(0, 19).replace(/[:T]/g, '-');
            const picFileName = `${courierName}_pic_signature_${date}.png`;
            const kurirFileName = `${courierName}_kurir_signature_${date}.png`;

            let formData = new FormData(this);
            formData.append('signature_pic', signaturePic.toDataURL('image/png'));
            formData.append('signature_kurir', signatureKurir.toDataURL('image/png'));
            formData.append('signature_pic_name', picFileName);
            formData.append('signature_kurir_name', kurirFileName);

            $('#kt_login_signin_submit').prop('disabled', true).text('Submitting...');

            $.ajax({
                url: "{{ route('delivery-recaps.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function (response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK',
                            customClass: {
                                popup: 'swal2-center-icon'
                            }
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Import Dibatalkan!',
                            html: response.message,
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'Tutup',
                            iconHtml: '', // penting agar icon tetap di tengah
                            customClass: {
                                popup: 'swal2-center-icon'
                            }
                        });
                    }

                    $('#kt_login_signin_submit').prop('disabled', false).text('Kirim');
                    $('#f_manifest')[0].reset();
                    signaturePic.clear();
                    signatureKurir.clear();

                    setTimeout(() => {
                        window.location.href = "{{ route('helper_online') }}";
                    }, 1500);
                },
                error: function (xhr) {
                    $('#kt_login_signin_submit').prop('disabled', false).text('Kirim');
                    let response = xhr.responseJSON;

                    if (response && response.success === false && response.list) {
                        Swal.fire({
                            icon: 'error',
                            title: response.title || 'Import Dibatalkan',
                            html: `
                        ${response.message}
                        <br>
                        <button id="exportCsvBtn" class="swal2-confirm swal2-styled"
                            style="background:#28a745;margin-top:10px;">
                            Export CSV
                        </button>
                    `,
                            iconHtml: '',
                            customClass: {
                                popup: 'swal2-center-icon'
                            },
                            didOpen: () => {
                                document.getElementById('exportCsvBtn').addEventListener('click', function () {
                                    exportCSV(response.list);
                                });
                            }
                        });
                        return;
                    }

                    if (response && response.errors) {
                        let messages = Object.values(response.errors).flat().join('<br>');
                        toastr.error(messages, 'Validasi Gagal!', { timeOut: 4000, progressBar: true });
                    } else {
                        toastr.error(xhr.responseText, 'Terjadi Kesalahan!', { timeOut: 4000, progressBar: true });
                    }
                }
            });
        });

        function exportCSV(data) {
            let csv = "No,No Resi,Status Saat Ini\n";
            data.forEach((item, i) => {
                csv += `${i + 1},${item.resi},${item.status}\n`;
            });

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'resi_belum_done_online.csv';
            link.click();
        }
    });
</script>

@include('app.delivery_recap.add_delivery_recap_js')
</body>
</html>
