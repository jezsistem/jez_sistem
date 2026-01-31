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
    loadWebCategoryData(1);

    // Search handler
    var searchTimeout;
    $('#web_category_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadWebCategoryData(1);
        }, 500);
    });

    // Add web category button
    $('#add_web_category_btn').on('click', function() {
        openWebCategoryModal();
        $('#web_category_modal_id').val('');
        $('#web_category_modal_mode').val('add');
        $('#web_category_modal_image').val('');
        $('#web_category_modal_banner').val('');
        $('#f_web_category')[0].reset();
        $('#imagePreview').attr('src', '').hide();
        $('#bannerPreview').attr('src', '').hide();
        $('#delete_web_category_btn').addClass('hidden');
    });

    // Form submit handler
    $('#f_web_category').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_web_category_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('cs_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeWebCategoryModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadWebCategoryData(currentPage);
                } else if (data.status == '400') {
                    closeWebCategoryModal();
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
    $(document).on('click', '#delete_web_category_btn', function() {
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
                    data: {_id: $('#web_category_modal_id').val()},
                    dataType: 'json',
                    url: "{{ url('cs_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                            closeWebCategoryModal();
                            loadWebCategoryData(currentPage);
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
        output.style.display = 'block';
        output.onload = function() {
            URL.revokeObjectURL(output.src);
        }
    };
    window.loadFile = loadFile;

    var loadBanner = function(event) {
        var output = document.getElementById('bannerPreview');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.style.display = 'block';
        output.onload = function() {
            URL.revokeObjectURL(output.src);
        }
    };
    window.loadBanner = loadBanner;

    // Delete image handler
    $('#imagePreview').on('click', function() {
        let cid = $('#web_category_modal_id').val();
        let image = $('#web_category_modal_image').val();
        if (!cid || !image) return;
        
        Swal.fire({
            title: 'Hapus Gambar ..?',
            text: 'Gambar akan terhapus',
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
                    data: {id: cid, image: image},
                    dataType: 'json',
                    url: "{{ url('delete_image_kategori') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Gambar berhasil dihapus', 'success');
                            $('#imagePreview').attr('src', '').hide();
                            $('#cs_image').val('');
                            loadWebCategoryData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Gambar gagal dihapus', 'error');
                        }
                    }
                });
            }
        });
    });

    // Delete banner handler
    $('#bannerPreview').on('click', function() {
        let cid = $('#web_category_modal_id').val();
        let image = $('#web_category_modal_banner').val();
        if (!cid || !image) return;
        
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
                    data: {id: cid, image: image},
                    dataType: 'json',
                    url: "{{ url('delete_banner_kategori') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Banner berhasil dihapus', 'success');
                            $('#bannerPreview').attr('src', '').hide();
                            $('#cs_banner').val('');
                            loadWebCategoryData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Banner gagal dihapus', 'error');
                        }
                    }
                });
            }
        });
    });
});

function loadWebCategoryData(page = 1) {
    currentPage = page;
    var search = $('#web_category_search').val();
    
    $.ajax({
        url: "{{ url('web_category_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#web_category_tbody').html('<tr><td colspan="6" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#web_category_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.cs_title));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.psc_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.cs_slug));
                    tr.append($('<td>').addClass('px-3 py-4').html('<img src="' + row.cs_image_url + '" class="w-16 h-16 object-cover rounded" />'));
                    tr.append($('<td>').addClass('px-3 py-4').html('<img src="' + row.cs_banner_url + '" class="w-20 h-12 object-cover rounded" />'));
                    tr.on('click', function() {
                        editWebCategory(row);
                    });
                    tbody.append(tr);
                });
            }

            createCustomPagination('#Wctb', response.total, response.current_page, response.total_pages, response.per_page, loadWebCategoryData);
        },
        error: function() {
            $('#web_category_tbody').html('<tr><td colspan="6" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function editWebCategory(row) {
    openWebCategoryModal();
    $('#web_category_modal_id').val(row.id);
    $('#web_category_modal_mode').val('edit');
    $('#web_category_modal_image').val(row.cs_image);
    $('#web_category_modal_banner').val(row.cs_banner);
    $('#cs_title').val(row.cs_title);
    $('#cs_slug').val(row.cs_slug);
    $('#cs_sub_category').val(row.cs_sub_category);
    $('#psc_id').val(row.psc_id);
    if (row.cs_image) {
        $('#imagePreview').attr('src', row.cs_image_url).show();
    }
    if (row.cs_banner) {
        $('#bannerPreview').attr('src', row.cs_banner_url).show();
    }
    if ($('#delete_access').val() == '1') {
        $('#delete_web_category_btn').removeClass('hidden');
    }
}

function openWebCategoryModal() {
    $('#WebCategoryModal').removeClass('hidden');
}

function closeWebCategoryModal() {
    $('#WebCategoryModal').addClass('hidden');
}

// Pagination function
function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage, loadFunction) {
    var wrapper = document.querySelector(tableId).closest('.bg-white');
    if (!wrapper) return;

    var paginationInfo = wrapper.querySelector('#web_category_pagination_info');
    var paginationControls = wrapper.querySelector('#web_category_pagination_controls');
    
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
