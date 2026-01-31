<script>
var productDiscountDataTable = null;
var productDiscountDetailDataTable = null;
var articleDataTable = null;

function loadProductDiscountData() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        url: "{{ url('product_discount_datatables') }}",
        type: 'GET',
        data: {
            search: $('#product_discount_search').val(),
            st_id: $('#st_id_filter').val()
        },
        dataType: 'json',
        success: function(response) {
            if (productDiscountDataTable) {
                productDiscountDataTable.destroy();
            }
            
            $('#ProductDiscounttb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    var html = '<tr class="hover:bg-gray-50" data-pd_id="' + row.pd_id + '" data-pd_name="' + row.pd_name + '" data-st_id="' + row.st_id + '" data-std_id="' + row.std_id + '" data-pd_type="' + row.pd_type + '" data-pd_value="' + row.pd_value + '" data-pd_date_start="' + row.pd_date_start + '" data-pd_date="' + row.pd_date + '">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + row.DT_RowIndex + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-900 cursor-pointer">' + (row.pd_name || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700 cursor-pointer">' + (row.st_id_show || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700 cursor-pointer">' + (row.dv_name || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700 cursor-pointer">' + (row.pd_type_show || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700 cursor-pointer">' + (row.pd_value || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700 cursor-pointer">' + (row.pd_date_start_show || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700 cursor-pointer">' + (row.pd_date_show || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.article || '') + '</td>';
                    html += '</tr>';
                    $('#ProductDiscounttb tbody').append(html);
                });
            } else {
                $('#ProductDiscounttb tbody').append('<tr><td colspan="9" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("ProductDiscounttb") && typeof simpleDatatables !== 'undefined') {
                productDiscountDataTable = new simpleDatatables.DataTable("#ProductDiscounttb", {
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
            console.error('Error loading product discount data:', xhr);
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data diskon.', 'error');
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
    loadProductDiscountData();

    // Search handler for main table
    $('#product_discount_search').on('keyup', function() {
        loadProductDiscountData();
    });

    // Search handler for detail table
    $('#product_discount_detail_search').on('keyup', function() {
        if (productDiscountDetailDataTable) {
            productDiscountDetailDataTable.draw();
        }
    });

    // Search handler for article table
    $('#article_search').on('keyup', function() {
        if (articleDataTable) {
            articleDataTable.draw();
        }
    });

    // Store filter handler
    $('#st_id_filter').on('change', function() {
        loadProductDiscountData();
    });

    // Click row to open modal (skip last column which is article button)
    $(document).on('click', '#ProductDiscounttb tbody tr td:not(:nth-child(9))', function() {
        var $row = $(this).closest('tr');
        var pd_id = $row.data('pd_id');
        var pd_name = $row.data('pd_name');
        var st_id = $row.data('st_id');
        var std_id = $row.data('std_id');
        var pd_type = $row.data('pd_type');
        var pd_value = $row.data('pd_value');
        var pd_date_start = $row.data('pd_date_start');
        var pd_date = $row.data('pd_date');
        
        document.getElementById('ProductDiscountModal').classList.remove('hidden');
        
        $('#pd_name').val(pd_name);
        $('#pd_type').val(pd_type);
        $('#st_id').val(st_id);
        $('#std_id').val(std_id);
        $('#pd_value').val(pd_value);
        $('#pd_date_start').val(pd_date_start);
        $('#pd_date').val(pd_date);
        $('#_id').val(pd_id);
        $('#_mode').val('edit');
        @if (isset($data['user']) && $data['user']->delete_access == '1')
            $('#delete_product_discount_btn').show();
        @endif
    });

    // Discount item button click
    $(document).on('click', '#discount_item_btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var pd_id = $(this).attr('data-pd_id');
        var pd_name = $(this).attr('data-pd_name');
        $('#_pd_id').val(pd_id);
        $('#_pd_id_detail').val(pd_id);
        $('#product_discount_label').text(pd_name);
        
        document.getElementById('ProductDiscountDetailModal').classList.remove('hidden');
        
        // Load detail data - you'll need to implement loadProductDiscountDetailData similar to target
        // For now, we'll use the existing DataTable approach from the old JS
        if ($('#ProductDiscountDetailtb').length && typeof $('#ProductDiscountDetailtb').DataTable === 'function') {
            var detailTable = $('#ProductDiscountDetailtb').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: false,
                ajax: {
                    url: "{{ url('product_discount_detail_datatables') }}",
                            data: function(d) {
                                d.search = $('#product_discount_detail_search').val();
                                d.pd_id = $('#_pd_id_detail').val();
                            }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'pdd_id', searchable: false },
                    { data: 'article_id', name: 'article_id' },
                    { data: 'ps_barcode', name: 'ps_barcode' },
                    { data: 'article', name: 'p_name' },
                    { data: 'p_color', name: 'p_color' },
                    { data: 'sz_name', name: 'sz_name' },
                    { data: 'sell_price', name: 'sell_price', orderable: false },
                    { data: 'sell_price_discount', name: 'sell_price_discount', orderable: false },
                    { data: 'action', name: 'action', orderable: false }
                ],
                order: [[0, 'desc']]
            });
            productDiscountDetailDataTable = detailTable;
        }
    });

    // Add product discount button
    $('#add_product_discount_btn').on('click', function() {
        document.getElementById('ProductDiscountModal').classList.remove('hidden');
        $('#_id').val('');
        $('#_mode').val('add');
        $('#f_pd')[0].reset();
        $('#delete_product_discount_btn').hide();
    });

    // Add product discount detail button
    $('#add_product_discount_detail_btn').on('click', function() {
        document.getElementById('ArticleModal').classList.remove('hidden');
        
        // Load article table using DataTable
        // Wait a bit for modal to be fully shown
        setTimeout(function() {
            if ($('#Articletb').length) {
                // Destroy existing table if any
                if (articleDataTable) {
                    try {
                        articleDataTable.destroy();
                    } catch(e) {
                        console.log('Error destroying article table:', e);
                    }
                }
                
                // Initialize DataTable if available
                if (typeof $.fn.DataTable !== 'undefined') {
                    articleDataTable = $('#Articletb').DataTable({
                        destroy: true,
                        processing: false,
                        serverSide: true,
                        responsive: false,
                        ajax: {
                            url: "{{ url('product_discount_article_datatables') }}",
                            data: function(d) {
                                d.search = $('#article_search').val();
                            }
                        },
                        columns: [
                            { data: 'DT_RowIndex', name: 'p_id', searchable: false },
                            { data: 'br_name', name: 'br_name' },
                            { data: 'p_name', name: 'p_name' },
                            { data: 'p_color', name: 'p_color' },
                            { data: 'sz_name_show', name: 'sz_name_show' }
                        ],
                        order: [[0, 'desc']]
                    });
                }
            }
        }, 300);
    });

    // Add article to list
    $(document).on('click', '#add_article_to_list', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var pst_id = $(this).attr('data-pst_id');
        var pd_id = $('#_pd_id_detail').val();
        $.ajax({
            type: "POST",
            data: {
                _pst_id: pst_id,
                _pd_id: pd_id
            },
            dataType: 'json',
            url: "{{ url('add_item_to_discount') }}",
            success: function(r) {
                if (r.status == '200') {
                    Swal.fire('Berhasil', 'Artikel berhasil ditambah ke dalam diskon', 'success');
                    if (articleDataTable) articleDataTable.draw(false);
                    if (productDiscountDetailDataTable) productDiscountDetailDataTable.draw();
                    loadProductDiscountData();
                } else if (r.status == '400') {
                    Swal.fire('Sudah Ada', 'Artikel sudah ada dalam list diskon', 'warning');
                } else {
                    Swal.fire('Error', 'Terjadi kesalahan saat menambahkan artikel', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan saat memproses permintaan', 'error');
            }
        });
        return false;
    });

    // Delete discount item
    $(document).on('click', '#delete_discount_item', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var pst_id = $(this).attr('data-pst_id');
        var pd_id = $('#_pd_id_detail').val();
        Swal.fire({
            title: "Hapus..?",
            text: "Yakin hapus data ini?",
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
                    data: {
                        _pst_id: pst_id,
                        _pd_id: pd_id
                    },
                    dataType: 'json',
                    url: "{{ url('delete_item_discount') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Artikel berhasil dihapus dari diskon', 'success');
                            if (articleDataTable) articleDataTable.draw();
                            if (productDiscountDetailDataTable) productDiscountDetailDataTable.draw();
                            loadProductDiscountData();
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus item', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan saat menghapus item.', 'error');
                    }
                });
                return false;
            }
        });
    });

    // Save product discount
    $('#f_pd').on('submit', function(e) {
        e.preventDefault();
        $("#save_product_discount_btn").html('Proses ..');
        $("#save_product_discount_btn").attr("disabled", true);
        var formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: "{{ url('pd_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#save_product_discount_btn").html('Simpan');
                $("#save_product_discount_btn").attr("disabled", false);

                if (data.status == '200') {
                    closeProductDiscountModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadProductDiscountData();
                } else if (data.status == '400') {
                    Swal.fire('Gagal', 'Data tidak tersimpan', 'warning');
                }
            },
            error: function() {
                $("#save_product_discount_btn").html('Simpan');
                $("#save_product_discount_btn").attr("disabled", false);
                Swal.fire('Error', 'Terjadi kesalahan saat memproses permintaan', 'error');
            }
        });
    });

    // Delete product discount
    $('#delete_product_discount_btn').on('click', function() {
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
                    data: {
                        _id: $('#_id').val()
                    },
                    dataType: 'json',
                    url: "{{ url('pd_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                            closeProductDiscountModal();
                            loadProductDiscountData();
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus data', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan saat memproses permintaan', 'error');
                    }
                });
                return false;
            }
        });
    });

    // Import modal from detail modal
    $('#import_modal_btn_detail').on('click', function() {
        document.getElementById('ImportModal').classList.remove('hidden');
    });

    // Import form
    $('#f_import').on('submit', function(e) {
        e.preventDefault();
        $("#import_data_btn").html('Proses ..');
        $("#import_data_btn").attr("disabled", true);
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ url('discount_import') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#import_data_btn").html('Import');
                $("#import_data_btn").attr("disabled", false);
                if (data.status == '200') {
                    closeImportModal();
                    Swal.fire('Berhasil', 'Data berhasil diimport', 'success');
                    $('#f_import')[0].reset();
                    loadProductDiscountData();
                    if (productDiscountDetailDataTable) productDiscountDetailDataTable.draw();
                } else if (data.status == '400') {
                    Swal.fire('File', 'File yang anda import kosong atau format tidak tepat', 'warning');
                } else {
                    Swal.fire('Gagal', 'Silahkan periksa format input pada template anda, pastikan kolom biru terisi sesuai dengan sistem', 'warning');
                }
            },
            error: function(data) {
                $("#import_data_btn").html('Import');
                $("#import_data_btn").attr("disabled", false);
                Swal.fire('Error', 'Terjadi kesalahan saat import', 'error');
            }
        });
    });
});
</script>
