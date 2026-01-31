<script>
var voucherTable = null;
var voucherDataCache = [];

// Store data when loading
function cacheVoucherData(data) {
    voucherDataCache = data;
}

function loadVoucherData() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        url: "{{ url('voucher_datatables') }}",
        type: 'GET',
        data: {
            search: $('#voucher_search').val(),
            length: -1
        },
        dataType: 'json',
        success: function(response) {
            if (voucherTable) {
                voucherTable.destroy();
            }
            
            const $tbody = $('#Vouchertb tbody');
            $tbody.empty();
            
            if (response.data && response.data.length > 0) {
                // Cache the raw data for edit functionality
                cacheVoucherData(response.data);
                
                response.data.forEach(function(row) {
                    let html = '<tr class="hover:bg-gray-50 cursor-pointer" data-id="' + row.id + '">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.DT_RowIndex || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-900">' + (row.vc_code || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.vc_discount || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.vc_type_show || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.vc_min_order || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.vc_reuse_show || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.vc_status_show || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.vc_cashback_show || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.vc_platform || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.vc_due_date_show || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.item || '-') + '</td>';
                    html += '</tr>';
                    $tbody.append(html);
                });
            } else {
                $tbody.append('<tr><td colspan="11" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("Vouchertb") && typeof simpleDatatables !== 'undefined') {
                voucherTable = new simpleDatatables.DataTable("#Vouchertb", {
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
            console.error('Error loading voucher data:', xhr);
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data voucher.', 'error');
        }
    });
}

function exportVoucherToExcel() {
    // Get all data from table
    var table = document.getElementById("Vouchertb");
    var csv = [];
    var rows = table.querySelectorAll("tr");
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");
        for (var j = 0; j < cols.length; j++) {
            row.push(cols[j].innerText);
        }
        csv.push(row.join(","));
    }
    
    // Download CSV file
    var csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
    var downloadLink = document.createElement("a");
    downloadLink.download = "Data_Voucher_" + new Date().toISOString().split('T')[0] + ".csv";
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Load initial data
    loadVoucherData();
    
    // Search handler
    $('#voucher_search').on('keyup', function() {
        loadVoucherData();
    });
    
    // Export Excel
    $('#voucher_excel_btn').on('click', function() {
        exportVoucherToExcel();
    });
    
    // Row click handler (edit)
    $(document).on('click', '#Vouchertb tbody tr', function() {
        var id = $(this).attr('data-id');
        if (!id) return;
        
        var row = voucherDataCache.find(r => r.id == id);
        if (row) {
            $('#random_input').addClass('hidden');
            $('#vc_code_input').removeClass('hidden');
            $('#vc_code').val(row.vc_code);
            $('#vc_discount').val(row.vc_discount);
            $('#vc_min_order').val(row.vc_min_order);
            $('#vc_type').val(row.vc_type);
            $('#vc_reuse').val(row.vc_reuse);
            $('#vc_status').val(row.vc_status);
            $('#vc_cashback').val(row.vc_cashback);
            $('#vc_platform').val(row.vc_platform);
            $('#vc_due_date').val(row.vc_due_date);
            $('#_id').val(row.id);
            $('#_mode').val('edit');
            @if(isset($data['user']) && $data['user']->delete_access == '1')
                $('#delete_voucher_btn').removeClass('hidden');
            @else
                $('#delete_voucher_btn').addClass('hidden');
            @endif
            document.getElementById('VoucherModal').classList.remove('hidden');
        }
    });
    
    // Add voucher button
    $('#add_voucher_btn').on('click', function() {
        $('#random_input').removeClass('hidden');
        $('#vc_code_input').removeClass('hidden');
        $('#_id').val('');
        $('#_mode').val('add');
        $('#f_voucher')[0].reset();
        $('#delete_voucher_btn').addClass('hidden');
        document.getElementById('VoucherModal').classList.remove('hidden');
    });
    
    // Check exists voucher
    $('#vc_code').on('change', function() {
        var vc_code = $(this).val();
        if (!vc_code) return;
        
        $.ajax({
            type: "POST",
            data: { vc_code: vc_code },
            dataType: 'json',
            url: "{{ url('check_exists_voucher') }}",
            success: function(r) {
                if (r.status == '200') {
                    Swal.fire('Kode Voucher', 'Kode voucher sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                    $('#vc_code').val('');
                    return false;
                }
            }
        });
    });
    
    // Form submit
    $('#f_voucher').on('submit', function(e) {
        e.preventDefault();
        var code = $('#vc_code').val();
        if (parseInt(code.length) < 6) {
            Swal.fire('Min 6 Karakter', 'Terdiri dari angka dan huruf', 'warning');
            return false;
        }
        
        $("#save_voucher_btn").html('Proses ..');
        $("#save_voucher_btn").prop("disabled", true);
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('voucher_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#save_voucher_btn").html('Simpan');
                $("#save_voucher_btn").prop("disabled", false);
                if (data.status == '200') {
                    document.getElementById('VoucherModal').classList.add('hidden');
                    toastr.success("Data berhasil disimpan", "Berhasil");
                    loadVoucherData();
                } else if (data.status == '400') {
                    document.getElementById('VoucherModal').classList.add('hidden');
                    toastr.warning("Data tidak tersimpan", "Gagal");
                }
            },
            error: function() {
                $("#save_voucher_btn").html('Simpan');
                $("#save_voucher_btn").prop("disabled", false);
                toastr.error("Terjadi kesalahan saat memproses permintaan", "Error");
            }
        });
    });
    
    // Delete voucher
    $('#delete_voucher_btn').on('click', function() {
        Swal.fire({
            title: "Hapus..?",
            text: "Yakin hapus data ini ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batalkan'
        }).then(function(isConfirm) {
            if (isConfirm.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: { _id: $('#_id').val() },
                    dataType: 'json',
                    url: "{{ url('voucher_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            toastr.success("Data berhasil dihapus", "Berhasil");
                            document.getElementById('VoucherModal').classList.add('hidden');
                            loadVoucherData();
                        } else {
                            toastr.error("Gagal hapus data", "Gagal");
                        }
                    },
                    error: function() {
                        toastr.error("Terjadi kesalahan saat memproses permintaan", "Error");
                    }
                });
            }
        });
    });
    
    // Random checkbox
    $(document).on('change', '#is_random', function() {
        $('#vc_qty').val('');
        if ($(this).is(':checked')) {
            $('#vc_qty').removeClass('hidden');
            $('#vc_code').prop('required', false);
            $('#vc_qty').prop('required', true);
            $('#vc_code_input').addClass('hidden');
        } else {
            $('#vc_qty').addClass('hidden');
            $('#vc_code').prop('required', true);
            $('#vc_qty').prop('required', false);
            $('#vc_code_input').removeClass('hidden');
        }
    });
});
</script>
