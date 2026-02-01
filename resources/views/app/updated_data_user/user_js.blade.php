<script>
var userCurrentPage = 1;
var groupCurrentPage = 1;
var perPage = 25;
var u_id = '';
var ma_id = [];

// ==================== GROUP FUNCTIONS ====================

function loadGroupData(page = 1) {
    groupCurrentPage = page;
    var search = $('#group_search').val();
    
    $.ajax({
        url: "{{ url('group_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#group_tbody').html('<tr><td colspan="3" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#group_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.g_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.g_description));
                    tr.on('click', function() {
                        openGroupModal('edit', row);
                    });
                    tbody.append(tr);
                });
            }

            createCustomPagination('#GroupTable', response.total, response.current_page, response.total_pages, response.per_page, loadGroupData, 'group');
        },
        error: function() {
            $('#group_tbody').html('<tr><td colspan="3" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function openGroupModal(mode, data = null) {
    $('#GroupModal').removeClass('hidden');
    if (mode === 'add') {
        $('#_id_gr').val('');
        $('#_mode_gr').val('add');
        $('#f_group')[0].reset();
        $('#delete_group_btn').addClass('hidden');
    } else {
        $('#_id_gr').val(data.id);
        $('#_mode_gr').val('edit');
        $('#gr_name').val(data.g_name);
        $('#gr_description').val(data.g_description);
        @if ($data['user']->delete_access == '1')
            $('#delete_group_btn').removeClass('hidden');
        @else
            $('#delete_group_btn').addClass('hidden');
        @endif
    }
}

function closeGroupModal() {
    $('#GroupModal').addClass('hidden');
}

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

// ==================== USER FUNCTIONS ====================

function loadUserData(page = 1) {
    userCurrentPage = page;
    var search = $('#user_search').val();
    var filter_delete = $('#filter_delete').val();
    
    $.ajax({
        url: "{{ url('user_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            filter_delete: filter_delete
        },
        success: function(response) {
            if (response.error) {
                $('#user_tbody').html('<tr><td colspan="13" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#user_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="13" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50');
                    if (row.u_delete == '1') {
                        tr.addClass('bg-red-50');
                    }
                    
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.u_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.g_name));
                    
                    // Menu Access button
                    var menuAccessBtn = $('<button>').addClass('px-3 py-1 text-xs font-medium text-white bg-blue-600 rounded hover:bg-red-600')
                        .html('<i class="fas fa-eye mr-1"></i>')
                        .attr('data-id', row.uid)
                        .addClass('menu_access_btn');
                    tr.append($('<td>').addClass('px-3 py-4').append(menuAccessBtn));
                    
                    // Delete Access
                    var deleteAccessBadge = $('<span>').addClass('px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded')
                        .text(row.delete_access_show);
                    tr.append($('<td>').addClass('px-3 py-4').append(deleteAccessBadge));
                    
                    tr.append($('<td>').addClass('px-3 py-4').text(row.stt_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.st_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.u_secret_code));
                    
                    // Status toggle
                    var statusToggle = createToggle(row.u_delete == '0', row.uid, 'toggle-delete');
                    tr.append($('<td>').addClass('px-3 py-4').append(statusToggle));
                    
                    // POS Access toggle
                    var posToggle = createToggle(row.pos_access == 1, row.uid, 'toggle-posaccesss');
                    tr.append($('<td>').addClass('px-3 py-4').append(posToggle));
                    
                    // Pick Access toggle
                    var pickToggle = createToggle(row.pick_access == 1, row.uid, 'toggle-pickaccess');
                    tr.append($('<td>').addClass('px-3 py-4').append(pickToggle));
                    
                    // Manual Attendance Access toggle
                    var manualToggle = createToggle(row.manual_attendance_access == 1, row.uid, 'toggle-manual_attendance_access');
                    tr.append($('<td>').addClass('px-3 py-4').append(manualToggle));
                    
                    // Detail button
                    var detailBtn = $('<button>').addClass('px-3 py-1 text-xs font-medium text-white bg-blue-600 rounded hover:bg-red-600')
                        .text('Detail')
                        .attr('data-uid', row.uid)
                        .addClass('btn-detail');
                    tr.append($('<td>').addClass('px-3 py-4').append(detailBtn));
                    
                    tbody.append(tr);
                });
            }

            createCustomPagination('#UserTable', response.total, response.current_page, response.total_pages, response.per_page, loadUserData, 'user');
        },
        error: function() {
            $('#user_tbody').html('<tr><td colspan="13" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function createToggle(checked, id, className) {
    var label = $('<label>').addClass('relative inline-flex items-center cursor-pointer');
    var input = $('<input>').attr('type', 'checkbox')
        .addClass('sr-only peer')
        .addClass(className)
        .attr('data-id', id)
        .prop('checked', checked);
    var div = $('<div>').addClass("w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600");
    label.append(input).append(div);
    return label;
}

function openUserModal(mode, data = null) {
    $('#UserModal').removeClass('hidden');
    if (mode === 'add') {
        $('#_id').val('');
        $('#_mode').val('add');
        $('#f_user')[0].reset();
        $('#gr_id').val('').trigger('change');
        $('#stt_id').val('').trigger('change');
        $('#st_id').val('').trigger('change');
        $('#delete_user_btn').addClass('hidden');
    } else {
        $('#_id').val(data.uid);
        $('#_mode').val('edit');
        $('#gr_id').val(data.gr_id).trigger('change');
        $('#stt_id').val(data.stt_id).trigger('change');
        $('#st_id').val(data.st_id).trigger('change');
        $('#delete_access').val(data.delete_access).trigger('change');
        $('#u_name').val(data.u_name);
        $('#u_nip').val(data.u_nip);
        $('#u_ktp').val(data.u_ktp);
        $('#u_secret_code').val(data.u_secret_code);
        $('#u_email').val(data.u_email);
        $('#u_phone').val(data.u_phone);
        $('#u_password').val('');
        $('#u_address').val(data.u_address);
        $('#join_date').val(data.join_date);
        $('#u_active').val(data.u_active);
        @if ($data['user']->g_name == 'administrator')
            $('#delete_user_btn').removeClass('hidden');
        @else
            $('#delete_user_btn').addClass('hidden');
        @endif
    }
}

function closeUserModal() {
    $('#UserModal').addClass('hidden');
}

// ==================== MENU ACCESS FUNCTIONS ====================

function loadUserMenu() {
    ma_id = [];
    $('.menu-option').remove();
    $.ajax({
        type: "POST",
        data: { u_id: u_id },
        dataType: 'json',
        url: "{{ url('load_user_menu') }}",
        success: function(r) {
            for (var i = 0; i < r.length; i++) {
                $('#menu_panel').append(
                    "<button type='button' class='px-3 py-1 text-xs font-medium text-white bg-blue-600 rounded hover:bg-red-600 mr-1 mb-1 menu-option' data-id='" + r[i]['id'] + "'>" + r[i]['ma_title'] + "</button>");
            }
        }
    });
}

function loadMenuAccessData(page = 1) {
    var search = $('#menu_search').val();
    
    $.ajax({
        url: "{{ url('uma_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            u_id: u_id,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#menu_access_tbody').html('<tr><td colspan="4" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#menu_access_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="4" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ma_title));
                    
                    // Default button
                    var defaultBtn = '';
                    if (row.uma_default == '1') {
                        defaultBtn = $('<button>').addClass('px-3 py-1 text-xs font-medium text-white bg-blue-600 rounded')
                            .html('<i class="fas fa-check"></i>')
                            .attr('data-id', row.id);
                    }
                    tr.append($('<td>').addClass('px-3 py-4').append(defaultBtn));
                    
                    // Delete button
                    var deleteBtn = $('<button>').addClass('px-3 py-1 text-xs font-medium text-white bg-red-500 rounded hover:bg-red-700')
                        .text('X')
                        .attr('data-id', row.id)
                        .addClass('delete_menu_access_btn');
                    tr.append($('<td>').addClass('px-3 py-4').append(deleteBtn));
                    
                    // Click row to set default
                    tr.on('click', 'td:not(:last-child)', function() {
                        setDefaultMenu(row.ma_id);
                    });
                    
                    tbody.append(tr);
                });
            }

            createCustomPagination('#MenuAccessTable', response.total, response.current_page, response.total_pages, response.per_page, loadMenuAccessData, 'menu_access');
        },
        error: function() {
            $('#menu_access_tbody').html('<tr><td colspan="4" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function setDefaultMenu(ma_id) {
    Swal.fire({
        title: "Set Default..?",
        text: "Set menu ini sebagai default ketika user login ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                data: { u_id: u_id, ma_id: ma_id },
                dataType: 'json',
                url: "{{ url('uma_default') }}",
                success: function(r) {
                    if (r.status == '200') {
                        toastr.success('Data berhasil disetups', 'Berhasil');
                        loadMenuAccessData(1);
                    } else {
                        toastr.error('Gagal setup data', 'Gagal');
                    }
                }
            });
        }
    });
}

function openMenuAccessModal(uid) {
    u_id = uid;
    $('#MenuAccessModal').removeClass('hidden');
    loadUserMenu();
    loadMenuAccessData(1);
}

function closeMenuAccessModal() {
    $('#MenuAccessModal').addClass('hidden');
}

// ==================== PAGINATION ====================

function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage, loadFunction, prefix) {
    var infoId = prefix + '_pagination_info';
    var controlsId = prefix + '_pagination_controls';
    
    var info = $('#' + infoId);
    var controls = $('#' + controlsId);
    
    info.empty();
    controls.empty();
    
    var start = (currentPage - 1) * perPage + 1;
    var end = Math.min(currentPage * perPage, totalRecords);
    info.text('Menampilkan ' + start + ' sampai ' + end + ' dari ' + totalRecords + ' data');
    
    if (totalPages <= 1) return;
    
    var prevBtn = $('<button>')
        .addClass('px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100')
        .text('Sebelumnya')
        .prop('disabled', currentPage === 1)
        .on('click', function() {
            if (currentPage > 1) {
                loadFunction(currentPage - 1);
            }
        });
    
    controls.append(prevBtn);
    
    var maxVisible = 5;
    var startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    var endPage = Math.min(totalPages, startPage + maxVisible - 1);
    
    if (startPage > 1) {
        controls.append($('<button>')
            .addClass('px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 hover:bg-gray-100')
            .text('1')
            .on('click', function() { loadFunction(1); }));
        if (startPage > 2) {
            controls.append($('<span>').addClass('px-3 py-1 text-sm text-gray-500').text('...'));
        }
    }
    
    for (var i = startPage; i <= endPage; i++) {
        var pageBtn = $('<button>')
            .addClass('px-3 py-1 text-sm font-medium border border-gray-300 hover:bg-gray-100')
            .text(i);
        
        if (i === currentPage) {
            pageBtn.addClass('text-white bg-blue-600');
        } else {
            pageBtn.addClass('text-gray-500 bg-white');
        }
        
        pageBtn.on('click', function() {
            loadFunction(parseInt($(this).text()));
        });
        
        controls.append(pageBtn);
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            controls.append($('<span>').addClass('px-3 py-1 text-sm text-gray-500').text('...'));
        }
        controls.append($('<button>')
            .addClass('px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 hover:bg-gray-100')
            .text(totalPages)
            .on('click', function() { loadFunction(totalPages); }));
    }
    
    var nextBtn = $('<button>')
        .addClass('px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100')
        .text('Selanjutnya')
        .prop('disabled', currentPage === totalPages)
        .on('click', function() {
            if (currentPage < totalPages) {
                loadFunction(currentPage + 1);
            }
        });
    
    controls.append(nextBtn);
}

// ==================== MAIN DOCUMENT READY ====================

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadUserData(1);

    // Toggle User Level section
    $('#toggle_user_level_btn').on('click', function() {
        $('#user_level_section').toggleClass('hidden');
        if (!$('#user_level_section').hasClass('hidden')) {
            loadGroupData(1);
        }
    });

    // ==================== GROUP HANDLERS ====================

    // Group search with debounce
    var groupSearchTimeout;
    $('#group_search').on('keyup', function() {
        clearTimeout(groupSearchTimeout);
        groupSearchTimeout = setTimeout(function() {
            loadGroupData(1);
        }, 500);
    });

    // Add group button
    $('#add_group_btn').on('click', function() {
        openGroupModal('add');
    });

    // Group form submit
    $('#f_group').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_group_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
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
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeGroupModal();
                    toastr.success('Data berhasil disimpan', 'Berhasil');
                    loadGroupData(groupCurrentPage);
                    reloadGroup();
                } else if (data.status == '400') {
                    closeGroupModal();
                    toastr.warning('Data tidak tersimpan', 'Gagal');
                }
            },
            error: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                toastr.error('Terjadi kesalahan', 'Error');
            }
        });
    });

    // Delete group button
    $('#delete_group_btn').on('click', function() {
        Swal.fire({
            title: "Hapus..?",
            text: "Yakin hapus data ini ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batalkan'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: { _id: $('#_id_gr').val() },
                    dataType: 'json',
                    url: "{{ url('gr_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            toastr.success('Data berhasil dihapus', 'Berhasil');
                            closeGroupModal();
                            loadGroupData(groupCurrentPage);
                            reloadGroup();
                        } else {
                            toastr.error('Gagal hapus data', 'Gagal');
                        }
                    }
                });
            }
        });
    });

    // Export group
    $('#export_group_btn').on('click', function(e) {
        e.stopPropagation();
        $('#export_group_menu').toggleClass('hidden');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#export_group_btn, #export_group_menu').length) {
            $('#export_group_menu').addClass('hidden');
        }
    });

    $('#export_group_excel_btn').on('click', function() {
        var table = document.getElementById('GroupTable');
        var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
        XLSX.writeFile(wb, "group_" + new Date().toISOString().split('T')[0] + ".xlsx");
        $('#export_group_menu').addClass('hidden');
    });

    // ==================== USER HANDLERS ====================

    // User search with debounce
    var userSearchTimeout;
    $('#user_search').on('keyup', function() {
        clearTimeout(userSearchTimeout);
        userSearchTimeout = setTimeout(function() {
            loadUserData(1);
        }, 500);
    });

    // Filter delete change
    $('#filter_delete').on('change', function() {
        loadUserData(1);
    });

    // Add user button
    $('#add_user_btn').on('click', function() {
        openUserModal('add');
    });

    // Detail button handler (event delegation)
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var uid = $(this).attr('data-uid');
        // Get row data from table
        var rowData = null;
        $('#user_tbody tr').each(function() {
            var detailBtn = $(this).find('.btn-detail');
            if (detailBtn.attr('data-uid') == uid) {
                // Extract data from row
                var cells = $(this).find('td');
                rowData = {
                    uid: uid,
                    u_name: $(cells[1]).text(),
                    g_name: $(cells[2]).text(),
                    // We need to load full data via AJAX
                };
                return false;
            }
        });
        
        // Load full user data
        $.ajax({
            url: "{{ url('user_datatables_simple') }}",
            type: 'GET',
            data: {
                page: 1,
                per_page: 1000,
                search: '',
                filter_delete: ''
            },
            success: function(response) {
                var userData = response.data.find(function(row) {
                    return row.uid == uid;
                });
                if (userData) {
                    openUserModal('edit', userData);
                }
            }
        });
    });

    // Menu access button handler (event delegation)
    $(document).on('click', '.menu_access_btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var uid = $(this).attr('data-id');
        openMenuAccessModal(uid);
    });

    // User form submit
    $('#f_user').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_user_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
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
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeUserModal();
                    toastr.success('Data berhasil disimpan', 'Berhasil');
                    loadUserData(userCurrentPage);
                    reloadGroup();
                } else if (data.status == '400') {
                    closeUserModal();
                    toastr.warning('Data tidak tersimpan', 'Gagal');
                }
            },
            error: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                toastr.error('Terjadi kesalahan', 'Error');
            }
        });
    });

    // Delete user button
    $('#delete_user_btn').on('click', function() {
        Swal.fire({
            title: "Hapus..?",
            text: "Yakin hapus data ini ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batalkan'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: { _id: $('#_id').val() },
                    dataType: 'json',
                    url: "{{ url('u_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            toastr.success('Data berhasil dihapus', 'Berhasil');
                            closeUserModal();
                            loadUserData(userCurrentPage);
                        } else {
                            toastr.error('Gagal hapus data', 'Gagal');
                        }
                    }
                });
            }
        });
    });

    // Check exists secret code
    $('#u_secret_code').on('change', function() {
        var u_secret_code = $(this).val();
        if (!u_secret_code) return;
        
        $.ajax({
            type: "POST",
            data: { _u_secret_code: u_secret_code },
            dataType: 'json',
            url: "{{ url('check_exists_secret_code') }}",
            success: function(r) {
                if (r.status == '200') {
                    Swal.fire('Kode', 'Kode sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                    $('#u_secret_code').val('');
                }
            }
        });
    });

    // Toggle switches handlers
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
                loadUserData(userCurrentPage);
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
            },
            error: function(xhr) {
                toastr.error('Gagal mengubah status.', 'Gagal');
            }
        });
    });

    $(document).on('change', '.toggle-manual_attendance_access', function() {
        let uid = $(this).data('id');
        let isChecked = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: '/update-manual-attendance-access',
            method: 'POST',
            data: {
                uid: uid,
                manual_attendance_access: isChecked,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                toastr.success('Data berhasil diubah', 'Berhasil');
            },
            error: function(xhr) {
                toastr.error('Gagal mengubah status.', 'Gagal');
            }
        });
    });

    // Export user
    $('#export_btn').on('click', function(e) {
        e.stopPropagation();
        $('#export_menu').toggleClass('hidden');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#export_btn, #export_menu').length) {
            $('#export_menu').addClass('hidden');
        }
    });

    $('#export_excel_btn').on('click', function() {
        var table = document.getElementById('UserTable');
        var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
        XLSX.writeFile(wb, "user_" + new Date().toISOString().split('T')[0] + ".xlsx");
        $('#export_menu').addClass('hidden');
    });

    // ==================== MENU ACCESS HANDLERS ====================

    // Menu option click handler
    $(document).on('click', '.menu-option', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        if ($(this).hasClass('bg-green-600')) {
            $(this).removeClass('bg-green-600').addClass('bg-blue-600');
            ma_id = $.grep(ma_id, function(value) {
                return value != id;
            });
        } else {
            $(this).removeClass('bg-blue-600').addClass('bg-green-600');
            ma_id.push(id);
        }
    });

    // Autocomplete menu
    $('#ma_id_access').on('keyup', function() {
        var query = $(this).val();
        if ($.trim(query) != '' && $.trim(query) != null) {
            $.ajax({
                url: "{{ url('autocomplete_menu') }}",
                method: "POST",
                data: { query: query },
                success: function(data) {
                    $('#menuList').fadeIn().html(data);
                }
            });
        } else {
            $('#menuList').fadeOut();
        }
    });

    // Autocomplete store
    $('#st_id_access').on('keyup', function() {
        var query = $(this).val();
        if ($.trim(query) != '' && $.trim(query) != null) {
            $.ajax({
                url: "{{ url('autocomplete_store') }}",
                method: "POST",
                data: { query: query },
                success: function(data) {
                    $('#storeList').fadeIn().html(data);
                }
            });
        } else {
            $('#storeList').fadeOut();
        }
    });

    // Add menu to list
    $(document).on('click', '#add_ma_to_list', function(e) {
        var id = $(this).attr('data-id');
        var ma_title = $(this).attr('data-ma_title');
        $('#ma_id_access_hidden').val(id);
        $('#ma_id_access').val(ma_title);
    });

    // Add store to list
    $(document).on('click', '#add_st_to_list', function(e) {
        var id = $(this).attr('data-id');
        var st_name = $(this).attr('data-st_name');
        $('#st_id_access_hidden').val(id);
        $('#st_id_access').val(st_name);
    });

    // Add menu access button
    $('#add_menu_access_btn').on('click', function() {
        if (ma_id.length <= 0) {
            Swal.fire('Tentukan Menu', 'Silahkan ketik menu pada pencarian, kemudian pilih salah satu', 'warning');
            return false;
        }
        $.ajax({
            type: "POST",
            data: { ma_id: ma_id, u_id: u_id },
            dataType: 'json',
            url: "{{ url('uma_save') }}",
            success: function(r) {
                if (r.status == '200') {
                    $('#ma_id_access_hidden').val('');
                    $('#ma_id_access').val('');
                    loadMenuAccessData(1);
                    loadUserMenu();
                    toastr.success('Data berhasil ditambah', 'Berhasil');
                } else {
                    toastr.error('Gagal tambah data', 'Gagal');
                }
            }
        });
    });

    // Delete menu access button
    $(document).on('click', '.delete_menu_access_btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var id = $(this).attr('data-id');
        Swal.fire({
            title: "Hapus..?",
            text: "Yakin hapus data ini ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batalkan'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: { id: id },
                    dataType: 'json',
                    url: "{{ url('uma_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            loadMenuAccessData(1);
                            loadUserMenu();
                            toastr.success('Data berhasil dihapus', 'Berhasil');
                        } else {
                            toastr.error('Gagal hapus data', 'Gagal');
                        }
                    }
                });
            }
        });
    });

    // Menu search with debounce
    var menuSearchTimeout;
    $('#menu_search').on('keyup', function() {
        clearTimeout(menuSearchTimeout);
        menuSearchTimeout = setTimeout(function() {
            loadMenuAccessData(1);
        }, 500);
    });

    // Close modals
    $('#close_group_btn, #close_group_btn_2').on('click', function() {
        closeGroupModal();
    });

    $('#close_user_btn, #close_user_btn_2').on('click', function() {
        closeUserModal();
    });

    $('#close_menu_access_btn, #close_menu_access_btn_2').on('click', function() {
        closeMenuAccessModal();
    });

    // Click outside to close autocomplete
    $(document).on('click', 'body', function(e) {
        if (!$(e.target).closest('#ma_id_access, #menuList').length) {
            $('#menuList').fadeOut();
        }
        if (!$(e.target).closest('#st_id_access, #storeList').length) {
            $('#storeList').fadeOut();
        }
    });

    $('#ma_id_access').on('focus', function() {
        $('#ma_id_access_hidden').val('');
        $('#ma_id_access').val('');
    });

    $('#st_id_access').on('focus', function() {
        $('#st_id_access_hidden').val('');
        $('#st_id_access').val('');
    });
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
