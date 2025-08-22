<script>
    var u_id = '';
    var ma_id = [];

    function reloadGroup() {
        $.ajax({
            type: "GET",
            dataType: 'html',
            url: "{{ url('reload_group') }}",
            success: function(r) {
                $('#gr_id').html(r);
            }
        });
    }

    function loadUserMenu() {
        ma_id = [];
        $('.menu-option').remove();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {
                u_id: u_id
            },
            dataType: 'json',
            url: "{{ url('load_user_menu') }}",
            success: function(r) {
                console.log(r);
                for (var i = 0; i < r.length; i++) {
                    $('#menu_panel').append(
                        "<a class='btn btn-sm btn-primary menu-option mr-1 mb-1' data-id='" + r[i][
                            'id'
                        ] + "'>" + r[i]['ma_title'] + "</a>");
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
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs'
            }],
            ajax: {
                url: "{{ url('group_datatables') }}",
                data: function(d) {
                    d.search = $('#group_search').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'id',
                    searchable: false
                },
                {
                    data: 'g_name',
                    name: 'g_name'
                },
                {
                    data: 'g_description',
                    name: 'g_description'
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

        group_table.buttons().container().appendTo($('#group_excel_btn'));
        $('#group_search').on('keyup', function() {
            group_table.draw(false);
        });

        $('#Grouptb tbody').on('click', 'tr', function() {
            var id = group_table.row(this).data().id;
            var gr_name = group_table.row(this).data().g_name;
            var gr_description = group_table.row(this).data().g_description;
            jQuery.noConflict();
            $('#GroupModal').modal('show');
            $('#gr_name').val(gr_name);
            $('#gr_description').val(gr_description);
            $('#_id_gr').val(id);
            $('#_mode_gr').val('edit');
            @if ($data['user']->delete_access == '1')
                $('#delete_group_btn').show();
            @endif
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
                data: {
                    _u_secret_code: u_secret_code
                },
                dataType: 'json',
                url: "{{ url('check_exists_secret_code') }}",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Kode',
                            'Kode sudah ada disistem, silahkan ganti dengan yang lain',
                            'warning');
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
                type: 'POST',
                url: "{{ url('gr_save') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_group_btn").html('Simpan');
                    $("#save_group_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#GroupModal").modal('hide');
                        toastr.success("Data berhasil disimpan", "Success");
                        group_table.draw(false);
                        reloadGroup();
                    } else if (data.status == '400') {
                        $("#GroupModal").modal('hide');
                        toastr.warning("Data tidak tersimpan", 'Gagal');
                    }
                },
                error: function(data) {
                    toastr.error('Terjadi kesalahan', 'Error');
                }
            });
        });


        $('#delete_group_btn').on('click', function() {
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
                        data: {
                            _id: $('#_id_gr').val(),
                            _item: $('#gr_name').val()
                        },
                        dataType: 'json',
                        url: "{{ url('gr_delete') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                toastr.success('Data berhasil dihapus', 'Berhasil');
                                $('#GroupModal').modal('hide');
                                group_table.draw(false);
                                reloadGroup();
                            } else {
                                toastr.error('Gagal hapus data', 'Gagal');
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
        $('#st_id').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#gr_id').select2({
            width: "100%",
            dropdownParent: $('#gr_id_parent')
        });
        $('#gr_id').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#stt_id').select2({
            width: "100%",
            dropdownParent: $('#stt_id_parent')
        });
        $('#stt_id').on('select2:open', function(e) {
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
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs'
            }],
            ajax: {
                url: "{{ url('user_datatables') }}",
                data: function(d) {
                    d.search = $('#user_search').val();
                    d.filter_delete = $('#filter_delete').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'uid',
                    searchable: false
                },
                {
                    data: 'u_name',
                    name: 'u_name'
                },
                {
                    data: 'g_name',
                    name: 'g_name'
                },
                {
                    data: 'menu_access',
                    name: 'uma'
                },
                {
                    data: 'delete_access_show',
                    name: 'delete_access'
                },
                {
                    data: 'stt_name',
                    name: 'stt_name'
                },
                {
                    data: 'st_name',
                    name: 'st_name'
                },
                {
                    data: 'u_secret_code',
                    name: 'u_secret_code'
                },
                {
                    data: 'u_email',
                    name: 'u_email'
                },
                {
                    data: 'u_delete',
                    name: 'u_delete'
                },
                {
                    data: 'pos_access',
                    name: 'pos_access'
                },
                {
                    data: 'pick_access',
                    name: 'pick_access',
                },
                {
                    data: 'action',
                    name: 'action'
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
            rowCallback: function(row, data, index) {
                if (data.u_delete === "1") {
                    $(row).css('background-color', '#f8d7da');
                }
            }
        });

        $('#filter_delete').on('change', function() {
            console.log($('#filter_delete').val())
            user_table.draw();
        });

        user_table.buttons().container().appendTo($('#user_excel_btn'));
        $('#user_search').on('keyup', function() {
            user_table.draw(false);
        });


        $('#Usertb tbody').on('click', '.btn-detail', function() {
            let uid = $(this).data('uid');
            let data = user_table.rows().data().toArray().find(row => row.uid == uid);
            if (data) {
                showUserModal(data);
            }
        });

        // Fungsi pemanggil modal
        function showUserModal(data) {
            jQuery.noConflict();
            $('#UserModal').modal('show');
            jQuery('#gr_id').val(data.gr_id).trigger('change');
            jQuery('#stt_id').val(data.stt_id).trigger('change');
            jQuery('#st_id').val(data.st_id).trigger('change');
            jQuery('#delete_access').val(data.delete_access).trigger('change');
            $('#u_name').val(data.u_name);
            $('#u_nip').val(data.u_nip);
            $('#u_ktp').val(data.u_ktp);
            $('#u_secret_code').val(data.u_secret_code);
            $('#u_email').val(data.u_email);
            $('#u_phone').val(data.u_phone);
            $('#u_password').val('');
            $('#u_address').val(data.u_address);
            jQuery('#u_delete').val(data.u_delete).trigger('change');
            jQuery('#pos_access').val(data.pos_access).trigger('change');
            jQuery('#pick_access').val(data.pick_access).trigger('change');
            $('#u_active').val(data.u_active);
            $('#_id').val(data.uid);
            $('#_mode').val('edit');

            @if ($data['user']->g_name == 'administrator')
                $('#delete_user_btn').show();
            @endif
        }

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
                type: 'POST',
                url: "{{ url('u_save') }}",
                data: formData,
                dataType: 'json',
                cache: false,
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
                        toastr.success('Data berhasil disimpan', 'Berhasil');
                        user_table.draw(false);
                    } else if (data.status == '400') {
                        $("#UserModal").modal('hide');
                        toastr.warning('Data tidak tersimpan', 'Gagal');
                    }
                },
                error: function(data) {
                    toastr.error('Terjadi kesalahan', 'Error');
                }
            });

        });

        $('#delete_user_btn').on('click', function() {
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
                        data: {
                            _id: $('#_id').val()
                        },
                        dataType: 'json',
                        url: "{{ url('u_delete') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                toastr.success('Data berhasil dihapus', 'Berhasil');
                                $('#UserModal').modal('hide');
                                user_table.draw(false);
                            } else {
                                toastr.error('Gagal hapus data', 'Gagal');
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
                url: "{{ url('uma_datatables') }}",
                data: function(d) {
                    d.u_id = u_id;
                    d.search = $('#menu_search').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'id',
                    searchable: false
                },
                {
                    data: 'ma_title',
                    name: 'ma_title'
                },
                {
                    data: 'uma_default',
                    name: 'uma_default',
                    orderable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false
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

        $('#ma_id_access').on('keyup', function() {
            var query = $(this).val();
            if ($.trim(query) != '' || $.trim(query) != null) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ url('autocomplete_menu') }}",
                    method: "POST",
                    data: {
                        query: query
                    },
                    success: function(data) {
                        $('#menuList').fadeIn();
                        $('#menuList').html(data);
                    }
                });
            } else {
                $('#menuList').fadeOut();
            }
        });

        $('#st_id_access').on('keyup', function() {
            var query = $(this).val();
            if ($.trim(query) != '' || $.trim(query) != null) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ url('autocomplete_store') }}",
                    method: "POST",
                    data: {
                        query: query
                    },
                    success: function(data) {
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
                swal('Tentukan Menu', 'Silahkan ketik menu pada pencarian, kemudian pilih salah satu',
                    'warning');
                return false;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {
                    ma_id: ma_id,
                    u_id: u_id
                },
                dataType: 'json',
                url: "{{ url('uma_save') }}",
                success: function(r) {
                    if (r.status == '200') {
                        $('#ma_id_access_hidden').val('');
                        $('#ma_id_access').val('');
                        menu_access_table.draw(false);
                        loadUserMenu();
                        toastr.success('Data berhasil ditambah', 'Berhasil');
                    } else {
                        toastr.error('Gagal tambah data', 'Gagal');
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
                data: {
                    id: id
                },
                dataType: 'json',
                url: "{{ url('uma_delete') }}",
                success: function(r) {
                    if (r.status == '200') {
                        menu_access_table.draw(false);
                        loadUserMenu();
                        toastr.success('Data berhasil dihapus', 'Berhasil');
                    } else {
                        toastr.error('Gagal hapus data', 'Gagal');
                    }
                }
            });

            return false;
        });

        $(document).on('click', 'body', function(e) {
            $('#menuList').fadeOut();
            $('#storeList').fadeOut();
        });

        $('#ma_id_access').on('focus', function() {
            $('#ma_id_access_hidden').val('');
            $('#ma_id_access').val('');
        });

        $('#st_id_access').on('focus', function() {
            $('#st_id_access_hidden').val('');
            $('#st_id_access').val('');
        });

        $('#MenuAccesstb tbody').on('click', 'tr td:not(:nth-child(4))', function() {
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
                        data: {
                            u_id: u_id,
                            ma_id: ma_id
                        },
                        dataType: 'json',
                        url: "{{ url('uma_default') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                toastr.success('Data berhasil disetups',
                                    'Berhasil');
                                menu_access_table.draw(false);
                            } else {
                                toastr.error('Gagal setup data', 'Gagal');
                            }
                        }
                    });

                    return false;
                }
            })

        });

        $(document).on('change', '.toggle-delete', function() {
            let uid = $(this).data('id');
            let isChecked = $(this).is(':checked') ? '0' : '1';

            $.ajax({
                url: '/update-delete-status',
                method: 'POST',
                data: {
                    uid: uid,
                    u_delete: isChecked,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success('Data berhasil diubah', 'Berhasil');
                    user_table.draw(false);
                },
                error: function(xhr) {
                    toastr.error('Gagal mengubah status.', 'Gagal');
                }
            });
        });

        $(document).on('change', '.toggle-posaccesss', function() {
            let uid = $(this).data('id');
            let isChecked = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: '/update-pos-access',
                method: 'POST',
                data: {
                    uid: uid,
                    pos_access: isChecked,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success('Data berhasil diubah', 'Berhasil');
                    // user_table.draw(false);
                },
                error: function(xhr) {
                    toastr.error('Gagal mengubah status.', 'Gagal');
                }
            });
        });
        
        $(document).on('change', '.toggle-pickaccess', function() {
            let uid = $(this).data('id');
            let isChecked = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: '/update-pick-access',
                method: 'POST',
                data: {
                    uid: uid,
                    pick_access: isChecked,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success('Data berhasil diubah', 'Berhasil');
                    // user_table.draw(false);
                },
                error: function(xhr) {
                    toastr.error('Gagal mengubah status.', 'Gagal');
                }
            });
        });


    });
</script>
