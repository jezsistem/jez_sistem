<script>
var crossOrderTable = null;
var confirmationTable = null;
var detailTable = null;
var historyTable = null;

// Load Cross Order Data
function loadCrossOrderData() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        url: "{{ url('cross_order_datatables_simple') }}",
        type: 'GET',
        data: {
            search: $('#invoice_tracking_search').val(),
            status: $('#status_filter').val(),
            st_id: $('#st_id_filter').val(),
            division: $('#std_id').val()
        },
        dataType: 'json',
        success: function(response) {
            if (crossOrderTable) {
                crossOrderTable.destroy();
            }
            
            const $tbody = $('#CrossOrdertb tbody');
            $tbody.empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    let html = '<tr class="hover:bg-gray-50 cursor-pointer" data-pt_id="' + row.pt_id + '">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.DT_RowIndex || '') + '</td>';
                    // Invoice column - may contain HTML buttons
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.pos_invoice || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.u_name || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.cust_name || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.st_name_end || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.u_name_end || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.pos_created || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.total_item || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.total_price || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.total_item_reject || '') + '</td>';
                    // Status column - may contain HTML buttons
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.pos_status || '') + '</td>';
                    html += '</tr>';
                    $tbody.append(html);
                });
            } else {
                $tbody.append('<tr><td colspan="11" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("CrossOrdertb") && typeof simpleDatatables !== 'undefined') {
                crossOrderTable = new simpleDatatables.DataTable("#CrossOrdertb", {
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
            console.error('Error loading cross order data:', xhr);
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data cross order.', 'error');
        }
    });
}

// Load Confirmation Data
function loadConfirmationData() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        url: "{{ url('confirmation_datatables') }}",
        type: 'GET',
        data: {
            pt_id: $('#pt_id').val(),
            length: -1
        },
        dataType: 'json',
        success: function(response) {
            if (confirmationTable) {
                confirmationTable.destroy();
            }
            
            const $tbody = $('#Confirmationtb tbody');
            $tbody.empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    let html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.DT_RowIndex || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-900">' + (row.article || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.pos_td_qty || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.pl_code || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.ready || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.note || '') + '</td>';
                    html += '</tr>';
                    $tbody.append(html);
                });
            } else {
                $tbody.append('<tr><td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("Confirmationtb") && typeof simpleDatatables !== 'undefined') {
                confirmationTable = new simpleDatatables.DataTable("#Confirmationtb", {
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
            console.error('Error loading confirmation data:', xhr);
        }
    });
}

// Load Detail Data
function loadDetailData() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        url: "{{ url('detail_datatables') }}",
        type: 'GET',
        data: {
            pt_id: $('#pt_id').val(),
            length: -1
        },
        dataType: 'json',
        success: function(response) {
            if (detailTable) {
                detailTable.destroy();
            }
            
            const $tbody = $('#Detailtb tbody');
            $tbody.empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    let html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.DT_RowIndex || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-900">' + (row.article || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.pos_td_qty || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.pl_code || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.ready || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.note || '') + '</td>';
                    html += '</tr>';
                    $tbody.append(html);
                });
            } else {
                $tbody.append('<tr><td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("Detailtb") && typeof simpleDatatables !== 'undefined') {
                detailTable = new simpleDatatables.DataTable("#Detailtb", {
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
            console.error('Error loading detail data:', xhr);
        }
    });
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Load initial data
    loadCrossOrderData();
    
    // Filter handlers
    $('#status_filter').on('change', function() {
        loadCrossOrderData();
    });
    
    $('#std_id').on('change', function() {
        loadCrossOrderData();
    });
    
    $('#st_id_filter').on('change', function() {
        loadCrossOrderData();
    });
    
    // Search handler
    $('#invoice_tracking_search').on('keyup', function() {
        loadCrossOrderData();
    });
    
    // Row click handler - open detail modal
    $(document).on('click', '#CrossOrdertb tbody tr', function() {
        var pt_id = $(this).attr('data-pt_id');
        if (!pt_id) return;
        $('#pt_id').val(pt_id);
        document.getElementById('DetailModal').classList.remove('hidden');
        setTimeout(function() {
            loadDetailData();
        }, 100);
    });
    
    // Confirmation button
    $(document).on('click', '#confirmation_btn', function(e) {
        e.stopPropagation();
        var pt_id = $(this).attr('data-pt_id');
        $('#pt_id').val(pt_id);
        document.getElementById('ConfirmationModal').classList.remove('hidden');
        setTimeout(function() {
            loadConfirmationData();
        }, 100);
    });
    
    // Detail button
    $(document).on('click', '#detail_btn', function(e) {
        e.stopPropagation();
        var pt_id = $(this).attr('data-pt_id');
        $('#pt_id').val(pt_id);
        document.getElementById('DetailModal').classList.remove('hidden');
        setTimeout(function() {
            loadDetailData();
        }, 100);
    });
    
    // Shipping number button
    $(document).on('click', '#shipping_number_btn', function(e) {
        e.stopPropagation();
        var pt_id = $(this).attr('data-pt_id');
        var cust_id = $(this).attr('data-cust_id');
        $('#_id').val(pt_id);
        $('#_cust_id').val(cust_id);
        
        // Fetch courier data
        $.ajax({
            url: "{{ url('get_resi_detail') }}/" + pt_id,
            type: 'GET',
            success: function(response) {
                if (response.status === '200') {
                    $('#courier').empty().append('<option value="">- Pilih -</option>');
                    $.each(response.couriers, function(index, courier) {
                        $('#courier').append('<option value="' + courier.id + '"' + 
                            (courier.id == response.cr_id ? ' selected' : '') + '>' + courier.cr_name + '</option>');
                    });
                    $('#pos_shipping_number').val(response.shipping_number);
                } else {
                    $('#courier').empty().append('<option value="">- Pilih -</option>');
                    $.each(response.couriers, function(index, courier) {
                        $('#courier').append('<option value="' + courier.id + '">' + courier.cr_name + '</option>');
                    });
                    $('#pos_shipping_number').val('');
                    Swal.fire('Error', 'No shipping details found.', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to fetch shipping details.', 'error');
            }
        });
        
        document.getElementById('ShippingNumberModal').classList.remove('hidden');
    });
    
    // Ready check handler
    $(document).on('click', '#ready_check', function(e) {
        e.stopPropagation();
        var ptd_id = $(this).attr('data-ptd_id');
        $.ajax({
            type: "POST",
            data: { _ptd_id: ptd_id },
            dataType: 'json',
            url: "{{ url('sv_cross_status') }}",
            success: function(r) {
                if (r.status == '200') {
                    Swal.fire('Berhasil', 'Berhasil mengubah status', 'success');
                    loadConfirmationData();
                } else {
                    Swal.fire('Gagal', 'Gagal', 'error');
                }
            }
        });
    });
    
    // Note change handler
    $(document).on('change', '#note', function(e) {
        e.stopPropagation();
        var ptd_id = $(this).attr('data-ptd_id');
        var note = $(this).val();
        $.ajax({
            type: "POST",
            data: { _ptd_id: ptd_id, _note: note },
            dataType: 'json',
            url: "{{ url('sv_cross_note') }}",
            success: function(r) {
                if (r.status == '200') {
                    Swal.fire('Berhasil', 'Berhasil mengubah catatan', 'success');
                    loadConfirmationData();
                } else {
                    Swal.fire('Gagal', 'Gagal', 'error');
                }
            }
        });
    });
    
    // Save confirmation button
    $(document).on('click', '#save_confirmation_btn', function() {
        var pt_id = $('#pt_id').val();
        Swal.fire({
            title: "Konfirmasi..?",
            text: "Yakin data sudah benar ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yakin',
            cancelButtonText: 'Batal'
        }).then(function(isConfirm) {
            if (isConfirm.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: { _pt_id: pt_id },
                    dataType: 'json',
                    url: "{{ url('sv_cross_order') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            document.getElementById('ConfirmationModal').classList.add('hidden');
                            loadCrossOrderData();
                            Swal.fire('Berhasil', 'Invoice berhasil dikonfirmasi', 'success');
                        } else {
                            Swal.fire('Gagal', 'Gagal konfirmasi', 'error');
                        }
                    }
                });
            }
        });
    });
    
    // Print button
    $(document).on('click', '#print_btn', function(e) {
        e.stopPropagation();
        var pt_id = $(this).attr('data-pt_id');
        $.ajax({
            type: "POST",
            data: { _pt_id: pt_id },
            dataType: 'json',
            url: "{{ url('print_cross_invoice') }}",
            success: function(r) {
                if (r.status == '200') {
                    var win = window.open('{{ url('/') }}/print_invoice/' + r.invoice, '_blank');
                    if (win) {
                        win.focus();
                    } else {
                        Swal.fire('Error', 'Please allow popups for this website', 'error');
                    }
                } else {
                    Swal.fire('Belum Diambil', 'Artikel Belum Diambil, silahkan info kepada gudang untuk cek aplikasi dan ambil sesuai invoice', 'warning');
                }
            }
        });
    });
    
    // Print resi button
    $(document).on('click', '#print_resi_btn', function(e) {
        e.stopPropagation();
        var pt_id = $(this).attr('data-pt_id');
        $.ajax({
            type: "POST",
            data: { _pt_id: pt_id },
            dataType: 'json',
            url: "{{ url('print_resi') }}",
            success: function(r) {
                if (r.status == '200') {
                    var win = window.open('{{ url('/') }}/upload/resi/' + r.resi_id, '_blank');
                    if (win) {
                        win.focus();
                        win.print();
                    } else {
                        Swal.fire('Error', 'Please allow popups for this website', 'error');
                    }
                } else {
                    Swal.fire('Error', r.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to process the request', 'error');
            }
        });
    });
    
    // Check resi button
    $(document).on('click', '#check_resi_btn', function() {
        var pt_id = $('#_id').val();
        $.ajax({
            type: "POST",
            data: { _pt_id: pt_id },
            dataType: 'json',
            url: "{{ url('check_resi') }}",
            success: function(r) {
                if (r.status == '200') {
                    var win = window.open('{{ url('/') }}/upload/resi/' + r.resi_id, '_blank');
                    if (win) {
                        win.focus();
                    } else {
                        Swal.fire('Error', 'Please allow popups for this website', 'error');
                    }
                } else {
                    Swal.fire('Error', r.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to process the request', 'error');
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
            url: "{{ url('sv_cross_order') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#save_shipping_number_btn").html('Simpan');
                $("#save_shipping_number_btn").prop("disabled", false);
                if (data.status == '200') {
                    document.getElementById('ShippingNumberModal').classList.add('hidden');
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    $('#f_shipping_number')[0].reset();
                    loadCrossOrderData();
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
    
    // Check resi PDF
    $(document).on('click', '.check_resi', function(e) {
        e.stopPropagation();
        const resiFile = $(this).data('resi');
        $.ajax({
            url: "{{ url('get_resi_pdf') }}",
            type: 'POST',
            data: {
                resi: resiFile,
                _token: '{{ csrf_token() }}'
            },
            xhrFields: {
                responseType: 'blob'
            },
            success: function(blob) {
                const url = URL.createObjectURL(blob);
                $('#resiPdfIframe').attr('src', url);
                document.getElementById('resiModal').classList.remove('hidden');
            },
            error: function() {
                Swal.fire('Error', 'Failed to load resi PDF.', 'error');
            }
        });
    });
});
</script>
