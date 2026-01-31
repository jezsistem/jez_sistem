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
    loadBrandData(1);

    // Search handler
    var searchTimeout;
    $('#brand_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadBrandData(1);
        }, 500);
    });

    // Add brand button
    $('#add_brand_btn').on('click', function() {
        openBrandModal();
        $('#brand_modal_id').val('');
        $('#brand_modal_mode').val('add');
        $('#brand_modal_image').val('');
        $('#brand_modal_banner').val('');
        $('#f_brand')[0].reset();
        $('#imagePreview').attr('src', '').hide();
        $('#bannerPreview').attr('src', '').hide();
        $('#delete_brand_btn').addClass('hidden');
    });

    // Form submit handler
    $('#f_brand').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_brand_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('web_brand_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeBrandModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadBrandData(currentPage);
                } else if (data.status == '400') {
                    closeBrandModal();
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
                    data: {_id: $('#brand_modal_id').val()},
                    dataType: 'json',
                    url: "{{ url('web_brand_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                            closeBrandModal();
                            loadBrandData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus data', 'error');
                        }
                    }
                });
            }
        });
    });

    // Check exists brand
    $('#br_name').on('change', function() {
        var br_name = $(this).val();
        $.ajax({
            type: "POST",
            data: {_br_name: br_name},
            dataType: 'json',
            url: "{{ url('web_brand_check_exists') }}",
            success: function(r) {
                if (r.status == '200') {
                    Swal.fire('Peringatan', 'Nama brand sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                    $('#br_name').val('');
                    return false;
                }
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

    // Delete logo handler
    $('#imagePreview').on('click', function() {
        let pid = $('#brand_modal_id').val();
        let image = $('#brand_modal_image').val();
        if (!pid || !image) return;
        
        Swal.fire({
            title: 'Hapus Logo Brand ..?',
            text: 'Logo Brand akan terhapus',
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
                    data: {id: pid, image: image},
                    dataType: 'json',
                    url: "{{ url('web_brand_delete_logo') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Logo berhasil dihapus', 'success');
                            $('#imagePreview').attr('src', '').hide();
                            $('#br_image').val('');
                            loadBrandData(currentPage);
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
        let bid = $('#brand_modal_id').val();
        let image = $('#brand_modal_banner').val();
        if (!bid || !image) return;
        
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
                    data: {id: bid, image: image},
                    dataType: 'json',
                    url: "{{ url('web_brand_delete_banner') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Banner berhasil dihapus', 'success');
                            $('#bannerPreview').attr('src', '').hide();
                            $('#br_banner').val('');
                            loadBrandData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Banner gagal dihapus', 'error');
                        }
                    }
                });
            }
        });
    });
});

function loadBrandData(page = 1) {
    currentPage = page;
    var search = $('#brand_search').val();
    
    $.ajax({
        url: "{{ url('web_brand_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search
        },
        success: function(response) {
            if (response.error) {
                $('#brand_tbody').html('<tr><td colspan="7" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#brand_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="7" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').html('<img src="' + row.br_image_url + '" class="w-16 h-16 object-cover rounded" />'));
                    tr.append($('<td>').addClass('px-3 py-4').html('<img src="' + row.br_banner_url + '" class="w-20 h-12 object-cover rounded" />'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.br_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.br_slug));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.br_description));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.is_local_label));
                    tr.on('click', function() {
                        editBrand(row);
                    });
                    tbody.append(tr);
                });
            }

            createCustomPagination('#Brandtb', response.total, response.current_page, response.total_pages, response.per_page, loadBrandData);
        },
        error: function() {
            $('#brand_tbody').html('<tr><td colspan="7" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function editBrand(row) {
    openBrandModal();
    $('#brand_modal_id').val(row.id);
    $('#brand_modal_mode').val('edit');
    $('#brand_modal_image').val(row.br_image);
    $('#brand_modal_banner').val(row.br_banner);
    $('#br_name').val(row.br_name);
    $('#br_slug').val(row.br_slug);
    $('#br_description').val(row.br_description);
    $('#is_local').val(row.is_local);
    if (row.br_image) {
        $('#imagePreview').attr('src', row.br_image_url).show();
    }
    if (row.br_banner) {
        $('#bannerPreview').attr('src', row.br_banner_url).show();
    }
    if ($('#delete_access').val() == '1') {
        $('#delete_brand_btn').removeClass('hidden');
    }
}

function openBrandModal() {
    $('#BrandModal').removeClass('hidden');
}

function closeBrandModal() {
    $('#BrandModal').addClass('hidden');
}

// Pagination function
function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage, loadFunction) {
    var wrapper = document.querySelector(tableId).closest('.bg-white');
    if (!wrapper) return;

    var paginationInfo = wrapper.querySelector('#brand_pagination_info');
    var paginationControls = wrapper.querySelector('#brand_pagination_controls');
    
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
