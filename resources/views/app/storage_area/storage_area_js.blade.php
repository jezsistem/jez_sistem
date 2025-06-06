<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function loadBins(areaId, binListId) {
            $.ajax({
                url: '/bin_list',
                type: 'GET',
                data: {
                    area_id: areaId,
                    st_id: $('#st_id').val(),
                    search: '',
                },
                success: function(response) {
                    if (response.status) {
                        const binListContainer = $('#' + binListId);
                        binListContainer.empty();

                        response.data.forEach(function(group, groupIndex) {
                            const groupId = 'group-' + group.pl_name.replace(
                                    /[^a-zA-Z0-9\s]/g, '').replace(/\s+/g, '-')
                                .toLowerCase();

                            const removeAllButton = $('<button>')
                                .addClass('btn btn-xs btn-warning remove-all-btn')
                                .css({
                                    'margin-right': '10px',
                                    'font-size': '12px',
                                    'padding': '4px 8px'
                                })
                                .data('group-id', groupId)
                                .data('group-index', groupIndex)
                                .data('bin-list-id', binListId)
                                .data('group-bins', group.bins)
                                .data('group-name', group.pl_name)
                                .text('-')
                                .on('click', function(e) {
                                    e.stopPropagation();
                                    handleRemoveAllBins($(this));
                                });

                            const groupHeader = $('<h5>')
                                .addClass(
                                    'mt-3 d-flex justify-content-between align-items-center'
                                )
                                .css({
                                    'cursor': 'pointer',
                                    'border': '1px solid #ddd',
                                    'padding': '10px',
                                    'border-radius': '5px',
                                    'background-color': '#f8f9fa'
                                })
                                .attr('data-toggle', 'collapse')
                                .attr('data-target', '#' + groupId)
                                .attr('aria-expanded', 'false')
                                .attr('aria-controls', groupId);

                            const headerContent = $('<div>')
                                .addClass('d-flex align-items-center');

                            headerContent.append(removeAllButton);
                            headerContent.append($('<span>').text(group.pl_name));

                            const arrowIcon = $('<span>')
                                .addClass('arrow-icon')
                                .html('&#x25BC;'); // Down arrow

                            groupHeader.append(headerContent);
                            groupHeader.append(arrowIcon);

                            const groupContent = $('<div>')
                                .addClass('collapse')
                                .attr('id', groupId)
                                .css({
                                    'border': '1px solid #ddd',
                                    'border-top': 'none',
                                    'padding': '10px',
                                    'border-radius': '0 0 5px 5px'
                                });

                            group.bins.forEach(function(bin) {
                                // Check if bin is already in temp list
                                const isInTemp = $(
                                    '#bin_list_temp .bin-button[data-id="' + bin
                                    .id + '"]').length > 0;

                                if (!isInTemp) {
                                    const binButton = $('<button>')
                                        .addClass(
                                            'btn btn-sm btn-secondary bin-button-remove m-1'
                                        )
                                        .css({
                                            'display': 'inline-block',
                                            'width': 'auto',
                                            'text-align': 'center'
                                        })
                                        .data('id', bin.id)
                                        .data('code', bin.pl_code)
                                        .data('group-id', groupId)
                                        .data('group-index', groupIndex)
                                        .data('bin-list-id', binListId)
                                        .text(bin.pl_code)
                                        .on('click', function(e) {
                                            e.stopPropagation();
                                            handleRemoveSingleBin($(this));
                                        });
                                    groupContent.append(binButton);
                                }
                            });

                            binListContainer.append(groupHeader);
                            binListContainer.append(groupContent);

                            // Toggle arrow direction on collapse/expand
                            groupHeader.on('click', function() {
                                const isExpanded = groupContent.hasClass('show');
                                arrowIcon.html(isExpanded ? '&#x25BC;' :
                                    '&#x25B2;'); // Down or Up arrow
                            });
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load bins. Please try again.'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred: ' + xhr.responseText
                    });
                }
            });
        }

        function handleRemoveAllBins(button) {
            const groupBins = button.data('group-bins');
            const groupName = button.data('group-name');
            const groupId = button.data('group-id');
            const groupIndex = button.data('group-index');
            const binListId = button.data('bin-list-id');

            let binListHtml =
                '<table style="width: 100%; margin: 10px 0;"><thead><tr><th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Kode Bin</th></tr></thead><tbody>';
            groupBins.forEach(function(bin) {
                binListHtml +=
                    `<tr><td style="border: 1px solid #ddd; padding: 8px;">${bin.pl_code}</td></tr>`;
            });
            binListHtml += '</tbody></table>';

            Swal.fire({
                title: `Hapus Semua Bin dari Area`,
                html: `<div><p>Bin berikut akan dihapus dari area ${groupName}:</p>${binListHtml}</div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const binIds = groupBins.map(bin => bin.id);
                    console.log('Removing bins:', binIds);

                    const saId = $('#storage_area_id').val();
                    const s_id = $('#st_id').val();

                    $.ajax({
                        url: '/storage_area_unlink',
                        type: 'POST',
                        data: {
                            binIds: binIds,
                            sa_id: saId,
                            st_id: s_id,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Semua bin berhasil dihapus dari area!'
                                });
                                // Reload the bin lists
                                loadBins(saId, 'bin_list');
                                loadBinsUnlink(null, 'bin_list_no_area');
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Kesalahan',
                                    text: 'Gagal menghapus bin dari area.'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: 'Terjadi kesalahan: ' + xhr.responseText
                            });
                        }
                    });
                }
            });
        }

        function handleRemoveSingleBin(button) {
            const binId = button.data('id');
            const binCode = button.data('code');
            const s_id = $('#st_id').val();

            console.log('Removing bins:', binId);

            let binTableHtml =
                '<table style="width: 100%; margin: 10px 0;"><thead><tr><th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Kode Bin</th></tr></thead><tbody>';
            binTableHtml += `<tr><td style="border: 1px solid #ddd; padding: 8px;">${binCode}</td></tr>`;
            binTableHtml += '</tbody></table>';

            Swal.fire({
                title: 'Hapus Bin dari Area',
                html: `<div><p>Apakah Anda yakin ingin menghapus bin ini dari area?</p>${binTableHtml}</div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const saId = $('#storage_area_id').val();

                    $.ajax({
                        url: '/storage_area_unlink',
                        type: 'POST',
                        data: {
                            binIds: [binId],
                            sa_id: saId,
                            st_id: s_id,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Bin berhasil dihapus dari area!'
                                });
                                // Reload the bin lists
                                loadBins(saId, 'bin_list');
                                loadBinsUnlink(null, 'bin_list_no_area');
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Kesalahan',
                                    text: 'Gagal menghapus bin dari area.'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: 'Terjadi kesalahan: ' + xhr.responseText
                            });
                        }
                    });
                }
            });
        }

        function loadBinsUnlink(areaId, binListId) {
            $.ajax({
                url: '/bin_list_no_area',
                type: 'GET',
                data: {
                    area_id: areaId,
                    st_id: $('#st_id').val(),
                    search: '',
                },
                success: function(response) {
                    if (response.status) {
                        const binListContainer = $('#' + binListId);
                        binListContainer.empty();

                        response.data.forEach(function(group, groupIndex) {
                            const groupId = 'group-' + group.pl_name.replace(
                                    /[^a-zA-Z0-9\s]/g, '').replace(/\s+/g, '-')
                                .toLowerCase();

                            const moveAllButton = $('<button>')
                                .addClass('btn btn-xs btn-primary move-all-btn')
                                .css({
                                    'margin-right': '10px',
                                    'font-size': '12px',
                                    'padding': '4px 8px'
                                })
                                .data('group-id', groupId)
                                .data('group-index', groupIndex)
                                .data('bin-list-id', binListId)
                                .text('+')
                                .on('click', function(e) {
                                    e.stopPropagation(); // Prevent header collapse
                                    moveAllBinsToTemp(groupId, groupIndex, binListId);
                                });

                            const groupHeader = $('<h5>')
                                .addClass(
                                    'mt-3 d-flex justify-content-between align-items-center'
                                )
                                .css({
                                    'cursor': 'pointer',
                                    'border': '1px solid #ddd',
                                    'padding': '10px',
                                    'border-radius': '5px',
                                    'background-color': '#f8f9fa'
                                })
                                .attr('data-toggle', 'collapse')
                                .attr('data-target', '#' + groupId)
                                .attr('aria-expanded', 'false')
                                .attr('aria-controls', groupId);

                            const headerContent = $('<div>')
                                .addClass('d-flex align-items-center');

                            headerContent.append(moveAllButton);
                            headerContent.append($('<span>').text(group.pl_name));

                            const arrowIcon = $('<span>')
                                .addClass('arrow-icon')
                                .html('&#x25BC;'); // Down arrow

                            groupHeader.append(headerContent);
                            groupHeader.append(arrowIcon);

                            const groupContent = $('<div>')
                                .addClass('collapse')
                                .attr('id', groupId)
                                .css({
                                    'border': '1px solid #ddd',
                                    'border-top': 'none',
                                    'padding': '10px',
                                    'border-radius': '0 0 5px 5px'
                                });

                            group.bins.forEach(function(bin) {
                                // Check if bin is already in temp list
                                const isInTemp = $(
                                    '#bin_list_temp .bin-button[data-id="' + bin
                                    .id + '"]').length > 0;

                                if (!isInTemp) {
                                    const binButton = $('<button>')
                                        .addClass(
                                            'btn btn-sm btn-secondary bin-button m-1'
                                        )
                                        .css({
                                            'display': 'inline-block',
                                            'width': 'auto',
                                            'text-align': 'center'
                                        })
                                        .data('id', bin.id)
                                        .data('code', bin.pl_code)
                                        .data('group-id', groupId)
                                        .data('group-index', groupIndex)
                                        .data('bin-list-id', binListId)
                                        .text(bin.pl_code);
                                    groupContent.append(binButton);
                                }
                            });

                            binListContainer.append(groupHeader);
                            binListContainer.append(groupContent);

                            // Toggle arrow direction on collapse/expand
                            groupHeader.on('click', function() {
                                const isExpanded = groupContent.hasClass('show');
                                arrowIcon.html(isExpanded ? '&#x25BC;' :
                                    '&#x25B2;'); // Down or Up arrow
                            });
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load bins. Please try again.'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred: ' + xhr.responseText
                    });
                }
            });
        }

        function moveAllBinsToTemp(groupId, groupIndex, binListId) {
            const groupContent = $('#' + binListId + ' #' + groupId);
            const binButtons = groupContent.find('.bin-button');

            binButtons.each(function() {
                const binId = $(this).data('id');
                const binCode = $(this).data('code');
                moveBinToTemp(binId, binCode, groupId, groupIndex, binListId);
            });

            binButtons.remove();
        }

        function moveBinToTemp(binId, binCode, groupId, groupIndex, binListId) {
            // Check if bin already exists in temp
            if ($('#bin_list_temp .bin-button[data-id="' + binId + '"]').length > 0) {
                return; // Don't add duplicate
            }

            const tempBinButton = $('<button>')
                .addClass('btn btn-sm btn-warning bin-button m-1')
                .css({
                    'display': 'inline-block',
                    'width': 'auto',
                    'text-align': 'center'
                })
                .data('id', binId)
                .data('code', binCode)
                .data('group-id', groupId)
                .data('group-index', groupIndex)
                .data('bin-list-id', binListId)
                .text(binCode);

            $('#bin_list_temp').append(tempBinButton);
        }

        function returnBinToGroup(binId, binCode, groupId, binListId) {
            const binButton = $('<button>')
                .addClass('btn btn-sm btn-secondary bin-button m-1')
                .css({
                    'display': 'inline-block',
                    'width': 'auto',
                    'text-align': 'center'
                })
                .data('id', binId)
                .data('code', binCode)
                .data('group-id', groupId)
                .data('bin-list-id', binListId)
                .text(binCode);

            $('#' + binListId + ' #' + groupId).append(binButton);
        }

        function removeBinFromList(binButton) {
            binButton.remove();
        }

        // Event handlers for bin movement
        $(document).on('click', '#bin_list_no_area .bin-button', function() {
            const binId = $(this).data('id');
            const binCode = $(this).data('code');
            const groupId = $(this).data('group-id');
            const groupIndex = $(this).data('group-index');
            const binListId = $(this).data('bin-list-id');

            moveBinToTemp(binId, binCode, groupId, groupIndex, binListId);
            removeBinFromList($(this));
        });

        $(document).on('click', '#bin_list_temp .bin-button', function() {
            const binId = $(this).data('id');
            const binCode = $(this).data('code');
            const groupId = $(this).data('group-id');
            const binListId = $(this).data('bin-list-id');

            removeBinFromList($(this));
            returnBinToGroup(binId, binCode, groupId, binListId);
        });

        $('#add_bin_to_area').on('click', function() {
            const tempBinIds = [];
            $('#bin_list_temp .bin-button').each(function() {
                tempBinIds.push($(this).data('id'));
            });

            if (tempBinIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Bins Selected',
                    text: 'Please select bins to add to the storage area.'
                });
                return;
            }

            const stId = $('#st_id').val();
            const saId = $('#storage_area_id').val(); // Assuming you store sa_id in modal data

            $.ajax({
                url: '/storage_area_link',
                type: 'POST',
                data: {
                    tempBinIds: tempBinIds,
                    st_id: stId,
                    sa_id: saId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Bins added to storage area successfully!'
                        });
                        $('#bin_list_temp').empty();
                        // Reload the bin lists
                        loadBinsUnlink(null, 'bin_list_no_area');
                        loadBins(saId, 'bin_list');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to add bins to storage area.'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred: ' + xhr.responseText
                    });
                }
            });
        });

        $('#st_id').select2({
            width: "100%",
            dropdownParent: $('#st_id_parent')
        });

        $('#st_id').on('select2:open', function(e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        var storage_area_table = $('#storage_area_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('/storage_area_datatable') }}",
                data: function(d) {
                    d.st_id = $('#st_id').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                }
            ]
        });

        $('#st_id').change(function() {
            storage_area_table.ajax.reload();
        });

        $('#storage_area_table').on('click', '.edit-btn', function(e) {
            e.stopPropagation();
            var id = $(this).data('id');
            Swal.fire('Edit button clicked for ID: ' + id);
        });

        $('#storage_area_table').on('click', '.delete-btn', function(e) {
            e.stopPropagation();
            var id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                        'Deleted!',
                        'Your item has been deleted.',
                        'success'
                    );
                }
            });
        });

        $(document).on('click', '[data-bs-dismiss="modal"]', function() {
            $(this).closest('.modal').modal('hide');
        });

        $('#addStorageAreaForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            formData += '&st_id=' + $('#st_id').val();

            if ($('#st_id').val() === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please select a store before creating a storage area.'
                });
                return;
            }

            $.ajax({
                url: '/storage_area_create',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Storage area created successfully!'
                        });
                        $('#addStorageAreaModal').modal('hide');
                        $('#addStorageAreaForm')[0].reset();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to create storage area. Please try again.'
                        });
                    }
                    storage_area_table.ajax.reload();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred: ' + xhr.responseText
                    });
                }
            });
        });

        $('#editStorageAreaForm').on('submit', function(e) {
            e.preventDefault();
            var formData = {
            id: $('#storage_area_id').val(),
            name: $('#edit_name').val(),
            description: $('#edit_description').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
            };

            $.ajax({
            url: '/storage_area_update',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.status) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Storage area updated successfully!'
                });
                $('#editStorageAreaModal').modal('hide');
                $('#detailStorageAreaModal').modal('hide');
                $('#editStorageAreaForm')[0].reset();
                storage_area_table.ajax.reload();
                } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to update storage area. Please try again.'
                });
                }
            },
            error: function(xhr) {
                Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred: ' + xhr.responseText
                });
            }
            });
        });

        $('#deleteStorageAreaBtn').on('click', function() {
            var storageAreaId = $('#storage_area_id').val();
            var storageAreaName = $('#detail_name').text();
            
            Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Anda akan menghapus area penyimpanan "${storageAreaName}". Tindakan ini tidak dapat dibatalkan!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
            }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                url: '/storage_area_delete/' + storageAreaId,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Area penyimpanan berhasil dihapus.'
                    });
                    $('#detailStorageAreaModal').modal('hide');
                    storage_area_table.ajax.reload();
                    } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan',
                        text: response.message || 'Gagal menghapus area penyimpanan.'
                    });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'Terjadi kesalahan: ' + xhr.responseText
                    });
                }
                });
            }
            });
        });

        jQuery.noConflict();

        $('#add_area_btn').on('click', function() {
            $('#addStorageAreaModal').modal('show');
        });

        $('#detailStorageAreaModal').on('hidden.bs.modal', function() {
            $('#bin_list_temp').empty();
        });

        $('#editStorageAreaBtn').on('click', function() {
            var storageAreaId = $('#storage_area_id').val();
            
            // Get storage area data
            $.ajax({
            url: '/storage_area/' + storageAreaId,
            type: 'GET',
            success: function(response) {
                if (response.status) {
                // Populate the edit form with current data
                $('#edit_name').val(response.data.name);
                $('#edit_description').val(response.data.description);
                $('#editStorageAreaModal').modal('show');
                } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load storage area data.'
                });
                }
            },
            error: function(xhr) {
                Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred: ' + xhr.responseText
                });
            }
            });
        });

        $('#storage_area_table tbody').on('click', 'tr', function() {
            var data = storage_area_table.row(this).data();
            if (data) {
                $('#detailStorageAreaModal').modal('show');
                $('#storage_area_id').val(data.id);
                $('#detail_name').text(data.name);
                $('#detail_description').text(data.description);
                $('#detail_created_at').text(data.created_at);

                loadBinsUnlink(null, 'bin_list_no_area');
                loadBins(data.id, 'bin_list');
            }
        });
    });
</script>
