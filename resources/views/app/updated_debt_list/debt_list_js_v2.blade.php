<script>
var currentPage = 1;
var perPage = 25;

function replaceComma(str) {
    var str_replace = str.replace(/,/g, '');
    return str_replace;
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load initial data
    loadDebtListData(1);

    // Search handler
    var searchTimeout;
    $('#debt_list_search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadDebtListData(1);
        }, 500);
    });

    // Store filter handler
    $('#st_id').on('change', function() {
        loadDebtListData(1);
    });

    // Add debt list button
    $('#add_debt_list_btn').on('click', function() {
        openDebtListModal();
        $('#debt_list_modal_id').val('');
        $('#debt_list_modal_mode').val('add');
        $('#f_debt')[0].reset();
        $('#st_id_data').val($('#st_id').val());
        $('#delete_debt_list_btn').addClass('hidden');
    });

    // Import modal button
    $('#import_modal_btn').on('click', function() {
        openImportModal();
    });

    // Form submit handler
    $('#f_debt').on('submit', function(e) {
        e.preventDefault();
        var saveBtn = $('#save_debt_list_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        formData.append('st_id_data', $('#st_id').val());
        
        $.ajax({
            type: 'POST',
            url: "{{ url('dl_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeDebtListModal();
                    Swal.fire('Berhasil', 'Data berhasil disimpan', 'success');
                    loadDebtListData(currentPage);
                } else if (data.status == '400') {
                    closeDebtListModal();
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
    $(document).on('click', '#delete_debt_list_btn', function() {
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
                    data: {_id: $('#debt_list_modal_id').val()},
                    dataType: 'json',
                    url: "{{ url('dl_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data berhasil dihapus', 'success');
                            closeDebtListModal();
                            loadDebtListData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus data', 'error');
                        }
                    }
                });
            }
        });
    });

    // Calculate total when DPP or VAT changes
    $('#dl_value').on('keyup', function() {
        var value = $(this).val();
        $('#dl_total').val(value);
    });

    $('#dl_vat').on('keyup', function() {
        var vat = $(this).val() || 0;
        var dpp = $('#dl_value').val() || 0;
        var total = parseFloat(dpp) + (parseFloat(dpp) / 100 * parseFloat(vat));
        $('#dl_total').val(total.toFixed(2));
    });

    // Payment value click handler
    $(document).on('click', '.payment_value_btn', function() {
        var dl_id = $(this).data('dl_id');
        var dl_value = $(this).data('dl_value');
        var payment = $(this).data('payment');
        openPaymentModal(dl_id, dl_value, payment);
    });

    // Add payment button
    $('#add_payment_btn').on('click', function() {
        var dl_value = $('#dl_value_payment').val();
        var payment = $('#payment').val();
        $('#payment_modal_mode').val('add');
        openAddPaymentModal();
        $('#f_payment')[0].reset();
        $('#dlp_value').val(parseFloat(dl_value) - parseFloat(payment));
        $('#value_remain').val(parseFloat(dl_value) - parseFloat(payment));
    });

    // Payment value input handler
    $('#dlp_value').on('keyup', function() {
        var dl_value = $('#dl_value_payment').val();
        var payment = $('#payment').val();
        var value = $(this).val() || 0;
        $('#value_remain').val(parseFloat(dl_value) - parseFloat(payment) - parseFloat(value));
    });

    // Payment form submit
    $('#f_payment').on('submit', function(e) {
        e.preventDefault();
        var dl_value = $('#dl_value_payment').val();
        var payment = $('#payment').val();
        var remain = $('#value_remain').val();
        if (remain < 0) {
            Swal.fire('Peringatan', 'Sisa hutang tidak boleh minus', 'warning');
            return false;
        }
        if (parseFloat(dl_value) == parseFloat(payment)) {
            Swal.fire('Peringatan', 'Sisa hutang sudah habis', 'warning');
            return false;
        }
        
        var saveBtn = $('#save_payment_btn');
        saveBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('dlp_save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                if (data.status == '200') {
                    closeAddPaymentModal();
                    Swal.fire('Berhasil', 'Data pembayaran berhasil disimpan', 'success');
                    loadPaymentData($('#dl_id').val());
                    loadDebtListData(currentPage);
                } else {
                    Swal.fire('Gagal', 'Data tidak tersimpan', 'warning');
                }
            },
            error: function(data) {
                saveBtn.html('Simpan').prop('disabled', false);
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });
    });

    // Delete payment handler
    $(document).on('click', '#delete_payment_btn', function() {
        Swal.fire({
            title: 'Hapus..?',
            text: 'Yakin hapus data pembayaran ini?',
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
                    data: {_id: $('#payment_modal_id').val()},
                    dataType: 'json',
                    url: "{{ url('dlp_delete') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            Swal.fire('Berhasil', 'Data pembayaran berhasil dihapus', 'success');
                            closeAddPaymentModal();
                            loadPaymentData($('#dl_id').val());
                            loadDebtListData(currentPage);
                        } else {
                            Swal.fire('Gagal', 'Gagal hapus data', 'error');
                        }
                    }
                });
            }
        });
    });

    // Import form submit
    $('#f_import').on('submit', function(e) {
        e.preventDefault();
        var importBtn = $('#import_data_btn');
        importBtn.html('Proses..').prop('disabled', true);
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ url('debt_import') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                importBtn.html('Import').prop('disabled', false);
                if (data.status == '200') {
                    closeImportModal();
                    Swal.fire('Berhasil', 'Data berhasil diimport', 'success');
                    loadDebtListData(currentPage);
                } else if (data.status == '400') {
                    closeImportModal();
                    Swal.fire('Peringatan', 'File yang anda import kosong atau format tidak tepat', 'warning');
                } else {
                    closeImportModal();
                    Swal.fire('Peringatan', 'Silahkan periksa format input pada template anda', 'warning');
                }
            },
            error: function(data) {
                importBtn.html('Import').prop('disabled', false);
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });
    });

    // Export handler
    $('#debt_list_export_btn').on('click', function() {
        var allData = [];
        var page = 1;
        var perPage = 1000;
        var search = $('#debt_list_search').val();
        var st_id = $('#st_id').val();
        
        function fetchAllData() {
            $.ajax({
                url: "{{ url('debt_list_datatables_simple') }}",
                type: 'GET',
                data: {
                    page: page,
                    per_page: perPage,
                    search: search,
                    st_id: st_id
                },
                success: function(response) {
                    if (response.error) {
                        Swal.fire('Error', response.error, 'error');
                        return;
                    }
                    
                    allData = allData.concat(response.data);
                    
                    if (page < response.total_pages) {
                        page++;
                        fetchAllData();
                    } else {
                        if (allData.length === 0) {
                            Swal.fire('Peringatan', 'Tidak ada data untuk diekspor', 'warning');
                            return;
                        }
                        
                        var csv = 'No,Store,Supplier,Brand,Invoice,Tanggal,Jatuh Tempo,DPP,VAT,TOTAL,Payment Value\n';
                        allData.forEach(function(row, index) {
                            csv += (index + 1) + ',"' + (row.st_name || '') + '","' + (row.ps_name || '') + '","' + (row.br_name || '') + '","' + (row.dl_invoice || '') + '","' + (row.dl_invoice_date || '') + '","' + (row.dl_invoice_due_date || '') + '","' + (row.dl_value || '') + '","' + (row.dl_vat || '') + '","' + (row.dl_total || '') + '","' + (row.payment_value || '') + '"\n';
                        });
                        
                        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                        var link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = 'Daftar Hutang - ' + new Date().toISOString().split('T')[0] + '.csv';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Gagal mengambil data untuk export', 'error');
                }
            });
        }
        
        fetchAllData();
    });
});

function loadDebtListData(page = 1) {
    currentPage = page;
    var search = $('#debt_list_search').val();
    var st_id = $('#st_id').val();
    
    $.ajax({
        url: "{{ url('debt_list_datatables_simple') }}",
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            search: search,
            st_id: st_id
        },
        success: function(response) {
            if (response.error) {
                $('#debt_list_tbody').html('<tr><td colspan="11" class="px-3 py-4 text-center text-red-500">' + response.error + '</td></tr>');
                return;
            }

            var tbody = $('#debt_list_tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html('<tr><td colspan="11" class="px-3 py-4 text-center text-gray-500">Tidak ada data</td></tr>');
            } else {
                response.data.forEach(function(row) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    tr.append($('<td>').addClass('px-3 py-4').text(row.no));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.st_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.ps_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.br_name));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.dl_invoice));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.dl_invoice_date));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.dl_invoice_due_date));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.dl_value));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.dl_vat));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.dl_total));
                    var paymentBtn = $('<button>').addClass('px-3 py-1 text-sm rounded ' + (row.is_paid ? 'bg-blue-600 text-white' : 'bg-green-600 text-white') + ' payment_value_btn')
                        .attr('data-dl_id', row.dl_id)
                        .attr('data-dl_value', row.dl_total.replace(/,/g, ''))
                        .attr('data-payment', row.payment_value.replace(/,/g, ''))
                        .text(row.payment_value);
                    tr.append($('<td>').addClass('px-3 py-4').append(paymentBtn));
                    tr.on('click', function(e) {
                        if (!$(e.target).hasClass('payment_value_btn')) {
                            editDebtList(row);
                        }
                    });
                    tbody.append(tr);
                });
            }

            createCustomPagination('#DebtListtb', response.total, response.current_page, response.total_pages, response.per_page, loadDebtListData);
        },
        error: function() {
            $('#debt_list_tbody').html('<tr><td colspan="11" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function loadPaymentData(dl_id) {
    $.ajax({
        url: "{{ url('payment_datatables') }}",
        type: 'GET',
        data: { dl_id: dl_id },
        dataType: 'json',
        success: function(response) {
            var tbody = $('#payment_tbody');
            tbody.empty();
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var tr = $('<tr>').addClass('bg-white border-b hover:bg-gray-50 cursor-pointer');
                    tr.append($('<td>').addClass('px-3 py-4').text(index + 1));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.dlp_date_show || '-'));
                    tr.append($('<td>').addClass('px-3 py-4').text(row.dlp_value || '-'));
                    tr.on('click', function() {
                        editPayment(row);
                    });
                    tbody.append(tr);
                });
            } else {
                tbody.html('<tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">Tidak ada data pembayaran</td></tr>');
            }
        },
        error: function() {
            $('#payment_tbody').html('<tr><td colspan="3" class="px-3 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data</td></tr>');
        }
    });
}

function editDebtList(row) {
    openDebtListModal();
    $('#debt_list_modal_id').val(row.dl_id);
    $('#debt_list_modal_mode').val('edit');
    $('#st_id_data').val(row.st_id);
    $('#ps_id').val(row.ps_id);
    $('#br_id').val(row.br_id);
    $('#dl_invoice').val(row.dl_invoice);
    if (row.dl_invoice_date && row.dl_invoice_date !== '-') {
        var dateParts = row.dl_invoice_date.split('/');
        if (dateParts.length === 3) {
            $('#dl_invoice_date').val(dateParts[2] + '-' + dateParts[1] + '-' + dateParts[0]);
        }
    }
    if (row.dl_invoice_due_date && row.dl_invoice_due_date !== '-') {
        var dueDate = row.dl_invoice_due_date.split(' [')[0];
        var dueDateParts = dueDate.split('/');
        if (dueDateParts.length === 3) {
            $('#dl_invoice_due_date').val(dueDateParts[2] + '-' + dueDateParts[1] + '-' + dueDateParts[0]);
        }
    }
    $('#dl_value').val(row.dl_value.replace(/,/g, ''));
    $('#dl_vat').val(row.dl_vat === '-' ? '' : row.dl_vat);
    $('#dl_total').val(row.dl_total.replace(/,/g, ''));
    if ($('#delete_access').val() == '1') {
        $('#delete_debt_list_btn').removeClass('hidden');
    }
}

function editPayment(row) {
    var dl_value = $('#dl_value_payment').val();
    var payment = $('#payment').val();
    if (parseFloat(dl_value) == parseFloat(payment)) {
        Swal.fire('Peringatan', 'Sisa hutang sudah habis', 'warning');
        return false;
    }
    openAddPaymentModal();
    $('#payment_modal_id').val(row.id);
    $('#payment_modal_mode').val('edit');
    $('#dlp_value').val(replaceComma(row.dlp_value || '0'));
    if (row.dlp_date) {
        var dateParts = row.dlp_date.split('/');
        if (dateParts.length === 3) {
            $('#dlp_date').val(dateParts[2] + '-' + dateParts[1] + '-' + dateParts[0]);
        } else {
            $('#dlp_date').val(row.dlp_date);
        }
    }
    $('#delete_payment_btn').removeClass('hidden');
}

function openDebtListModal() {
    $('#DebtListModal').removeClass('hidden');
}

function closeDebtListModal() {
    $('#DebtListModal').addClass('hidden');
}

function openPaymentModal(dl_id, dl_value, payment) {
    $('#PaymentModal').removeClass('hidden');
    $('#dl_id').val(dl_id);
    $('#dl_value_payment').val(dl_value);
    $('#payment').val(payment);
    loadPaymentData(dl_id);
}

function closePaymentModal() {
    $('#PaymentModal').addClass('hidden');
}

function openAddPaymentModal() {
    $('#AddPaymentModal').removeClass('hidden');
}

function closeAddPaymentModal() {
    $('#AddPaymentModal').addClass('hidden');
    $('#delete_payment_btn').addClass('hidden');
}

function openImportModal() {
    $('#ImportModal').removeClass('hidden');
}

function closeImportModal() {
    $('#ImportModal').addClass('hidden');
    $('#f_import')[0].reset();
}

// Pagination function
function createCustomPagination(tableId, totalRecords, currentPage, totalPages, perPage, loadFunction) {
    var wrapper = document.querySelector(tableId).closest('.bg-white');
    if (!wrapper) return;

    var paginationInfo = wrapper.querySelector('#debt_list_pagination_info');
    var paginationControls = wrapper.querySelector('#debt_list_pagination_controls');
    
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
