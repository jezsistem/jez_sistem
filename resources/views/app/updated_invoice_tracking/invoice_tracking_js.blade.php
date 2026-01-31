<script>
var invoiceTrackingTable = null;
var date = '';

var loadFile = function(event) {
    var output = document.getElementById('imagePreview');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
        URL.revokeObjectURL(output.src)
    }
};

// Load Invoice Tracking Data
function loadInvoiceTrackingData() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        url: "{{ url('invoice_tracking_datatables') }}",
        type: 'GET',
        data: {
            search: $('#invoice_tracking_search').val(),
            status: $('#status_filter').val(),
            division: $('#std_id').val(),
            st_id: $('#st_id_filter').val(),
            date: date,
            length: -1
        },
        dataType: 'json',
        success: function(response) {
            if (invoiceTrackingTable) {
                invoiceTrackingTable.destroy();
            }
            
            const $tbody = $('#InvoiceTrackingtb tbody');
            $tbody.empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    let html = '<tr class="hover:bg-gray-50" data-pt_id="' + row.pt_id + '">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.DT_RowIndex || '') + '</td>';
                    // Invoice column - may contain HTML buttons
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.pos_invoice || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.u_name || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.cust_name || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.dv_name || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.pos_created || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.total_item || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.pos_real_price || '') + '</td>';
                    // Resi column - may contain HTML buttons
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.pos_shipping_number || '') + '</td>';
                    // Info column - may contain HTML buttons
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.pos_shipment || '') + '</td>';
                    // Status column - may contain HTML buttons
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.pos_status || '') + '</td>';
                    html += '</tr>';
                    $tbody.append(html);
                });
            } else {
                $tbody.append('<tr><td colspan="11" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("InvoiceTrackingtb") && typeof simpleDatatables !== 'undefined') {
                invoiceTrackingTable = new simpleDatatables.DataTable("#InvoiceTrackingtb", {
                    searchable: true,
                    sortable: true,
                    perPage: 25,
                    perPageSelect: [10, 25, 50, 100],
                    classes: {
                        wrapper: "datatable-wrapper",
                        top: "datatable-top",
                        bottom: "datatable-bottom",
                        search: "datatable-search",
                        selector: "datatable-selector",
                        table: "datatable-table",
                        sorter: "datatable-sorter"
                    },
                    labels: {
                        placeholder: "Cari...",
                        perPage: "",
                        noRows: "Tidak ada data",
                        info: "Menampilkan {start} sampai {end} dari {rows} data",
                        noResults: "Tidak ada hasil pencarian"
                    }
                });
            }
        },
        error: function(xhr) {
            console.error('Error loading invoice tracking data:', xhr);
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data invoice tracking.', 'error');
        }
    });
}

function number_format(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Load initial data
    loadInvoiceTrackingData();
    
    // Filter handlers
    $('#status_filter').on('change', function() {
        loadInvoiceTrackingData();
    });
    
    $('#std_id').on('change', function() {
        loadInvoiceTrackingData();
    });
    
    $('#st_id_filter').on('change', function() {
        loadInvoiceTrackingData();
    });
    
    // Search handler - minimum 4 characters
    $('#invoice_tracking_search').on('keyup', function() {
        var query = $(this).val();
        if (jQuery.trim(query).length > 3 || jQuery.trim(query).length < 1) {
            loadInvoiceTrackingData();
        }
    });
    
    // Image preview click handler
    $('#imagePreview').on('click', function() {
        $(this).attr('src', '');
        $("#image").val('');
    });
    
    // Shipping number button
    $(document).on('click', '#shipping_number_btn', function(e) {
        e.stopPropagation();
        var pt_id = $(this).attr('data-pt_id');
        var cust_id = $(this).attr('data-cust_id');
        var image = $(this).attr('data-img');
        $('#imagePreview').attr('src', '');
        $('#_id').val(pt_id);
        $('#_cust_id').val(cust_id);
        document.getElementById('ShippingNumberModal').classList.remove('hidden');
        if (image != '') {
            $('#imagePreview').attr('src', "{{ asset('upload/shipping_img/600x600') }}/" + image);
        }
    });
    
    // DP payment button
    $(document).on('click', '#dp_payment_btn', function(e) {
        e.stopPropagation();
        var pt_id = $(this).attr('data-pt_id');
        $.ajax({
            type: 'GET',
            url: "{{ url('invoice_dp_repayment_details') }}/" + pt_id,
            dataType: 'json',
            success: function(data) {
                $('#_pt_id').val(pt_id);
                $('#total_payment_real_price').text('Rp ' + number_format(data.data.total_sales));
                $('#first_payment_amount').text('Rp ' + number_format(data.data.payment_1));
                $('#difference_payment').text('Rp ' + number_format(data.data.outstanding));
                $('#dp_date').text(data.data.paydate);
                $('#dp_method').text(data.data.payment_method_1);
                $('#dp_notes').text(data.data.notes || '-');
                
                // Populate payment methods dropdown
                $('#payment_method').empty().append('<option value="">- Pilih Metode Pembayaran -</option>');
                $.each(data.data.payment_methods, function(key, value) {
                    $('#payment_method').append('<option value="' + key + '">' + value + '</option>');
                });
                
                document.getElementById('DPPaymentModal').classList.remove('hidden');
            },
            error: function(data) {
                Swal.fire('Error', 'Failed to load payment details', 'error');
            }
        });
    });
    
    // Payment DP input validation
    $('#payment_dp').on('input', function() {
        var paymentDpValue = parseFloat($(this).val());
        var differencePaymentText = $('#difference_payment').text().replace('Rp ', '').replace(/\./g, '');
        var differencePaymentValue = parseFloat(differencePaymentText);
        
        if (paymentDpValue > differencePaymentValue || paymentDpValue < 0) {
            $(this).val(differencePaymentValue);
            Swal.fire('Error', 'Pembayaran DP tidak boleh lebih dari sisa pembayaran atau kurang dari 0', 'error');
        }
    });
    
    // Waybill tracking button
    $(document).on('click', '#waybill_tracking_btn', function(e) {
        e.stopPropagation();
        var waybill_number = $(this).text();
        var pt_id = $(this).attr('data-id');
        $('#waybill_tracking').html('');
        document.getElementById('WaybillTrackingModal').classList.remove('hidden');
        $.ajax({
            type: 'POST',
            url: "{{ url('waybill_tracking') }}",
            data: {
                _waybill_number: waybill_number,
                _id: pt_id
            },
            dataType: 'html',
            success: function(data) {
                loadInvoiceTrackingData();
                $('#waybill_tracking').html(data);
            },
            error: function(data) {
                Swal.fire('Error', data, 'error');
            }
        });
    });
    
    // Shipping number form submit
    $('#f_shipping_number').on('submit', function(e) {
        e.preventDefault();
        $("#save_shipping_number_btn").html('Proses ..');
        $("#save_shipping_number_btn").prop("disabled", true);
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ url('shipping_number_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#save_shipping_number_btn").html('Simpan');
                $("#save_shipping_number_btn").prop("disabled", false);
                if (data.status == '200') {
                    $('#imagePreview').trigger('click');
                    document.getElementById('ShippingNumberModal').classList.add('hidden');
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    $('#f_shipping_number')[0].reset();
                    loadInvoiceTrackingData();
                } else if (data.status == '400') {
                    Swal.fire('Gagal', 'Data tidak tersimpan', 'warning');
                }
            },
            error: function(data) {
                $("#save_shipping_number_btn").html('Simpan');
                $("#save_shipping_number_btn").prop("disabled", false);
                Swal.fire('Error', data, 'error');
            }
        });
    });
    
    // DP payment form submit
    $('#f_payment_dp').on('submit', function(e) {
        e.preventDefault();
        $("#save_payment_dp_btn").html('Proses ..');
        $("#save_payment_dp_btn").prop("disabled", true);
        
        // Validate payment amount
        var paymentDpValue = parseFloat($('#payment_dp').val());
        var differencePaymentText = $('#difference_payment').text().replace('Rp ', '').replace(/\./g, '');
        var differencePaymentValue = parseFloat(differencePaymentText);
        
        if (paymentDpValue !== differencePaymentValue) {
            $("#save_payment_dp_btn").html('Simpan');
            $("#save_payment_dp_btn").prop("disabled", false);
            Swal.fire('Error', 'Pelunasan DP harus sama dengan sisa pembayaran', 'error');
            return;
        }
        
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ url('invoice_dp_repayment') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#save_payment_dp_btn").html('Simpan');
                $("#save_payment_dp_btn").prop("disabled", false);
                if (data.status == '200') {
                    document.getElementById('DPPaymentModal').classList.add('hidden');
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    $('#f_payment_dp')[0].reset();
                    loadInvoiceTrackingData();
                } else if (data.status == '400') {
                    Swal.fire('Gagal', 'Data tidak tersimpan', 'warning');
                }
            },
            error: function(data) {
                $("#save_payment_dp_btn").html('Simpan');
                $("#save_payment_dp_btn").prop("disabled", false);
                Swal.fire('Error', data, 'error');
            }
        });
    });
    
    // Check invoice button
    $('#check_invoice_btn').on('click', function() {
        document.getElementById('CheckInvoiceModal').classList.remove('hidden');
        $('#complaint_invoice').val('');
        $('#invoice_result').html('');
    });
    
    // Search complaint invoice
    $('#search_complaint_btn').on('click', function() {
        var invoice = $('#complaint_invoice').val();
        $.ajax({
            type: 'POST',
            url: "{{ url('search_invoice') }}",
            data: { invoice: invoice },
            dataType: 'json',
            success: function(data) {
                if (data.status == '200') {
                    Swal.fire('Ditemukan', data.invoice, 'success');
                    $('#invoice_result').html('Ditemukan, invoice barunya adalah : ' + data.invoice);
                } else if (data.status == '400') {
                    Swal.fire('Tidak Ada', 'Data invoice yang berkaitan', 'warning');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to search invoice', 'error');
            }
        });
    });
});
</script>
