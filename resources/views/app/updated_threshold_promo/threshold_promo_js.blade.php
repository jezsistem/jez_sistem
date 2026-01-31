<script>
var promoRecommendationTable = null;
var promoRecommendationDetailTable = null;
var promoRecommendationDataCache = [];
var promoRecommendationDetailDataCache = [];
var pr_id = '';

function cachePromoRecommendationData(data) {
    promoRecommendationDataCache = data;
}

function cachePromoRecommendationDetailData(data) {
    promoRecommendationDetailDataCache = data;
}

// Function to attach detail table handlers - moved outside document.ready
function attachDetailTableHandlers() {
    // Remove existing handlers to avoid duplicates
    $(document).off('click', '#PromoRecommendationDetailtb .delete-btn');
    $(document).off('click', '#PromoRecommendationDetailtb .edit-btn');
    
    // Use event delegation on the table body to handle dynamically added buttons
    var detailTableBody = document.querySelector('#PromoRecommendationDetailtb tbody');
    if (!detailTableBody) return;
    
    // Delete detail handler
    detailTableBody.addEventListener('click', function(e) {
        var target = e.target.closest('.delete-btn');
        if (target) {
            e.preventDefault();
            e.stopPropagation();
            const prd_id = target.getAttribute('data-id') || target.dataset.id;
            if (!prd_id) {
                Swal.fire('Error', 'Failed to retrieve the ID for deletion.', 'error');
                return;
            }
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
                        type: "DELETE",
                        url: "{{ url('threshold_promo_detail_delete') }}/" + prd_id,
                        success: function(r) {
                            if (r.status == '200') {
                                Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                                loadPromoRecommendationDetailData(pr_id);
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
        }
    });
    
    // Edit detail handler
    detailTableBody.addEventListener('click', function(e) {
        var target = e.target.closest('.edit-btn');
        if (target) {
            e.preventDefault();
            e.stopPropagation();
            const prd_id = target.getAttribute('data-id') || target.dataset.id;
            const discount = target.getAttribute('data-discount') || target.dataset.discount;
            if (!prd_id) {
                Swal.fire('Error', 'Failed to retrieve the ID for editing.', 'error');
                return;
            }
            $('#edit_threshold_promo_id').val(prd_id);
            $('#threshold_discount').val(discount);
            document.getElementById('EditThresholdPromoModal').classList.remove('hidden');
        }
    });
}

function loadPromoRecommendationData() {
    $.ajax({
        url: "{{ url('threshold_promo_datatables_simple') }}",
        type: 'GET',
        data: {
            search: $('#threshold_promo_search').val(),
            channel: $('#channel_threshold_promo').val(),
            date_start: $('#threshold_promo_date_start').val(),
            date_end: $('#threshold_promo_date_end').val()
        },
        dataType: 'json',
        success: function(response) {
            if (promoRecommendationTable) {
                promoRecommendationTable.destroy();
            }
            
            $('#PromoRecommendationtb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                cachePromoRecommendationData(response.data);
                response.data.forEach(function(row) {
                    let html = '<tr class="hover:bg-gray-50" data-pr_id="' + (row.pr_id || '') + '">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.no || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-900">' + (row.pr_code || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.channel || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.created_at || '') + '</td>';
                    html += '</tr>';
                    $('#PromoRecommendationtb tbody').append(html);
                });
            } else {
                $('#PromoRecommendationtb tbody').append('<tr><td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("PromoRecommendationtb") && typeof simpleDatatables !== 'undefined') {
                promoRecommendationTable = new simpleDatatables.DataTable("#PromoRecommendationtb", {
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
            console.error('Error loading promo recommendation data:', xhr);
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data threshold promo', 'error');
        }
    });
}

function loadPromoRecommendationDetailData(pr_id_param) {
    $.ajax({
        url: "{{ url('threshold_promo_detail_datatables_simple') }}",
        type: 'GET',
        data: {
            pr_id: pr_id_param
        },
        dataType: 'json',
        success: function(response) {
            if (promoRecommendationDetailTable) {
                promoRecommendationDetailTable.destroy();
            }
            
            $('#PromoRecommendationDetailtb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                cachePromoRecommendationDetailData(response.data);
                response.data.forEach(function(row) {
                    let html = '<tr class="hover:bg-gray-50" data-prd_id="' + (row.prd_id || '') + '">';
                    html += '<td class="px-4 py-3 text-sm text-gray-500 text-center">' + (row.no || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-900">' + (row.article_id || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.p_name || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.promo_disc || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.p_price_tag || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.price_discount || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.notes || '') + '</td>';
                    html += '<td class="px-4 py-3 text-sm text-gray-700">' + (row.action || '') + '</td>';
                    html += '</tr>';
                    $('#PromoRecommendationDetailtb tbody').append(html);
                });
            } else {
                $('#PromoRecommendationDetailtb tbody').append('<tr><td colspan="8" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("PromoRecommendationDetailtb") && typeof simpleDatatables !== 'undefined') {
                promoRecommendationDetailTable = new simpleDatatables.DataTable("#PromoRecommendationDetailtb", {
                    searchable: true,
                    sortable: false,
                    perPage: 10,
                    classes: {
                        wrapper: "datatable-wrapper",
                        top: "datatable-top",
                        bottom: "datatable-bottom",
                        search: "datatable-search",
                        table: "datatable-table"
                    }
                });
                
                // Re-attach event handlers after table is rendered
                setTimeout(function() {
                    attachDetailTableHandlers();
                }, 200);
            }
        },
        error: function(xhr) {
            console.error('Error loading promo recommendation detail data:', xhr);
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data detail', 'error');
        }
    });
}

function initDateRangePicker() {
    var picker = $('#kt_dashboard_daterangepicker');
    if (picker.length === 0) {
        return;
    }
    var start = moment();
    var end = moment();

    function cb(start, end, label) {
        var title = '';
        var range = '';
        var hidden_range_start = '';
        var hidden_range_end = '';

        if (label == 'All Days' || !label) {
            title = '';
            range = 'All Days';
            hidden_range_start = '';
            hidden_range_end = '';
        } else if ((end - start) < 100 || label == 'Today') {
            title = 'Today:';
            range = start.format('MMM D');
            hidden_range_start = start.format('YYYY-MM-DD');
            hidden_range_end = start.format('YYYY-MM-DD');
        } else if (label == 'Yesterday') {
            title = 'Yesterday:';
            range = start.format('MMM D');
            hidden_range_start = start.format('YYYY-MM-DD');
            hidden_range_end = start.format('YYYY-MM-DD');
        } else {
            range = start.format('MMM D') + ' - ' + end.format('MMM D');
            hidden_range_start = start.format('YYYY-MM-DD');
            hidden_range_end = end.format('YYYY-MM-DD');
        }

        $('#threshold_promo_date_start').val(hidden_range_start);
        $('#threshold_promo_date_end').val(hidden_range_end);
        $('#kt_dashboard_daterangepicker_date').html(range);
        $('#kt_dashboard_daterangepicker_title').html(title);
        loadPromoRecommendationData();
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
    cb(start, end, 'All Days');
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    loadPromoRecommendationData();
    initDateRangePicker();
    
    // Search handler
    $('#threshold_promo_search').on('keyup', function() {
        var query = $(this).val();
        if (jQuery.trim(query).length > 3 || jQuery.trim(query).length < 1) {
            loadPromoRecommendationData();
        }
    });
    
    // Channel filter handler
    $('#channel_threshold_promo').on('change', function() {
        loadPromoRecommendationData();
    });
    
    // Row click handler (open detail modal) - prevent click on pagination/controls
    $(document).on('click', '#PromoRecommendationtb tbody tr', function(e) {
        // Don't trigger if clicking on a button or link
        if ($(e.target).is('button, a, input, select, textarea') || $(e.target).closest('button, a, input, select, textarea').length > 0) {
            return;
        }
        
        var pr_id_param = $(this).attr('data-pr_id');
        if (!pr_id_param) return;
        
        pr_id = pr_id_param;
        var row = promoRecommendationDataCache.find(r => r.pr_id == pr_id_param);
        if (row) {
            $('.pr_code').text('(' + (row.pr_code || '') + ')');
            loadPromoRecommendationDetailData(pr_id_param);
            document.getElementById('PromoRecommendationDetailModal').classList.remove('hidden');
        }
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
            url: "{{ url('threshold_promo_import') }}",
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
                    loadPromoRecommendationData();
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
    
    // Export detail button handler
    $('#ExportArticleData').on('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: "Ekspor Data",
            text: "Apakah Anda ingin mengekspor data?",
            icon: "info",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ekspor',
            cancelButtonText: 'Batal'
        }).then(function(isConfirm) {
            if (isConfirm.isConfirmed) {
                var url = "{{ url('export_threshold_promo_detail') }}" + "?pr_id=" + pr_id;
                window.location.href = url;
            }
        });
    });
    
    // Export button handler
    $('#export_btn').on('click', function(e) {
        e.preventDefault();
        var search = $('#threshold_promo_search').val();
        var channel = $('#channel_threshold_promo').val();
        var date_start = $('#threshold_promo_date_start').val();
        var date_end = $('#threshold_promo_date_end').val();
        
        Swal.fire({
            title: "Ekspor Data",
            text: "Apakah Anda ingin mengekspor data?",
            icon: "info",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ekspor',
            cancelButtonText: 'Batal'
        }).then(function(isConfirm) {
            if (isConfirm.isConfirmed) {
                var url = "{{ url('export_threshold_promo') }}" + "?search=" + encodeURIComponent(search) +
                    "&channel=" + encodeURIComponent(channel) +
                    "&date_start=" + encodeURIComponent(date_start) +
                    "&date_end=" + encodeURIComponent(date_end);
                window.location.href = url;
            }
        });
    });
    
    // Delete promo recommendation handler
    $('#delete_promo_recommendation').on('click', function() {
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
                        _id: pr_id
                    },
                    dataType: 'json',
                    url: "{{ url('threshold_promo_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                            document.getElementById('PromoRecommendationDetailModal').classList.add('hidden');
                            loadPromoRecommendationData();
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
    
    
    // Save edit detail handler
    $('#save_edit_threshold_promo_btn').on('click', function() {
        var prd_id = $('#edit_threshold_promo_id').val();
        var discount = $('#threshold_discount').val();
        
        $.ajax({
            type: "POST",
            url: "{{ url('threshold_promo_detail_update') }}/" + prd_id,
            data: {
                discount: discount,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(r) {
                if (r.status == '200') {
                    Swal.fire('Berhasil', 'Data berhasil diupdate', 'success');
                    document.getElementById('EditThresholdPromoModal').classList.add('hidden');
                    loadPromoRecommendationDetailData(pr_id);
                } else {
                    Swal.fire('Gagal', 'Gagal update data', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan saat memproses permintaan', 'error');
            }
        });
    });
});
</script>
