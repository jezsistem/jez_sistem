@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
var currentPage = 1;
var perPage = 25;
var totalRecords = 0;
var totalPages = 0;

// ==================== WHATSAPP DATA ====================
function loadWhatsappData(page = 1) {
    currentPage = page;
    
    $('#WhatsappTableBody').html(`
        <tr>
            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                <div class="flex flex-col items-center">
                    <i class="fa fa-spinner fa-spin text-3xl text-gray-400 mb-2"></i>
                    <span>Memuat data...</span>
                </div>
            </td>
        </tr>
    `);
    
    $.ajax({
        url: "{{ url('whatsapp_datatables_simple') }}",
        type: "GET",
        data: {
            page: page,
            per_page: $('#per_page').val(),
            search: $('#whatsapp_search').val()
        },
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                toastr.error(response.error, 'Error');
                $('#WhatsappTableBody').html(`
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fa fa-exclamation-triangle text-3xl text-red-400 mb-2"></i>
                                <span>${response.error}</span>
                            </div>
                        </td>
                    </tr>
                `);
                return;
            }
            
            totalRecords = response.total;
            totalPages = response.total_pages;
            currentPage = response.current_page;
            perPage = response.per_page;
            
            renderWhatsappTable(response.data);
            updatePagination();
        },
        error: function(xhr) {
            toastr.error('Gagal memuat data', 'Error');
            $('#WhatsappTableBody').html(`
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fa fa-exclamation-triangle text-3xl text-red-400 mb-2"></i>
                            <span>Gagal memuat data</span>
                        </div>
                    </td>
                </tr>
            `);
        }
    });
}

function renderWhatsappTable(data) {
    if (data.length === 0) {
        $('#WhatsappTableBody').html(`
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <i class="fa fa-inbox text-3xl text-gray-400 mb-2"></i>
                        <span>Tidak ada data pesan</span>
                    </div>
                </td>
            </tr>
        `);
        return;
    }
    
    var html = '';
    data.forEach(function(row) {
        html += `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${row.no}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">${escapeHtml(row.wa_receiver)}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">${escapeHtml(row.wa_phone)}</td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-medium rounded-full ${row.wa_status_class}">${row.wa_status}</span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">${row.created_at}</td>
            </tr>
        `;
    });
    
    $('#WhatsappTableBody').html(html);
}

function escapeHtml(text) {
    if (text === null || text === undefined) return '-';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}

function updatePagination() {
    var start = ((currentPage - 1) * perPage) + 1;
    var end = Math.min(currentPage * perPage, totalRecords);
    
    if (totalRecords === 0) {
        start = 0;
        end = 0;
    }
    
    $('#showing_start').text(start);
    $('#showing_end').text(end);
    $('#total_records').text(totalRecords);
    
    // Generate numbered pagination
    var container = $('#pagination_container');
    container.empty();
    
    if (totalPages <= 1) return;
    
    // Previous button
    var prevBtn = $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (currentPage === 1 ? 'border-gray-200 text-gray-400 cursor-not-allowed' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .html('‹')
        .prop('disabled', currentPage === 1)
        .on('click', function() {
            if (currentPage > 1) loadWhatsappData(currentPage - 1);
        });
    container.append(prevBtn);
    
    // Page numbers
    var startPage = Math.max(1, currentPage - 2);
    var endPage = Math.min(totalPages, currentPage + 2);
    
    // First page
    if (startPage > 1) {
        container.append(createPageBtn(1));
        if (startPage > 2) {
            container.append($('<span>').addClass('px-2 text-sm text-gray-500').text('...'));
        }
    }
    
    // Middle pages
    for (var i = startPage; i <= endPage; i++) {
        container.append(createPageBtn(i));
    }
    
    // Last page
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            container.append($('<span>').addClass('px-2 text-sm text-gray-500').text('...'));
        }
        container.append(createPageBtn(totalPages));
    }
    
    // Next button
    var nextBtn = $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (currentPage === totalPages ? 'border-gray-200 text-gray-400 cursor-not-allowed' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .html('›')
        .prop('disabled', currentPage === totalPages)
        .on('click', function() {
            if (currentPage < totalPages) loadWhatsappData(currentPage + 1);
        });
    container.append(nextBtn);
}

function createPageBtn(page) {
    var isActive = page === currentPage;
    return $('<button>')
        .addClass('px-3 py-1.5 text-sm border rounded-lg ' + (isActive ? 'bg-green-600 text-white border-green-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'))
        .text(page)
        .on('click', function() {
            loadWhatsappData(page);
        });
}

// ==================== DOCUMENT READY ====================
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Load initial data
    loadWhatsappData(1);
    
    // Search with debounce
    var searchTimeout;
    $('#whatsapp_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadWhatsappData(1);
        }, 300);
    });
    
    // Per page change
    $('#per_page').on('change', function() {
        perPage = $(this).val();
        loadWhatsappData(1);
    });
    
    // Add whatsapp button
    $('#add_whatsapp_btn').on('click', function() {
        $('#_id').val('');
        $('#_mode').val('add');
        $('#f_whatsapp')[0].reset();
        $('#wa_phone_container').addClass('hidden');
        $('#WaModal').removeClass('hidden');
    });
    
    // Type change - show/hide phone input
    $('#wa_type').on('change', function() {
        if ($(this).val() == 'people') {
            $('#wa_phone_container').removeClass('hidden');
            $('#wa_phone').prop('required', true);
        } else {
            $('#wa_phone_container').addClass('hidden');
            $('#wa_phone').prop('required', false);
        }
    });
    
    // Save whatsapp form
    $('#f_whatsapp').on('submit', function(e) {
        e.preventDefault();
        $("#save_whatsapp_btn").html('<i class="fa fa-spinner fa-spin mr-2"></i>Mengirim...');
        $("#save_whatsapp_btn").prop("disabled", true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('send_wa') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#save_whatsapp_btn").html('<i class="fa fa-paper-plane mr-2"></i>Kirim');
                $("#save_whatsapp_btn").prop("disabled", false);
                
                if (data.status == '200') {
                    $('#WaModal').addClass('hidden');
                    $('#f_whatsapp')[0].reset();
                    toastr.success("Pesan berhasil dikirim", "Berhasil");
                    loadWhatsappData(1);
                } else {
                    toastr.warning("Pesan gagal dikirim", "Gagal");
                }
            },
            error: function(xhr) {
                $("#save_whatsapp_btn").html('<i class="fa fa-paper-plane mr-2"></i>Kirim');
                $("#save_whatsapp_btn").prop("disabled", false);
                toastr.error("Terjadi kesalahan saat mengirim pesan", "Error");
            }
        });
    });
    
    // Close modal buttons
    $(document).on('click', '.close-modal', function() {
        $(this).closest('.modal-overlay').addClass('hidden');
    });
    
    // Close modal on overlay click
    $(document).on('click', '.modal-overlay', function(e) {
        if (e.target === this) {
            $(this).addClass('hidden');
        }
    });
});
</script>
@endpush
