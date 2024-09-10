<script>
    var u_id = '';
    var ma_id = [];

    function reloadGroup()
    {
        $.ajax({
            type: "GET",
            dataType: 'html',
            url: "<?php echo e(url('reload_group')); ?>",
            success: function(r) {
                $('#gr_id').html(r);
            }
        });
    }

    function loadUserMenu()
    {
        ma_id = [];
        $('.menu-option').remove();
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {u_id:u_id},
            dataType: 'json',
            url: "<?php echo e(url('load_user_menu')); ?>",
            success: function(r) {
                console.log(r);
                for (var i = 0; i < r.length; i++) {
                    $('#menu_panel').append("<a class='btn btn-sm btn-primary menu-option mr-1 mb-1' data-id='"+r[i]['id']+"'>"+r[i]['ma_title']+"</a>");
                }
            }
        });
    }

    $(document).delegate('.menu-option', 'click', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        if ($(this).hasClass('btn-success')) {
            $(this).removeClass('btn-success');
            $(this).addClass('btn-primary');
            ma_id = $.grep(ma_id, function(value) {
                        return value != id;
                    });
        } else {
            $(this).removeClass('btn-primary');
            $(this).addClass('btn-success');
            ma_id.push(id);
        }
    });


    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var group_table = $('#Grouptb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('group_datatables')); ?>",
                data : function (d) {
                    d.search = $('#group_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'g_name', name: 'g_name' },
            { data: 'g_description', name: 'g_description' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        group_table.buttons().container().appendTo($('#group_excel_btn' ));
        $('#group_search').on('keyup', function() {
            group_table.draw(false);
        });

        $('#Grouptb tbody').on('click', 'tr', function () {
            var id = group_table.row(this).data().id;
            var gr_name = group_table.row(this).data().g_name;
            var gr_description = group_table.row(this).data().g_description;
            jQuery.noConflict();
            $('#GroupModal').modal('show');
            $('#gr_name').val(gr_name);
            $('#gr_description').val(gr_description);
            $('#_id_gr').val(id);
            $('#_mode_gr').val('edit');
            <?php if( $data['user']->delete_access == '1' ): ?>
            $('#delete_group_btn').show();
            <?php endif; ?>
        });

        $('#add_group_btn').on('click', function() {
            jQuery.noConflict();
            $('#GroupModal').modal('show');
            $('#_id_gr').val('');
            $('#_mode_gr').val('add');
            $('#f_group')[0].reset();
            $('#delete_group_btn').hide();
        });

        $('#u_secret_code').on('change', function() {
            var u_secret_code = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_u_secret_code:u_secret_code},
                dataType: 'json',
                url: "<?php echo e(url('check_exists_secret_code')); ?>",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Kode', 'Kode sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#u_secret_code').val('');
                        return false;
                    }
                }
            });
        });

        $('#f_group').on('submit', function(e) {
            e.preventDefault();
            $("#save_group_btn").html('Proses ..');
            $("#save_group_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('gr_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_group_btn").html('Simpan');
                    $("#save_group_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#GroupModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        group_table.draw(false);
                        reloadGroup();
                    } else if (data.status == '400') {
                        $("#GroupModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_group_btn').on('click', function(){
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
                        data: {_id:$('#_id_gr').val(),_item:$('#gr_name').val()},
                        dataType: 'json',
                        url: "<?php echo e(url('gr_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#GroupModal').modal('hide');
                                group_table.draw(false);
                                reloadGroup();
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        // ======================= USER ===================== //

        $('#st_id').select2({
            width: "100%",
            dropdownParent: $('#st_id_parent')
        });
        $('#st_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#gr_id').select2({
            width: "100%",
            dropdownParent: $('#gr_id_parent')
        });
        $('#gr_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#stt_id').select2({
            width: "100%",
            dropdownParent: $('#stt_id_parent')
        });
        $('#stt_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        var user_table = $('#Usertb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('user_datatables')); ?>",
                data : function (d) {
                    d.search = $('#user_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'uid', searchable: false},
            { data: 'u_name', name: 'u_name' },
            { data: 'g_name', name: 'g_name' },
            { data: 'menu_access', name: 'uma' },
            { data: 'delete_access_show', name: 'delete_access' },
            { data: 'stt_name', name: 'stt_name' },
            { data: 'st_name', name: 'st_name' },
            { data: 'u_nip', name: 'u_nip' },
            { data: 'u_ktp', name: 'u_ktp' },
            { data: 'u_secret_code', name: 'u_secret_code' },
            { data: 'u_phone', name: 'u_phone' },
            { data: 'u_email', name: 'u_email' },
            { data: 'u_address', name: 'u_address' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        user_table.buttons().container().appendTo($('#user_excel_btn' ));
        $('#user_search').on('keyup', function() {
            user_table.draw(false);
        });

        $('#Usertb tbody').on('click', 'tr td:not(:nth-child(4), :nth-child(5))', function () {
            var uid = user_table.row(this).data().uid;
            var gr_id = user_table.row(this).data().gr_id;
            var stt_id = user_table.row(this).data().stt_id;
            var st_id = user_table.row(this).data().st_id;
            var u_name = user_table.row(this).data().u_name;
            var u_nip = user_table.row(this).data().u_nip;
            var u_ktp = user_table.row(this).data().u_ktp;
            var u_secret_code = user_table.row(this).data().u_secret_code;
            var u_email = user_table.row(this).data().u_email;
            var u_phone = user_table.row(this).data().u_phone;
            var u_address = user_table.row(this).data().u_address;
            var delete_access = user_table.row(this).data().delete_access;
            jQuery.noConflict();
            $('#UserModal').modal('show');
            jQuery('#gr_id').val(gr_id).trigger('change');
            jQuery('#stt_id').val(stt_id).trigger('change');
            jQuery('#st_id').val(st_id).trigger('change');
            jQuery('#delete_access').val(delete_access).trigger('change');
            $('#u_name').val(u_name);
            $('#u_nip').val(u_nip);
            $('#u_ktp').val(u_ktp);
            $('#u_secret_code').val(u_secret_code);
            $('#u_email').val(u_email);
            $('#u_phone').val(u_phone);
            $('#u_password').val('');
            $('#u_address').val(u_address);
            $('#_id').val(uid);
            $('#_mode').val('edit');

            <?php if( $data['user']->g_name == 'administrator' ): ?>
            $('#delete_user_btn').show();
            <?php endif; ?>
        });

        $('#add_user_btn').on('click', function() {
            jQuery.noConflict();
            $('#UserModal').modal('show');
            $('#_id').val('');
            jQuery('#gr_id').val('').trigger('change');
            jQuery('#stt_id').val('').trigger('change');
            jQuery('#st_id').val('').trigger('change');
            $('#_mode').val('add');
            $('#f_user')[0].reset();
            $('#delete_user_btn').hide();
        });

        $('#f_user').on('submit', function(e) {
            e.preventDefault();
            $("#save_user_btn").html('Proses ..');
            $("#save_user_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('u_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_user_btn").html('Simpan');
                    $("#save_user_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#UserModal").modal('hide');
                        jQuery('#gr_id').val('').trigger('change');
                        jQuery('#stt_id').val('').trigger('change');
                        jQuery('#st_id').val('').trigger('change');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        user_table.draw(false);
                    } else if (data.status == '400') {
                        $("#UserModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_user_btn').on('click', function(){
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
                        url: "<?php echo e(url('u_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#UserModal').modal('hide');
                                user_table.draw(false);
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        var menu_access_table = $('#MenuAccesstb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            ajax: {
                url : "<?php echo e(url('uma_datatables')); ?>",
                data : function (d) {
                    d.u_id = u_id;
                    d.search = $('#menu_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'ma_title', name: 'ma_title' },
            { data: 'uma_default', name: 'uma_default', orderable: false },
            { data: 'action', name: 'action', orderable: false },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        $('#menu_search').on('keyup', function() {
            menu_access_table.draw(false);
        });

        $(document).delegate('#menu_access_btn', 'click', function(e) {
            e.preventDefault();
            jQuery.noConflict();
            u_id = $(this).attr('data-id');
            $('#MenuAccessModal').modal('show');
            loadUserMenu();
            menu_access_table.draw();
        });

        $('#ma_id_access').on('keyup', function(){
            var query = $(this).val();
            if($.trim(query) != '' || $.trim(query) != null) {
                $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url:"<?php echo e(url('autocomplete_menu')); ?>",
                    method:"POST",
                    data:{query:query},
                    success:function(data){
                        $('#menuList').fadeIn();
                        $('#menuList').html(data);
                    }
                });
            } else {
                $('#menuList').fadeOut();
            }
        });

        $('#st_id_access').on('keyup', function(){
            var query = $(this).val();
            if($.trim(query) != '' || $.trim(query) != null) {
                $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url:"<?php echo e(url('autocomplete_store')); ?>",
                    method:"POST",
                    data:{query:query},
                    success:function(data){
                        $('#storeList').fadeIn();
                        $('#storeList').html(data);
                    }
                });
            } else {
                $('#storeList').fadeOut();
            }
        });

        $(document).on('click', '#add_ma_to_list', function(e) {
            var id = $(this).attr('data-id');
            var ma_title = $(this).attr('data-ma_title');
            $('#ma_id_access_hidden').val(id);
            $('#ma_id_access').val(ma_title);
        });

        $(document).on('click', '#add_st_to_list', function(e) {
            var id = $(this).attr('data-id');
            var st_name = $(this).attr('data-st_name');
            $('#st_id_access_hidden').val(id);
            $('#st_id_access').val(st_name);
        });

        $(document).on('click', '#add_menu_access_btn', function(e) {
            if (ma_id.length <= 0) {
                swal('Tentukan Menu', 'Silahkan ketik menu pada pencarian, kemudian pilih salah satu', 'warning');
                return false;
            }
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {ma_id:ma_id, u_id:u_id},
                dataType: 'json',
                url: "<?php echo e(url('uma_save')); ?>",
                success: function(r) {
                    if (r.status == '200'){
                        $('#ma_id_access_hidden').val('');
                        $('#ma_id_access').val('');
                        menu_access_table.draw(false);
                        loadUserMenu();
                        swal("Berhasil", "Data berhasil ditambah", "success");
                    } else {
                        swal('Gagal', 'Gagal tambah data', 'error');
                    }
                }
            });
            return false;
        });

        $(document).on('click', '#delete_menu_access_btn', function(e) {
            var id = $(this).attr('data-id');
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {id:id},
                dataType: 'json',
                url: "<?php echo e(url('uma_delete')); ?>",
                success: function(r) {
                    if (r.status == '200'){
                        menu_access_table.draw(false);
                        loadUserMenu();
                        toast("Berhasil", "Data berhasil dihapus", "success");
                    } else {
                        toast('Gagal', 'Gagal hapus data', 'error');
                    }
                }
            });
            return false;
        });
        
        $(document).on('click', 'body', function(e) {
            $('#menuList').fadeOut();
            $('#storeList').fadeOut();
        });

        $('#ma_id_access').on('focus', function(){
            $('#ma_id_access_hidden').val('');
            $('#ma_id_access').val('');
        });

        $('#st_id_access').on('focus', function(){
            $('#st_id_access_hidden').val('');
            $('#st_id_access').val('');
        });

        $('#MenuAccesstb tbody').on('click', 'tr td:not(:nth-child(4))', function () {
            var ma_id = menu_access_table.row(this).data().ma_id;
            swal({
                title: "Set Default..?",
                text: "Set menu ini sebagai default ketika user login ?",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Ya'
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
                        data: {u_id:u_id, ma_id:ma_id},
                        dataType: 'json',
                        url: "<?php echo e(url('uma_default')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil disetups", "success");
                                menu_access_table.draw(false);
                            } else {
                                swal('Gagal', 'Gagal setup data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })

        });

    });
</script>
<?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/user/user_js.blade.php ENDPATH**/ ?>