<script>
var userShiftCurrentPage = 1;
var perPage = 25;

function loadCurrentShiftData(page = 1) {
    userShiftCurrentPage = page;
    var search = $('#user_shift_search').val();
    var st_id = $('#st_id_filter').val();
    
    $.ajax({
        url: "{{ url('report_current_shift_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            st_id: st_id
        },
        success: function(response) {
            if (response.error) {
                $('#user_shift_tbody').html('<tr><td colspan="6" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#user_shift_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.u_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.st_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.date));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.start_time));
                    tr.append($('<td>').addClass('px-3 py-4 text-right').text(row.total_pos_payment_price));
                    tr.on('click', function() {
                        loadCurrentShiftDetail(row);
                    });
                    tbody.append(tr);
                });
            }

            createCustomPagination('#UserShiftTable', response.total, response.current_page, response.total_pages, response.per_page, loadCurrentShiftData, 'user_shift');
        },
        error: function() {
            $('#user_shift_tbody').html('<tr><td colspan="6" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function loadCurrentShiftDetail(row) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Get current date and time for end_time
    var now = new Date();
    var end_time = now.getFullYear() + '-' + 
                   String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                   String(now.getDate()).padStart(2, '0') + ' ' + 
                   String(now.getHours()).padStart(2, '0') + ':' + 
                   String(now.getMinutes()).padStart(2, '0') + ':' + 
                   String(now.getSeconds()).padStart(2, '0');

    $.ajax({
        url: "{{ url('current_shift_detail') }}",
        type: 'POST',
        data: {
            id: row.id,
            start_time_original: row.start_time_original,
            end_time_original: end_time,
            date: row.date,
            start_time: row.start_time,
            end_time: end_time,
            total_pos_real_price: row.total_pos_real_price.replace(/[^0-9]/g, ''),
            st_name: row.st_name,
            u_name: row.u_name,
            st_id: row.st_id
        },
        success: function(response) {
            $('#UserShiftModal').removeClass('hidden');
            $('#UserShiftModalBody').html(response);

            // Product sold button handler
            $('#userShiftDetailSoldBtn').on('click', function() {
                $.ajax({
                    url: "{{ url('report_shift_product_sold') }}",
                    type: 'POST',
                    data: {
                        id: row.id,
                        start_time_original: row.start_time_original,
                        end_time_original: end_time,
                    },
                    success: function(response_detail) {
                        $('#UserShiftDetailSoldModal').removeClass('hidden');
                        $('#UserShiftDetailSoldModalBody').html(response_detail);
                    },
                    error: function(response) {
                        console.log(response);
                        Swal.fire('Error', 'Gagal memuat data produk terjual', 'error');
                    }
                });
            });

            // Product refund button handler
            $('#userShiftDetailRefundBtn').on('click', function() {
                $.ajax({
                    url: "{{ url('report_shift_product_refund') }}",
                    type: 'POST',
                    data: {
                        id: row.id,
                        start_time_original: row.start_time_original,
                        end_time_original: end_time,
                    },
                    success: function(response_detail) {
                        $('#UserShiftDetailRefundModal').removeClass('hidden');
                        $('#UserShiftDetailRefundModalBody').html(response_detail);
                    },
                    error: function(response) {
                        console.log(response);
                        Swal.fire('Error', 'Gagal memuat data produk refund', 'error');
                    }
                });
            });
        },
        error: function(response) {
            console.log(response);
            Swal.fire('Error', 'Gagal memuat detail shift', 'error');
        }
    });
}

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

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadCurrentShiftData(1);

    // Search handler with debounce
    var userShiftSearchTimeout;
    $('#user_shift_search').on('keyup', function() {
        clearTimeout(userShiftSearchTimeout);
        userShiftSearchTimeout = setTimeout(function() {
            loadCurrentShiftData(1);
        }, 500);
    });

    // Close modal handlers
    $('#close_user_shift_btn, #close_user_shift_btn_2').on('click', function() {
        $('#UserShiftModal').addClass('hidden');
    });

    $('#close_user_shift_detail_sold_btn, #close_user_shift_detail_sold_btn_2').on('click', function() {
        $('#UserShiftDetailSoldModal').addClass('hidden');
    });

    $('#close_user_shift_detail_refund_btn, #close_user_shift_detail_refund_btn_2').on('click', function() {
        $('#UserShiftDetailRefundModal').addClass('hidden');
    });
});
</script>
