<script src="https://cdn.tiny.cloud/1/323apjbgqf1hr5qmcz0u8uwvl3oymnrypmtg98wfpvhw0khd/tinymce/5/tinymce.min.js"
    referrerpolicy="origin"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>


<script>
    function toggleFlag(column, productId) {
        fetch(`/data_produk/update-flag/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    column: column
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update status on the page
                    const statusSpan = document.getElementById(column + '_status');
                    statusSpan.textContent = data.newValue ? '✓' : '✗';
                }
            })
            .catch(error => console.error('Error:', error));
    }


    // Function to show all sizes (All Schema)
    function showAllSchema() {
        const rows = document.querySelectorAll("#sizeTable tbody tr");
        rows.forEach(row => {
            row.style.display = ""; // Menampilkan semua baris
        });

        console.log(allSchemaBtn, stockedSchemaBtn);

        // Update button styles
        document.getElementById("allSchemaBtn").classList.add("btn-primary");
        document.getElementById("allSchemaBtn").classList.remove("btn-secondary");

        document.getElementById("stockedSchemaBtn").classList.add("btn-secondary");
        document.getElementById("stockedSchemaBtn").classList.remove("btn-primary");
    }

    // Function to show only checked sizes (Stocked Schema)
    function showStockedSchema() {
        const rows = document.querySelectorAll("#sizeTable tbody tr");
        rows.forEach(row => {
            const checkbox = row.querySelector("input[type='checkbox']");
            if (checkbox && checkbox.checked) {
                row.style.display = ""; // Menampilkan baris yang dicentang
            } else {
                row.style.display = "none"; // Menyembunyikan baris yang tidak dicentang
            }
        });

        // Update button styles
        document.getElementById("stockedSchemaBtn").classList.add("btn-primary");
        document.getElementById("stockedSchemaBtn").classList.remove("btn-secondary");

        document.getElementById("allSchemaBtn").classList.add("btn-secondary");
        document.getElementById("allSchemaBtn").classList.remove("btn-primary");
    }




    function generateQR(value) {
        jQuery.noConflict();
        jQuery('#product_qr').empty();
        jQuery('#product_qr').css({
            'width': 128,
            'height': 128
        })
        jQuery('#product_qr').qrcode({
            width: 128,
            height: 128,
            text: value
        });
    }

    function activateColumn() {
        $('input').prop('disabled', false);
        $('input[type="text"][data-running-code="' + value.sz_id +
            '"]').prop('disabled', true);
    }

    // tinymce.init({
    //     selector: 'textarea',
    //     plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
    //     toolbar_mode: 'floating',
    //     height: '150px'
    // });

    $(document).on('focusin', function(e) {
        if ($(e.target).closest(".mce-window").length) {
            e.stopImmediatePropagation();
        }
    });

    $('.decimal').keypress(function(evt) {
        return (/^[0-9]*\.?[0-9]*$/).test($(this).val() + evt.key);
    });

    function getProductSize(id) {
        var barcode = $('#product_size_barcode' + id).val();
        var sz_id = $('#product_size_barcode' + id).attr('data-id');
        var sz_barcode = sz_id + '-' + barcode;
        var current_sz_id = $('#_sz_id').val();
        var current_sz_barcode = $('#_sz_barcode').val();
        var price_tag = $('#product_size_price_tag' + id).val();
        var current_sz_price_tag = $('#_sz_price_tag').val();
        var sell_price = $('#product_size_sell_price' + id).val();
        var current_sz_sell_price = $('#_sz_sell_price').val();
        var purchase_price = $('#product_size_purchase_price' + id).val();
        var current_sz_purchase_price = $('#_sz_purchase_price').val();
        var mode = $('#_mode').val();

        // Jika checkbox dicentang
        if ($('#product_size' + id).is(':checked')) {
            if ($('#_sz_id').val() != '') {
                current_sz_id = $('#_sz_id').val() + sz_id + '|';
            } else {
                current_sz_id = sz_id + '|';
            }
            $('#_sz_id').val(current_sz_id);

            // Mode edit
            if (mode == 'edit') {
                $('#product_size_barcode' + id).show();
                $('#product_size_running_code' + id).show();
                if ($('#product_size_running_code' + id).val() != '') {
                    $('#product_size_barcode' + id).prop('disabled', false);
                } else {
                    $('#product_size_barcode' + id).prop('disabled', true);
                }
                if ($('#product_size_running_code' + id).val() != '') {
                    $('#product_size_purchase_price' + id).show();
                    $('#product_size_purchase_price' + id).prop('disabled', false);
                    $('#product_size_sell_price' + id).show();
                    $('#product_size_sell_price' + id).prop('disabled', false);
                    $('#product_size_price_tag' + id).show();
                    $('#product_size_price_tag' + id).prop('disabled', false);
                } else {
                    $('#product_size_purchase_price' + id).show();
                    $('#product_size_purchase_price' + id).prop('disabled', true);
                    $('#product_size_sell_price' + id).show();
                    $('#product_size_sell_price' + id).prop('disabled', true);
                    $('#product_size_price_tag' + id).show();
                    $('#product_size_price_tag' + id).prop('disabled', true);
                }
                $('#product_size_running_code' + id).prop('disabled', true);
            } else {
                $('#product_size_barcode' + id).show();
                $('#product_size_barcode' + id).prop('disabled', true);
                $('#product_size_barcode' + id).val('');
            }

            // Jika checkbox tidak dicentang
        } else {
            // Sembunyikan kolom barcode dan input lainnya
            $('#product_size_barcode' + id).hide();
            $('#product_size_running_code' + id).hide();
            $('#product_size_price_tag' + id).hide();
            $('#product_size_sell_price' + id).hide();
            $('#product_size_purchase_price' + id).hide();

            // Kosongkan nilai inputnya
            $('#product_size_barcode' + id).val('');
            $('#product_size_running_code' + id).val('');
            $('#product_size_price_tag' + id).val('');
            $('#product_size_sell_price' + id).val('');
            $('#product_size_purchase_price' + id).val('');

            // Lanjutkan dengan logika untuk menghapus data dari input hidden (sz_id, barcode, dsb.)
            if (current_sz_id.indexOf(sz_id) > -1) {
                var new_current_sz_id = current_sz_id.replace(sz_id + '|', '');
                var new_sz_id = new_current_sz_id.replace('||', '|');
                $('#_sz_id').val(new_sz_id);
                if ($('#_sz_id').val() == '|') {
                    $('#_sz_id').val('');
                }
            }
            if (current_sz_barcode.indexOf(sz_barcode) > -1) {
                var new_current_sz_barcode = current_sz_barcode.replace(sz_barcode + '|', '');
                var new_sz_barcode = new_current_sz_barcode.replace('||', '|');
                $('#_sz_barcode').val(new_sz_barcode);
                if ($('#_sz_barcode').val() == '|') {
                    $('#_sz_barcode').val('');
                }
            }
            if (current_sz_sell_price.indexOf(sell_price) > -1) {
                var new_current_sz_sell_price = current_sz_sell_price.replace(sell_price + '|', '');
                var new_sz_sell_price = new_current_sz_sell_price.replace('||', '|');
                $('#_sz_sell_price').val(new_sz_sell_price);
                if ($('#_sz_sell_price').val() == '|') {
                    $('#_sz_sell_price').val('');
                }
            }
            if (current_sz_purchase_price.indexOf(purchase_price) > -1) {
                var new_current_sz_purchase_price = current_sz_purchase_price.replace(purchase_price + '|', '');
                var new_sz_purchase_price = new_current_sz_purchase_price.replace('||', '|');
                $('#_sz_purchase_price').val(new_sz_purchase_price);
                if ($('#_sz_purchase_price').val() == '|') {
                    $('#_sz_purchase_price').val('');
                }
            }

            // Mode edit
            if (mode == 'edit') {
                $('#product_size_barcode' + id).hide();
                $('#product_size_running_code' + id).hide();
                $('#product_size_barcode' + id).prop('required', false);
            } else {
                $('#product_size_barcode' + id).prop('required', false);
                $('#product_size_barcode' + id).val('');
                $('#product_size_barcode' + id).hide();
            }
        }
    }

    function getProductSizeBarcode(id) {
        var barcode = $('#product_size_barcode' + id).val();
        var running = $('#product_size_running_code' + id).val();
        var sz_id = $('#product_size_barcode' + id).attr('data-id');
        var sz_barcode = sz_id + '-' + barcode;

        // Menyimpan barcode baru
        var current_sz_barcode = $('#_sz_barcode').val();
        if (current_sz_barcode != '') {
            current_sz_barcode += sz_barcode + '|';
        } else {
            current_sz_barcode = sz_barcode + '|';
        }
        $('#_sz_barcode').val(current_sz_barcode);

        // Menonaktifkan input barcode
        $('#product_size_barcode' + id).prop('disabled', true);

        // Setup AJAX untuk CSRF Token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Memeriksa apakah barcode sudah ada
        $.ajax({
            type: "POST",
            data: {
                _barcode: barcode
            },
            dataType: 'json',
            url: "{{ url('check_exists_barcode') }}",
            success: function(response) {
                if (response.status == '200') {
                    // Jika barcode sudah ada
                    swal('Barcode', 'Barcode sudah ada dalam sistem, silahkan ganti dengan yang lain',
                        'warning');
                    $('#product_size_barcode' + id).val('');
                    $('#product_size_barcode' + id).prop('disabled', false);
                } else {
                    // Jika barcode belum ada, lanjutkan untuk menyimpan
                    $.ajax({
                        type: "POST",
                        data: {
                            _running: running,
                            _barcode: barcode,
                            _id: id
                        },
                        dataType: 'json',
                        url: "{{ url('update_barcode') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                toast('Tersimpan', 'Barcode berhasil tersimpan', 'success');
                            } else {
                                toast('Gagal', 'Terjadi kesalahan saat menyimpan barcode',
                                    'error');
                            }
                        },
                        error: function() {
                            toast('Gagal', 'Terjadi kesalahan saat menyimpan barcode', 'error');
                        }
                    });
                }
            },
            error: function() {
                toast('Gagal', 'Terjadi kesalahan saat memeriksa barcode', 'error');
            }
        });
    }



    // function getProductSizeBarcode(id) {
    //     var barcode = $('#product_size_barcode' + id).val();
    //     var running = $('#product_size_running_code' + id).val();
    //     var sz_id = $('#product_size_barcode' + id).attr('data-id');
    //     var sz_barcode = sz_id + '-' + barcode;
    //     if ($('#_sz_barcode').val() != '') {
    //         var current_sz_barcode = $('#_sz_barcode').val() + sz_barcode + '|';
    //     } else {
    //         var current_sz_barcode = sz_barcode + '|';
    //     }

    //     $('#_sz_barcode').val(current_sz_barcode);
    //     $('#product_size_barcode' + id).prop('disabled', true);
    //     //alert(sz_barcode+' ==== '+current_sz_barcode);
    //     $.ajaxSetup({
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         }
    //     });
    //     $.ajax({
    //         type: "POST",
    //         data: {
    //             _barcode: barcode
    //         },
    //         dataType: 'json',
    //         url: "{{ url('check_exists_barcode') }}",
    //         success: function(r) {
    //             $.ajaxSetup({
    //                 headers: {
    //                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //                 }
    //             });
    //             $.ajax({
    //                 type: "POST",
    //                 data: {
    //                     _running: running,
    //                     _barcode: barcode,
    //                     _id: id
    //                 },
    //                 dataType: 'json',
    //                 url: "{{ url('update_barcode') }}",
    //                 success: function(r) {
    //                     if (r.status == '200') {
    //                         toast('Tersimpan', 'Barcode berhasil tersimpan', 'success');
    //                     }
    //                 }
    //             });
    //             // if (r.status == '200') {
    //             //     swal('Barcode', 'Barcode sudah ada dalam sistem, silahkan ganti dengan yang lain', 'warning');
    //             //     $('#product_size_barcode'+id).val('');
    //             //     $('#product_size_barcode'+id).prop('disabled', false);
    //             //     //alert(r.hasil);
    //             //     return false;
    //             // } else {
    //             //     $.ajaxSetup({
    //             //         headers: {
    //             //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //             //         }
    //             //     });
    //             //     $.ajax({
    //             //         type: "POST",
    //             //         data: {_running:running, _barcode:barcode},
    //             //         dataType: 'json',
    //             //         url: "{{ url('update_barcode') }}",
    //             //         success: function(r) {
    //             //             if (r.status == '200') {
    //             //                 toast('Tersimpan', 'Barcode berhasil tersimpan', 'success');
    //             //             }
    //             //         }
    //             //     });
    //             // }
    //         }
    //     });
    // }

    function getProductPriceTag(id) {
        var price_tag = $('#product_size_price_tag' + id).val();
        var barcode = $('#product_size_barcode' + id).val();

        console.log(barcode);
        $.ajax({
            type: "POST",
            data: {
                _barcode: barcode,
                _price_tag: price_tag
            },
            dataType: 'json',
            url: "{{ url('update_price_tag') }}",
            success: function(r) {
                if (r.status == '200') {
                    toast('Diupdate', 'Harga banderol berhasil diupdate', 'success');
                }
            }
        });
    }

    function getProductSellPrice(id) {
        var sell_price = $('#product_size_sell_price' + id).val();
        var barcode = $('#product_size_barcode' + id).val();
        $.ajax({
            type: "POST",
            data: {
                _barcode: barcode,
                _sell_price: sell_price
            },
            dataType: 'json',
            url: "{{ url('update_sell_price') }}",
            success: function(r) {
                if (r.status == '200') {
                    toast('Diupdate', 'Harga jual berhasil diupdate', 'success');
                }
            }
        });
    }

    function updateSzIdFilterOptions(selectedValue) {
        try {
            fetchDataBasedOnSchemaId(selectedValue).then(function(newData) {
                $('#sz_id_filter').empty();
                $.each(newData, function(key, value) {
                    $('#sz_id_filter').append('<option value="' + value.value + '">' + value.text +
                        '</option>');
                });
                $('#sz_id_filter').trigger('change');
            }).catch(function(error) {
                console.error(error);
            });
        } catch (error) {
            console.error(error);
        }
    }

    function fetchDataBasedOnSchemaId(schemaId) {
        return new Promise(function(resolve, reject) {
            var dataReturn = [];
            $.ajax({
                type: "GET",
                url: "{{ url('reload_size_schema') }}",
                data: {
                    schema_id: schemaId
                },
                success: function(data) {
                    $.each(data, function(key, value) {
                        dataReturn.push({
                            text: value.sz_name,
                            value: value.id
                        });
                    });
                    resolve(dataReturn);
                },
                error: function(xhr, status, error) {
                    reject(error);
                }
            });
        });
    }

    function getProductPurchasePrice(id) {
        var purchase_price = $('#product_size_purchase_price' + id).val();
        var barcode = $('#product_size_barcode' + id).val();
        $.ajax({
            type: "POST",
            data: {
                _barcode: barcode,
                _purchase_price: purchase_price
            },
            dataType: 'json',
            url: "{{ url('update_purchase_price') }}",
            success: function(r) {
                if (r.status == '200') {
                    toast('Diupdate', 'Harga beli berhasil diupdate', 'success');
                }
            }
        })
    }


    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var product_table = $('#Producttb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs',
                "exportOptions": {
                    orthogonal: 'export'
                }
            }],
            ajax: {
                url: "{{ url('product_datatables') }}",
                data: function(d) {
                    d.search = $('#product_search').val();
                    d.pc_id = $('#pc_id_filter').val();
                    d.psc_id = $('#psc_id_filter').val();
                    d.pssc_id = $('#pssc_id_filter').val();
                    d.br_id_filter = $('#br_id_filter').val();
                    d.ps_id_filter = $('#ps_id_filter').val();
                    d.mc_id_filter = $('#mc_id_filter').val();
                    d.sz_id_filter = $('#sz_id_filter').val();
                    d.p_active_filter = $('#p_active_filter').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'pid',
                    searchable: false
                },
                {
                    data: 'article_id',
                    name: 'article_id'
                },
                {
                    data: 'p_name_show',
                    name: 'p_name'
                },
                {
                    data: 'p_color_show',
                    name: 'p_color'
                },
                {
                    data: 'br_name',
                    name: 'br_name'
                },
                {
                    data: 'ps_name_show',
                    name: 'ps_name'
                },
                {
                    data: 'p_price_tag_show',
                    name: 'p_price_tag',
                    render: function(data, type, row) {
                        return type === 'export' ?
                            data.replace(/[$,]/g, '') :
                            data;
                    }
                },
                {
                    data: 'p_purchase_price_show',
                    name: 'p_purchase_price',
                    render: function(data, type, row) {
                        return type === 'export' ?
                            data.replace(/[$,]/g, '') :
                            data;
                    }
                },
                {
                    data: 'p_sell_price_show',
                    name: 'p_sell_price',
                    render: function(data, type, row) {
                        return type === 'export' ?
                            data.replace(/[$,]/g, '') :
                            data;
                    }
                },
                {
                    data: 'p_active',
                    name: 'p_active'
                },
                {
                    data: 'p_detail',
                    name: 'p_detail',
                    sortable: false
                },
            ],
            columnDefs: [{
                    "targets": 0,
                    "className": "text-center",
                    "width": "0%"
                },
                {
                    targets: 9,
                    render: function(data, type, row) {
                        if (row.p_active == '1') {
                            return '<span class="badge badge-success">Aktif</span>';
                        } else {
                            return '<span class="badge badge-danger">Tidak Aktif</span>';
                        }
                    }
                }
            ],
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
            language: {
                "lengthMenu": "_MENU_",
            },
            order: [
                [0, 'desc']
            ],
        });

        product_table.buttons().container().appendTo($('#product_excel_btn'));
        $('#product_search').on('keyup', function() {
            product_table.draw();
        });

        $('#br_id_filter, #ps_id_filter, #mc_id_filter, #sz_id_filter, #p_active_filter, #pc_id_filter, #psc_id_filter, #pssc_id_filter')
            .on('change', function() {
                product_table.draw();
            });

        {{-- $('#pc_id').on('change', function() { --}}
        {{--    var label = $('#pc_id option:selected').text(); --}}
        {{--    var pc_id = $('#pc_id').val(); --}}
        {{--    if (label == 'Tampilkan Semua') { --}}
        {{--        // $('#product_display').fadeIn(); --}}
        {{--        $('#product_category_selected_label').text('Tampilkan Semua'); --}}
        {{--        $('#psc_id').html("<select class='form-control' id='psc_id' name='psc_id' required><option value=''>- Pilih Sub Kategori -</option></select>"); --}}
        {{--        $('#pssc_id').html("<select class='form-control' id='pssc_id' name='pssc_id' required><option value=''>- Pilih Sub-Sub Kategori -</option></select>"); --}}
        {{--        product_table.draw(); --}}
        {{--        return false; --}}
        {{--    } else if (label != '- Pilih Kategori -' && label != 'Tampilkan Semua') { --}}
        {{--        $('#product_category_selected_label').text(label+' |'); --}}
        {{--        $.ajax({ --}}
        {{--            type: "GET", --}}
        {{--            data: {_pc_id:pc_id}, --}}
        {{--            dataType: 'html', --}}
        {{--            url: "{{ url('reload_product_sub_category')}}", --}}
        {{--            success: function(r) { --}}
        {{--                $('#psc_id').html(r); --}}
        {{--            } --}}
        {{--        }); --}}
        {{--    } else { --}}
        {{--        $('#product_category_selected_label').text(''); --}}
        {{--        $('#psc_id').html("<select class='form-control' id='psc_id' name='psc_id' required><option value=''>- Pilih Sub Kategori -</option></select>"); --}}
        {{--    } --}}
        {{--    $('#pssc_id').html("<select class='form-control' id='pssc_id' name='pssc_id' required><option value=''>- Pilih Sub-Sub Kategori -</option></select>"); --}}
        {{--    $('#product_sub_category_selected_label').text(''); --}}
        {{--    // $('#product_display').fadeOut(); --}}
        {{-- }); --}}

        {{-- $('#psc_id').on('change', function() { --}}
        {{--    var label = $('#psc_id option:selected').text(); --}}
        {{--    var pc_id = $('#pc_id').val(); --}}
        {{--    var psc_id = $('#psc_id').val(); --}}
        {{--    if (label != '- Pilih Sub Kategori -' && label != '- Pilih -') { --}}
        {{--        $('#product_sub_category_selected_label').text(label+' |'); --}}
        {{--        $.ajax({ --}}
        {{--            type: "GET", --}}
        {{--            data: {_psc_id:psc_id}, --}}
        {{--            dataType: 'html', --}}
        {{--            url: "{{ url('reload_product_sub_sub_category')}}", --}}
        {{--            success: function(r) { --}}
        {{--                $('#pssc_id').html(r); --}}
        {{--            } --}}
        {{--        }); --}}
        {{--        $.ajax({ --}}
        {{--            type: "GET", --}}
        {{--            data: {_psc_id:psc_id}, --}}
        {{--            dataType: 'html', --}}
        {{--            url: "{{ url('reload_size')}}", --}}
        {{--            success: function(r) { --}}
        {{--                $('#reload_size').html(r); --}}
        {{--            } --}}
        {{--        }); --}}
        {{--    } else { --}}
        {{--        $('#product_sub_category_selected_label').text(''); --}}
        {{--        $('#pssc_id').html("<select class='form-control' id='pssc_id' name='pssc_id' required><option value=''>- Pilih Sub-Sub Kategori -</option></select>"); --}}
        {{--        // $('#product_display').fadeOut(); --}}
        {{--    } --}}
        {{--    // $('#product_display').fadeOut(); --}}
        {{-- }); --}}

        // $('#pssc_id').on('change', function() {
        //     var label = $('#pssc_id option:selected').text();
        //     if (label != '- Pilih Sub-Sub Kategori -') {
        //         $('#product_sub_sub_category_selected_label').text(label);
        //         // $('#product_display').fadeIn();
        //         product_table.draw();
        //     } else {
        //         $('#product_sub_sub_category_selected_label').text('');
        //         // $('#product_display').fadeOut();
        //     }
        // });

        {{-- $('#_pc_id').on('change', function() { --}}
        {{--    var pc_id = $('#_pc_id').val(); --}}
        {{--    $('#_psc_id').prop('disabled', false); --}}
        {{--    $('#_psc_id').html("<select class='form-control' id='psc_id' name='psc_id' required><option value=''>- Pilih Sub Kategori -</option></select>"); --}}
        {{--    $('#_pssc_id').html("<select class='form-control' id='pssc_id' name='pssc_id' required><option value=''>- Pilih Sub-Sub Kategori -</option></select>"); --}}
        {{--    $.ajax({ --}}
        {{--        type: "GET", --}}
        {{--        data: {_pc_id:pc_id}, --}}
        {{--        dataType: 'html', --}}
        {{--        url: "{{ url('reload_product_sub_category')}}", --}}
        {{--        success: function(r) { --}}
        {{--            $('#_psc_id').html(r); --}}
        {{--        } --}}
        {{--    }); --}}
        {{-- }); --}}

        {{-- $('#_psc_id').on('change', function() { --}}
        {{--    var psc_id = $('#_psc_id').val(); --}}
        {{--    $('#_pssc_id').prop('disabled', false); --}}
        {{--    $('#_pssc_id').html("<select class='form-control' id='pssc_id' name='pssc_id' required><option value=''>- Pilih Sub-Sub Kategori -</option></select>"); --}}
        {{--    $.ajax({ --}}
        {{--        type: "GET", --}}
        {{--        data: {_psc_id:psc_id}, --}}
        {{--        dataType: 'html', --}}
        {{--        url: "{{ url('reload_product_sub_sub_category')}}", --}}
        {{--        success: function(r) { --}}
        {{--            $('#_pssc_id').html(r); --}}
        {{--        } --}}
        {{--    }); --}}
        {{-- }); --}}

        $('#pc_id_filter').select2({
            width: "300px",
            dropdownParent: $('#pc_id_filter_parent')
        });

        $('#pc_id_filter').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#psc_id_filter').select2({
            width: "300px",
            dropdownParent: $('#psc_id_filter_parent')
        });

        $('#psc_id_filter').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#pssc_id_filter').select2({
            width: "300px",
            dropdownParent: $('#pssc_id_filter_parent')
        });

        $('#pssc_id_filter').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#sz_schema_id').select2({
            width: "300px",
            dropdownParent: $('#sz_schema_id_parent')
        });

        $('#sz_schema_id').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#sz_schema_id').on('change', function(e) {
            // Get the selected value in sz_schema_id
            var selectedValue = $(this).val();

            updateSzIdFilterOptions(selectedValue);
        });

        $('#br_id_filter').select2({
            width: "130px",
            dropdownParent: $('#br_id_filter_parent')
        });
        $('#br_id_filter').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#ps_id_filter').select2({
            width: "130px",
            dropdownParent: $('#ps_id_filter_parent')
        });
        $('#ps_id_filter').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#sz_id_filter').select2({
            width: "130px",
            dropdownParent: $('#sz_id_filter_parent')
        });
        $('#sz_id_filter').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#p_active_filter').select2({
            width: "130px",
        });


        $('#mc_id_filter').select2({
            width: "130px",
            dropdownParent: $('#mc_id_filter_parent')
        });
        $('#mc_id_filter').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#br_id').select2({
            width: "100%",
            dropdownParent: $('#br_id_parent')
        });
        $('#br_id').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#gn_id').select2({
            width: "100%",
            dropdownParent: $('#gn_id_parent')
        });
        $('#gn_id').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#ss_id').select2({
            width: "100%",
            dropdownParent: $('#ss_id_parent')
        });
        $('#ss_id').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#ps_id').select2({
            width: "100%",
            dropdownParent: $('#ps_id_parent')
        });
        $('#ps_id').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#pu_id').select2({
            width: "100%",
            dropdownParent: $('#pu_id_parent')
        });
        $('#pu_id').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#mc_id').select2({
            width: "100%",
            dropdownParent: $('#mc_id_parent')
        });
        $('#mc_id').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#sz_schema_modal_id').select2({
            width: "100%",
            dropdownParent: $('#sz_schema_modal_id_parent')
        });
        $('#sz_schema_modal_id').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#import_modal_btn').on('click', function() {
            jQuery.noConflict();
            $('#ImportModal').modal('show');
            $('#f_import')[0].reset();
        });

        $('#mass_update_product').on('click', function() {
            jQuery.noConflict();
            $('#MassUpdateModal').modal('show');
            $('#f_mass_update')[0].reset();
        });

        {{-- $('#sz_schema_modal_id').on('change', function (e) { --}}
        {{--    // Get the selected value in sc_schema_modal_id --}}
        {{--    var selectedValue = $(this).val(); --}}

        {{--    $.ajax({ --}}
        {{--        type: "GET", --}}
        {{--        data: {_sz_schema:selectedValue}, --}}
        {{--        dataType: 'html', --}}
        {{--        url: "{{ url('reload_size_schema_modal')}}", --}}
        {{--        success: function(r) { --}}
        {{--            $('#reload_size').html(r); --}}
        {{--            // checkSize(selectedValue); --}}
        {{--        } --}}
        {{--    }); --}}
        {{-- }); --}}

        function checkSize(id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {
                    _p_id: id
                },
                dataType: 'json',
                url: "{{ url('check_product_stock') }}",
                success: function(r) {
                    console.log("id checksize : " + id);
                    if (r.data != '400') {
                        var _sz_barcode_edit = '';
                        $.each($(r.data), function(key, value) {
                            //alert(value.sz_id+' '+value.ps_barcode+' '+value.ps_running_code);
                            _sz_barcode_edit = _sz_barcode_edit + value.sz_id + '-' + value
                                .ps_barcode + '|';
                            $('input[type="checkbox"][data-id="' + value.sz_id + '"]').prop(
                                'checked', true);
                            $('input[type="checkbox"][data-id="' + value.sz_id + '"]').prop(
                                'disabled', true);
                            $('input[type="text"][data-barcode="' + value.sz_id + '"]')
                                .show();
                            $('input[type="text"][data-running-code="' + value.sz_id + '"]')
                                .show();
                            $('input[type="text"][data-running-code="' + value.sz_id +
                                '"]').val(value.psid);
                            $('input[type="text"][data-barcode="' + value.sz_id + '"]')
                                .prop('disabled', true);
                            $('input[type="text"][data-barcode="' + value.sz_id + '"]').val(
                                value.ps_barcode);
                            $('input[type="number"][data-purchase-price="' + value.sz_id +
                                '"]').show();
                            $('input[type="number"][data-purchase-price="' + value.sz_id +
                                '"]').val(value.ps_purchase_price);
                            $('input[type="number"][data-sell-price="' + value.sz_id + '"]')
                                .show();
                            $('input[type="number"][data-sell-price="' + value.sz_id + '"]')
                                .val(value.ps_sell_price);
                            $('input[type="number"][data-price-tag="' + value.sz_id + '"]')
                                .show();
                            $('input[type="number"][data-price-tag="' + value.sz_id + '"]')
                                .val(value.ps_price_tag);
                            if ($('input[type="text"][data-barcode="' + value.sz_id + '"]')
                                .val() == '') {
                                $('input[type="text"][data-barcode="' + value.sz_id + '"]')
                                    .prop('disable d', false);
                            } else {
                                $('input[type="text"][data-barcode="' + value.sz_id + '"]')
                                    .prop('disabled', true);
                            }
                            $('input[type="number"][data-running-code="' + value.sz_id +
                                '"]').show();
                            $('input[type="text"][data-running-code="' + value.sz_id +
                                '"]').prop('disabled', true);

                        });
                        //alert(_sz_barcode_edit);
                        $('#_sz_barcode_edit').val(_sz_barcode_edit);
                    }
                }
            });
        }

        function handleSchemaChange(selectedValue, id, psc_id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "GET",
                data: {
                    _sz_schema: selectedValue,
                    _id: id,
                    _psc_id: psc_id
                }, // Pass id as data parameter
                dataType: 'html',
                url: "{{ url('reload_size_schema_modal') }}",
                success: function(r) {
                    $('#reload_size').html(r);
                    console.log('schema id : ' + id); // Now id should be accessible here
                    checkSize(id);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }


        //producttb 
        $('#Producttb tbody').on('click', 'tr td:not(:nth-child(11))', function() {
            jQuery('#product_qr').empty();
            $('#f_product')[0].reset();
            $('#_sz_barcode').val('');
            $('#_sz_id').val('');
            $('#_sz_sell_price').val('');
            $('input[type="checkbox"]').prop('disabled', false);
            $('.psb_hidden').hide();
            $('#_pc_id').prop('disabled', false);
            var product_label = $('#product_category_component').text();
            var id = product_table.row(this).data().pid;
            var p_name = product_table.row(this).data().p_name;
            var article_id = product_table.row(this).data().article_id;
            var p_description = product_table.row(this).data().p_description;
            var p_aging = product_table.row(this).data().p_aging;
            var p_color = product_table.row(this).data().p_color;
            var p_price_tag = product_table.row(this).data().p_price_tag;
            var p_purchase_price = product_table.row(this).data().p_purchase_price;
            var p_sell_price = product_table.row(this).data().p_sell_price;
            var p_weight = product_table.row(this).data().p_weight;
            var pc_id = product_table.row(this).data().pc_id;
            var psc_id = product_table.row(this).data().psc_id;
            var pssc_id = product_table.row(this).data().pssc_id;
            var br_id = product_table.row(this).data().br_id;
            var ps_id = product_table.row(this).data().ps_id;
            var pu_id = product_table.row(this).data().pu_id;
            var gn_id = product_table.row(this).data().gn_id;
            var ss_id = product_table.row(this).data().ss_id;
            var mc_id = product_table.row(this).data().mc_id;
            var schema_size = product_table.row(this).data().schema_size;
            var subcategory1 = product_table.row(this).data().subcategory1;
            var subcategory2 = product_table.row(this).data().subcategory2;
            var mp_best_seller = product_table.row(this).data().mp_best_seller;
            var mp_stock_masking = product_table.row(this).data().mp_stock_masking;
            var complement = product_table.row(this).data().complement;
            var consignment = product_table.row(this).data().consignment;
            var check_pc_id = $('#pc_id').val();
            var is_everlast = product_table.row(this).data().is_everlast;
            var is_supersale = product_table.row(this).data().is_supersale;
            var is_reguler = product_table.row(this).data().is_reguler;
            var p_turnoverclass = product_table.row(this).data().p_turnoverclass;

            console.log(product_table.row(this).data())
            console.log(subcategory1)
            if (check_pc_id == 'all') {
                $('#category_arrow_label').hide();
            } else {
                $('#category_arrow_label').show();
                // console.log('id : '+id);
                // checkSize(id);
            }

            if ($.trim(p_description) != '') {
                tinyMCE.get('p_description').setContent(p_description);
            }

            $('#_pc_id').val(pc_id);
            $.ajax({
                type: "GET",
                dataType: 'html',
                url: "{{ url('psc_reload') }}",
                success: function(r) {
                    $('#_psc_id').html(r);
                    $('#_psc_id').val(psc_id);
                    // $('#_psc_id').prop('disabled', true);
                }
            });
            $.ajax({
                type: "GET",
                dataType: 'html',
                url: "{{ url('pssc_reload') }}",
                success: function(r) {
                    $('#_pssc_id').html(r);
                    $('#_pssc_id').val(pssc_id);
                    // $('#_pssc_id').prop('disabled', true);
                }
            });
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {
                    _p_id: id
                },
                dataType: 'json',
                url: "{{ url('check_product_po') }}",
                success: function(r) {
                    if (r.status == '200') {
                        $('#_pc_id').prop('disabled', true);
                    }
                }
            });



            // $('#sz_schema_modal_id').on('change', function (e) {
            //     // Get the selected value in sc_schema_modal_id
            //     var selectedValue = $('#sz_schema_modal_id').val();
            //     handleSchemaChange(selectedValue, article_id);
            // });
            handleSchemaChange(schema_size, id, psc_id);
            jQuery.noConflict();
            $('#ProductModal').modal('show');
            $('#product_label_modal').text(product_label);
            $('#pcpscpssc_edit').show();
            $('#barcode_running_label').show();


            // Dekode HTML entities pada p_name
            function decodeHtmlEntity(str) {
                const textArea = document.createElement('textarea');
                textArea.innerHTML = str;
                return textArea.value;
            }
            $('#p_name').val(decodeHtmlEntity(p_name));
            $('#article_id').val(article_id);
            $('#p_aging').val(p_aging);
            $('#p_color').val(p_color);
            $('#p_price_tag').val(p_price_tag);
            $('#p_purchase_price').val(p_purchase_price);
            $('#p_sell_price').val(p_sell_price);
            $('#p_weight').val(p_weight);
            $('#_current_pc_id').val(pc_id);
            $('#_current_psc_id').val(psc_id);
            $('#_current_pssc_id').val(pssc_id);
            $('#subcatone').text(subcategory1);
            $('#subcattwo').text(subcategory2);
            if (consignment == '1') {
                $('#consignment').prop('checked', true);
            } else {
                $('#consignment').prop('checked', false);
            }

            if (complement == '1') {
                $('#complement').prop('checked', true);
            } else {
                $('#complement').prop('checked', false);
            }

            if (mp_best_seller == '1') {
                $('#mp_best_seller').prop('checked', true);
            } else {
                $('#mp_best_seller').prop('checked', false);
            }

            if (mp_stock_masking == '1') {
                $('#mp_stock_masking').prop('checked', true);
            } else {
                $('#mp_stock_masking').prop('checked', false);
            }

            if (is_everlast == '1') {
                $('#is_everlast').prop('checked', true);
            } else {
                $('#is_everlast').prop('checked', false);
            }

            if (is_supersale == '1') {
                $('#is_supersale').prop('checked', true);
            } else {
                $('#is_supersale').prop('checked', false);
            }

            if (is_reguler == '1') {
                $('#is_reguler').prop('checked', true);
            } else {
                $('#is_reguler').prop('checked', false);
            }

            $('#p_turnoverclass').val(p_turnoverclass);
            jQuery('#br_id').val(br_id).trigger('change');
            jQuery('#ps_id').val(ps_id).trigger('change');
            jQuery('#pu_id').val(pu_id).trigger('change');
            jQuery('#gn_id').val(gn_id).trigger('change');
            jQuery('#ss_id').val(ss_id).trigger('change');
            jQuery('#mc_id').val(mc_id).trigger('change');
            jQuery('#sz_schema_modal_id').val(schema_size).trigger('change');
            $('#_id').val(id);
            $('#_mode').val('edit');
            @if ($data['user']->delete_access == '1')
                $('#delete_product_btn').show();
            @endif
            generateQR(article_id);
        });

        $('#article_id').on('change', function() {
            var article_id = $(this).val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {
                    _article_id: article_id
                },
                dataType: 'json',
                url: "{{ url('check_exists_article_id') }}",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Kode',
                            'Article ID sudah ada disistem, silahkan ganti dengan yang lain',
                            'warning');
                        $('#article_id').val('');
                        return false;
                    }
                }
            });
        });

        $('#add_product_btn').on('click', function(e) {
            e.preventDefault();
            jQuery('#product_qr').empty();
            var check_pc_id = $('#pc_id').val();
            if (check_pc_id == 'all') {
                swal('Kategori',
                    'Silahkan tentukan kategori, sub kategori dan sub-sub kategori terlebih dahulu untuk menambah data',
                    'warning');
                return false;
            }
            jQuery.noConflict();
            $('#ProductModal').modal('show');
            $('#category_arrow_label').show();
            $('#pcpscpssc_edit').show();
            var product_label = $('#product_category_component').text();
            $('#product_label_modal').text(product_label);
            $('input[type="checkbox"]').prop('disabled', false);
            $('input[type="checkbox"]').prop('disabled', false);
            $('#_id').val('');
            $('#_mode').val('add');
            $('#_sz_barcode').val('');
            $('#_sz_id').val('');
            $('#_sz_sell_price').val('');
            $('#f_product')[0].reset();
            $('.psb_hidden').hide();
            $('#barcode_running_label').hide();
            $('#delete_product_btn').hide();
            jQuery('#br_id').val('').trigger('change');
            jQuery('#ps_id').val('').trigger('change');
            jQuery('#pu_id').val('').trigger('change');
            jQuery('#gn_id').val('').trigger('change');
            jQuery('#ss_id').val('').trigger('change');
            jQuery('#mc_id').val('').trigger('change');
            jQuery('#sz_schema_modal_id').val('').trigger('change');
            $('#_pc_id').prop('required', false);
            $('#_psc_id').prop('required', false);
            $('#_pssc_id').prop('required', false);
        });

        $(document).delegate('#product_detail_btn', 'click', function() {
            var id = $(this).attr('data-id');
            var article = $(this).attr('data-article');
            jQuery.noConflict();
            $('#ProductDetailModal').modal('show');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {
                    _id: id,
                    _art: article
                },
                dataType: 'html',
                url: "{{ url('product_detail') }}",
                success: function(r) {
                    $('#productDetailContent').html(r);
                    $.ajax({
                        type: "POST",
                        data: {
                            _p_id: id,
                            _art: article
                        },
                        dataType: 'json',
                        url: "{{ url('check_product_stock') }}",
                        success: function(r) {
                            if (r.data != '400') {
                                $.each($(r.data), function(key, value) {
                                    $('#ProductStockDetailtb tr:last')
                                        .after(
                                            "<tr id='ProductStockDetailAppend'><td>" +
                                            value.sz_name +
                                            "</td><td>" + value.qty +
                                            "</td><td>" + value
                                            .ps_barcode + "</td><td>" +
                                            formatToRupiah(value
                                                .ps_price_tag) +
                                            "</td></tr>");
                                });
                            } else {
                                $('#ProductStockDetailAppend').remove();
                            }
                        }
                    });
                }
            });
        });

        function formatToRupiah(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value);
        }

        $('#close_import_btn').on('click', function() {
            $("#import_data_btn").html('Import');
            $("#import_data_btn").attr("disabled", false);
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            $("#import_data_btn").html('Proses ..');
            $("#import_data_btn").attr("disabled", true);
            var formData = new FormData(this);

            $.ajax({
                type: 'POST',
                url: "{{ url('p_import') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#import_data_btn").html('Import');
                    $("#import_data_btn").attr("disabled", false);
                    jQuery.noConflict();

                    if (data.status == '200') {
                        $("#ImportModal").modal('hide');
                        toastr.success('Data berhasil diimpor', 'Berhasil');
                        $('#f_import')[0].reset();
                        product_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#ImportModal").modal('hide');

                        if (data.error_messages && data.error_messages.length > 0) {
                            var errorMessageHtml = '';
                            data.error_messages.forEach(function(message) {
                                errorMessageHtml += message + '<br>';
                            });

                            toastr.warning(
                                'File yang Anda impor kosong atau format tidak tepat:<br>' +
                                errorMessageHtml, 'Peringatan');
                        } else {
                            toastr.warning(
                                'File yang Anda impor kosong atau format tidak tepat',
                                'Peringatan');
                        }

                    } else {
                        if (data.same_article_id.length > 0) {
                            var same_article_id = data.same_article_id.join(', ');
                            toastr.warning('Article ID ' + same_article_id +
                                ' sudah ada di sistem, silahkan ganti dengan yang lain',
                                'Peringatan');
                            return false;
                        }

                        $("#ImportModal").modal('hide');
                        toastr.warning(
                            'Silahkan periksa format input pada template Anda, pastikan kolom biru terisi sesuai dengan sistem',
                            'Peringatan');
                    }
                },
                error: function(data) {
                    console.log(data);
                    toastr.error('Terjadi kesalahan saat mengimpor data', 'Error');
                }
            });
        });

        $('#f_mass_update').on('submit', function(e) {
            e.preventDefault();
            $("#import_data_btn").html('Proses ..');
            $("#import_data_btn").attr("disabled", true);
            var formData = new FormData(this);

            $.ajax({
                type: 'POST',
                url: "{{ url('mass_update_product') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#import_data_btn").html('Import');
                    $("#import_data_btn").attr("disabled", false);
                    jQuery.noConflict();

                    if (data.status == '200') {
                        $("#ImportModal").modal('hide');
                        toastr.success('Data berhasil diimpor', 'Berhasil');
                        $('#f_import')[0].reset();
                        product_table.ajax.reload();

                    } else {

                        // Display error IDs in a table using Swal
                        if (data.error_ids && data.error_ids.length > 0) {
                            var errorIds = data.error_ids;
                            Swal.fire({
                                title: 'Error IDs',
                                html: `
                                    <div style="overflow-x:auto;">
                                        <table class="table" style="width:100%; text-align:left; border-collapse: collapse;">
                                            <thead>
                                                <tr>
                                                    <th style="border: 1px solid #ccc; padding: 8px;">Error ID</th>
                                                </tr>
                                            </thead>
                                            <tbody id="error-table-body">
                                                <!-- Data masuk sini -->
                                            </tbody>
                                        </table>
                                        <br/>
                                        <div style="text-align: center;">
                                            <button id="export_error_ids" class="swal2-confirm swal2-styled" style="background-color:#28a745; margin-right:10px;">Export to Excel</button>
                                            <button id="close_error_alert" class="swal2-cancel swal2-styled" style="background-color:#dc3545;">Close</button>
                                        </div>
                                    </div>
                                `,
                                icon: 'warning',
                                showConfirmButton: false,
                                didOpen: () => {
                                    let tbody = document.getElementById('error-table-body');
                                    errorIds.forEach(function(id) {
                                        let row = document.createElement('tr');
                                        row.innerHTML = `
                                            <td style="border: 1px solid #ccc; padding: 8px;">${id || '-'}</td>
                                        `;
                                        tbody.appendChild(row);
                                    });

                                    // Export to Excel button
                                    document.getElementById('export_error_ids')
                                        .addEventListener('click', function() {
                                            let wb = XLSX.utils.book_new();
                                            let ws_data = [
                                                ["Error ID"], // Header
                                                ...errorIds.map(id => [id]) // Each ID in its own array
                                            ];
                                            let ws = XLSX.utils.aoa_to_sheet(ws_data);
                                            XLSX.utils.book_append_sheet(wb, ws, "Error IDs");
                                            XLSX.writeFile(wb, "Error_IDs.xlsx");
                                        });

                                    // Close button
                                    document.getElementById('close_error_alert')
                                        .addEventListener('click', function() {
                                            Swal.close();
                                        });
                                }
                            });
                        }
                    }
                    
                },
                error: function(data) {
                    console.log(data);
                    toastr.error('Terjadi kesalahan saat mengimpor data', 'Error');
                }
            });
        });


        $('#f_product').on('submit', function(e) {
            e.preventDefault();
            $("#save_product_btn").html('Proses ..');
            $("#save_product_btn").attr("disabled", true);
            var formData = new FormData(this);
            var check_pc_id = $('#pc_id').val();

            if (check_pc_id == 'all') {
                if ($('#_pc_id').val() == '') {
                    swal('Kategori', 'Silahkan tentukan kategori terlebih dahulu', 'warning');
                    return false;
                } else if ($('#_psc_id').val() == '') {
                    swal('Sub Kategori', 'Silahkan tentukan sub kategori terlebih dahulu', 'warning');
                    return false;
                } else if ($('#_pssc_id').val() == '') {
                    swal('Sub-Sub Kategori', 'Silahkan tentukan sub-sub kategori terlebih dahulu',
                        'warning');
                    return false;
                }
            } else {
                formData.append('pc_id', $('#_pc_id').val());
                formData.append('psc_id', $('#_psc_id').val());
                formData.append('pssc_id', $('#_pssc_id').val());
            }

            $.ajax({
                type: 'POST',
                url: "{{ url('p_save') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_product_btn").html('Simpan');
                    $("#save_product_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#ProductModal").modal('hide');

                        swal('Berhasil', 'Data berhasil disimpan', 'success');

                        toastr.success('Data berhasil disimpan', 'Berhasil');

                        console.log(data.consignment);
                        console.log(data.complement);
                        product_table.draw(false);
                    } else if (data.status == '400') {
                        $("#ProductModal").modal('hide');
                        toastr.warning('Data tidak tersimpan', 'Gagal');
                    } else {
                        $("#ProductModal").modal('hide');
                        toastr.warning(
                            'Data gagal disimpan karena ada perubahan data yang terikat ke suatu pencatatan transaksi',
                            'Relationship');
                    }
                },
                error: function(data) {
                    $("#save_product_btn").html('Simpan');
                    $("#save_product_btn").attr("disabled", false);
                    toastr.error('Terjadi kesalahan saat menyimpan data', 'Error');
                }
            });
        });


        $('#delete_product_btn').on('click', function() {
            swal({
                title: "Hapus..?",
                text: "Yakin hapus data ini?",
                icon: "warning",
                buttons: [
                    'Batalkan',
                    'Hapus'
                ],
                dangerMode: true,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {
                            _id: $('#_id').val()
                        },
                        dataType: 'json',
                        url: "{{ url('p_delete') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                toastr.success("Data berhasil dihapus", "Berhasil");
                                $('#ProductModal').modal('hide');
                                product_table.ajax.reload();
                            } else {
                                toastr.error('Gagal hapus data', 'Gagal');
                            }
                        },
                        error: function() {
                            toastr.error('Terjadi kesalahan saat menghapus data',
                                'Error');
                        }
                    });
                }
            });
        });


        var product = $('#Ptb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs'
            }],
            ajax: {
                url: "{{ url('scan_adjustment_product_datatables') }}",
                data: function(d) {
                    d.search = $('#p_search').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'id',
                    searchable: false
                },
                {
                    data: 'br_name',
                    name: 'br_name'
                },
                {
                    data: 'p_name',
                    name: 'p_name'
                },
                {
                    data: 'p_color',
                    name: 'p_color'
                },
                {
                    data: 'sz_name',
                    name: 'sz_name'
                },
                {
                    data: 'ps_barcode_show',
                    name: 'ps_barcode'
                },
            ],
            columnDefs: [{
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [
                [0, 'desc']
            ],
        });

        $('#p_search').on('keyup', function() {
            product.draw(false);
        });

        $(document).delegate('#product_barcode_btn', 'click', function(e) {
            e.preventDefault();
            jQuery.noConflict();
            $('#ProductBarcodeModal').modal('show');
            product.draw(false);
        });

        $(document).delegate('#input_barcode', 'change', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            var barcode = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {
                    id: id,
                    barcode: barcode
                },
                dataType: 'json',
                url: "{{ url('scan_adjustment_barcode_update') }}",
                success: function(r) {
                    if (r.status == '200') {
                        toastr.success("Data berhasil diupdate", "Berhasil");
                        product.draw(false);
                    } else {
                        toastr.error('Gagal update data', 'Gagal');
                    }
                },
                error: function() {
                    toastr.error('Terjadi kesalahan saat memperbarui data', 'Error');
                }
            });
            return false;
        });




    });
</script>
