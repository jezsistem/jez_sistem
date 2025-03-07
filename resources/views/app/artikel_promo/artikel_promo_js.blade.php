<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var data_perusahaan_table = $('#ArtikelPromotb').DataTable({
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
                data: function(d) {
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
                    data: 'start_date',
                    name: 'start_date'
                },
                {
                    data: 'end_date',
                    name: 'end_date'
                },
                {
                    data: 'promo_type',
                    name: 'promo_type'
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

        articles_promo_table.buttons().container().appendTo($('#artikel_promo_excel_btn'));
        $('#artikel_promo_search').on('keyup', function() {
            articles_promo_table.draw();
        });

        $('#ArtikelPromotb tbody').on('click', 'tr', function() {
            var id = articles_promo_table.row(this).data().id;
            var p_id = articles_promo_table.row(this).data().p_id;
            var st_id = articles_promo_table.row(this).data().st_id;
            var promo_name = articles_promo_table.row(this).data().promo_name;
            var start_date = articles_promo_table.row(this).data().start_date;
            var end_date = articles_promo_table.row(this).data().end_date;
            var promo_type = articles_promo_table.row(this).data().promo_type;
            var promo_disc = articles_promo_table.row(this).data().promo_disc;
            var promo_note = articles_promo_table.row(this).data().promo_note;
            jQuery.noConflict();
            $('#ArtikelPromoModal').modal('show');
            $('#p_id').val(p_id);
            $('#st_id').val(st_id);
            $('#promo_name').val(promo_name);
            $('#start_date').val(start_date);
            $('#end_date').val(end_date);
            $('#promo_type').val(promo_type);
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

        $('#add_artikel_promo_btn').on('click', function() {
            jQuery.noConflict();
            $('#ArtikelPromoModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_artikel_promo')[0].reset();
            $('#delete_artikel_promo_btn').hide();
        });

        $('#f_import').on('submit', function(e) {
    e.preventDefault();
    $("#import_data_btn").html('Proses ..');
    $("#import_data_btn").attr("disabled", true);

    var p_id = $('#import_p_id').val();
    var st_id = $('#import_st_id').val();

    if (p_id == '') {
        swal('Produk', 'Produk kosong, silahkan diisi dulu', 'warning');
        $("#import_data_btn").html('Import').attr("disabled", false);
        return false;
    }
    if (st_id == '') {
        swal('Store', 'Store kosong, silahkan diisi dulu', 'warning');
        $("#import_data_btn").html('Import').attr("disabled", false);
        return false;
    }

    var formData = new FormData(this);
    formData.append('_p_id', p_id);
    formData.append('_st_id', st_id);

    $.ajax({
        type: 'POST',
        url: "{{ url('artikel_promo_import') }}",
        data: formData,
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {
            console.log(data);
            $("#import_data_btn").html('Import').attr("disabled", false);
            jQuery.noConflict();

            if (data.status == '200') {
                $("#ImportModal").modal('hide');
                swal('Berhasil', 'Data berhasil diimport', 'success');
                $('#f_import_artikelpromo')[0].reset();
                articles_promo_table.ajax.reload();
            } else if (data.status == '400') {
                $("#ImportModal").modal('hide');
                swal('Gagal', 'Data gagal diimport', 'warning');
            }
        },
        error: function(xhr, status, error) {
            swal('Error', 'Terjadi kesalahan: ' + xhr.responseText, 'error');
            $("#import_data_btn").html('Import').attr("disabled", false);
        }
    });
});

        $('#f_artikel_promo').on('submit', function(e) {
            e.preventDefault();
            $("#save_artikel_promo_btn").html('Proses ..');
            $("#save_artikel_promo_btn").attr("disabled", true);
            var formData = new FormData(this);

            $.ajax({
                type: 'POST',
                url: "{{ url('ap_save') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_artikel_promo_btn").html('Simpan');
                    $("#save_artikel_promo_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#ArtikelPromoModal").modal('hide');
                        toastr.success('Data berhasil disimpan'); // Toastr success message
                        articles_promo_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#ArtikelPromoModal").modal('hide');
                        toastr.warning('Data tidak tersimpan'); // Toastr warning message
                    }
                },
                error: function(data) {
                    toastr.error(
                        'Terjadi kesalahan saat menyimpan data'); // Toastr error message
                        
                }
            });
        });

        $(document).delegate('#export_btn', 'click', function(e) {
            e.preventDefault();

            window.location.href = "{{ url('export-artikel-promo') }}?type=" + type + "";
        });

        $('#delete_artikel_promo_btn').on('click', function() {
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
                            _id: $('#_id').val(),
                            _item: $('#ap_name').val()
                        },
                        dataType: 'json',
                        url: "{{ url('ap_delete') }}",
                        success: function(r) {
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
                        error: function() {
                            toastr.error(
                            'Terjadi kesalahan saat menghapus data'); // Toastr error for AJAX error
                        }
                    });
                    return false;
                }
            });
        });

        $(document).ready(function() {
            // Open the second modal when the button is clicked
            $("#ImportModalBtn").click(function() {
                $("#ImportModal").modal("show");
            });
        });

    });
</script>