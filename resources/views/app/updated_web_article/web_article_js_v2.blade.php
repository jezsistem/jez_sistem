<script src="https://cdn.tiny.cloud/1/323apjbgqf1hr5qmcz0u8uwvl3oymnrypmtg98wfpvhw0khd/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
const apiUrl = '{{ url('/') }}/api/product/';
var currentPage = 1;
var perPage = 25;
var ecommerceUrl = '';

// Image preview functions
var mainImagesPreview = function(input, placeToInsertImagePreview) {
    if (input.files) {
        var filesAmount = input.files.length;
        for (i = 0; i < filesAmount; i++) {
            var reader = new FileReader();
            reader.onload = function(event) {
                $($.parseHTML('<img style="width:300px;" class="p_main_image_item">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
            }
            reader.readAsDataURL(input.files[i]);
        }
    }
};

var imagesPreview = function(input, placeToInsertImagePreview) {
    if (input.files) {
        var filesAmount = input.files.length;
        for (i = 0; i < filesAmount; i++) {
            var reader = new FileReader();
            reader.onload = function(event) {
                $($.parseHTML('<img style="width:300px; padding:3px;" class="p_image_item">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
            }
            reader.readAsDataURL(input.files[i]);
        }
    }
};

var sizeChartPreview = function(input, placeToInsertImagePreview) {
    if (input.files) {
        var filesAmount = input.files.length;
        for (i = 0; i < filesAmount; i++) {
            var reader = new FileReader();
            reader.onload = function(event) {
                $($.parseHTML('<img style="width:300px;" class="p_size_chart_item">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
            }
            reader.readAsDataURL(input.files[i]);
        }
    }
};

$(document).ready(function() {
    // Inisialisasi kosong, akan diisi dari response AJAX (response.ecommerce_url)
    ecommerceUrl = '';
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize TinyMCE
    tinymce.init({
        selector: '#p_description',
        plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
        toolbar_mode: 'floating',
        height : '150px'
    });

    // Load initial data
    loadArticleData(1);

    // Search handler
    var searchTimeout;
    $('#article_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadArticleData(1);
        }, 500);
    });

    // Filter handlers
    $('#pc_id, #img_filter').on('change', function() {
        loadArticleData(1);
    });

    // Image modal handlers
    $('#p_main_image').on('change', function() {
        mainImagesPreview(this, '#p_main_image_preview');
    });

    $('#p_image').on('change', function() {
        imagesPreview(this, '#p_image_preview');
    });

    $('#p_size_chart').on('change', function() {
        sizeChartPreview(this, '#p_size_chart_preview');
    });

    // Delete image handlers
    $('#p_main_image_preview').on('click', '.p_main_image_item', function() {
        var pid = $('#img_pid').val();
        var image = $('#_main_image').val();
        Swal.fire({
            title: 'Hapus Gambar Utama ..?',
            text: 'Gambar utama akan terhapus',
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
                    data: {pid:pid, image:image},
                    dataType: 'json',
                    url: "{{ url('delete_main_image')}}",
                    success: function(r) {
                        if (r.status == '200'){
                            Swal.fire('Berhasil', 'Gambar berhasil dihapus', 'success');
                            $(".p_main_image_item").remove();
                            $("#p_main_image").val('');
                            $('#_main_image').val('');
                            loadArticleData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Gambar gagal dihapus', 'error');
                        }
                    }
                });
            }
        });
    });

    $('#p_image_preview').on('click', '.p_image_item', function() {
        var pid = $('#img_pid').val();
        var image = $('#_detail_image').val();
        Swal.fire({
            title: 'Hapus Gambar Detail ..?',
            text: 'Semua gambar akan terhapus, dan harus upload ulang semua (sementara)',
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
                    data: {pid:pid, image:image},
                    dataType: 'json',
                    url: "{{ url('delete_image')}}",
                    success: function(r) {
                        if (r.status == '200'){
                            Swal.fire('Berhasil', 'Gambar berhasil dihapus', 'success');
                            $(".p_image_item").remove();
                            $("#p_image").val('');
                            $('#_detail_image').val('');
                            loadArticleData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Gambar gagal dihapus', 'error');
                        }
                    }
                });
            }
        });
    });

    $('#p_size_chart_preview').on('click', '.p_size_chart_item', function() {
        var pid = $('#img_pid').val();
        var image = $('#_chart_image').val();
        Swal.fire({
            title: 'Hapus Size Chart ..?',
            text: 'Size chart akan terhapus',
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
                    data: {pid:pid, image:image},
                    dataType: 'json',
                    url: "{{ url('delete_chart_image')}}",
                    success: function(r) {
                        if (r.status == '200'){
                            Swal.fire('Berhasil', 'Gambar berhasil dihapus', 'success');
                            $(".p_size_chart_item").remove();
                            $("#p_size_chart").val('');
                            $('#_chart_image').val('');
                            loadArticleData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Gambar gagal dihapus', 'error');
                        }
                    }
                });
            }
        });
    });

    // Form submit handler
    $('#f_article').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_p_image_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        let TotalImages = $('#p_image')[0].files.length;
        let images = $('#p_image')[0];
        for (let i = 0; i < TotalImages; i++) {
            formData.append('p_images' + i, images.files[i]);
        }
        formData.append('TotalImages', TotalImages);
        var pid = $('#img_pid').val();
        formData.append('pid', pid);
        
        $.ajax({
            type:'POST',
            url: "{{ url('web_article_image_save')}}",
            data: formData,
            dataType: 'json',
            cache:false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeImageModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadArticleData(currentPage);
                } else {
                    Swal.fire('Gagal', 'Data tidak tersimpan', 'warning');
                }
            },
            error: function(data){
                saveBtn.html('Simpan').prop('disabled', false);
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });
    });

    // Save description handler
    $(document).on('click', '#save_p_description_btn', function() {
        var pid = $('#pid').val();
        var description = tinyMCE.get('p_description').getContent();
        var video = $('#p_video').val();
        $.ajax({
            type: "POST",
            data: {pid:pid, description:description, video:video},
            dataType: 'json',
            url: "{{ url('save_description')}}",
            success: function(r) {
                if (r.status == '200'){
                    Swal.fire('Berhasil', 'Deskripsi berhasil disimpan', 'success');
                    closeDescriptionModal();
                    loadArticleData(currentPage);
                } else {
                    Swal.fire('Gagal', 'Deskripsi gagal disimpan', 'error');
                }
            }
        });
    });

    // Slug handlers
    $(document).on('change', '#p_slug_input', function() {
        var pid = $(this).attr('data-id');
        var slug = $(this).val().replace(' ', '-');
        // Langsung update slug tanpa konfirmasi, seperti halaman lama
        sendGenerateSlug(pid, slug);
    });

    $(document).on('click', '#generate_slug_btn', function() {
        var pid = $(this).attr('data-id');
        var slug = $(this).attr('data-slug').replace('/', '-');

        // Jika Swal (SweetAlert2) tidak tersedia, langsung eksekusi
        if (typeof Swal === 'undefined') {
            sendGenerateSlug(pid, slug);
            return;
        }

        Swal.fire({
            title: 'Generate Slug ..?',
            text: 'URL slug akan berubah menjadi \n'+slug+'',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Lanjut',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                sendGenerateSlug(pid, slug);
            }
        });
    });

    // Weight handler
    $(document).on('change', '#p_weight', function() {
        var weight = $(this).val();
        var id = $(this).attr('data-id');
        $.ajax({
            type: "POST",
            data: {id:id, weight:weight},
            dataType: 'json',
            url: "{{ url('save_weight')}}",
            success: function(r) {
                if (r.status == '200'){
                    Swal.fire('Berhasil', 'Berat berhasil disimpan', 'success');
                } else {
                    Swal.fire('Gagal', 'Berat gagal disimpan', 'error');
                }
            }
        });
    });

    // Image edit click handler
    $(document).on('click', '#p_image_edit', function() {
        $(".p_main_image_item").remove();
        $(".p_image_item").remove();
        $(".p_size_chart_item").remove();
        $("#p_main_image").val('');
        $("#p_image").val('');
        $("#p_size_chart").val('');
        $('#_main_image').val('');
        $('#_detail_image').val('');
        $('#_chart_image').val('');
        
        var pid = $(this).attr('data-id');
        var name = $(this).attr('data-name');
        var main_image = $(this).attr('data-main_image');
        var chart_image = $(this).attr('data-chart_image');
        var image = $(this).attr('data-image');
        
        openImageModal();
        $('#image_label').text(name);
        $('#img_pid').val(pid);
        
        if (main_image != '') {
            $('#p_main_image_preview').append('<img src="'+apiUrl+'300/'+main_image+'" style="width:300px;" class="p_main_image_item"/>');
            $('#_main_image').val(main_image);
        }
        if (chart_image != '') {
            $('#p_size_chart_preview').append('<img src="'+apiUrl+'size_chart/'+chart_image+'" style="width:300px;" class="p_size_chart_item"/>');
            $('#_chart_image').val(chart_image);
        }
        if (image != '' && image != '||||||') {
            var arr = image.split('|');
            $.each( arr, function( index, value ) {
                $('#p_image_preview').append('<img src="'+apiUrl+'300/'+value+'" style="width:300px; padding:3px;" class="p_image_item"/>');
            });
            $('#_detail_image').val(image);
        }
    });

    // Description edit click handler
    $(document).on('click', '#p_description_edit', function() {
        var pid = $(this).attr('data-id');
        var name = $(this).attr('data-name');
        var description = $(this).attr('data-description');
        var video = $(this).attr('data-video');
        
        openDescriptionModal();
        $('#description_label').text(name);
        tinyMCE.get('p_description').setContent(description || '');
        $('#p_video').val(video || '');
        $('#pid').val(pid);
    });
});

// Helper untuk generate slug (dipakai oleh input change & tombol edit)
function sendGenerateSlug(pid, slug) {
    $.ajax({
        type: "POST",
        data: {pid:pid, slug:slug},
        dataType: 'json',
        url: "{{ url('generate_slug')}}",
        success: function(r) {
            if (r.status == '200'){
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Berhasil', 'Slug berhasil dibuat', 'success');
                }
                $('.p_slug_input_'+pid).val(slug);
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Gagal', 'Slug gagal dibuat', 'error');
                } else {
                    alert('Slug gagal dibuat');
                }
            }
        },
        error: function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Error', 'Terjadi kesalahan saat membuat slug', 'error');
            } else {
                alert('Terjadi kesalahan saat membuat slug');
            }
        }
    });
}

function loadArticleData(page) {
    currentPage = page;
    var search = $('#article_search').val();
    var pc_id = $('#pc_id').val();
    var img_filter = $('#img_filter').val();

    $('#article_tbody').html(`
        <tr>
            <td colspan="12" class="px-3 py-4 text-center text-gray-500">
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
        url: "{{ url('web_article_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            pc_id: pc_id,
            img_filter: img_filter
        },
        success: function(response) {
            if (response.error) {
                $('#article_tbody').html(`
                    <tr>
                        <td colspan="12" class="px-3 py-4 text-center text-red-500">${response.error}</td>
                    </tr>
                `);
                return;
            }

            ecommerceUrl = response.ecommerce_url || ecommerceUrl;
            renderArticleTable(response.data);
            createCustomPagination('#Articletb', response.total, response.current_page, response.total_pages, response.per_page, loadArticleData);
        },
        error: function(xhr) {
            var errorMsg = 'Terjadi kesalahan saat memuat data';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMsg = xhr.responseJSON.error;
            }
            $('#article_tbody').html(`
                <tr>
                    <td colspan="12" class="px-3 py-4 text-center text-red-500">${errorMsg}</td>
                </tr>
            `);
        }
    });
}

function renderArticleTable(data) {
    if (!data || data.length === 0) {
        $('#article_tbody').html(`
            <tr>
                <td colspan="12" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td>
            </tr>
        `);
        return;
    }

    var html = '';
    data.forEach(function(row) {
        html += `
            <tr class="bg-white border-b hover:bg-gray-50">
                <td class="px-3 py-3">${row.no}</td>
                <td class="px-3 py-3">${row.article_id}</td>
                <td class="px-3 py-3">${row.br_name}</td>
                <td class="px-3 py-3">${row.p_name}</td>
                <td class="px-3 py-3">${row.p_color}</td>
                <td class="px-3 py-3">
                    <input class="w-20 px-2 py-1 text-sm border border-gray-300 rounded" type="number" id="p_weight" value="${row.p_weight}" data-id="${row.pid}" placeholder="Berat(gr)"/>
                </td>
                <td class="px-3 py-3">
                    <img data-chart_image="${row.p_size_chart || ''}" data-main_image="${row.p_main_image || ''}" data-image="" data-name="${row.product_name}" id="p_image_edit" data-id="${row.pid}" style="width:100px; cursor:pointer;" src="${row.p_main_image_url}" alt="main_image"/>
                </td>
                <td class="px-3 py-3">
                    <img data-chart_image="${row.p_size_chart || ''}" data-main_image="${row.p_main_image || ''}" data-image="" data-name="${row.product_name}" id="p_image_edit" data-id="${row.pid}" style="width:100px; cursor:pointer;" src="${row.p_size_chart_url}" alt="chart_image"/>
                </td>
                <td class="px-3 py-3">
                    <div class="flex items-center gap-1">
                        <input id="p_slug_input" data-id="${row.pid}" class="flex-1 px-2 py-1 text-sm border border-gray-300 rounded p_slug_input_${row.pid}" type="text" value="${row.p_slug}" autocomplete="off"/>
                        <i id="generate_slug_btn" data-slug="${row.p_slug_base}" data-id="${row.pid}" class="fas fa-edit px-2 py-1 text-sm bg-blue-600 text-white rounded cursor-pointer hover:bg-blue-700"></i>
                    </div>
                </td>
                <td class="px-3 py-3">
                    <button data-video="${row.p_video || ''}" data-description="${(row.p_description || '').replace(/"/g, '&quot;')}" data-name="${row.product_name}" id="p_description_edit" data-id="${row.pid}" class="px-2 py-1 text-xs font-medium rounded ${row.p_description_class} cursor-pointer">
                        Detail
                    </button>
                </td>
                <td class="px-3 py-3">${row.stok}</td>
                <td class="px-3 py-3">
                    <a href="${row.ecommerce_url}/${row.p_slug}" target="_blank" class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800 hover:bg-green-200">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
        `;
    });
    $('#article_tbody').html(html);
}

function openImageModal() {
    document.getElementById('ImageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('ImageModal').classList.add('hidden');
    $(".p_main_image_item").remove();
    $(".p_image_item").remove();
    $(".p_size_chart_item").remove();
    $("#p_main_image").val('');
    $("#p_image").val('');
    $("#p_size_chart").val('');
    $('#_main_image').val('');
    $('#_detail_image').val('');
    $('#_chart_image').val('');
    $('#f_article')[0].reset();
}

function openDescriptionModal() {
    document.getElementById('DescriptionModal').classList.remove('hidden');
}

function closeDescriptionModal() {
    document.getElementById('DescriptionModal').classList.add('hidden');
    tinyMCE.get('p_description').setContent('');
    $('#p_video').val('');
    $('#pid').val('');
}

function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage, loadFunction) {
    var infoDiv = document.getElementById('article_pagination_info');
    var controlsDiv = document.getElementById('article_pagination_controls');
    
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
