<script>
var namesetDataTable = null;
var namesetDataArray = [];

function loadNamesetData() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        url: "{{ url('nameset_datatables_simple') }}",
        type: 'GET',
        data: {
            search: $('#nameset_search').val()
        },
        dataType: 'json',
        success: function(response) {
            if (namesetDataTable) {
                namesetDataTable.destroy();
            }
            
            namesetDataArray = response.data || [];
            $('#NamesetDatatb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    var html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + row.no + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-900">' + row.pos_invoice + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.stt_name + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500" style="word-break: break-word; max-width: 400px;">' + row.article + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.pos_created + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500" style="word-break: break-word; max-width: 250px;">' + row.pos_note + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-500">' + row.action + '</td>';
                    html += '</tr>';
                    $('#NamesetDatatb tbody').append(html);
                });
            } else {
                $('#NamesetDatatb tbody').append('<tr><td colspan="7" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            // Initialize SimpleDatatables
            if (document.getElementById("NamesetDatatb") && typeof simpleDatatables !== 'undefined') {
                namesetDataTable = new simpleDatatables.DataTable("#NamesetDatatb", {
                    searchable: true,
                    sortable: true,
                    perPage: 25,
                    perPageSelect: [10, 25, 50, 100],
                    fixedColumns: false,
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
            }
        },
        error: function(xhr) {
            console.error('Error loading data:', xhr);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat memuat data'
            });
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
    loadNamesetData();

    // Search handler
    var searchTimeout;
    $('#nameset_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        var query = $(this).val();
        searchTimeout = setTimeout(function() {
            loadNamesetData();
        }, 400);
    });

    // Finish nameset button handler
    $(document).on('click', '#nameset_finish_btn', function() {
        var ptd_id = $(this).attr('data-ptd_id');
        
        Swal.fire({
            title: 'Selesai..?',
            text: 'Yakin nameset sudah selesai ?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yakin',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: {
                        _type: 'finish_nameset',
                        _ptd_id: ptd_id
                    },
                    dataType: 'json',
                    url: "{{ url('update_data_nameset') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            loadNamesetData(); // Reload data
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Berhasil menyelesaikan nameset',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Gagal menyelesaikan nameset'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat memproses data'
                        });
                    }
                });
            }
        });
    });

});
</script>
