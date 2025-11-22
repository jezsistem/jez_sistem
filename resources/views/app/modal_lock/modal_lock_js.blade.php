<script>
    var modal_lock_table = '';
    var modal_lock_config_table = '';
    var modal_lock_allowed_models_table = '';

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

    function addConfig() {
        // Add your logic to add a new configuration
        jQuery.noConflict();
        $('#modalAddEditConfig').modal('show');
    }

    function addAllowedModel() {
        // Add your logic to add a new allowed model
        jQuery.noConflict();
        $('#modalAddEditAllowedModel').modal('show');
    }

    function saveConfig() {
        var action = $('#configAction').val();
        var url = action === 'edit' 
            ? "{{ url('modal_lock/config/update') }}/" + $('#configId').val()
            : "{{ url('modal_lock/config/save') }}";
        var method = action === 'edit' ? 'PUT' : 'POST';
        
        $.ajax({
            type: method,
            url: url,
            data: $('#formConfig').serialize(),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status == '200') {
                    swal("Success! Configuration saved!", {
                        icon: "success",
                    });
                    modal_lock_config_table.ajax.reload();
                    jQuery.noConflict();
                    $('#modalAddEditConfig').modal('hide');
                } else {
                    swal("Error saving configuration!", {
                        icon: "error",
                    });
                }
            }
        });
    }

    function editConfig(id) {
        // Add your logic to edit configuration
        $.ajax({
            type: "GET",
            url: "{{ url('modal_lock/config/edit') }}/" + id,
            dataType: 'json',
            success: function(response) {
                if (response.status == '200') {
                    $('#configAction').val('edit');
                    $('#configId').val(response.data.id);
                    $('#configName').val(response.data.name);
                    $('#configValue').val(response.data.value);
                    $('#configDescription').val(response.data.description);
                    jQuery.noConflict();
                    modal_lock_config_table.draw(false);
                    $('#modalAddEditConfig').modal('show');
                } else {
                    swal("Error fetching configuration!", {
                        icon: "error",
                    });
                }
            }
        });
    }

    function deleteConfig(id) {
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this configuration!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ url('modal_lock/config/delete') }}/" + id,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status == '200') {
                            swal("Poof! Configuration has been deleted!", {
                                icon: "success",
                            });
                            modal_lock_config_table.ajax.reload();
                        } else {
                            swal("Error deleting configuration!", {
                                icon: "error",
                            });
                        }
                    }
                });
            } else {
                swal("Your configuration is safe!");
            }
        });
    }

    function saveAllowedModel() {
        var action = $('#allowedModelAction').val();
        var url = action === 'edit' 
            ? "{{ url('modal_lock/allowed_models/update') }}/" + $('#allowedModelId').val()
            : "{{ url('modal_lock/allowed_models/save') }}";
        var method = action === 'edit' ? 'PUT' : 'POST';
        
        $.ajax({
            type: method,
            url: url,
            data: $('#formAllowedModel').serialize(),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status == '200') {
                    swal("Success! Allowed Model saved!", {
                        icon: "success",
                    });
                    modal_lock_allowed_models_table.ajax.reload();
                    jQuery.noConflict();
                    $('#modalAddEditAllowedModel').modal('hide');
                } else {
                    swal("Error saving Allowed Model!", {
                        icon: "error",
                    });
                }
            }
        });
    }

    function editAllowedModel(id) {
        // Add your logic to edit allowed model
        $.ajax({
            type: "GET",
            url: "{{ url('modal_lock/allowed_models/edit') }}/" + id,
            dataType: 'json',
            success: function(response) {
                if (response.status == '200') {
                    $('#allowedModelAction').val('edit');
                    $('#allowedModelId').val(response.data.id);
                    $('#allowedModelType').val(response.data.model_type);
                    $('#allowedModelIdentifier').val(response.data.identifier);
                    $('#allowedModelIsActive').val(response.data.is_active);
                    jQuery.noConflict();
                    modal_lock_allowed_models_table.draw(false);
                    $('#modalAddEditAllowedModel').modal('show');
                } else {
                    swal("Error fetching Allowed Model!", {
                        icon: "error",
                    });
                }
            }
        });
    }

    function deleteAllowedModel(id) {
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this Allowed Model!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ url('modal_lock/allowed_models/delete') }}/" + id,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status == '200') {
                            swal("Poof! Allowed Model has been deleted!", {
                                icon: "success",
                            });
                            modal_lock_allowed_models_table.ajax.reload();
                        } else {
                            swal("Error deleting Allowed Model!", {
                                icon: "error",
                            });
                        }
                    }
                });
            } else {
                swal("Your Allowed Model is safe!");
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
            dom: 'lrt<"text-right"ip>',
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

        modal_lock_config_table = $('#config_table').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lrt<"text-right"ip>',
            ajax: {
                url: "{{ url('modal_lock/config/datatables') }}",
            },
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'value',
                    name: 'value'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
        });

        modal_lock_allowed_models_table = $('#allowed_models_table').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lrt<"text-right"ip>',
            ajax: {
                url: "{{ url('modal_lock/allowed_models/datatables') }}",
            },
            columns: [{
                    data: 'model_type',
                    name: 'model_type'
                },
                {
                    data: 'identifier',
                    name: 'identifier'
                },
                {
                    data: 'is_active',
                    name: 'is_active'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
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

        $('#btn_modal_config').on('click', function() {
            jQuery.noConflict();
            modal_lock_config_table.draw();
            $('#modalConfig').modal('show');
        });

        $('#btn_modal_allowed_models').on('click', function() {
            jQuery.noConflict();
            $('#modalAllowedModels').modal('show');
        });

        $('.close_modal_config').on('click', function() {
            jQuery.noConflict();
            $('#modalConfig').modal('hide');
        });

        $('.close_modal_allowed_models').on('click', function() {
            jQuery.noConflict();
            $('#modalAllowedModels').modal('hide');
        });

        $('.close_modal_add_edit_config').on('click', function() {
            jQuery.noConflict();
            $('#modalAddEditConfig').modal('hide');
        });

        $('.close_modal_add_edit_allowed_model').on('click', function() {
            jQuery.noConflict();
            $('#modalAddEditAllowedModel').modal('hide');
        });



    });
</script>
