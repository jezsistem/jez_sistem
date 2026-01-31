<script>
var currentPage = 1;
var perPage = 25;
var currentTopdealsArticlePage = 1;
var currentArticleListPage = 1;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadTopdealsData(1);

    // Search handler
    var searchTimeout;
    $('#topdeals_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadTopdealsData(1);
        }, 500);
    });

    // Add topdeals button
    $('#add_topdeals_btn').on('click', function() {
        openTopdealsModal();
        $('#topdeals_modal_id').val('');
        $('#topdeals_modal_mode').val('add');
        $('#f_topdeals')[0].reset();
        $('#delete_topdeals_btn').addClass('hidden');
    });

    // Form submit handler
    $('#f_topdeals').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_topdeals_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('td_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeTopdealsModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadTopdealsData(currentPage);
                } else if (data.status == '400') {
                    closeTopdealsModal();
                    Swal.fire('Gagal', 'Data tidak tersimpan', 'warning');
                }
            },
            error: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });
    });

    // Delete handler
    $(document).on('click', '#delete_topdeals_btn', function() {
        Swal.fire({
            title: 'Hapus..?',
            text: 'Yakin hapus data ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batalkan'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: {_id: $('#topdeals_modal_id').val()},
                    dataType: 'json',
                    url: "{{ url('td_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                            closeTopdealsModal();
                            loadTopdealsData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus data', 'error');
                        }
                    }
                });
            }
        });
    });

    // Article detail button
    $(document).on('click', '#article_detail_btn', function() {
        var id = $(this).data('id');
        $('#_td_id').val(id);
        openArticleModal();
        loadTopdealsArticleData(id, 1);
    });

    // Add topdeals article button
    $(document).on('click', '#add_topdeals_article_btn', function() {
        openArticleListModal();
        loadArticleListData(1);
    });

    // Check article button
    $(document).on('click', '#check_article_btn', function() {
        var p_id = $(this).data('id');
        var td_id = $('#_td_id').val();
        $.ajax({
            type: "POST",
            data: {p_id: p_id, td_id: td_id},
            dataType: 'json',
            url: "{{ url('add_topdeals') }}",
            success: function(r) {
                if (r.status == '200') {
                    Swal.fire('Berhasil', 'Berhasil menambah artikel', 'success');
                    loadTopdealsData(currentPage);
                    loadTopdealsArticleData(td_id, currentTopdealsArticlePage);
                    closeArticleListModal();
                } else if (r.status == '300') {
                    Swal.fire('Sudah ada', 'Artikel sudah ada dalam list', 'warning');
                } else {
                    Swal.fire('Gagal', 'Gagal menambah artikel', 'error');
                }
            }
        });
    });

    // Delete topdeals article button
    $(document).on('click', '#delete_topdeals_article_btn', function() {
        var id = $(this).data('id');
        var td_id = $('#_td_id').val();
        Swal.fire({
            title: 'Hapus..?',
            text: 'Yakin hapus artikel ini dari topdeals?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batalkan'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: {id: id},
                    dataType: 'json',
                    url: "{{ url('delete_topdeals') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Berhasil hapus artikel', 'success');
                            loadTopdealsData(currentPage);
                            loadTopdealsArticleData(td_id, currentTopdealsArticlePage);
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus artikel', 'error');
                        }
                    }
                });
            }
        });
    });

    // Article list search
    var articleListSearchTimeout;
    $(document).on('keyup', '#article_list_search', function() {
        clearTimeout(articleListSearchTimeout);
        articleListSearchTimeout = setTimeout(function() {
            loadArticleListData(1);
        }, 500);
    });
});

function loadTopdealsData(page) {
    currentPage = page;
    var search = $('#topdeals_search').val();

    $('#topdeals_tbody').html(`
        <tr>
            <td colspan="5" class="px-3 py-4 text-center text-gray-500">
                <div class="flex justify-center items-center">
                    <svg class="animate-spin h-5 w-5 mr-3 text-blue-500" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memuat data...
                </div>
            </td>
        </tr>
    `);

    $.ajax({
        url: "{{ url('topdeals_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#topdeals_tbody').html(`
                    <tr>
                        <td colspan="5" class="px-3 py-4 text-center text-red-500">${response.error}</td>
                    </tr>
                `);
                return;
            }

            renderTopdealsTable(response.data);
            createCustomPagination('#Topdealstb', response.total, response.current_page, response.total_pages, response.per_page, loadTopdealsData, 'topdeals');
        },
        error: function(xhr) {
            var errorMsg = 'Terjadi kesalahan saat memuat data';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMsg = xhr.responseJSON.error;
            }
            $('#topdeals_tbody').html(`
                <tr>
                    <td colspan="5" class="px-3 py-4 text-center text-red-500">${errorMsg}</td>
                </tr>
            `);
        }
    });
}

function renderTopdealsTable(data) {
    if (!data || data.length === 0) {
        $('#topdeals_tbody').html(`
            <tr>
                <td colspan="5" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td>
            </tr>
        `);
        return;
    }

    var html = '';
    data.forEach(function(row) {
        html += `
            <tr class="bg-white border-b hover:bg-gray-50 cursor-pointer" 
                data-id="${row.id}" 
                data-name="${row.td_name}"
                data-due-date="${row.td_due_date_val}"
                data-due-time="${row.td_due_time}"
                data-status="${row.td_status}">
                <td class="px-3 py-3">${row.no}</td>
                <td class="px-3 py-3">${row.td_name}</td>
                <td class="px-3 py-3">
                    <button data-id="${row.id}" id="article_detail_btn" class="px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800 hover:bg-blue-200" onclick="event.stopPropagation();">
                        ${row.article}
                    </button>
                </td>
                <td class="px-3 py-3">${row.td_due_date_show}</td>
                <td class="px-3 py-3">
                    <span class="px-2 py-1 text-xs font-medium rounded ${row.td_status_class}">${row.td_status_label}</span>
                </td>
            </tr>
        `;
    });
    $('#topdeals_tbody').html(html);

    // Row click handler
    $('#topdeals_tbody tr').on('click', function(e) {
        if ($(e.target).closest('button').length) return;
        
        var id = $(this).data('id');
        var name = $(this).data('name');
        var dueDate = $(this).data('due-date');
        var dueTime = $(this).data('due-time');
        var status = $(this).data('status');
        
        openTopdealsModal();
        $('#topdeals_modal_id').val(id);
        $('#topdeals_modal_mode').val('edit');
        $('#td_name').val(name);
        $('#td_due_date').val(dueDate);
        $('#td_due_time').val(dueTime);
        $('#td_status').val(status);
        
        @if($data['user']->delete_access == '1')
        $('#delete_topdeals_btn').removeClass('hidden');
        @endif
    });
}

function loadTopdealsArticleData(td_id, page) {
    currentTopdealsArticlePage = page;
    
    $('#topdeals_article_tbody').html(`
        <tr>
            <td colspan="4" class="px-3 py-4 text-center text-gray-500">Memuat data...</td>
        </tr>
    `);

    $.ajax({
        url: "{{ url('topdeals_article_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            td_id: td_id
        },
        success: function(response) {
            if (response.error) {
                $('#topdeals_article_tbody').html(`
                    <tr>
                        <td colspan="4" class="px-3 py-4 text-center text-red-500">${response.error}</td>
                    </tr>
                `);
                return;
            }

            renderTopdealsArticleTable(response.data);
            createCustomPagination('#TopdealsArticletb', response.total, response.current_page, response.total_pages, response.per_page, function(p) { loadTopdealsArticleData(td_id, p); }, 'topdeals_article');
        },
        error: function(xhr) {
            $('#topdeals_article_tbody').html(`
                <tr>
                    <td colspan="4" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan</td>
                </tr>
            `);
        }
    });
}

function renderTopdealsArticleTable(data) {
    if (!data || data.length === 0) {
        $('#topdeals_article_tbody').html(`
            <tr>
                <td colspan="4" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td>
            </tr>
        `);
        return;
    }

    var html = '';
    data.forEach(function(row) {
        html += `
            <tr class="bg-white border-b hover:bg-gray-50">
                <td class="px-3 py-3">${row.no}</td>
                <td class="px-3 py-3">${row.br_name}</td>
                <td class="px-3 py-3">${row.p_name}</td>
                <td class="px-3 py-3">${row.p_color}</td>
                <td class="px-3 py-3">
                    <button data-id="${row.id}" id="delete_topdeals_article_btn" class="px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-800 hover:bg-red-200">
                        Hapus
                    </button>
                </td>
            </tr>
        `;
    });
    $('#topdeals_article_tbody').html(html);
}

function loadArticleListData(page) {
    currentArticleListPage = page;
    var search = $('#article_list_search').val();
    
    $('#article_list_tbody').html(`
        <tr>
            <td colspan="4" class="px-3 py-4 text-center text-gray-500">Memuat data...</td>
        </tr>
    `);

    $.ajax({
        url: "{{ url('article_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#article_list_tbody').html(`
                    <tr>
                        <td colspan="4" class="px-3 py-4 text-center text-red-500">${response.error}</td>
                    </tr>
                `);
                return;
            }

            renderArticleListTable(response.data);
            createCustomPagination('#ArticleListtb', response.total, response.current_page, response.total_pages, response.per_page, loadArticleListData, 'article_list');
        },
        error: function(xhr) {
            $('#article_list_tbody').html(`
                <tr>
                    <td colspan="4" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan</td>
                </tr>
            `);
        }
    });
}

function renderArticleListTable(data) {
    if (!data || data.length === 0) {
        $('#article_list_tbody').html(`
            <tr>
                <td colspan="4" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td>
            </tr>
        `);
        return;
    }

    var html = '';
    data.forEach(function(row) {
        html += `
            <tr class="bg-white border-b hover:bg-gray-50">
                <td class="px-3 py-3">${row.no}</td>
                <td class="px-3 py-3">${row.br_name}</td>
                <td class="px-3 py-3">${row.p_name}</td>
                <td class="px-3 py-3">${row.p_color}</td>
                <td class="px-3 py-3">
                    <button data-id="${row.id}" id="check_article_btn" class="px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800 hover:bg-blue-200">
                        add
                    </button>
                </td>
            </tr>
        `;
    });
    $('#article_list_tbody').html(html);
}

function openTopdealsModal() {
    document.getElementById('TopdealsModal').classList.remove('hidden');
}

function closeTopdealsModal() {
    document.getElementById('TopdealsModal').classList.add('hidden');
    $('#topdeals_modal_id').val('');
    $('#topdeals_modal_mode').val('');
    $('#f_topdeals')[0].reset();
}

function openArticleModal() {
    document.getElementById('ArticleModal').classList.remove('hidden');
}

function closeArticleModal() {
    document.getElementById('ArticleModal').classList.add('hidden');
    $('#_td_id').val('');
}

function openArticleListModal() {
    document.getElementById('ArticleListModal').classList.remove('hidden');
}

function closeArticleListModal() {
    document.getElementById('ArticleListModal').classList.add('hidden');
    $('#article_list_search').val('');
}

function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage, loadFunction, prefix) {
    prefix = prefix || 'default';
    var infoDiv = document.getElementById(prefix + '_pagination_info');
    var controlsDiv = document.getElementById(prefix + '_pagination_controls');
    
    if (!infoDiv || !controlsDiv) return;
    
    var start = totalRecords > 0 ? (currentPage - 1) * perPage + 1 : 0;
    var end = Math.min(currentPage * perPage, totalRecords);
    var infoText = totalRecords > 0 ? 'Menampilkan ' + start + ' sampai ' + end + ' dari ' + totalRecords + ' data' : 'Tidak ada data';
    infoDiv.textContent = infoText;
    
    controlsDiv.innerHTML = '';
    
    if (totalPages <= 1) return;
    
    var prevBtn = document.createElement('button');
    prevBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50' + (currentPage === 1 ? ' opacity-50 cursor-not-allowed' : '');
    prevBtn.textContent = '‹';
    prevBtn.type = 'button';
    prevBtn.disabled = currentPage === 1;
    prevBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (currentPage > 1) loadFunction(currentPage - 1);
    });
    controlsDiv.appendChild(prevBtn);
    
    var startPage = Math.max(1, currentPage - 2);
    var endPage = Math.min(totalPages, currentPage + 2);
    
    if (startPage > 1) {
        var firstBtn = document.createElement('button');
        firstBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50';
        firstBtn.textContent = '1';
        firstBtn.type = 'button';
        firstBtn.addEventListener('click', function(e) {
            e.preventDefault();
            loadFunction(1);
        });
        controlsDiv.appendChild(firstBtn);
        if (startPage > 2) {
            var ellipsis = document.createElement('span');
            ellipsis.className = 'px-2 text-sm text-gray-500';
            ellipsis.textContent = '...';
            controlsDiv.appendChild(ellipsis);
        }
    }
    
    for (var i = startPage; i <= endPage; i++) {
        (function(page) {
            var pageBtn = document.createElement('button');
            pageBtn.className = 'px-3 py-1 text-sm border rounded ' + (page === currentPage ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-50');
            pageBtn.textContent = page;
            pageBtn.type = 'button';
            pageBtn.addEventListener('click', function(e) {
                e.preventDefault();
                loadFunction(page);
            });
            controlsDiv.appendChild(pageBtn);
        })(i);
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            var ellipsis = document.createElement('span');
            ellipsis.className = 'px-2 text-sm text-gray-500';
            ellipsis.textContent = '...';
            controlsDiv.appendChild(ellipsis);
        }
        var lastBtn = document.createElement('button');
        lastBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50';
        lastBtn.textContent = totalPages;
        lastBtn.type = 'button';
        lastBtn.addEventListener('click', function(e) {
            e.preventDefault();
            loadFunction(totalPages);
        });
        controlsDiv.appendChild(lastBtn);
    }
    
    var nextBtn = document.createElement('button');
    nextBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50' + (currentPage === totalPages ? ' opacity-50 cursor-not-allowed' : '');
    nextBtn.textContent = '›';
    nextBtn.type = 'button';
    nextBtn.disabled = currentPage === totalPages;
    nextBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (currentPage < totalPages) loadFunction(currentPage + 1);
    });
    controlsDiv.appendChild(nextBtn);
}
</script>
