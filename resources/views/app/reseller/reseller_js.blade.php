<script>
    function reloadCity(province)
    {
        $.ajax({
            type: "GET",
            data: {_province:province},
            dataType: 'html',
            url: "{{ url('reload_city')}}",
            success: function(r) {
                $('#cust_city').html(r);
            }
        });
    }

    function reloadSubdistrict(city)
    {
        $.ajax({
            type: "GET",
            data: {_city:city},
            dataType: 'html',
            url: "{{ url('reload_subdistrict')}}",
            success: function(r) {
                $('#cust_subdistrict').html(r);
            }
        });
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var reseller_table = $('#Customertb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"lip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('rs_customer_datatables') }}",
                data : function (d) {
                    d.search = $('#customer_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'cust_name', name: 'cust_name' },
            { data: 'cust_username', name: 'cust_username' },
            { data: 'rl_name_show', name: 'rl_name' },
            { data: 'rad_amount_show', name: 'rad_amount' },
            { data: 'cust_phone', name: 'cust_phone' },
            { data: 'cust_email', name: 'cust_email' },
            { data: 'cust_address_show', name: 'cust_address' },
            { data: 'rd_balance_show', name: 'rd_balance' },
            { data: 'total_show', name: 'total' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            language: {
                "lengthMenu": "_MENU_",
            },
            order: [[0, 'desc']],
        });

        reseller_table.buttons().container().appendTo($('#reseller_excel_btn' ));
        $('#customer_search').on('keyup', function() {
            reseller_table.draw(false);
        });

        $('#Customertb tbody').on('click', 'tr', function () {
            var id = reseller_table.row(this).data().id;
            var rl_id = reseller_table.row(this).data().rl_id;
            var rad_id = reseller_table.row(this).data().rad_id;
            var cust_name = reseller_table.row(this).data().cust_name;
            var cust_phone = reseller_table.row(this).data().cust_phone;
            var cust_username = reseller_table.row(this).data().cust_username;
            var cust_email = reseller_table.row(this).data().cust_email;
            var cust_province = reseller_table.row(this).data().cust_province;
            var cust_city = reseller_table.row(this).data().cust_city;
            var cust_subdistrict = reseller_table.row(this).data().cust_subdistrict;
            var cust_address = reseller_table.row(this).data().cust_address;
            var cust_token_active = reseller_table.row(this).data().cust_token_active;
            var cust_total = reseller_table.row(this).data().cust_total;
            jQuery.noConflict();
            $('#RSModal').modal('show');
            $('#rl_id').val(rl_id);
            $('#rad_id').val(rad_id);
            $('#cust_name').val(cust_name);
            $('#cust_phone').val(cust_phone);
            $('#cust_username').val(cust_username);
            $('#cust_email').val(cust_email);
            $('#cust_province').val(cust_province);
            $('#cust_token_active').val(cust_token_active);
            $('#cust_total').val(cust_total);
            reloadCity(cust_province);
            setTimeout(() => {
                $('#cust_city').val(cust_city);
            }, 1000);
            reloadSubdistrict(cust_city);
            setTimeout(() => {
                $('#cust_subdistrict').val(cust_subdistrict);
            }, 2000);
            $('#cust_address').val(cust_address);
            $('#_id').val(id);
            $('#_mode').val('edit');
            $('#delete_btn').show();
        });

        $('#cust_phone').on('change', function() {
            var cust_phone = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_cust_phone:cust_phone},
                dataType: 'json',
                url: "{{ url('check_exists_customer')}}",
                success: function(r) {
                    if (r.status == '200') {
                        swal('No Telepon', 'Nomor telepon sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#cust_phone').val('');
                        return false;
                    }
                }
            });
        });

        $('#cust_province').on('change', function() {
            var province = $(this).val();
            reloadCity(province);
        });

        $('#cust_city').on('change', function() {
            var city = $(this).val();
            reloadSubdistrict(city);
        });

        $('#add_btn').on('click', function() {
            jQuery.noConflict();
            $('#RSModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_data')[0].reset();
            $('#delete_btn').hide();
        });

        $('#f_data').on('submit', function(e) {
            e.preventDefault();
            $("#save_btn").html('Proses ..');
            $("#save_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('rs_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_btn").html('Simpan');
                    $("#save_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#RSModal").modal('hide');
                        reseller_table.draw(false);
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                    } else if (data.status == '400') {
                        $("#RSModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_btn').on('click', function(){
            swal({
                title: "Hapus..?",
                text: "Yakin hapus data ini ?",
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
                        data: {_id:$('#_id').val()},
                        dataType: 'json',
                        url: "{{ url('rs_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                $('#RSModal').modal('hide');
                                reseller_table.draw(false);
                                swal("Berhasil", "Data berhasil dihapus", "success");
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });
    });
</script>
