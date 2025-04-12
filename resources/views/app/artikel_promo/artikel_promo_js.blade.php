<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var data_article_promo_tb = '';
        data_article_promo_tb = $('#ArtikelPromotb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs'
            }],
            ajax: {
                url: "{{ url('artikel_promo_datatables') }}",
                data: function (d) {
                    d.search = $('#artikel_promo_search').val();
                }
            },
            columns: [{
                data: 'DT_RowIndex',
                name: 'a_id',
                searchable: false
            },
                {
                    data: 'article_id',
                    name: 'article_id'
                },
                {
                    data: 'p_name',
                    name: 'p_name'
                },
                {
                    data: 'st_code',
                    name: 'st_code'
                },
                {
                    data: 'promo_name',
                    name: 'promo_name'
                },
                {
                    data: 'date_start',
                    name: 'date_start'
                },
                {
                    data: 'date_end',
                    name: 'date_end'
                },
                {
                    data: 'promo_disc',
                    name: 'promo_disc'
                },
                {
                    data: 'p_price_tag',
                    name: 'p_price_tag'
                },
                {
                    data: 'price_discount',
                    name: 'price_discount'
                },
                {
                    data: 'promo_note',
                    name: 'promo_note'
                },
            ],
            columnDefs: [{
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
            order: [
                [0, 'desc']
            ],
        });

        data_article_promo_tb.buttons().container().appendTo($('#artikel_promo_excel_btn'));
        $('#artikel_promo_search').on('keyup', function () {
            articles_promo_table.draw();
        });

        $('#f_import').on('submit', function (e) {
            // console.log('jkasdaksjd');
            e.preventDefault();
            $('#import_data_btn').html('Proses...');
            $('#import_data_btn').attr('disabled', true);
            var formData = new FormData(this);

            $.ajax({
                type: 'POST',
                url: "{{ url('artikel_promo_import') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $("#import_data_btn").html('Import');
                    $("#import_data_btn").attr("disabled", false);
                    jQuery.noConflict();

                    if (data.status == '200') {
                        $("#ImportModal").modal('hide');
                        toastr.success('Data berhasil diimport', 'Berhasil');
                        $('#f_import')[0].reset();

                        // start_bin_table.draw();
                        data_article_promo_tb.ajax.reload();
                        excelImportData = data.data['processedData'];

                        checkMissingBarcode(data.data['missingBarcode']);

                    } else if (data.status == '400') {
                        $("#ImportModal").modal('hide');
                        toastr.warning('File yang anda import kosong atau format tidak tepat', 'File');
                    } else {
                        $("#ImportModal").modal('hide');
                        toastr.error('Terjadi kesalahan saat memproses file', 'Error');
                    }
                },
                error: function (data) {
                    toastr.error('An error occurred while processing your request', 'Error');
                }
            });
        });

        $('#ArtikelPromotb tbody').on('click', '.artikel-promo-row', function() {
            var data = data_articles_promo_tb.row(this).data();
            if (!data) return;

            jQuery.noConflict();
            $('#ArtikelPromo').modal('show');
            $('#p_id').val(p_id);
            $('#st_id').val(st_id);
            $('#promo_name').val(promo_name);
            $('#date_start').val(date_start);
            $('#date_end').val(date_end);
            $('#promo_disc').val(promo_disc);
            $('#promo_note').val(promo_note);
            $('#_id').val(id);
            $('#_mode').val('edit');

            @if ($data['user']->delete_access == '1')
                $('#delete_artikel_promo_btn').show();
            @endif
        });


        // $('#dp_name').on('change', function() {
        //     var dp_name_name = $(this).val();
        //     $.ajaxSetup({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         }
        //     });
        //     $.ajax({
        //         type: "POST",
        //         data: {
        //             _dp_name: dp_name
        //         },
        //         dataType: 'json',
        //         url: "{{ url('check_exists_data_perusahaan') }}",
        //         success: function(r) {
        //             if (r.status == '200') {
        //                 swal('Perusahaan',
        //                     'Data Perusahaan sudah ada disistem, silahkan ganti dengan yang lain',
        //                     'warning');
        //                 $('#dp_name').val('');
        //                 return false;
        //             }
        //         }
        //     });
        // });

        $('#add_artikel_promo_btn').on('click', function () {
            jQuery.noConflict();
            $('#ArtikelPromoModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_artikel_promo')[0].reset();
            $('#delete_artikel_promo_btn').hide();
        });


        //button import


        $('#delete_artikel_promo_btn').on('click', function () {
            swal({
                title: "Hapus..?",
                text: "Yakin hapus data ini?",
                icon: "warning",
                buttons: [
                    'Batalkan',
                    'Hapus'
                ],
                dangerMode: true,
            }).then(function (isConfirm) {
                if (isConfirm) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {
                            _id: $('#_id').val(),
                            _item: $('#ap_name').val()
                        },
                        dataType: 'json',
                        url: "{{ url('ap_delete') }}",
                        success: function (r) {
                            if (r.status == '200') {
                                $('#artikel_promoModal').modal('hide');
                                toastr.success(
                                    'Data berhasil dihapus'); // Change to Toastr success message
                                articles_promo_table.ajax.reload();
                            } else {
                                toastr.error(
                                    'Gagal hapus data'); // Change to Toastr error message
                            }
                        },
                        error: function () {
                            toastr.error(
                                'Terjadi kesalahan saat menghapus data'); // Toastr error for AJAX error
                        }
                    });
                    return false;
                }
            });
        });

        $(document).ready(function () {
            // Open the second modal when the button is clicked
            $("#ImportModalBtn").click(function () {
                $("#ImportModal").modal("show");
            });
        });

    });
</script>