<script>
var currentPage = 1;
var perPage = 25;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize TinyMCE
    tinymce.init({
        selector: '#bcc_content',
        plugins: 'advlist autolink lists link image media charmap print preview hr anchor pagebreak',
        toolbar_mode: 'floating',
    });

    document.addEventListener('focusin', function(e) {
        if (e.target.closest(".mce-window") !== null) {
            e.stopImmediatePropagation();
        }
    });

    // Load initial data
    loadBlogData(1);

    // Search handler
    var searchTimeout;
    $('#bcc_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadBlogData(1);
        }, 500);
    });

    // Filter handler
    $('#bc_id_filter').on('change', function() {
        loadBlogData(1);
    });

    // Add blog button
    $('#add_bcc_btn').on('click', function() {
        openBlogModal();
        $('#bcc_modal_id').val('');
        $('#bcc_modal_mode').val('add');
        $('#bcc_modal_image').val('');
        $('#f_bcc')[0].reset();
        $('#bcc_image_preview').html('');
        tinyMCE.get('bcc_content').setContent('');
        $('#delete_bcc_btn').addClass('hidden');
    });

    // Form submit handler
    $('#f_bcc').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_bcc_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        formData.append('bcc_content', tinyMCE.get('bcc_content').getContent());
        
        $.ajax({
            type: 'POST',
            url: "{{ url('bcc_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeBlogModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadBlogData(currentPage);
                } else if (data.status == '400') {
                    closeBlogModal();
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
    $(document).on('click', '#delete_bcc_btn', function() {
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
                    data: {_id: $('#bcc_modal_id').val()},
                    dataType: 'json',
                    url: "{{ url('bcc_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                            closeBlogModal();
                            loadBlogData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus data', 'error');
                        }
                    }
                });
            }
        });
    });

    // Image preview
    $('#bcc_image').on('change', function() {
        var input = this;
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#bcc_image_preview').html('<img src="' + e.target.result + '" class="w-64 h-64 object-cover rounded" />');
            };
            reader.readAsDataURL(input.files[0]);
        }
    });

    $('#bcc_image_preview').on('click', function() {
        $(this).html('');
        $('#bcc_image').val('');
        $('#bcc_modal_image').val('');
    });

    // Blog Category handlers
    $('#add_bc_btn').on('click', function() {
        openBlogCategoryModal();
        $('#bc_modal_id').val('');
        $('#bc_modal_mode').val('add');
        $('#f_bc')[0].reset();
    });

    $('#edit_bc_btn').on('click', function() {
        var id = $('#bc_id_filter').val();
        if (!id) {
            Swal.fire('Peringatan', 'Silahkan pilih kategori blog terlebih dahulu', 'warning');
            return;
        }
        $.ajax({
            type: "POST",
            data: {_id: id},
            dataType: 'json',
            url: "{{ url('get_bc') }}",
            success: function(r) {
                if (r.status == '200') {
                    openBlogCategoryModal();
                    $('#bc_modal_id').val(id);
                    $('#bc_modal_mode').val('edit');
                    $('#bc_name').val(r.bc_name);
                } else {
                    Swal.fire('Error', 'Gagal mengambil data', 'error');
                }
            }
        });
    });

    $('#delete_bc_btn').on('click', function() {
        var id = $('#bc_id_filter').val();
        if (!id) {
            Swal.fire('Peringatan', 'Silahkan pilih kategori blog terlebih dahulu', 'warning');
            return;
        }
        Swal.fire({
            title: 'Hapus..?',
            text: 'Yakin hapus kategori blog ini? Semua konten dalam kategori ini juga akan terhapus.',
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
                    data: {_id: id},
                    dataType: 'json',
                    url: "{{ url('bc_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data berhasil dihapus', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus data', 'error');
                        }
                    }
                });
            }
        });
    });

    $('#f_bc').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_bc_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('bc_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeBlogCategoryModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success').then(() => {
                        location.reload();
                    });
                } else if (data.status == '400') {
                    closeBlogCategoryModal();
                    Swal.fire('Gagal', 'Data tidak tersimpan', 'warning');
                }
            },
            error: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });
    });
});

function openBlogCategoryModal() {
    $('#BcModal').removeClass('hidden');
}

function closeBlogCategoryModal() {
    $('#BcModal').addClass('hidden');
}

function loadBlogData(page = 1) {
    currentPage = page;
    var search = $('#bcc_search').val();
    var bc_id = $('#bc_id_filter').val();
    
    $.ajax({
        url: "{{ url('bcc_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            bc_id: bc_id
        },
        success: function(response) {
            if (response.error) {
                $('#bcc_tbody').html('<tr><td colspan="5" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#bcc_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="5" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.bct_title));
                    tr.append($('<td>').addClass('px-3 py-4').html('<img src="' + row.bct_image_url + '" class="w-16 h-16 object-cover rounded" />'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.bct_views));
                    var actionBtn = $('<a>').attr('href', row.ecommerce_url + '/' + row.bct_slug).attr('target', '_blank').addClass('px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm').html('<i class="fa fa-eye"></i>');
                    tr.append($('<td>').addClass('px-3 py-4').append(actionBtn));
                    tr.on('click', function(e) {
                        if (!$(e.target).closest('a').length) {
                            editBlog(row);
                        }
                    });
                    tbody.append(tr);
                });
            }

            createCustomPagination('#Bcctb', response.total, response.current_page, response.total_pages, response.per_page, loadBlogData);
        },
        error: function() {
            $('#bcc_tbody').html('<tr><td colspan="5" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function editBlog(row) {
    openBlogModal();
    $('#bcc_modal_id').val(row.id);
    $('#bcc_modal_mode').val('edit');
    $('#bcc_modal_image').val(row.bct_image);
    $('#bc_id').val(row.bc_id);
    $('#bcc_title').val(row.bct_title);
    $('#bcc_keywords').val(row.bct_keywords);
    if (row.bct_image) {
        $('#bcc_image_preview').html('<img src="' + row.bct_image_url + '" class="w-64 h-64 object-cover rounded" />');
    }
    tinyMCE.get('bcc_content').setContent(row.bct_content);
    if ($('#delete_access').val() == '1') {
        $('#delete_bcc_btn').removeClass('hidden');
    }
}

function openBlogModal() {
    $('#BccModal').removeClass('hidden');
}

function closeBlogModal() {
    $('#BccModal').addClass('hidden');
}

// Pagination function
function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage, loadFunction) {
    var wrapper = document.querySelector(tableId).closest('.bg-white');
    if (!wrapper) return;

    var paginationInfo = wrapper.querySelector('#bcc_pagination_info');
    var paginationControls = wrapper.querySelector('#bcc_pagination_controls');
    
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
