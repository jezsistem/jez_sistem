<script>
var artikelPromoTable = null;
var artikelPromoDataCache = [];
var currentPage = 1;
var perPage = 25;
var totalRecords = 0;
var totalPages = 0;

function cacheArtikelPromoData(data) {
    artikelPromoDataCache = data;
}

function loadArtikelPromoData(page = 1) {
    console.log('loadArtikelPromoData called, page:', page);
    currentPage = page;
    
    $.ajax({
        url: "{{ url('artikel_promo_datatables_simple') }}",
        type: 'GET',
        data: {
            search: $('#artikel_promo_search').val() || '',
            date_start: $('#artikelpromo_date').val() || '',
            page: page,
            per_page: perPage
        },
        dataType: 'json',
        beforeSend: function() {
            console.log('Loading artikel promo data...');
            // Show loading indicator
            $('#ArtikelPromotb tbody').html('<tr><td colspan="11" class="px-4 py-4 text-center text-sm text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat data...</td></tr>');
        },
        success: function(response) {
            console.log('Response received:', response);
            if (artikelPromoTable) {
                artikelPromoTable.destroy();
            }
            
            $('#ArtikelPromotb tbody').empty();
            
            if (response && response.data && response.data.length > 0) {
                console.log('Data found:', response.data.length, 'rows of', response.total, 'total');
                cacheArtikelPromoData(response.data);
                totalRecords = response.total || 0;
                totalPages = response.total_pages || 1;
                
                // Render rows
                var fragment = document.createDocumentFragment();
                var tbody = document.querySelector('#ArtikelPromotb tbody');
                
                response.data.forEach(function(row) {
                    var tr = document.createElement('tr');
                    tr.className = 'hover:bg-gray-50';
                    tr.setAttribute('data-a_id', row.a_id || '');
                    
                    tr.innerHTML = 
                        '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.no || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-900">' + (row.article_id || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.p_name || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.st_code || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.promo_name || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.date_start || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.date_end || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.promo_disc || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.p_price_tag || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.price_discount || '') + '</td>' +
                        '<td class="px-4 py-3 text-sm text-gray-700">' + (row.promo_note || '') + '</td>';
                    
                    fragment.appendChild(tr);
                });
                
                tbody.appendChild(fragment);
            } else {
                console.log('No data found');
                $('#ArtikelPromotb tbody').append('<tr><td colspan="11" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("ArtikelPromotb") && typeof simpleDatatables !== 'undefined') {
                console.log('Initializing SimpleDatatables...');
                artikelPromoTable = new simpleDatatables.DataTable("#ArtikelPromotb", {
                    searchable: false, // Disable client-side search, use server-side
                    sortable: false, // Disable client-side sort, use server-side
                    perPage: perPage,
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
                
                // Create custom pagination for server-side
                setTimeout(function() {
                    createCustomPagination('#ArtikelPromotb', totalRecords, currentPage, totalPages, perPage);
                    
                    // Handle per page change
                    var perPageSelect = document.querySelector('#ArtikelPromotb').closest('.datatable-wrapper').querySelector('.datatable-selector');
                    if (perPageSelect) {
                        perPageSelect.addEventListener('change', function() {
                            perPage = parseInt(this.value);
                            loadArtikelPromoData(1);
                        });
                    }
                }, 100);
                
                console.log('SimpleDatatables initialized');
            } else {
                console.error('ArtikelPromotb element not found or simpleDatatables not loaded');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading artikel promo data:', xhr, status, error);
            console.error('Response:', xhr.responseText);
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data artikel promo: ' + (xhr.responseJSON?.message || error), 'error');
        }
    });
}

function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage) {
    var wrapper = document.querySelector(tableId).closest('.datatable-wrapper');
    if (!wrapper) return;
    
    var bottom = wrapper.querySelector('.datatable-bottom');
    if (!bottom) return;
    
    // Remove existing custom pagination if any
    var existingPagination = bottom.querySelector('.custom-pagination');
    if (existingPagination) {
        existingPagination.remove();
    }
    
    // Hide SimpleDatatables pagination and info
    var simplePagination = bottom.querySelector('.datatable-pagination');
    if (simplePagination) {
        simplePagination.style.display = 'none';
    }
    
    // Hide SimpleDatatables info to prevent duplication
    var simpleInfo = bottom.querySelector('.datatable-info');
    if (simpleInfo) {
        simpleInfo.style.display = 'none';
    }
    
    // Create custom pagination
    var paginationDiv = document.createElement('div');
    paginationDiv.className = 'custom-pagination flex items-center gap-2';
    
    var start = totalRecords > 0 ? (currentPage - 1) * perPage + 1 : 0;
    var end = Math.min(currentPage * perPage, totalRecords);
    var infoText = totalRecords > 0 ? 'Menampilkan ' + start + ' sampai ' + end + ' dari ' + totalRecords + ' data' : 'Tidak ada data';
    
    // Info text
    var infoSpan = document.createElement('span');
    infoSpan.className = 'text-sm text-gray-600 mr-4';
    infoSpan.textContent = infoText;
    paginationDiv.appendChild(infoSpan);
    
    if (totalPages > 1) {
        // Previous button
        var prevBtn = document.createElement('button');
        prevBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50' + (currentPage === 1 ? ' opacity-50 cursor-not-allowed' : '');
        prevBtn.textContent = '‹';
        prevBtn.disabled = currentPage === 1;
        prevBtn.onclick = function(e) {
            e.preventDefault();
            if (currentPage > 1) {
                loadArtikelPromoData(currentPage - 1);
            }
        };
        paginationDiv.appendChild(prevBtn);
        
        // Page numbers
        var startPage = Math.max(1, currentPage - 2);
        var endPage = Math.min(totalPages, currentPage + 2);
        
        if (startPage > 1) {
            var firstBtn = document.createElement('button');
            firstBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50';
            firstBtn.textContent = '1';
            firstBtn.onclick = function(e) {
                e.preventDefault();
                loadArtikelPromoData(1);
            };
            paginationDiv.appendChild(firstBtn);
            if (startPage > 2) {
                var ellipsis = document.createElement('span');
                ellipsis.className = 'px-2 text-sm text-gray-500';
                ellipsis.textContent = '...';
                paginationDiv.appendChild(ellipsis);
            }
        }
        
        for (var i = startPage; i <= endPage; i++) {
            var pageBtn = document.createElement('button');
            pageBtn.className = 'px-3 py-1 text-sm border rounded ' + (i === currentPage ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-50');
            pageBtn.textContent = i;
            pageBtn.onclick = function(e, page) {
                e.preventDefault();
                loadArtikelPromoData(page);
            }.bind(null, null, i);
            paginationDiv.appendChild(pageBtn);
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                var ellipsis = document.createElement('span');
                ellipsis.className = 'px-2 text-sm text-gray-500';
                ellipsis.textContent = '...';
                paginationDiv.appendChild(ellipsis);
            }
            var lastBtn = document.createElement('button');
            lastBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50';
            lastBtn.textContent = totalPages;
            lastBtn.onclick = function(e) {
                e.preventDefault();
                loadArtikelPromoData(totalPages);
            };
            paginationDiv.appendChild(lastBtn);
        }
        
        // Next button
        var nextBtn = document.createElement('button');
        nextBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50' + (currentPage === totalPages ? ' opacity-50 cursor-not-allowed' : '');
        nextBtn.textContent = '›';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.onclick = function(e) {
            e.preventDefault();
            if (currentPage < totalPages) {
                loadArtikelPromoData(currentPage + 1);
            }
        };
        paginationDiv.appendChild(nextBtn);
    }
    
    bottom.appendChild(paginationDiv);
}

function initDateRangePicker() {
    var picker = $('#kt_dashboard_daterangepicker');
    if (picker.length === 0) {
        return;
    }
    var start = moment().subtract(null, null);
    var end = moment();

    function cb(start, end, label) {
        var title = '';
        var range = '';
        var hidden_range = '';

        if (label == 'All Days' || !label) {
            title = '';
            range = 'All Days';
            hidden_range = '';
        } else if ((end - start) < 100 || label == 'Today') {
            title = 'Today:';
            range = start.format('MMM D');
            hidden_range = start.format('YYYY-MM-DD');
        } else if (label == 'Yesterday') {
            title = 'Yesterday:';
            range = start.format('MMM D');
            hidden_range = start.format('YYYY-MM-DD');
        } else {
            range = start.format('MMM D') + ' - ' + end.format('MMM D');
            hidden_range = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
        }

        $('#artikelpromo_date').val(hidden_range);
        $('#kt_dashboard_daterangepicker_date').html(range);
        $('#kt_dashboard_daterangepicker_title').html(title);
        // Only reload data if this is not the initial call
        if (label !== undefined) {
            loadArtikelPromoData(1); // Reset to first page
        }
    }

    picker.daterangepicker({
        startDate: start,
        endDate: end,
        opens: 'left',
        applyClass: 'btn-primary',
        cancelClass: 'btn-light-primary',
        locale: {
            format: 'DD MMM YYYY',
            separator: ' - ',
            applyLabel: 'Terapkan',
            cancelLabel: 'Batal',
            fromLabel: 'Dari',
            toLabel: 'Sampai',
            customRangeLabel: 'Custom',
            weekLabel: 'W',
            daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            firstDay: 1
        },
        ranges: {
            'All Days': [null, null],
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);
    // Set initial display without triggering data load
    $('#artikelpromo_date').val('');
    $('#kt_dashboard_daterangepicker_date').html('All Days');
    $('#kt_dashboard_daterangepicker_title').html('');
}

function exportArtikelPromoToExcel() {
    window.location.href = "{{ url('export_artikel_promo') }}";
}

$(document).ready(function() {
    console.log('Document ready - artikel_promo_v2');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Initialize date picker first
    initDateRangePicker();
    
    // Load data after a short delay to ensure everything is ready
    setTimeout(function() {
        console.log('Loading artikel promo data on page load...');
        loadArtikelPromoData(1); // Start with page 1
    }, 200);
    
    // Search handler
    $('#artikel_promo_search').on('keyup', function() {
        var query = $(this).val();
        // Reset to first page when searching
        loadArtikelPromoData(1);
    });
    
    // Row click handler (edit) - prevent click on pagination/controls
    $(document).on('click', '#ArtikelPromotb tbody tr', function(e) {
        // Don't trigger if clicking on a button or link
        if ($(e.target).is('button, a, input, select, textarea') || $(e.target).closest('button, a, input, select, textarea').length > 0) {
            return;
        }
        
        var a_id = $(this).attr('data-a_id');
        if (!a_id) return;
        
        var row = artikelPromoDataCache.find(r => r.a_id == a_id);
        if (row) {
            $('#_id').val(row.a_id);
            $('#article_id').val(row.article_id || '');
            $('#st_id').val(row.st_id || '');
            $('#st_code').val(row.st_code || '');
            $('#promo_name').val(row.promo_name || '');
            // Convert date format from DD-MM-YYYY to YYYY-MM-DD for date input
            var date_start = row.date_start;
            var date_end = row.date_end;
            if (date_start && date_start.includes('-')) {
                var parts_start = date_start.split('-');
                if (parts_start.length === 3) {
                    date_start = parts_start[2] + '-' + parts_start[1] + '-' + parts_start[0];
                }
            }
            if (date_end && date_end.includes('-')) {
                var parts_end = date_end.split('-');
                if (parts_end.length === 3) {
                    date_end = parts_end[2] + '-' + parts_end[1] + '-' + parts_end[0];
                }
            }
            $('#date_start').val(date_start || '');
            $('#date_end').val(date_end || '');
            // Remove % from promo_disc if present
            var promo_disc = (row.promo_disc || '').toString().replace('%', '');
            $('#promo_disc').val(promo_disc);
            $('#promo_note').val(row.promo_note || '');
            $('#_mode').val('edit');
            
            if ("{{ $data['user']->delete_access }}" == '1') {
                $('#delete_artikel_promo_btn').show();
            } else {
                $('#delete_artikel_promo_btn').hide();
            }
            
            document.getElementById('ArtikelPromoModal').classList.remove('hidden');
        }
    });
    
    // Add button handler
    $('#add_artikel_promo_btn').on('click', function() {
        $('#_id').val('');
        $('#_mode').val('add');
        $('#ArtikelPromoform')[0].reset();
        $('#delete_artikel_promo_btn').hide();
        document.getElementById('ArtikelPromoModal').classList.remove('hidden');
    });
    
    // Form submit handler
    $('#ArtikelPromoform').on('submit', function(e) {
        e.preventDefault();
        $("#save_artikel_promo_btn").html('Proses ..');
        $("#save_artikel_promo_btn").attr("disabled", true);
        var formData = new FormData(this);
        var isEdit = $('#_mode').val() === 'edit';
        
        $.ajax({
            type: 'POST',
            url: "{{ url('artikel_promo_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#save_artikel_promo_btn").html('Simpan');
                $("#save_artikel_promo_btn").attr("disabled", false);
                
                if (data.status == '200') {
                    document.getElementById('ArtikelPromoModal').classList.add('hidden');
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadArtikelPromoData();
                } else if (data.status == '400') {
                    Swal.fire('Gagal', 'Data tidak tersimpan', 'warning');
                } else {
                    Swal.fire('Error', data.message || 'Terjadi kesalahan', 'error');
                }
            },
            error: function(xhr) {
                $("#save_artikel_promo_btn").html('Simpan').attr("disabled", false);
                Swal.fire('Error', 'Terjadi kesalahan saat memproses', 'error');
            }
        });
    });
    
    // Delete button handler
    $('#delete_artikel_promo_btn').on('click', function() {
        Swal.fire({
            title: "Hapus..?",
            text: "Yakin hapus data ini ?",
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
                    url: "{{ url('artikel_promo_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                            document.getElementById('ArtikelPromoModal').classList.add('hidden');
                            loadArtikelPromoData();
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus data', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan saat memproses permintaan', 'error');
                    }
                });
            }
        });
    });
    
    // Import modal handler
    $('#import_modal_btn').on('click', function() {
        document.getElementById('ImportModal').classList.remove('hidden');
    });
    
    // Import form handler
    $('#f_import').on('submit', function(e) {
        e.preventDefault();
        $('#import_data_btn').html('Proses...');
        $('#import_data_btn').attr('disabled', true);
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('artikel_promo_import') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#import_data_btn").html('Import');
                $("#import_data_btn").attr("disabled", false);
                
                if (data.status == '200') {
                    document.getElementById('ImportModal').classList.add('hidden');
                    Swal.fire('Berhasil', 'Data berhasil diimport', 'success');
                    $('#f_import')[0].reset();
                    loadArtikelPromoData();
                } else if (data.status == '400') {
                    document.getElementById('ImportModal').classList.add('hidden');
                    Swal.fire('Gagal', 'File yang anda import kosong atau format tidak tepat', 'warning');
                } else {
                    document.getElementById('ImportModal').classList.add('hidden');
                    Swal.fire('Error', 'Terjadi kesalahan saat memproses file', 'error');
                }
            },
            error: function(data) {
                $("#import_data_btn").html('Import');
                $("#import_data_btn").attr("disabled", false);
                Swal.fire('Error', 'Terjadi kesalahan saat memproses', 'error');
            }
        });
    });
    
    // Export button handler
    $('#export_btn').on('click', function(e) {
        e.preventDefault();
        exportArtikelPromoToExcel();
    });
});
</script>
