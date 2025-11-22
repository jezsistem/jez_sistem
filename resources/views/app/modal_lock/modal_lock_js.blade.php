<script>
    var modal_lock_table = '';
    function deleteLockModal(id) {
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this data!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: "POST",
                    url: "{{ url('modal_lock/delete') }}",
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == '200') {
                            swal("Poof! Your data has been deleted!", {
                                icon: "success",
                            });
                            modal_lock_table.ajax.reload();
                        } else {
                            swal("Error deleting data!", {
                                icon: "error",
                            });
                        }
                    }
                });
            } else {
                swal("Your data is safe!");
            }
        });
    }
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        modal_lock_table = $('#ModalLocktb').DataTable({
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
                url: "{{ url('modal_lock/datatables') }}",
                data: function(d) {
                    d.search = $('#modal_lock_search').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'id',
                    searchable: false
                },
                {
                    data: 'u_name',
                    name: 'u_name'
                },
                {
                    data: 'lockable_id',
                    name: 'lockable_id'
                },
                {
                    data: 'lockable_type',
                    name: 'lockable_type'
                },
                {
                    data: 'identifier',
                    name: 'identifier'
                },
                {
                    data: 'expires_at',
                    name: 'expires_at'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
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

        modal_lock_table.buttons().container().appendTo($('#modal_lock_excel_btn'));
        $('#modal_lock_search').on('keyup', function() {
            modal_lock_table.draw();
        });

        $('#dp_name').on('change', function() {
            var dp_name_name = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {
                    _dp_name: dp_name
                },
                dataType: 'json',
                url: "{{ url('check_exists_modal_lock') }}",
                success: function(r) {
                    if (r.status == '200') {
                        swal('Perusahaan',
                            'Modal Lock sudah ada disistem, silahkan ganti dengan yang lain',
                            'warning');
                        $('#dp_name').val('');
                        return false;
                    }
                }
            });
        });

    });
</script>
