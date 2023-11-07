<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var topdeals_table = $('#Topdealstb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('topdeals_datatables') }}",
                data : function (d) {
                    d.search = $('#topdeals_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'td_name', name: 'td_name' },
            { data: 'article_show', name: 'article' },
            { data: 'td_due_date_show', name: 'td_due_date' },
            { data: 'td_status_show', name: 'td_status' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        var topdeals_article_table = $('#TopdealsArticletb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('topdeals_article_datatables') }}",
                data : function (d) {
                    d.td_id = $('#_td_id').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'br_name', name: 'br_name' },
            { data: 'p_name', name: 'p_name' },
            { data: 'p_color', name: 'p_color' },
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

        var article_table = $('#ArticleListtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('article_datatables') }}",
                data : function (d) {
                    d.search = $('#article_list_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'br_name', name: 'br_name' },
            { data: 'p_name', name: 'p_name' },
            { data: 'p_color', name: 'p_color' },
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

        topdeals_table.buttons().container().appendTo($('#topdeals_excel_btn' ));
        $('#topdeals_search').on('keyup', function() {
            topdeals_table.draw();
        });

        $('#article_list_search').on('keyup', function() {
            article_table.draw();
        });

        $(document).delegate('#check_article_btn', 'click', function() {
            var p_id = $(this).attr('data-id');
            var td_id = $('#_td_id').val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {p_id:p_id, td_id:td_id},
                dataType: 'json',
                url: "{{ url('add_topdeals')}}",
                success: function(r) {
                    if (r.status == '200') {
                        toast("Berhasil", "Berhasil menambah artikel", "success");
                        topdeals_table.draw();
                        topdeals_article_table.draw();
                    } else if (r.status == '300') {
                        toast("Sudah ada", "Artikel sudah ada dalam list", "warning");
                    } else {
                        toast('Gagal', 'Gagal menambah artikel', 'error');
                    }
                }
            });
            return false;
        });

        $(document).delegate('#delete_topdeals_article_btn', 'click', function() {
            var id = $(this).attr('data-id');
            swal({
                title: "Hapus..?",
                text: "Yakin hapus artikel ini dari topdeals ?",
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
                        data: {id:id},
                        dataType: 'json',
                        url: "{{ url('delete_topdeals')}}",
                        success: function(r) {
                            if (r.status == '200') {
                                toast("Berhasil", "Berhasil hapus artikel", "success");
                                topdeals_table.draw();
                                topdeals_article_table.draw();
                            } else {
                                toast('Gagal', 'Gagal hapus artikel', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#Topdealstb tbody').on('click', 'tr td:not(:nth-child(3))', function () {
            var id = topdeals_table.row(this).data().id;
            var td_name = topdeals_table.row(this).data().td_name;
            var td_due_date = topdeals_table.row(this).data().td_due_date_val;
            var td_due_time = topdeals_table.row(this).data().td_due_time;
            var td_status = topdeals_table.row(this).data().td_status;
            jQuery.noConflict();
            $('#TopdealsModal').modal('show');
            $('#td_name').val(td_name);
            $('#td_due_date').val(td_due_date);
            $('#td_due_time').val(td_due_time);
            $('#td_status').val(td_status);
            $('#_id').val(id);
            $('#_mode').val('edit');
            @if ( $data['user']->delete_access == '1' )
                $('#delete_topdeals_btn').show();
            @endif
        });

        $('#add_topdeals_btn').on('click', function() {
            jQuery.noConflict();
            $('#TopdealsModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_topdeals')[0].reset();
            $('#delete_topdeals_btn').hide();
        });

        $(document).delegate('#article_detail_btn', 'click', function() {
            jQuery.noConflict();
            var id = $(this).attr('data-id');
            $('#_td_id').val(id);
            $('#ArticleModal').modal('show');
            topdeals_article_table.draw();
        });

        $(document).delegate('#add_topdeals_article_btn', 'click', function() {
            jQuery.noConflict();
            $('#ArticleListModal').modal('show');
        });

        $('#f_topdeals').on('submit', function(e) {
            e.preventDefault();
            $("#save_topdeals_btn").html('Proses ..');
            $("#save_topdeals_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('td_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_topdeals_btn").html('Simpan');
                    $("#save_topdeals_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#TopdealsModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        topdeals_table.ajax.reload();
                    } else if (data.status == '400') {
                        $("#TopdealsModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_topdeals_btn').on('click', function(){
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
                        url: "{{ url('td_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#TopdealsModal').modal('hide');
                                topdeals_table.ajax.reload();
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