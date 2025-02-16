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
                    data: 'st_code',
                    name: 'st_code'
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

        artikel_promo_table.buttons().container().appendTo($('#artikel_promo_excel_btn'));
        $('#artikel_promo_search').on('keyup', function() {
            artikel_promo_table.draw();
        });

        $('#ArtikelPromotb tbody').on('click', 'tr', function() {
            var id = artikel_promo_table.row(this).data().id;
            var p_id = artikel_promo_table.row(this).data().p_id;
            var st_id = artikel_promo_table.row(this).data().st_id;
            var date_start = artikel_promo_table.row(this).data().date_start;
            var date_end = artikel_promo_table.row(this).data().date_end;
            var promo_cat = artikel_promo_table.row(this).data().promo_cat;
            var promo_price = artikel_promo_table.row(this).data().promo_price;
            var promo_note = artikel_promo_table.row(this).data().promo_note;
            jQuery.noConflict();
            $('#ArtikelPromoModal').modal('show');
            $('#p_id').val(p_id);
            $('#st_id').val(st_id);
            $('#date_start').val(date_start);
            $('#date_end').val(date_end);
            $('#promo_cat').val(promo_cat);
            $('#promo_price').val(promo_price);
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

        $('#add_artikel_prommo_btn').on('click', function() {
            jQuery.noConflict();
            $('#ArtikelPromoModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_artikel_prommo')[0].reset();
            $('#delete_artikel_prommo_btn').hide();
        });

        $('#f_artikel_prommo').on('submit', function(e) {
            e.preventDefault();
            $("#save_artikel_prommo_btn").html('Proses ..');
            $("#save_artikel_prommo_btn").attr("disabled", true);
            var formData = new FormData(this);

            $.ajax({
                type: 'POST',
                url: "{{ url('dp_save') }}",
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
                        data_perusahaan_table.ajax.reload();
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

            var type = 'npwp';
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
                            _item: $('#dp_name').val()
                        },
                        dataType: 'json',
                        url: "{{ url('dp_delete') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                $('#artikel_promoModal').modal('hide');
                                toastr.success(
                                'Data berhasil dihapus'); // Change to Toastr success message
                                artikel_promo_table.ajax.reload();
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


    });
</script>