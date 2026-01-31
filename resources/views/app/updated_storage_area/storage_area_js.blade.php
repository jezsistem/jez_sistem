<script>
var storageAreaDataTable = null;
var storageAreaDataArray = [];

// Copy all bin management functions from original file
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
                    const groupId = 'group-' + group.pl_name.replace(/[^a-zA-Z0-9\s]/g, '').replace(/\s+/g, '-').toLowerCase();
                    const removeAllButton = $('<button>').addClass('btn btn-xs btn-warning remove-all-btn').css({'margin-right': '10px', 'font-size': '12px', 'padding': '4px 8px'}).data('group-id', groupId).data('group-index', groupIndex).data('bin-list-id', binListId).data('group-bins', group.bins).data('group-name', group.pl_name).text('-').on('click', function(e) { e.stopPropagation(); handleRemoveAllBins($(this)); });
                    const groupHeader = $('<h5>').addClass('bin-group-header').attr('data-toggle', 'collapse').attr('data-target', '#' + groupId).attr('aria-expanded', 'false').attr('aria-controls', groupId);
                    const headerContent = $('<div>').addClass('d-flex align-items-center');
                    headerContent.append(removeAllButton);
                    headerContent.append($('<span>').text(group.pl_name));
                    const arrowIcon = $('<span>').addClass('arrow-icon').html('&#x25BC;');
                    groupHeader.append(headerContent);
                    groupHeader.append(arrowIcon);
                    const groupContent = $('<div>').addClass('collapse bin-group-content').attr('id', groupId);
                    group.bins.forEach(function(bin) {
                        const isInTemp = $('#bin_list_temp .bin-button[data-id="' + bin.id + '"]').length > 0;
                        if (!isInTemp) {
                            const binButton = $('<button>').addClass('btn btn-sm btn-secondary bin-button-remove m-1 bin-button').css({'display': 'inline-block', 'width': 'auto', 'text-align': 'center'}).data('id', bin.id).data('code', bin.pl_code).data('group-id', groupId).data('group-index', groupIndex).data('bin-list-id', binListId).text(bin.pl_code).on('click', function(e) { e.stopPropagation(); handleRemoveSingleBin($(this)); });
                            groupContent.append(binButton);
                        }
                    });
                    binListContainer.append(groupHeader);
                    binListContainer.append(groupContent);
                    groupHeader.on('click', function() {
                        const isExpanded = groupContent.hasClass('show');
                        arrowIcon.html(isExpanded ? '&#x25BC;' : '&#x25B2;');
                    });
                });
            }
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
                    const groupId = 'group-' + group.pl_name.replace(/[^a-zA-Z0-9\s]/g, '').replace(/\s+/g, '-').toLowerCase();
                    const moveAllButton = $('<button>').addClass('btn btn-xs btn-primary move-all-btn').css({'margin-right': '10px', 'font-size': '12px', 'padding': '4px 8px'}).data('group-id', groupId).data('group-index', groupIndex).data('bin-list-id', binListId).text('+').on('click', function(e) { e.stopPropagation(); moveAllBinsToTemp(groupId, groupIndex, binListId); });
                    const groupHeader = $('<h5>').addClass('bin-group-header').attr('data-toggle', 'collapse').attr('data-target', '#' + groupId).attr('aria-expanded', 'false').attr('aria-controls', groupId);
                    const headerContent = $('<div>').addClass('d-flex align-items-center');
                    headerContent.append(moveAllButton);
                    headerContent.append($('<span>').text(group.pl_name));
                    const arrowIcon = $('<span>').addClass('arrow-icon').html('&#x25BC;');
                    groupHeader.append(headerContent);
                    groupHeader.append(arrowIcon);
                    const groupContent = $('<div>').addClass('collapse bin-group-content').attr('id', groupId);
                    group.bins.forEach(function(bin) {
                        const isInTemp = $('#bin_list_temp .bin-button[data-id="' + bin.id + '"]').length > 0;
                        if (!isInTemp) {
                            const binButton = $('<button>').addClass('btn btn-sm btn-secondary bin-button m-1').css({'display': 'inline-block', 'width': 'auto', 'text-align': 'center'}).data('id', bin.id).data('code', bin.pl_code).data('group-id', groupId).data('group-index', groupIndex).data('bin-list-id', binListId).text(bin.pl_code);
                            groupContent.append(binButton);
                        }
                    });
                    binListContainer.append(groupHeader);
                    binListContainer.append(groupContent);
                    groupHeader.on('click', function() {
                        const isExpanded = groupContent.hasClass('show');
                        arrowIcon.html(isExpanded ? '&#x25BC;' : '&#x25B2;');
                    });
                });
            }
        }
    });
}

function handleRemoveAllBins(button) {
    const groupBins = button.data('group-bins');
    const groupName = button.data('group-name');
    const binIds = groupBins.map(bin => bin.id);
    const saId = $('#storage_area_id').val();
    const s_id = $('#st_id').val();
    Swal.fire({
        title: `Hapus Semua Bin dari Area`,
        html: `<div><p>Bin berikut akan dihapus dari area ${groupName}:</p><p>Total: ${binIds.length} bin</p></div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus semua!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/storage_area_unlink',
                type: 'POST',
                data: { binIds: binIds, sa_id: saId, st_id: s_id, _token: $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    if (response.status) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Semua bin berhasil dihapus dari area!' });
                        loadBins(saId, 'bin_list');
                        loadBinsUnlink(null, 'bin_list_no_area');
                    } else {
                        Swal.fire({ icon: 'error', title: 'Kesalahan', text: 'Gagal menghapus bin dari area.' });
                    }
                }
            });
        }
    });
}

function handleRemoveSingleBin(button) {
    const binId = button.data('id');
    const binCode = button.data('code');
    const saId = $('#storage_area_id').val();
    const s_id = $('#st_id').val();
    Swal.fire({
        title: 'Hapus Bin dari Area',
        html: `<div><p>Apakah Anda yakin ingin menghapus bin ${binCode} dari area?</p></div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/storage_area_unlink',
                type: 'POST',
                data: { binIds: [binId], sa_id: saId, st_id: s_id, _token: $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    if (response.status) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Bin berhasil dihapus dari area!' });
                        loadBins(saId, 'bin_list');
                        loadBinsUnlink(null, 'bin_list_no_area');
                    } else {
                        Swal.fire({ icon: 'error', title: 'Kesalahan', text: 'Gagal menghapus bin dari area.' });
                    }
                }
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
    if ($('#bin_list_temp .bin-button[data-id="' + binId + '"]').length > 0) return;
    const tempBinButton = $('<button>').addClass('btn btn-sm btn-warning bin-button m-1').css({'display': 'inline-block', 'width': 'auto', 'text-align': 'center'}).data('id', binId).data('code', binCode).data('group-id', groupId).data('group-index', groupIndex).data('bin-list-id', binListId).text(binCode);
    $('#bin_list_temp').append(tempBinButton);
}

function returnBinToGroup(binId, binCode, groupId, binListId) {
    const binButton = $('<button>').addClass('btn btn-sm btn-secondary bin-button m-1').css({'display': 'inline-block', 'width': 'auto', 'text-align': 'center'}).data('id', binId).data('code', binCode).data('group-id', groupId).data('bin-list-id', binListId).text(binCode);
    $('#' + binListId + ' #' + groupId).append(binButton);
}

function removeBinFromList(binButton) {
    binButton.remove();
}

function loadStorageAreaData() {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
    
    $.ajax({
        url: "{{ url('/storage_area_datatable') }}",
        type: 'GET',
        data: {
            st_id: $('#st_id').val(),
            length: -1
        },
        dataType: 'json',
        success: function(response) {
            if (storageAreaDataTable) {
                storageAreaDataTable.destroy();
            }
            
            storageAreaDataArray = response.data || [];
            $('#storage_area_table tbody').empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var html = '<tr class="hover:bg-gray-50 cursor-pointer clickable-row" data-id="' + (row.id || '') + '" data-name="' + (row.name || '') + '" data-description="' + (row.description || '') + '" data-created_at="' + (row.created_at || '') + '">';
                    html += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center">' + (index + 1) + '</td>';
                    html += '<td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">' + (row.name || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + (row.description || '') + '</td>';
                    html += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">' + (row.created_at || '') + '</td>';
                    html += '</tr>';
                    $('#storage_area_table tbody').append(html);
                });
            } else {
                $('#storage_area_table tbody').append('<tr><td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("storage_area_table") && typeof simpleDatatables !== 'undefined') {
                storageAreaDataTable = new simpleDatatables.DataTable("#storage_area_table", {
                    searchable: true,
                    sortable: true,
                    perPage: 10,
                    perPageSelect: [5, 10, 25, 50, 100],
                    classes: {
                        wrapper: "datatable-wrapper",
                        top: "datatable-top",
                        bottom: "datatable-bottom",
                        search: "datatable-search",
                        selector: "datatable-selector",
                        table: "datatable-table",
                        thead: "",
                        tbody: "",
                        sorter: "datatable-sorter"
                    },
                    labels: {
                        placeholder: "Cari...",
                        perPage: "",
                        noRows: "Tidak ada data",
                        info: "Menampilkan {start} sampai {end} dari {rows} data",
                        noResults: "Tidak ada hasil pencarian"
                    }
                });
                
                setTimeout(function() {
                    $('#storage_area_table tbody').off('click', 'tr').on('click', 'tr', function() {
                        var $row = $(this);
                        var id = $row.data('id');
                        var name = $row.data('name');
                        var description = $row.data('description');
                        var created_at = $row.data('created_at');
                        
                        if (id) {
                            $('#storage_area_id').val(id);
                            $('#detail_name').text(name);
                            $('#detail_description').text(description);
                            $('#detail_created_at').text(created_at);
                            openDetailStorageAreaModal();
                            loadBinsUnlink(null, 'bin_list_no_area');
                            loadBins(id, 'bin_list');
                        }
                    });
                }, 100);
            }
        },
        error: function(xhr) {
            console.error('Error loading data:', xhr);
        }
    });
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
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

    $('#st_id').change(function() {
        loadStorageAreaData();
    });

    // Bin movement handlers
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
            Swal.fire({ icon: 'warning', title: 'No Bins Selected', text: 'Please select bins to add to the storage area.' });
            return;
        }

        const stId = $('#st_id').val();
        const saId = $('#storage_area_id').val();

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
                    Swal.fire({ icon: 'success', title: 'Success', text: 'Bins added to storage area successfully!' });
                    $('#bin_list_temp').empty();
                    loadBinsUnlink(null, 'bin_list_no_area');
                    loadBins(saId, 'bin_list');
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to add bins to storage area.' });
                }
            }
        });
    });

    $('#add_area_btn').on('click', function() {
        openAddStorageAreaModal();
    });

    $('#addStorageAreaForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        formData += '&st_id=' + $('#st_id').val();

        if ($('#st_id').val() === '') {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Please select a store before creating a storage area.' });
            return;
        }

        $.ajax({
            url: '/storage_area_create',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.status) {
                    Swal.fire({ icon: 'success', title: 'Success', text: 'Storage area created successfully!' });
                    closeAddStorageAreaModal();
                    $('#addStorageAreaForm')[0].reset();
                    loadStorageAreaData();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to create storage area. Please try again.' });
                }
            }
        });
    });

    $('#editStorageAreaBtn').on('click', function() {
        var storageAreaId = $('#storage_area_id').val();
        $.ajax({
            url: '/storage_area/' + storageAreaId,
            type: 'GET',
            success: function(response) {
                if (response.status) {
                    $('#edit_name').val(response.data.name);
                    $('#edit_description').val(response.data.description);
                    openEditStorageAreaModal();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load storage area data.' });
                }
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
                    Swal.fire({ icon: 'success', title: 'Success', text: 'Storage area updated successfully!' });
                    closeEditStorageAreaModal();
                    closeDetailStorageAreaModal();
                    $('#editStorageAreaForm')[0].reset();
                    loadStorageAreaData();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Failed to update storage area. Please try again.' });
                }
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
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Area penyimpanan berhasil dihapus.' });
                            closeDetailStorageAreaModal();
                            loadStorageAreaData();
                        } else {
                            Swal.fire({ icon: 'error', title: 'Kesalahan', text: response.message || 'Gagal menghapus area penyimpanan.' });
                        }
                    }
                });
            }
        });
    });

    $('#detailStorageAreaModal').on('hidden.bs.modal', function() {
        $('#bin_list_temp').empty();
    });
});
</script>
