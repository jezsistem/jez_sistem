<script>
var currentPage = 1;
var perPage = 25;
var currentBrandPage = 1;
var currentArticlePage = 1;
var currentBnId = null;
var currentBnbId = null;
var currentBrId = null;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadBannerData(1);

    // Search handler
    var searchTimeout;
    $('#wb_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadBannerData(1);
        }, 500);
    });

    // Add banner button
    $('#add_wb_btn').on('click', function() {
        openBannerModal();
        $('#wb_modal_id').val('');
        $('#wb_modal_mode').val('add');
        $('#wb_modal_image').val('');
        $('#f_wb')[0].reset();
        $('#imagePreview').attr('src', '');
        $('#delete_wb_btn').addClass('hidden');
    });

    // Form submit handler
    $('#f_wb').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_wb_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('wb_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeBannerModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadBannerData(currentPage);
                } else if (data.status == '400') {
                    closeBannerModal();
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
    $(document).on('click', '#delete_wb_btn', function() {
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
                    data: {_id: $('#wb_modal_id').val()},
                    dataType: 'json',
                    url: "{{ url('wb_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                            closeBannerModal();
                            loadBannerData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus data', 'error');
                        }
                    }
                });
            }
        });
    });

    // Image preview
    var loadFile = function(event) {
        var output = document.getElementById('imagePreview');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
            URL.revokeObjectURL(output.src);
        }
    };
    window.loadFile = loadFile;

    // Brand form submit
    $(document).on('submit', '#f_brand', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_brand_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        formData.append('bn_id', currentBnId);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('bb_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeBrandEditModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadBrandData(currentBrandPage);
                    loadBannerData(currentPage);
                } else if (data.status == '400') {
                    closeBrandEditModal();
                    Swal.fire('Gagal', 'Data tidak tersimpan', 'warning');
                }
            },
            error: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });
    });

    // Article form submit
    $(document).on('submit', '#f_article', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_article_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        formData.append('bnb_id', currentBnbId);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('bbd_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeArticleEditModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadArticleData(currentArticlePage);
                    loadBrandData(currentBrandPage);
                    loadBannerData(currentPage);
                } else if (data.status == '400') {
                    closeArticleEditModal();
                    Swal.fire('Gagal', 'Data tidak tersimpan', 'warning');
                }
            },
            error: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });
    });

    // Add brand button
    $(document).on('click', '#add_brand_btn', function() {
        openBrandEditModal();
        $('#bb_modal_id').val('');
        $('#bb_modal_mode').val('add');
        $('#f_brand')[0].reset();
        $('#delete_brand_btn').addClass('hidden');
    });

    // Add article button
    $(document).on('click', '#add_article_btn', function() {
        openArticleEditModal();
        $('#bbd_modal_id').val('');
        $('#bbd_modal_mode').val('add');
        $('#f_article')[0].reset();
        $('#delete_article_btn').addClass('hidden');
    });

    // Delete brand handler
    $(document).on('click', '#delete_brand_btn', function() {
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
                    data: {_id: $('#bb_modal_id').val()},
                    dataType: 'json',
                    url: "{{ url('bb_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                            closeBrandEditModal();
                            loadBrandData(currentBrandPage);
                            loadBannerData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus data', 'error');
                        }
                    }
                });
            }
        });
    });

    // Delete article handler
    $(document).on('click', '#delete_article_btn', function() {
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
                    data: {_id: $('#bbd_modal_id').val()},
                    dataType: 'json',
                    url: "{{ url('bbd_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                            closeArticleEditModal();
                            loadArticleData(currentArticlePage);
                            loadBrandData(currentBrandPage);
                            loadBannerData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus data', 'error');
                        }
                    }
                });
            }
        });
    });

    // Brand search handler
    var brandSearchTimeout;
    $(document).on('keyup', '#brand_search', function() {
        clearTimeout(brandSearchTimeout);
        brandSearchTimeout = setTimeout(function() {
            loadBrandData(1);
        }, 500);
    });

    // Article search handler
    var articleSearchTimeout;
    $(document).on('keyup', '#article_search', function() {
        clearTimeout(articleSearchTimeout);
        articleSearchTimeout = setTimeout(function() {
            loadArticleData(1);
        }, 500);
    });
});

function loadBannerData(page = 1) {
    currentPage = page;
    var search = $('#wb_search').val();
    
    $.ajax({
        url: "{{ url('wb_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#wb_tbody').html('<tr><td colspan="8" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#wb_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="8" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').html('<img src="' + row.bn_image_url + '" class="w-20 h-12 object-cover rounded" />'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.bn_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.bn_slug));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.is_child_label));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.bn_sort));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.bn_filter_label));
                    var brandBtn = $('<button>')
                        .addClass('px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-red-600')
                        .text(row.brand_count)
                        .attr('data-id', row.id)
                        .on('click', function(e) {
                            e.stopPropagation();
                            currentBnId = row.id;
                            loadBrandData(1);
                            openBrandModal();
                        });
                    tr.append($('<td>').addClass('px-3 py-4').append(brandBtn));
                    tr.on('click', function() {
                        editBanner(row);
                    });
                    tbody.append(tr);
                });
            }

            createCustomPagination('#Wbtb', response.total, response.current_page, response.total_pages, response.per_page, loadBannerData);
        },
        error: function() {
            $('#wb_tbody').html('<tr><td colspan="8" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function editBanner(row) {
    openBannerModal();
    $('#wb_modal_id').val(row.id);
    $('#wb_modal_mode').val('edit');
    $('#wb_modal_image').val(row.bn_image);
    $('#bn_name').val(row.bn_name);
    $('#bn_slug').val(row.bn_slug);
    $('#bn_sort').val(row.bn_sort);
    $('#is_child').val(row.is_child);
    $('#bn_filter').val(row.bn_filter);
    if (row.bn_image) {
        $('#imagePreview').attr('src', row.bn_image_url);
    }
    if ($('#delete_access').val() == '1') {
        $('#delete_wb_btn').removeClass('hidden');
    }
}

function loadBrandData(page = 1) {
    currentBrandPage = page;
    var search = $('#brand_search').val();
    
    $.ajax({
        url: "{{ url('bb_brand_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            bn_id: currentBnId
        },
        success: function(response) {
            if (response.error) {
                $('#brand_tbody').html('<tr><td colspan="3" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#brand_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.br_name));
                    var articleBtn = $('<button>')
                        .addClass('px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-red-600')
                        .text(row.article_count)
                        .attr('data-id', row.id)
                        .attr('data-br_id', row.br_id)
                        .on('click', function(e) {
                            e.stopPropagation();
                            currentBnbId = row.id;
                            currentBrId = row.br_id;
                            loadArticleData(1);
                            openArticleModal();
                        });
                    tr.append($('<td>').addClass('px-3 py-4').append(articleBtn));
                    tr.on('click', function() {
                        editBrand(row);
                    });
                    tbody.append(tr);
                });
            }

            createCustomPagination('#Brandtb', response.total, response.current_page, response.total_pages, response.per_page, loadBrandData);
        },
        error: function() {
            $('#brand_tbody').html('<tr><td colspan="3" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function loadArticleData(page = 1) {
    currentArticlePage = page;
    var search = $('#article_search').val();
    
    $.ajax({
        url: "{{ url('bb_article_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            bnb_id: currentBnbId
        },
        success: function(response) {
            if (response.error) {
                $('#article_tbody').html('<tr><td colspan="2" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#article_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="2" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.pssc_name));
                    tr.on('click', function() {
                        editArticle(row);
                    });
                    tbody.append(tr);
                });
            }

            createCustomPagination('#Articletb', response.total, response.current_page, response.total_pages, response.per_page, loadArticleData);
        },
        error: function() {
            $('#article_tbody').html('<tr><td colspan="2" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function editBrand(row) {
    openBrandEditModal();
    $('#bb_modal_id').val(row.id);
    $('#bb_modal_mode').val('edit');
    $('#br_id').val(row.br_id).trigger('change');
    if ($('#delete_access').val() == '1') {
        $('#delete_brand_btn').removeClass('hidden');
    }
}

function editArticle(row) {
    openArticleEditModal();
    $('#bbd_modal_id').val(row.id);
    $('#bbd_modal_mode').val('edit');
    $('#pssc_id').val(row.pssc_id).trigger('change');
    if ($('#delete_access').val() == '1') {
        $('#delete_article_btn').removeClass('hidden');
    }
}

// Modal functions
function openBannerModal() {
    $('#WbModal').removeClass('hidden');
}

function closeBannerModal() {
    $('#WbModal').addClass('hidden');
}

function openBrandModal() {
    $('#BrandModal').removeClass('hidden');
    loadBrandData(1);
}

function closeBrandModal() {
    $('#BrandModal').addClass('hidden');
}

function openBrandEditModal() {
    $('#BrandEditModal').removeClass('hidden');
}

function closeBrandEditModal() {
    $('#BrandEditModal').addClass('hidden');
}

function openArticleModal() {
    $('#ArticleModal').removeClass('hidden');
    loadArticleData(1);
    // Reload article dropdown
    $.ajax({
        type: "GET",
        data: {br_id: currentBrId},
        dataType: 'html',
        url: "{{ url('reload_article') }}",
        success: function(r) {
            $('#article_reload').html(r);
        }
    });
}

function closeArticleModal() {
    $('#ArticleModal').addClass('hidden');
}

function openArticleEditModal() {
    $('#ArticleEditModal').removeClass('hidden');
}

function closeArticleEditModal() {
    $('#ArticleEditModal').addClass('hidden');
}


// Pagination function
function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage, loadFunction) {
    var wrapper = document.querySelector(tableId).closest('.bg-white');
    if (!wrapper) return;

    var paginationInfo = wrapper.querySelector('#wb_pagination_info, #brand_pagination_info, #article_pagination_info');
    var paginationControls = wrapper.querySelector('#wb_pagination_controls, #brand_pagination_controls, #article_pagination_controls');
    
    if (!paginationInfo || !paginationControls) return;

    // Remove existing pagination if any
    paginationControls.innerHTML = '';

    var start = totalRecords > 0 ? (currentPage - 1) * perPage + 1 : 0;
    var end = Math.min(currentPage * perPage, totalRecords);
    var infoText = totalRecords > 0 ? 'Menampilkan ' + start + ' sampai ' + end + ' dari ' + totalRecords + ' data' : 'Tidak ada data';

    paginationInfo.textContent = infoText;

    if (totalPages > 1) {
        // Previous button
        var prevBtn = document.createElement('button');
        prevBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50' + (currentPage === 1 ? ' opacity-50 cursor-not-allowed' : '');
        prevBtn.textContent = '‹';
        prevBtn.type = 'button';
        prevBtn.disabled = currentPage === 1;
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (currentPage > 1) {
                loadFunction(currentPage - 1);
            }
        });
        paginationControls.appendChild(prevBtn);

        // Page numbers
        var startPage = Math.max(1, currentPage - 2);
        var endPage = Math.min(totalPages, currentPage + 2);

        if (startPage > 1) {
            var firstBtn = document.createElement('button');
            firstBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50';
            firstBtn.textContent = '1';
            firstBtn.type = 'button';
            firstBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                loadFunction(1);
            });
            paginationControls.appendChild(firstBtn);
            if (startPage > 2) {
                var ellipsis = document.createElement('span');
                ellipsis.className = 'px-2 text-sm text-gray-500';
                ellipsis.textContent = '...';
                paginationControls.appendChild(ellipsis);
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
                    e.stopPropagation();
                    loadFunction(page);
                });
                paginationControls.appendChild(pageBtn);
            })(i);
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                var ellipsis = document.createElement('span');
                ellipsis.className = 'px-2 text-sm text-gray-500';
                ellipsis.textContent = '...';
                paginationControls.appendChild(ellipsis);
            }
            var lastBtn = document.createElement('button');
            lastBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50';
            lastBtn.textContent = totalPages;
            lastBtn.type = 'button';
            lastBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                loadFunction(totalPages);
            });
            paginationControls.appendChild(lastBtn);
        }

        // Next button
        var nextBtn = document.createElement('button');
        nextBtn.className = 'px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50' + (currentPage === totalPages ? ' opacity-50 cursor-not-allowed' : '');
        nextBtn.textContent = '›';
        nextBtn.type = 'button';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (currentPage < totalPages) {
                loadFunction(currentPage + 1);
            }
        });
        paginationControls.appendChild(nextBtn);
    }
}
</script>
