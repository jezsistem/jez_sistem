<script>
var currentPage = 1;
var perPage = 25;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadWebSubCategoryData(1);

    // Search handler
    var searchTimeout;
    $('#wsc_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadWebSubCategoryData(1);
        }, 500);
    });

    // Add web sub category button
    $('#add_web_sub_category_btn').on('click', function() {
        openWebSubCategoryModal();
        $('#wsc_modal_id').val('');
        $('#wsc_modal_mode').val('add');
        $('#wsc_modal_banner').val('');
        $('#f_wsc')[0].reset();
        $('#bannerPreview').attr('src', '').hide();
    });

    // Form submit handler
    $('#f_wsc').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_wsc_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('wsc_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeWebSubCategoryModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadWebSubCategoryData(currentPage);
                } else if (data.status == '400') {
                    closeWebSubCategoryModal();
                    Swal.fire('Gagal', 'Data tidak tersimpan', 'warning');
                }
            },
            error: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });
    });

    // Banner preview
    var loadBanner = function(event) {
        var output = document.getElementById('bannerPreview');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.style.display = 'block';
        output.onload = function() {
            URL.revokeObjectURL(output.src);
        }
    };
    window.loadBanner = loadBanner;

    // Delete banner handler
    $('#bannerPreview').on('click', function() {
        let sid = $('#wsc_modal_id').val();
        let image = $('#wsc_modal_banner').val();
        if (!sid || !image) return;
        
        Swal.fire({
            title: 'Hapus Gambar Banner ..?',
            text: 'Gambar Banner akan terhapus',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: {id: sid, image: image},
                    dataType: 'json',
                    url: "{{ url('delete_image_sub_kategori') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Banner berhasil dihapus', 'success');
                            $('#bannerPreview').attr('src', '').hide();
                            $('#psc_banner').val('');
                            loadWebSubCategoryData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Banner gagal dihapus', 'error');
                        }
                    }
                });
            }
        });
    });
});

function loadWebSubCategoryData(page = 1) {
    currentPage = page;
    var search = $('#wsc_search').val();
    
    $.ajax({
        url: "{{ url('wsc_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#wsc_tbody').html('<tr><td colspan="4" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#wsc_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="4" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.psc_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.psc_slug));
                    tr.append($('<td>').addClass('px-3 py-4').html('<img src="' + row.psc_banner_url + '" class="w-20 h-12 object-cover rounded" />'));
                    tr.on('click', function() {
                        editWebSubCategory(row);
                    });
                    tbody.append(tr);
                });
            }

            createCustomPagination('#Wsctb', response.total, response.current_page, response.total_pages, response.per_page, loadWebSubCategoryData);
        },
        error: function() {
            $('#wsc_tbody').html('<tr><td colspan="4" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function editWebSubCategory(row) {
    openWebSubCategoryModal();
    $('#wsc_modal_id').val(row.id);
    $('#wsc_modal_mode').val('edit');
    $('#wsc_modal_banner').val(row.psc_banner);
    $('#psc_name').val(row.psc_name);
    $('#psc_slug').val(row.psc_slug);
    if (row.psc_banner) {
        $('#bannerPreview').attr('src', row.psc_banner_url).show();
    }
}

function openWebSubCategoryModal() {
    $('#WebSubCategoryModal').removeClass('hidden');
}

function closeWebSubCategoryModal() {
    $('#WebSubCategoryModal').addClass('hidden');
}

// Pagination function
function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage, loadFunction) {
    var wrapper = document.querySelector(tableId).closest('.bg-white');
    if (!wrapper) return;

    var paginationInfo = wrapper.querySelector('#wsc_pagination_info');
    var paginationControls = wrapper.querySelector('#wsc_pagination_controls');
    
    if (!paginationInfo || !paginationControls) return;

    paginationControls.innerHTML = '';

    var start = totalRecords > 0 ? (currentPage - 1) * perPage + 1 : 0;
    var end = Math.min(currentPage * perPage, totalRecords);
    var infoText = totalRecords > 0 ? 'Menampilkan ' + start + ' sampai ' + end + ' dari ' + totalRecords + ' data' : 'Tidak ada data';

    paginationInfo.textContent = infoText;

    if (totalPages > 1) {
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
