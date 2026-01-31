<script>
function daysInMonth(month, year) {
    return new Date(year, month, 0).getDate();
}

var targetDataTable = null;
var targetDetailDataTable = null;
var global_tr_id, global_st_id, global_stt_id;
var targetDataArray = []; // Store data for export

function loadTargetData() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        url: "{{ url('target_datatables') }}",
        type: 'GET',
        data: {
            search: $('#target_search').val()
        },
        dataType: 'json',
        success: function(response) {
            if (targetDataTable) {
                targetDataTable.destroy();
            }
            
            // Store data for export
            targetDataArray = response.data || [];
            
            $('#Targettb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    var html = '<tr class="hover:bg-gray-50" data-tr_id="' + row.tr_id + '" data-tr_date="' + row.tr_date + '">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + row.DT_RowIndex + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-900 cursor-pointer" style="cursor: pointer;">' + (row.tr_date_show || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.tr_amount_show || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.tr_amount_get || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.tr_progress || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.tr_total_sale || '') + '</td>';
                    html += '</tr>';
                    $('#Targettb tbody').append(html);
                });
            } else {
                $('#Targettb tbody').append('<tr><td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("Targettb") && typeof simpleDatatables !== 'undefined') {
                targetDataTable = new simpleDatatables.DataTable("#Targettb", {
                    searchable: true,
                    sortable: true,
                    perPage: 25,
                    perPageSelect: [10, 25, 50, 100],
                    classes: {
                        wrapper: "datatable-wrapper",
                        top: "datatable-top",
                        bottom: "datatable-bottom",
                        search: "datatable-search",
                        selector: "datatable-selector",
                        table: "datatable-table",
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
            }
        },
        error: function(xhr) {
            console.error('Error loading target data:', xhr);
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data target.', 'error');
        }
    });
}

function loadTargetDetailData(tr_id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        url: "{{ url('target_detail_datatables') }}",
        type: 'GET',
        data: {
            tr_id: tr_id
        },
        dataType: 'json',
        success: function(response) {
            if (targetDetailDataTable) {
                targetDetailDataTable.destroy();
            }
            
            $('#TargetDetailtb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    var html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + row.DT_RowIndex + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-900">' + (row.st_name || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.stt_name || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.tr_amount_show || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.tr_amount_get || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.tr_progress || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.tr_total_sale || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.action || '') + '</td>';
                    html += '</tr>';
                    $('#TargetDetailtb tbody').append(html);
                });
            } else {
                $('#TargetDetailtb tbody').append('<tr><td colspan="8" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("TargetDetailtb") && typeof simpleDatatables !== 'undefined') {
                targetDetailDataTable = new simpleDatatables.DataTable("#TargetDetailtb", {
                    searchable: true,
                    sortable: true,
                    perPage: 25,
                    perPageSelect: [10, 25, 50, 100],
                    classes: {
                        wrapper: "datatable-wrapper",
                        top: "datatable-top",
                        bottom: "datatable-bottom",
                        search: "datatable-search",
                        selector: "datatable-selector",
                        table: "datatable-table",
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
            }
        },
        error: function(xhr) {
            console.error('Error loading target detail data:', xhr);
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data detail target.', 'error');
        }
    });
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadTargetData();

    // Search handler
    $('#target_search').on('keyup', function() {
        loadTargetData();
    });

    // Click row to open modal (skip last column which has button)
    $(document).on('click', '#Targettb tbody tr td:not(:nth-child(6))', function() {
        var $row = $(this).closest('tr');
        var tr_id = $row.data('tr_id');
        var tr_date = $row.data('tr_date');
        document.getElementById('TargetModal').classList.remove('hidden');
        $('#tr_date').val(tr_date);
        $('#_id').val(tr_id);
        $('#_mode').val('edit');
        @if (isset($data['user']) && $data['user']->delete_access == '1')
            $('#delete_target_btn').show();
        @endif
    });

    // Check exists target
    $('#tr_date').on('change', function() {
        var tr_date = $(this).val();
        $.ajax({
            type: "POST",
            data: {
                _tr_date: tr_date
            },
            dataType: 'json',
            url: "{{ url('check_exists_target') }}",
            success: function(r) {
                if (r.status == '200') {
                    Swal.fire('Target', 'Periode target sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                    $('#tr_date').val('');
                    return false;
                }
            }
        });
    });

    // Add target button
    $('#add_target_btn').on('click', function() {
        document.getElementById('TargetModal').classList.remove('hidden');
        $('#_id').val('');
        $('#_mode').val('add');
        $('#f_target')[0].reset();
        $('#delete_target_btn').hide();
    });

    // Import modal button
    $('#import_modal_btn').on('click', function() {
        document.getElementById('ImportModal').classList.remove('hidden');
    });

    // Add target detail button
    $('#add_target_detail_btn').on('click', function() {
        document.getElementById('SubSubTargetModal').classList.remove('hidden');
        $('#save_target_detail_btn').show();
        var date = $('#target_date').val();
        var dt = date.split('-');
        var total_row = daysInMonth(dt[0], dt[1]);
        $('#target_amount').empty();
        for (let i = 0; i < total_row; i++) {
            $('#target_amount').append(
                '<input class="w-full px-4 py-2 mb-2 border border-gray-300 rounded-lg text-sm" type="number" data-date="' + dt[1] + '-' + dt[0] + '-' + (i + 1) + '" id="target_amount' + (i + 1) + '" placeholder="Tanggal ' + (i + 1) + '"/>'
            );
        }
    });

    // Close SubSubTargetModal handler
    $('#SubSubTargetModal').on('hidden.bs.modal', function() {
        $('#target_amount').empty();
    });

    // Save target detail
    $('#save_target_detail_btn').on('click', function() {
        var date = $('#target_date').val();
        var tr_id = $('#tr_id').val();
        var st_id = $('#st_id').val();
        var stt_id = $('#stt_id').val();
        var dt = date.split('-');
        var total_row = daysInMonth(dt[0], dt[1]);
        var arr = [];

        if (st_id == '') {
            Swal.fire('Store', 'Store kosong, silahkan diisi dulu', 'warning');
            return false;
        }
        if (stt_id == '') {
            Swal.fire('Divisi', 'Divisi kosong, silahkan diisi dulu', 'warning');
            return false;
        }
        for (let i = 0; i < total_row; i++) {
            if ($('#target_amount' + (i + 1) + '').val() == '') {
                Swal.fire('Kosong', 'Masih ada target yang kosong, silahkan diisi dulu', 'warning');
                return false;
            }
            arr[i] = [$('#target_amount' + (i + 1) + '').attr('data-date'), $('#target_amount' + (i + 1) + '').val()];
        }
        $.ajax({
            type: "POST",
            data: {
                _tr_id: tr_id,
                _st_id: st_id,
                _stt_id: stt_id,
                _arr: arr
            },
            dataType: 'json',
            url: "{{ url('sv_target_detail') }}",
            success: function(r) {
                if (r.status == '200') {
                    closeSubSubTargetModal();
                    loadTargetDetailData(tr_id);
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                } else {
                    Swal.fire('Gagal', 'Gagal simpan data', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan saat memproses permintaan.', 'error');
            }
        });
        return false;
    });

    // Save target
    $('#f_target').on('submit', function(e) {
        e.preventDefault();
        $("#save_target_btn").html('Proses ..');
        $("#save_target_btn").attr("disabled", true);

        var formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: "{{ url('tr_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#save_target_btn").html('Simpan');
                $("#save_target_btn").attr("disabled", false);

                if (data.status == '200') {
                    closeTargetModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadTargetData();
                } else if (data.status == '400') {
                    Swal.fire('Gagal', 'Data tidak tersimpan', 'warning');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $("#save_target_btn").html('Simpan');
                $("#save_target_btn").attr("disabled", false);
                Swal.fire('Error', 'Terjadi kesalahan: ' + textStatus, 'error');
            }
        });
    });

    // Delete target
    $('#delete_target_btn').on('click', function() {
        Swal.fire({
            title: "Hapus..?",
            text: "Yakin hapus data ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batalkan'
        }).then(function(isConfirm) {
            if (isConfirm.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: {
                        _id: $('#_id').val()
                    },
                    dataType: 'json',
                    url: "{{ url('tr_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                            closeTargetModal();
                            loadTargetData();
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus data', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan saat menghapus data.', 'error');
                    }
                });
                return false;
            }
        });
    });

    // Sub target detail click (button [Penjualan] [Item Terjual])
    $(document).on('click', '#sub_target_detail', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var target_date = $(this).attr('data-target_date');
        var tr_id = $(this).attr('data-tr_id');
        $('#target_date').val(target_date);
        $('#tr_id').val(tr_id);
        document.getElementById('SubTargetModal').classList.remove('hidden');
        loadTargetDetailData(tr_id);
    });

    // Tr amount button click (button target amount di detail)
    $(document).on('click', '#tr_amount_btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var str_id = $(this).attr('data-id');
        $('#save_target_detail_btn').show();
        $.ajax({
            type: "POST",
            data: {
                _str_id: str_id
            },
            dataType: 'json',
            url: "{{ url('check_str') }}",
            success: function(r) {
                if (r.status == '200') {
                    document.getElementById('SubSubTargetModal').classList.remove('hidden');
                    var date = $('#target_date').val();
                    var dt = date.split('-');
                    $('#target_amount').empty();
                    $.each(r.sstr, function(index, value) {
                        $('#target_amount').append(
                            '<input class="w-full px-4 py-2 mb-2 border border-gray-300 rounded-lg text-sm edit_amount" data-sstr_id="' + value.id + '" type="number" data-date="' + dt[1] + '-' + dt[0] + '-' + (index + 1) + '" id="target_amount' + (index + 1) + '" value="' + value.sstr_amount + '" placeholder="Tanggal ' + (index + 1) + '"/>'
                        );
                    });
                }
            }
        });
        return false;
    });

    // Edit amount change
    $(document).on('change', '.edit_amount', function() {
        var sstr_id = $(this).attr('data-sstr_id');
        var value = $(this).val();
        $.ajax({
            type: "POST",
            data: {
                _sstr_id: sstr_id,
                _value: value
            },
            dataType: 'json',
            url: "{{ url('edit_target') }}",
            success: function(r) {
                if (r.status == '200') {
                    Swal.fire('Berhasil', 'Data berhasil diubah', 'success');
                    var tr_id = $('#tr_id').val();
                    if (tr_id) {
                        loadTargetDetailData(tr_id);
                    }
                }
            }
        });
        return false;
    });

    // Delete sub target
    $(document).on('click', '#delete_sub_target', function() {
        var str_id = $(this).attr('data-str_id');
        Swal.fire({
            title: "Hapus..?",
            text: "Yakin hapus data ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batalkan'
        }).then(function(isConfirm) {
            if (isConfirm.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: {
                        _str_id: str_id
                    },
                    dataType: 'json',
                    url: "{{ url('delete_sub_target') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Berhasil hapus data', 'success');
                            var tr_id = $('#tr_id').val();
                            loadTargetDetailData(tr_id);
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus data', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan saat menghapus data.', 'error');
                    }
                });
                return false;
            }
        });
    });

    // Import form
    $('#f_import').on('submit', function(e) {
        e.preventDefault();
        $("#import_data_btn").html('Proses ..');
        $("#import_data_btn").attr("disabled", true);

        var tr_id = $('#tr_id').val();
        var st_id = $('#import_st_id').val();
        var stt_id = $('#import_stt_id').val();

        if (st_id == '') {
            Swal.fire('Store', 'Store kosong, silahkan diisi dulu', 'warning');
            $("#import_data_btn").html('Import');
            $("#import_data_btn").attr("disabled", false);
            return false;
        }
        if (stt_id == '') {
            Swal.fire('Divisi', 'Divisi kosong, silahkan diisi dulu', 'warning');
            $("#import_data_btn").html('Import');
            $("#import_data_btn").attr("disabled", false);
            return false;
        }

        var formData = new FormData(this);
        formData.append('_tr_id', tr_id);
        formData.append('_st_id', st_id);
        formData.append('_stt_id', stt_id);

        $.ajax({
            type: 'POST',
            url: "{{ url('sv_target_detail_import') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#import_data_btn").html('Import');
                $("#import_data_btn").attr("disabled", false);
                if (data.status == '200') {
                    closeImportModal();
                    Swal.fire('Berhasil', 'Data berhasil diimport', 'success');
                    $('#f_import')[0].reset();
                    var tr_id = $('#tr_id').val();
                    if (tr_id) {
                        loadTargetDetailData(tr_id);
                    }
                } else if (data.status == '400') {
                    Swal.fire('Gagal', 'Data gagal diimport', 'warning');
                }
            },
            error: function(data) {
                $("#import_data_btn").html('Import');
                $("#import_data_btn").attr("disabled", false);
                Swal.fire('Error', 'Terjadi kesalahan saat import', 'error');
            }
        });
    });

    // Import modal button in SubTargetModal
    $('#ImportModalBtn').on('click', function() {
        document.getElementById('ImportModal').classList.remove('hidden');
    });

    // Export Excel button
    $('#target_excel_btn').on('click', function() {
        exportTargetToExcel();
    });
});

// Function to export target data to Excel
function exportTargetToExcel() {
    if (targetDataArray.length === 0) {
        Swal.fire('Info', 'Tidak ada data untuk diekspor', 'info');
        return;
    }

    // Create CSV content
    var csvContent = '\uFEFF'; // BOM for UTF-8
    csvContent += 'No,Bulan Tahun,Target,Tercapai,%,Penjualan Item Terjual\n';

    targetDataArray.forEach(function(row, index) {
        var no = index + 1;
        var date = row.tr_date_show || '';
        // Extract text from HTML buttons
        var target = $(row.tr_amount_show).text() || row.tr_amount_show || '';
        var tercapai = $(row.tr_amount_get).text() || row.tr_amount_get || '';
        var progress = $(row.tr_progress).text() || row.tr_progress || '';
        var sale = $(row.tr_total_sale).text() || row.tr_total_sale || '';
        
        csvContent += no + ',"' + date + '","' + target + '","' + tercapai + '","' + progress + '","' + sale + '"\n';
    });

    // Create blob and download
    var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    var link = document.createElement('a');
    var url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'target_' + new Date().toISOString().split('T')[0] + '.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    Swal.fire('Berhasil', 'Data berhasil diekspor', 'success');
}
</script>
