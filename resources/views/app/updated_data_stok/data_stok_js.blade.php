<script>
var modal_opened = '';

function reloadSubCategory(type, id) {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
    $.ajax({
        type: "POST",
        data: { _type: type, _id: id },
        dataType: 'html',
        url: "{{ url('stock_data_reload_sub_category') }}",
        success: function(r) { $('#psc_id').html(r); }
    });
}

function reloadSubOnSubCategory(type, id) {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
    $.ajax({
        type: "POST",
        data: { _type: type, _id: id },
        dataType: 'html',
        url: "{{ url('stock_data_reload_sub_sub_category') }}",
        success: function(r) { $('#pssc_id').html(r); }
    });
}

function reloadCategory(type, id) {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
    $.ajax({
        type: "POST",
        data: { _type: type, _id: id },
        dataType: 'html',
        url: "{{ url('stock_data_reload_category') }}",
        success: function(r) { $('#pc_id').html(r); }
    });
}

function initializeScanner(elementId) {
    return new Html5QrcodeScanner(elementId, {
        qrbox: { width: 250, height: 250 },
        fps: 30,
    });
}

var scanner_main = initializeScanner('reader_main');
var scanner_change_display = initializeScanner('reader_change_display');

$(document).ready(function() {
    scanner_main.render(success, error);
    $('#br_id, #pc_id, #psc_id, #pssc_id, #sz_id, #gender_id, #p_name, #main_color_id').val('');
    
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Stock Data Table
    var stock_data_table = $('#StockDatatb').DataTable({
        destroy: true,
        processing: false,
        serverSide: true,
        responsive: false,
        dom: '<"flex justify-between items-center mb-4"l><"overflow-x-auto"t><"flex justify-between items-center mt-4"ip>',
        buttons: [{ "extend": 'excelHtml5', "text": 'Excel', "className": 'btn btn-primary btn-xs' }],
        ajax: {
            url: "{{ url('stock_data_datatables') }}",
            data: function(d) {
                d.search = $('#stock_data_search').val();
                d.search_scan = $('#stock_data_search_scan').val();
                d.br_id = $('#br_id').val();
                d.pc_id = $('#pc_id').val();
                d.psc_id = $('#psc_id').val();
                d.pssc_id = $('#pssc_id').val();
                d.sz_id = $('#sz_id').val();
                d.gender_id = $('#gender_id').val();
                d.p_name = $('#p_name').val();
                d.display_status = $('#display_status').val();
                d.st_id = $('#st_id_filter').val();
                d.is_zero = $('#is_zero').val();
            },
        },
        columns: [
            { data: 'article_name', name: 'article_name', orderable: false },
            { data: 'article_stock', name: 'article_stock', orderable: false },
        ],
        columnDefs: [{ "targets": 0, "className": "text-left", "width": "0%" }],
        rowCallback: function(row, data, index) {
            if (data.article_stock.indexOf("<table></table>") >= 0) {
                $(row).hide();
            }
        },
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        language: { "lengthMenu": "_MENU_" },
        order: [[0, 'desc']],
    });
    var oSettings = stock_data_table.settings();

    // Pick Available/Zero Buttons
    $('#pickAvailableBtn').on('click', function() {
        $('#is_zero').val(0);
        $('#pickZeroBtn').removeClass('bg-blue-600').addClass('bg-gray-500');
        $('#pickAvailableBtn').removeClass('bg-gray-500').addClass('bg-blue-600');
        $('th.text-dark, th.font-semibold').first().text('Stok Tersedia');
        stock_data_table.draw();
    });

    $('#pickZeroBtn').on('click', function() {
        $('#is_zero').val(1);
        $('#pickZeroBtn').removeClass('bg-gray-500').addClass('bg-blue-600');
        $('#pickAvailableBtn').removeClass('bg-blue-600').addClass('bg-gray-500');
        $('th.text-dark, th.font-semibold').first().text('Stok Semua Varian');
        stock_data_table.draw();
    });

    // Pickup List Table
    var pickup_list_table = $('#PickupListtb').DataTable({
        destroy: true,
        processing: false,
        serverSide: true,
        responsive: false,
        dom: '<"overflow-x-auto"t><"flex justify-between items-center mt-4"ip>',
        ajax: {
            url: "{{ url('pickup_list_datatables') }}",
            data: function(d) {
                d.search = $('#pick_data_search').val();
                d.st_id = "{{ $data['user']->st_id }}";
            }
        },
        columns: [
            { data: 'article', name: 'p_name', orderable: false },
            { data: 'sa_name', name: 'sa_name', orderable: false },
            { data: 'datetime', name: 'plst_created', orderable: false },
            { data: 'user', name: 'user', orderable: false },
            { data: 'status', name: 'p_name', orderable: false },
            { data: 'action', name: 'p_name', orderable: false },
        ],
        columnDefs: [{ "targets": 0, "className": "text-left", "width": "0%" }],
        order: [[0, 'desc']],
    });

    // Filter List Table
    var filter_list_table = $('#FilterListtb').DataTable({
        destroy: true,
        processing: false,
        serverSide: true,
        responsive: false,
        dom: '<"overflow-x-auto"t><"flex justify-between items-center mt-4"ip>',
        ajax: {
            url: "{{ url('filter-products') }}",
            type: "GET",
            data: function(d) {
                d.st_id = $('#st_id_filter').val();
                d.pc_id = $('#pc_id').val();
                d.psc_id = $('#psc_id').val();
                d.pssc_id = $('#pssc_id').val();
                d.br_id = $('#br_id').val();
                d.sz_id = $('#sz_id').val();
                d.min_price = $('#min_price_filter').val();
                d.max_price = $('#max_price_filter').val();
                d.main_color_id = $('#main_color_id').val();
            }
        },
        columns: [
            { data: 'article_id', name: 'article_id' },
            { data: 'p_name', name: 'p_name' },
            { data: 'SKU', name: 'ps_barcode' },
            { data: 'sz_name', name: 'sizes.sz_name' },
            { data: 'harga', orderable: false, searchable: false },
            { data: 'bin', name: 'pl_code' },
            { data: 'qty', name: 'qty' },
            { data: 'action', orderable: false, searchable: false },
        ],
        columnDefs: [{ "targets": 0, "className": "text-left", "width": "0%" }],
        order: [[0, 'desc']],
    });

    // Waiting List Table
    var waiting_list_table = $('#WaitingListtb').DataTable({
        destroy: true,
        processing: false,
        serverSide: true,
        responsive: false,
        dom: '<"overflow-x-auto"t><"flex justify-between items-center mt-4"ip>',
        ajax: {
            url: "{{ url('waiting_list_datatables') }}",
            data: function(d) {
                d.search = $('#waiting_data_search').val();
                d.st_id = "{{ $data['user']->st_id }}";
            }
        },
        columns: [
            { data: 'article_id', name: 'article_id', orderable: false },
            { data: 'article', name: 'p_name', orderable: false },
            { data: 'bin', name: 'pl_code', orderable: false },
            { data: 'datetime', name: 'plst_created', orderable: false },
            { data: 'user', name: 'user', orderable: false },
            { data: 'status', name: 'p_name', orderable: false },
            { data: 'action', name: 'p_name', orderable: false },
        ],
        columnDefs: [{ "targets": 0, "className": "text-left", "width": "0%" }],
        order: [[0, 'desc']],
    });

    function debounce(func, delay) {
        let timeoutId;
        return function(...args) {
            if (timeoutId) clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    }

    function success(result) {
        var hasil = result;
        if (hasil.startsWith(']C1')) hasil = hasil.replace(']C1', '');
        alert(hasil);
        if (modal_opened == 'ScanChangeDisplay') {
            $('#sku_display').val(hasil);
            $('#sku_display').trigger('change');
        } else {
            $('#stock_data_search').val(hasil);
            stock_data_table.ajax.reload();
        }
    }

    function error(err) {
        console.error(err);
    }

    // Search handlers
    $('#stock_data_search').on('keydown', function(e) {
        if (e.key === "Enter") e.preventDefault();
    });

    $('#stock_data_search').on('keyup', debounce(function() {
        var query = $(this).val();
        if ($.trim(query).length > 4 || $.trim(query).length != 0) {
            stock_data_table.draw();
        }
    }, 400));

    $('#pick_data_search').on('keyup', function() {
        pickup_list_table.draw();
    });

    $('#waiting_data_search').on('keyup', function() {
        waiting_list_table.draw();
    });

    // Copy button
    $(document).delegate('#copy-button', 'click', function(e) {
        var textToCopy = $(this).attr('title');
        var tempTextarea = $('<textarea>');
        $('body').append(tempTextarea);
        tempTextarea.val(textToCopy).select();
        document.execCommand('copy');
        tempTextarea.remove();
        swal({ title: "Copied!", text: "Text copied to clipboard: " + textToCopy, icon: "success" });
    });

    // Pickup Item
    $(document).delegate('#pickup_item', 'click', function(e) {
        e.preventDefault();
        var pick_access = $(this).attr('data-pick_access');
        if (pick_access == 0 || pick_access === '' || pick_access === null) {
            Swal.fire({ icon: 'error', title: 'Akses Ditolak', text: 'Anda tidak memiliki izin untuk pickup item ini. Hubungi Administrator.', confirmButtonText: 'OK' });
            return false;
        }

        var st_id = $('#st_id_filter').val();
        var pst_id = $(this).attr('data-pst_id');
        var pl_id = $(this).attr('data-pl_id');
        var qty = $(this).attr('data-qty');
        var pls_id = $(this).attr('data-pls_id');
        var p_name = $(this).attr('data-p_name');
        var pl_code = $(this).attr('data-pl_code');
        var bin = $(this).attr('data-bin');
        var freeze = $(this).attr('data-freeze');
        var sa_id = $(this).attr('data-sa_id');
        var sa_name = $(this).attr('data-sa_name');
        var user_st_id = {{ \Illuminate\Support\Facades\Auth::user()->st_id }};

        if (freeze == 1) {
            Swal.fire({ icon: 'info', title: 'Bin Under Maintenance', text: 'This bin is now under maintenance. Please contact the logistics team.', confirmButtonText: 'OK' });
        } else {
            @if (strtolower($data['user']->stt_name) == 'offline' || strtolower($data['user']->stt_name) == 'online' || $data['user']->pick_access == 1)
            if (user_st_id == {{ $data['user']->st_id }}) {
                let article_id = $(this).data('p_article');
                $.ajax({
                    type: "GET",
                    url: "{{ url('get_articles_promo') }}/" + article_id,
                    success: function(response) {
                        let promoInfo = '';
                        if (response.data && response.data.length > 0) {
                            let promoData = [];
                            response.data.forEach(function(promo) {
                                let originalPrice = promo.p_price_tag;
                                let discount = promo.promo_disc;
                                let discountedPrice = originalPrice - (originalPrice * (discount / 100));
                                promoData.push({ promo_name: promo.promo_name, discount: discount, discount_price: discountedPrice });
                            });
                            promoInfo = promoData.map(function(item) {
                                return 'Promo: ' + item.discount + '% - ' + item.promo_name + '\nDiscount Price: Rp ' + item.discount_price.toLocaleString('id-ID');
                            }).join('\n\n');
                        } else {
                            promoInfo = 'No promo available for this article.';
                        }

                        swal({
                            title: sa_name,
                            text: "Yakin pickup item " + p_name + " dari area " + sa_name + " ?\n\nPromotions:\n" + promoInfo,
                            icon: "warning",
                            buttons: ['Batal', 'Yakin'],
                            dangerMode: false,
                        }).then(function(isConfirm) {
                            if (isConfirm) {
                                $.ajax({
                                    type: "POST",
                                    data: { _sa_id: sa_id, _pst_id: pst_id, _st_id: user_st_id },
                                    dataType: 'json',
                                    url: "{{ url('pickup_item') }}",
                                    success: function(r) {
                                        if (r.status == '200') {
                                            toast("Berhasil", "Item berhasil dipickup", "success");
                                            stock_data_table.draw();
                                            pickup_list_table.draw();
                                        } else {
                                            Swal.fire({ icon: 'error', title: r.title ?? 'Gagal', text: r.message ?? 'Gagal pickup item' });
                                        }
                                    },
                                    error: function(xhr) {
                                        Swal.fire({ icon: 'error', title: xhr.responseJSON?.title ?? 'Error', text: xhr.responseJSON?.message ?? 'Terjadi kesalahan' });
                                    }
                                });
                            }
                        });
                    },
                    error: function() {
                        swal('Error', 'Failed to fetch promo data', 'error');
                    }
                });
            } else {
                swal({
                    title: "Pickup..?",
                    text: "Yakin pickup item " + p_name + " dari bin " + bin + " ?",
                    icon: "warning",
                    buttons: ['Batal', 'Yakin'],
                    dangerMode: false,
                }).then(function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            type: "POST",
                            data: { _pls_id: pls_id, _pst_id: pst_id, _pl_id: pl_id, _pl_code: pl_code },
                            dataType: 'json',
                            url: "{{ url('pickup_item') }}",
                            success: function(r) {
                                if (r.status == '200') {
                                    toast("Berhasil", "Item berhasil dipickup", "success");
                                    stock_data_table.draw();
                                    pickup_list_table.draw();
                                } else {
                                    Swal.fire({ icon: 'error', title: r.title ?? 'Gagal', text: r.message ?? 'Gagal pickup item' });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({ icon: 'error', title: xhr.responseJSON?.title ?? 'Error', text: xhr.responseJSON?.message ?? 'Terjadi kesalahan' });
                            }
                        });
                    }
                });
            }
            @endif
        }
    });

    // Reset Button
    $('#reset_btn').on('click', function() {
        $("#br_id").val("").trigger('change');
        $("#pc_id").val("").trigger('change');
        $("#psc_id").val("").trigger('change');
        $("#pssc_id").val("").trigger('change');
        $("#sz_id").val("").trigger('change');
        $("#main_color_id").val("").trigger('change');
        $("#p_name").val("").trigger('change');
        $("#stock_data_search").val("");
        $('#display_status').val('');
        oSettings[0]._iDisplayLength = 10;
    });

    // Select2 Initialization
    $('#br_id').select2({ multiple: true, width: "100%", dropdownParent: $('#br_id_parent'), closeOnSelect: true, placeholder: "BRANDS", allowClear: true });
    $('#pc_id').select2({ multiple: true, width: "100%", dropdownParent: $('#pc_id_parent'), closeOnSelect: true, placeholder: "KATEGORI", allowClear: true });
    $('#psc_id').select2({ multiple: true, width: "100%", dropdownParent: $('#psc_id_parent'), closeOnSelect: true, placeholder: "SUB KATEGORI", allowClear: true });
    $('#pssc_id').select2({ multiple: true, width: "100%", dropdownParent: $('#pssc_id_parent'), closeOnSelect: true, placeholder: "SUB ON SUB KATEGORI", allowClear: true });
    $('#sz_id').select2({ multiple: true, width: "100%", dropdownParent: $('#sz_id_parent'), closeOnSelect: true, placeholder: "SIZE / UKURAN", allowClear: true });
    $('#min_price_filter').select2({ multiple: false, width: "100%", dropdownParent: $('#min_price_filter_parent'), closeOnSelect: true, placeholder: "MINIMUM PRICE", allowClear: true });
    $('#max_price_filter').select2({ multiple: false, width: "100%", dropdownParent: $('#max_price_filter_parent'), closeOnSelect: true, placeholder: "MAX PRICE", allowClear: true });
    $('#main_color_id').select2({ multiple: true, width: "100%", dropdownParent: $('#main_color_id_parent'), closeOnSelect: true, placeholder: "MAIN COLOR", allowClear: true });

    // Category change handlers
    $('#pc_id').on('change', function() { reloadSubCategory('product_category', $(this).val()); });
    $('#psc_id').on('change', function() { reloadSubOnSubCategory('product_sub_category', $(this).val()); });
    $('#br_id').on('change', function() { reloadCategory('brand', $(this).val()); });

    // Modal buttons
    $('#pickup_list_btn').on('click', function() {
        openPickupListModal();
        pickup_list_table.draw();
    });

    $('#waiting_list_btn').on('click', function() {
        openWaitingListModal();
        waiting_list_table.draw();
    });

    $('#filter_list_btn').on('click', function() {
        openFilterListModal();
        filter_list_table.draw();
    });

    $('#change_display_btn').on('click', function() {
        scanner_main.clear();
        modal_opened = 'ScanChangeDisplay';
        openChangeDisplayModal();
        scanner_change_display.render(success, error);
    });

    // SKU Display change
    $('#sku_display').on('change', function() {
        var sku = $(this).val();
        if (sku.trim() !== '') {
            $.ajax({
                type: "GET",
                url: "{{ url('get_item_data_by_sku') }}/" + sku,
                dataType: 'json',
                success: function(response) {
                    if (response) {
                        $('#product_name').text(response.p_name || '-');
                        var variant = '';
                        if (response.p_color && response.sz_name) variant = response.p_color + ' / ' + response.sz_name;
                        else if (response.p_color) variant = response.p_color;
                        else if (response.sz_name) variant = response.sz_name;
                        else variant = '-';
                        $('#product_variant').text(variant);
                    } else {
                        $('#product_name').text('-');
                        $('#product_variant').text('-');
                    }
                },
                error: function() {
                    $('#product_name').text('-');
                    $('#product_variant').text('-');
                }
            });
        } else {
            $('#product_name').text('-');
            $('#product_variant').text('-');
        }
    });

    // Ganti Display Form
    $('#f_ganti_display').on('submit', function(event) {
        event.preventDefault();
        var sku = $('#sku_display').val();
        var product_name = $('#product_name').text();
        if (sku.trim() === '') { swal('Error', 'SKU tidak boleh kosong', 'error'); return; }
        if (product_name.trim() === '-' || product_name.trim() === '') { swal('Error', 'Nama produk tidak ditemukan', 'error'); return; }

        swal({
            title: "Ganti Display..?",
            text: "Yakin ganti display untuk SKU " + sku + " ?",
            icon: "warning",
            buttons: ['Batal', 'Yakin'],
            dangerMode: false,
        }).then(function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "POST",
                    url: "{{ url('change_display_stock_data') }}",
                    data: { _pst_barcode: sku },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == '200') {
                            swal('Berhasil', 'Display stock data berhasil diubah', 'success');
                            closeChangeDisplayModal();
                            stock_data_table.draw();
                        } else {
                            swal('Gagal', response.message || 'Gagal mengubah display stock data', 'error');
                        }
                    },
                    error: function() {
                        swal('Error', 'Terjadi kesalahan saat mengubah display stock data', 'error');
                    }
                });
            }
        });
    });

    // Cancel Pickup
    $(document).delegate('#cancel_pickup_btn', 'click', function() {
        var plst_id = $(this).attr('data-plst_id');
        var pls_id = $(this).attr('data-pls_id');
        var pst_id = $(this).attr('data-pst_id');
        var pl_id = $(this).attr('data-pl_id');
        var p_name = $(this).attr('data-p_name');
        var pl_code = $(this).attr('data-pl_code');
        swal({
            title: "Batal..?",
            text: "Yakin batalkan pickup untuk item " + p_name + " ?",
            icon: "warning",
            buttons: ['Batal', 'Yakin'],
            dangerMode: false,
        }).then(function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "POST",
                    data: { _plst_id: plst_id, _pls_id: pls_id, _pst_id: pst_id, _pl_id: pl_id, _pl_code: pl_code },
                    dataType: 'json',
                    url: "{{ url('cancel_pickup_item') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            toast("Berhasil", "Pickup item berhasil dibatalkan", "success");
                            pickup_list_table.draw();
                            stock_data_table.draw();
                        } else {
                            toast('Gagal', 'Gagal batalkan pickup', 'error');
                        }
                    }
                });
            }
        });
    });

    // Pick Display
    $(document).delegate('#pick_diplay_btn', 'click', function() {
        var plst_id = $(this).attr('data-plst_id');
        var pls_id = $(this).attr('data-pls_id');
        var pst_id = $(this).attr('data-pst_id');
        var pl_id = $(this).attr('data-pl_id');
        var p_name = $(this).attr('data-p_name');
        var pl_code = $(this).attr('data-pl_code');
        swal({
            title: "Move to Display..?",
            text: "Yakin pick display untuk item " + p_name + " ?",
            icon: "warning",
            buttons: ['Batal', 'Yakin'],
            dangerMode: false,
        }).then(function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "POST",
                    data: { _plst_id: plst_id, _pls_id: pls_id, _pst_id: pst_id, _pl_id: pl_id, _pl_code: pl_code },
                    dataType: 'json',
                    url: "{{ url('move_to_display_by_waiting_list') }}",
                    success: function(response) {
                        if (response.status == '200') {
                            swal('Berhasil', 'Pickup item berhasil dipindahkan ke display', 'success');
                            waiting_list_table.draw();
                            pickup_list_table.draw();
                            stock_data_table.draw();
                        } else {
                            swal('Gagal', response.message, 'error');
                        }
                    }
                });
            }
        });
    });

    // Store filter change
    $('#st_id_filter').on('change', function() {
        stock_data_table.draw();
    });

    // Form submit
    $('#f_search').on('submit', function(event) {
        event.preventDefault();
        var debounceTimeout;
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(function() {
            var query = $('#stock_data_search').val();
            stock_data_table.draw();
        }, 300);
    });
});

// Request count update
function updateRequestCount() {
    let previousCount = parseInt($('#request_count').text(), 10) || 0;
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
    $.ajax({
        url: '{{ url('request_count_pickup') }}',
        type: "POST",
        data: { _st_id: {{ \Illuminate\Support\Facades\Auth::user()->st_id }} },
        success: function(response) {
            const newCount = response.count;
            $('#request_count').text(newCount);
            if (newCount < previousCount) {
                const audio = new Audio("{{ asset('music/lily.mp3') }}");
                audio.play();
            }
            previousCount = newCount;
        },
        error: function() {
            console.error('Failed to fetch request count.');
        }
    });
}
setInterval(updateRequestCount, 3000);
updateRequestCount();
</script>
