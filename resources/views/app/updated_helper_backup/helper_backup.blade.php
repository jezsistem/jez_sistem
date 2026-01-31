@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- User Info Header -->
    <div class="bg-blue-600 rounded-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">{{ $data['user']->u_name }}</h1>
                <p class="text-sm opacity-90 mt-1">{{ $data['subtitle'] }}</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="openChangePasswordModal()" 
                        class="px-4 py-2 bg-green-500 text-white text-sm font-medium rounded-lg hover:bg-green-600 transition-colors">
                    Ganti Password
                </button>
                <a href="{{ url('logout') }}" 
                   class="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-500 transition-colors">
                    Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Storage Area Selection -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="flex items-center gap-4">
            <h3 class="text-lg font-semibold text-gray-900">Pilih Storage Area:</h3>
            <div id="storage_area_select" class="flex-1"></div>
        </div>
    </div>

    <!-- Action Buttons Section 1 -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Utama</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <button onclick="openOutModal()" 
                    id="out_btn"
                    class="px-6 py-4 bg-gray-800 text-white text-base font-semibold rounded-lg hover:bg-gray-900 transition-colors">
                Keluar Rak
            </button>
            <button onclick="openScanInModal()" 
                    id="scan_in_btn"
                    class="px-6 py-4 bg-gray-800 text-white text-base font-semibold rounded-lg hover:bg-gray-900 transition-colors">
                Scan Masuk Rak
            </button>
            <button onclick="openScanInRefundModal()" 
                    id="scan_in_refund_btn"
                    class="px-6 py-4 bg-gray-800 text-white text-base font-semibold rounded-lg hover:bg-gray-900 transition-colors">
                Scan Masuk Refund
            </button>
            <button onclick="openKeepOnModal()" 
                    id="scan_keep_btn"
                    class="px-6 py-4 bg-gray-800 text-white text-base font-semibold rounded-lg hover:bg-gray-900 transition-colors">
                Keep Online
            </button>
            <button onclick="openTakeOnlineModal()" 
                    id="take_online_btn"
                    class="px-6 py-4 bg-gray-800 text-white text-base font-semibold rounded-lg hover:bg-gray-900 transition-colors">
                Ambil Online
            </button>
            <button onclick="openTakeCrossOrderModal()" 
                    id="take_cross_order_btn"
                    class="px-6 py-4 bg-gray-800 text-white text-base font-semibold rounded-lg hover:bg-gray-900 transition-colors">
                Ambil Cross Order
            </button>
        </div>
    </div>

    <!-- Action Buttons Section 2 -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Menu Lainnya</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <a href="{{ url('cross_order') }}" 
               class="px-6 py-4 bg-gray-800 text-white text-base font-semibold rounded-lg hover:bg-gray-900 transition-colors text-center">
                Cross Order
            </a>
            <a href="{{ url('setup_lokasi_stok_v2') }}" 
               class="px-6 py-4 bg-gray-800 text-white text-base font-semibold rounded-lg hover:bg-gray-900 transition-colors text-center">
                Mutasi
            </a>
            <a href="{{ url('setup_lokasi_stok_v2') }}" 
               class="px-6 py-4 bg-gray-800 text-white text-base font-semibold rounded-lg hover:bg-gray-900 transition-colors text-center">
                Data Artikel
            </a>
            <button onclick="openTransferInvoiceModal()" 
                    id="transfer_invoice_btn"
                    class="px-6 py-4 bg-gray-800 text-white text-base font-semibold rounded-lg hover:bg-gray-900 transition-colors">
                Check Transfer Invoice
            </button>
        </div>
    </div>

    <!-- Action Buttons Section 3 -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Transfer</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <button onclick="openScanTransferModal()" 
                    id="scan_transfer_btn"
                    class="px-6 py-4 bg-gray-800 text-white text-base font-semibold rounded-lg hover:bg-gray-900 transition-colors">
                Scan Ambil Item Transfer
            </button>
        </div>
    </div>
</div>

<!-- Hidden inputs -->
<input type="hidden" id="dashboard_date" value="" />
<input type="hidden" id="st_id" value="{{ $data['user']->st_id }}" />
<div id="reader_default" style="visibility: hidden;width:50px; height:50px; position:fixed; z-index:-1; left:0; top:0;"></div>

<!-- Include Modals (Flowbite) -->
@include('app.updated_helper_backup.helper_backup_modals')
@endsection

@push('scripts')
<!-- Define modal functions FIRST in global scope (before dashboard_js) -->
<script>
// Flowbite Modal Helper Functions (Global scope - must be defined early)
(function() {
    'use strict';
    
    function openModal(modalId) {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            modalElement.classList.remove('hidden');
            modalElement.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeModal(modalId) {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            modalElement.classList.add('hidden');
            modalElement.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        }
    }

    // Expose helper functions globally FIRST
    window.openModal = openModal;
    window.closeModal = closeModal;

    // Modal open/close functions (Global scope - accessible from onclick)
    window.openOutModal = function() {
        openModal('OutModal');
    };

    window.closeOutModal = function() {
        closeModal('OutModal');
    };

    window.openScanInModal = function() {
        openModal('ScanInModal');
    };

    window.closeScanInModal = function() {
        closeModal('ScanInModal');
    };

    window.openScanInRefundModal = function() {
        openModal('ScanInRefundModal');
    };

    window.closeScanInRefundModal = function() {
        closeModal('ScanInRefundModal');
    };

    window.openKeepOnModal = function() {
        openModal('KeepOnModal');
    };

    window.closeKeepOnModal = function() {
        closeModal('KeepOnModal');
    };

    window.openTakeOnlineModal = function() {
        openModal('PickOnModal');
    };

    window.closePickOnModal = function() {
        closeModal('PickOnModal');
    };

    window.openTakeCrossOrderModal = function() {
        // Load cross invoice content before opening modal
        if (typeof jQuery !== 'undefined') {
            var $ = jQuery;
            $.ajax({
                url: "{{ url('reload_cross_order_invoice') }}",
                method: "POST",
                success: function(data) {
                    $('#cross_invoice_content').html(data);
                    openModal('TakeCrossOrderModal');
                },
                error: function() {
                    openModal('TakeCrossOrderModal');
                }
            });
        } else {
            openModal('TakeCrossOrderModal');
        }
    };

    window.closeTakeCrossOrderModal = function() {
        closeModal('TakeCrossOrderModal');
    };

    window.openTransferInvoiceModal = function() {
        // Load invoice select before opening modal
        if (typeof jQuery !== 'undefined') {
            var $ = jQuery;
            $('#_transfer_mode').val('check');
            $.ajax({
                type: "GET",
                dataType: 'html',
                url: "{{ url('reload_transfer_invoice_check') }}",
                success: function(r) {
                    $('.transfer_invoice').html(r);
                    openModal('TransferModal');
                },
                error: function() {
                    openModal('TransferModal');
                }
            });
        } else {
            openModal('TransferModal');
        }
    };

    window.closeTransferModal = function() {
        closeModal('TransferModal');
    };

    window.openScanTransferModal = function() {
        // Load invoice select before opening modal
        if (typeof jQuery !== 'undefined') {
            var $ = jQuery;
            $('#_scan_transfer_mode').val('get');
            $.ajax({
                type: "GET",
                dataType: 'html',
                url: "{{ url('reload_scan_transfer_invoice') }}",
                success: function(r) {
                    $('.scan_transfer_invoice').html(r);
                    openModal('ScanTransferModal');
                },
                error: function() {
                    openModal('ScanTransferModal');
                }
            });
        } else {
            openModal('ScanTransferModal');
        }
    };

    window.closeScanTransferModal = function() {
        closeModal('ScanTransferModal');
    };

    window.openChangePasswordModal = function() {
        openModal('ChangePasswordModal');
    };

    window.closeChangePasswordModal = function() {
        closeModal('ChangePasswordModal');
    };

    window.closeScanTransferDetailModal = function() {
        closeModal('ScanTransferDetailModal');
    };

    window.closeTransferDetailModal = function() {
        closeModal('TransferDetailModal');
    };

    window.closeOrderListModal = function() {
        closeModal('OrderListModal');
    };

    window.closeBinModal = function() {
        closeModal('binModal');
    };

    window.closeTakeTransferItemModal = function() {
        closeModal('TakeTransferItemModal');
    };
})();
</script>

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- SweetAlert CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.css" />
<style>
    .select2-container--default .select2-selection--multiple {
        min-height: 48px;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #3b82f6;
        border: 1px solid #2563eb;
        color: white;
        padding: 4px 8px;
        margin: 4px;
        border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 8px;
    }
</style>
@endpush

@push('scripts')
<!-- Define modal functions FIRST in global scope (before any other scripts) -->
<script>
// Flowbite Modal Helper Functions (Global scope - must be defined early)
(function() {
    'use strict';
    
    function openModal(modalId) {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            modalElement.classList.remove('hidden');
            modalElement.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeModal(modalId) {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            modalElement.classList.add('hidden');
            modalElement.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        }
    }

    // Expose helper functions globally FIRST
    window.openModal = openModal;
    window.closeModal = closeModal;

    // Modal open/close functions (Global scope - accessible from onclick)
    window.openOutModal = function() {
        openModal('OutModal');
    };

    window.closeOutModal = function() {
        closeModal('OutModal');
    };

    window.openScanInModal = function() {
        openModal('ScanInModal');
    };

    window.closeScanInModal = function() {
        closeModal('ScanInModal');
    };

    window.openScanInRefundModal = function() {
        openModal('ScanInRefundModal');
    };

    window.closeScanInRefundModal = function() {
        closeModal('ScanInRefundModal');
    };

    window.openKeepOnModal = function() {
        openModal('KeepOnModal');
    };

    window.closeKeepOnModal = function() {
        closeModal('KeepOnModal');
    };

    window.openTakeOnlineModal = function() {
        openModal('PickOnModal');
    };

    window.closePickOnModal = function() {
        closeModal('PickOnModal');
    };

    window.openTakeCrossOrderModal = function() {
        // Load cross invoice content before opening modal
        if (typeof jQuery !== 'undefined') {
            var $ = jQuery;
            $.ajax({
                url: "{{ url('reload_cross_order_invoice') }}",
                method: "POST",
                success: function(data) {
                    $('#cross_invoice_content').html(data);
                    openModal('TakeCrossOrderModal');
                },
                error: function() {
                    openModal('TakeCrossOrderModal');
                }
            });
        } else {
            openModal('TakeCrossOrderModal');
        }
    };

    window.closeTakeCrossOrderModal = function() {
        closeModal('TakeCrossOrderModal');
    };

    window.openTransferInvoiceModal = function() {
        // Load invoice select before opening modal
        if (typeof jQuery !== 'undefined') {
            var $ = jQuery;
            $('#_transfer_mode').val('check');
            $.ajax({
                type: "GET",
                dataType: 'html',
                url: "{{ url('reload_transfer_invoice_check') }}",
                success: function(r) {
                    $('.transfer_invoice').html(r);
                    openModal('TransferModal');
                },
                error: function() {
                    openModal('TransferModal');
                }
            });
        } else {
            openModal('TransferModal');
        }
    };

    window.closeTransferModal = function() {
        closeModal('TransferModal');
    };

    window.openScanTransferModal = function() {
        // Load invoice select before opening modal
        if (typeof jQuery !== 'undefined') {
            var $ = jQuery;
            $('#_scan_transfer_mode').val('get');
            $.ajax({
                type: "GET",
                dataType: 'html',
                url: "{{ url('reload_scan_transfer_invoice') }}",
                success: function(r) {
                    $('.scan_transfer_invoice').html(r);
                    openModal('ScanTransferModal');
                },
                error: function() {
                    openModal('ScanTransferModal');
                }
            });
        } else {
            openModal('ScanTransferModal');
        }
    };

    window.closeScanTransferModal = function() {
        closeModal('ScanTransferModal');
    };

    window.openChangePasswordModal = function() {
        openModal('ChangePasswordModal');
    };

    window.closeChangePasswordModal = function() {
        closeModal('ChangePasswordModal');
    };

    window.closeScanTransferDetailModal = function() {
        closeModal('ScanTransferDetailModal');
    };

    window.closeTransferDetailModal = function() {
        closeModal('TransferDetailModal');
    };

    window.closeOrderListModal = function() {
        closeModal('OrderListModal');
    };

    window.closeBinModal = function() {
        closeModal('binModal');
    };

    window.closeTakeTransferItemModal = function() {
        closeModal('TakeTransferItemModal');
    };
})();
</script>

<!-- HTML5 QR Code Scanner -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js"
        integrity="sha512-k/KAe4Yff9EUdYI5/IAHlwUswqeipP+Cp5qnrsUjTPCgl51La2/JhyyjNciztD7mWNKLSXci48m7cctATKfLlQ==" 
        crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- SweetAlert JS (for swal function) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
        integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
<!-- jQuery Toast JS (for toast function) -->
<script src="{{ asset('cdn/jquery.toast.min.js') }}"></script>

<!-- Ensure $ is available globally before including dashboard_js -->
<script>
// Ensure jQuery $ is available globally and wrap all handlers
(function() {
    'use strict';
    
    // Wait for jQuery to be available
    function ensureJQuery() {
        if (typeof jQuery === 'undefined') {
            setTimeout(ensureJQuery, 50);
            return;
        }
        
        // Ensure $ is available globally
        if (typeof window.$ === 'undefined') {
            window.$ = jQuery;
        }
        
        // Override jQuery.noConflict to preserve $ in global scope
        var originalNoConflict = jQuery.noConflict;
        jQuery.noConflict = function(deep) {
            var result = originalNoConflict.call(this, deep);
            // Always restore $ to jQuery after noConflict
            window.$ = jQuery;
            return result;
        };
    }
    
    // Start checking
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', ensureJQuery);
    } else {
        ensureJQuery();
    }
    
    // Also restore $ after dashboard_js is loaded (in case noConflict is called)
    window.addEventListener('load', function() {
        if (typeof jQuery !== 'undefined' && typeof window.$ === 'undefined') {
            window.$ = jQuery;
        }
    });
})();
</script>

<!-- Include dashboard_js - jQuery should be available from layout -->
<!-- jQuery is loaded in layout (line 728) before @stack('scripts'), so it should be available -->
<!-- Override problematic handlers before including dashboard_js -->
<script>
// Override handlers that use .modal() before dashboard_js loads
(function() {
    'use strict';
    
    function overrideModalHandlers() {
        if (typeof jQuery === 'undefined') {
            setTimeout(overrideModalHandlers, 50);
            return;
        }
        
        var $ = jQuery;
        
        // Store original handlers to override after dashboard_js loads
        window._overrideTransferHandlers = function() {
            // Override transfer_btn handler
            $('#transfer_btn').off('click').on('click', function(e) {
                e.preventDefault();
                $('#_transfer_mode').val('get');
                $.ajax({
                    type: "GET",
                    dataType: 'html',
                    url: "{{ url('reload_transfer_invoice') }}",
                    success: function(r) {
                        $('.transfer_invoice').html(r);
                        if (typeof openModal === 'function') {
                            openModal('TransferModal');
                        }
                    }
                });
            });
            
            // Override scan_transfer_btn handler
            $('#scan_transfer_btn').off('click').on('click', function(e) {
                e.preventDefault();
                $('#_scan_transfer_mode').val('get');
                $.ajax({
                    type: "GET",
                    dataType: 'html',
                    url: "{{ url('reload_scan_transfer_invoice') }}",
                    success: function(r) {
                        $('.scan_transfer_invoice').html(r);
                        if (typeof openModal === 'function') {
                            openModal('ScanTransferModal');
                        }
                    }
                });
            });
            
            // Override transfer_invoice_btn handler
            $('#transfer_invoice_btn').off('click').on('click', function(e) {
                e.preventDefault();
                $('#_transfer_mode').val('check');
                $.ajax({
                    type: "GET",
                    dataType: 'html',
                    url: "{{ url('reload_transfer_invoice_check') }}",
                    success: function(r) {
                        $('.transfer_invoice').html(r);
                        if (typeof openModal === 'function') {
                            openModal('TransferModal');
                        }
                    }
                });
            });
            
            // Handler for transfer_invoice select change (to open TransferDetailModal)
            // This matches the old version behavior: when invoice is selected, open detail modal
            $(document).off('change', '#transfer_invoice').on('change', '#transfer_invoice', function(e) {
                e.preventDefault();
                var invoice = $('#transfer_invoice option:selected').text();
                $('#transfer_invoice_modal_label').text(invoice);
                $('#transfer_invoice_label').val(invoice);
                // Close TransferModal and open TransferDetailModal
                closeModal('TransferModal');
                setTimeout(function() {
                    if (typeof openModal === 'function') {
                        openModal('TransferDetailModal');
                    }
                    // Wait a bit for table to be initialized
                    setTimeout(function() {
                        if (typeof transfer_list_table !== 'undefined') {
                            transfer_list_table.draw();
                        }
                    }, 200);
                }, 100);
            });
            
            // Handler for scan_transfer_invoice select change (to open ScanTransferDetailModal)
            // This matches the old version behavior: when invoice is selected, open detail modal
            $(document).off('change', '#scan_transfer_invoice').on('change', '#scan_transfer_invoice', function(e) {
                e.preventDefault();
                var invoice = $('#scan_transfer_invoice option:selected').text();
                $('#scan_transfer_invoice_modal_label').text(invoice);
                $('#scan_transfer_invoice_label').val(invoice);
                // Close ScanTransferModal and open ScanTransferDetailModal
                closeModal('ScanTransferModal');
                setTimeout(function() {
                    if (typeof openModal === 'function') {
                        openModal('ScanTransferDetailModal');
                    }
                    // Wait a bit for table to be initialized
                    setTimeout(function() {
                        if (typeof scan_transfer_list_table !== 'undefined') {
                            scan_transfer_list_table.draw();
                        }
                    }, 200);
                }, 100);
            });
            
            // Handler for cross_invoice select change (to show/hide table)
            // This matches the old version behavior - uses 'd-none' like old version
            $(document).off('change', '#cross_invoice').on('change', '#cross_invoice', function() {
                var invoice = $(this).val();
                if (invoice == '' || invoice == null) {
                    $('#TakeCrossOrdertb').addClass('d-none');
                } else {
                    $('#TakeCrossOrdertb').removeClass('d-none');
                    // Wait a bit for table to be initialized
                    setTimeout(function() {
                        if (typeof take_cross_order_table !== 'undefined') {
                            take_cross_order_table.draw();
                        }
                    }, 200);
                }
            });
            
            // Handler for get_cross_item_btn (Ambil button in TakeCrossOrderModal)
            // This ensures the handler works even if dashboard_js handler doesn't fire
            $(document).off('click', '#get_cross_item_btn').on('click', '#get_cross_item_btn', function() {
                var ptd_id = $(this).attr('data-ptd_id');
                var ptd_qty = $(this).attr('data-ptd_qty');
                swal({
                    title: "Ambil..?",
                    text: "Yakin sudah benar ?",
                    icon: "warning",
                    buttons: [
                        'Batal',
                        'Yakin'
                    ],
                    dangerMode: false,
                }).then(function(isConfirm) {
                    if (isConfirm) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: "POST",
                            data: {
                                _ptd_id: ptd_id,
                                _ptd_qty: ptd_qty
                            },
                            dataType: 'json',
                            url: "{{ url('get_cross_item_status') }}",
                            success: function(r) {
                                if (r.status == '200') {
                                    toast("Berhasil", "Berhasil ambil artikel", "success");
                                    if (typeof take_cross_order_table !== 'undefined') {
                                        take_cross_order_table.draw();
                                    }
                                } else {
                                    toast('Gagal', 'Gagal', 'error');
                                }
                            }
                        });
                        return false;
                    }
                });
            });
        };
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', overrideModalHandlers);
    } else {
        overrideModalHandlers();
    }
})();
</script>

@include('app.dashboard.helper.dashboard_js')

<script>
// Immediately override handlers that use .modal() after dashboard_js is included
// This must be done immediately to prevent dashboard_js handlers from attaching
(function() {
    'use strict';
    
    function overrideModalHandlersImmediately() {
        if (typeof jQuery === 'undefined') {
            setTimeout(overrideModalHandlersImmediately, 50);
            return;
        }
        
        var $ = jQuery;
        
        // Override transfer_invoice_btn handler immediately (before dashboard_js attaches its handler)
        $('#transfer_invoice_btn').off('click').on('click', function(e) {
            e.preventDefault();
            $('#_transfer_mode').val('check');
            $.ajax({
                type: "GET",
                dataType: 'html',
                url: "{{ url('reload_transfer_invoice_check') }}",
                success: function(r) {
                    $('.transfer_invoice').html(r);
                    if (typeof openModal === 'function') {
                        openModal('TransferModal');
                    }
                }
            });
        });
        
        // Override transfer_btn handler
        $('#transfer_btn').off('click').on('click', function(e) {
            e.preventDefault();
            $('#_transfer_mode').val('get');
            $.ajax({
                type: "GET",
                dataType: 'html',
                url: "{{ url('reload_transfer_invoice') }}",
                success: function(r) {
                    $('.transfer_invoice').html(r);
                    if (typeof openModal === 'function') {
                        openModal('TransferModal');
                    }
                }
            });
        });
        
        // Override scan_transfer_btn handler
        $('#scan_transfer_btn').off('click').on('click', function(e) {
            e.preventDefault();
            $('#_scan_transfer_mode').val('get');
            $.ajax({
                type: "GET",
                dataType: 'html',
                url: "{{ url('reload_scan_transfer_invoice') }}",
                success: function(r) {
                    $('.scan_transfer_invoice').html(r);
                    if (typeof openModal === 'function') {
                        openModal('ScanTransferModal');
                    }
                }
            });
        });
    }
    
    // Try to override immediately
    overrideModalHandlersImmediately();
    
    // Also try after a short delay to catch any handlers that attach later
    setTimeout(overrideModalHandlersImmediately, 100);
})();
</script>

<script>
// Apply overrides after dashboard_js is loaded
(function() {
    'use strict';
    if (typeof window._overrideTransferHandlers === 'function') {
        // Wait longer for dashboard_js to finish initializing (tables need time)
        setTimeout(function() {
            if (typeof jQuery !== 'undefined') {
                window._overrideTransferHandlers();
            }
        }, 500);
    }
})();
</script>

<script>
// Restore $ after dashboard_js is included (in case noConflict was called)
(function() {
    'use strict';
    if (typeof jQuery !== 'undefined') {
        window.$ = jQuery;
    }
})();
</script>

<script>
// Wait for jQuery to be fully loaded, then initialize
(function() {
    'use strict';
    
    // Function to initialize everything after jQuery is ready
    function initHelperBackup() {
        // Ensure jQuery is available
        if (typeof jQuery === 'undefined') {
            console.error('jQuery is not loaded, retrying...');
            // Retry after a short delay
            setTimeout(initHelperBackup, 100);
            return;
        }
        
        var $ = jQuery;
        
        // Override loadStorageAreas to include Select2 initialization
        window.loadStorageAreas = function() {
            $.ajax({
                type: "GET",
                dataType: 'html',
                url: "{{ url('reload_storage_area') }}",
                success: function(r) {
                    $("#storage_area_select").html(r);
                    // Re-initialize select2 after content is loaded
                    if ($('#storage_area').length) {
                        $('#storage_area').select2({
                            width: "100%",
                            dropdownParent: $('#storage_area').parent()
                        });
                        // Change Select2 rendered text size
                        $('#storage_area').on('select2:open', function () {
                            $('.select2-results__option').css('font-size', '1rem');
                        });
                        $('.select2-selection__rendered').css('font-size', '1rem');
                        $('#storage_area').on('select2:select', function () {
                            $('.select2-selection__choice').css('font-size', '1rem');
                        });
                    }
                }
            });
            return false;
        };
        
        // Call loadStorageAreas after initialization
        $(document).ready(function() {
            // Ensure $ is still available (in case noConflict was called)
            if (typeof window.$ === 'undefined' && typeof jQuery !== 'undefined') {
                window.$ = jQuery;
            }
            
            // Setup AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // Override Bootstrap modal handlers to use Flowbite modals
            // These handlers are in dashboard_js but use .modal() which doesn't work with Flowbite
            // We'll override them here to use our Flowbite modal functions
            
            // Transfer button handler (for 'get' mode)
            $('#transfer_btn').off('click').on('click', function(e) {
                e.preventDefault();
                $('#_transfer_mode').val('get');
                $.ajax({
                    type: "GET",
                    dataType: 'html',
                    url: "{{ url('reload_transfer_invoice') }}",
                    success: function(r) {
                        $('.transfer_invoice').html(r);
                        openModal('TransferModal');
                    }
                });
            });
            
            // Handler for transfer_invoice select change (to open TransferDetailModal)
            // This matches the old version behavior: when invoice is selected, open detail modal
            $(document).off('change', '#transfer_invoice').on('change', '#transfer_invoice', function(e) {
                e.preventDefault();
                var invoice = $('#transfer_invoice option:selected').text();
                $('#transfer_invoice_modal_label').text(invoice);
                $('#transfer_invoice_label').val(invoice);
                // Close TransferModal and open TransferDetailModal
                closeModal('TransferModal');
                setTimeout(function() {
                    if (typeof openModal === 'function') {
                        openModal('TransferDetailModal');
                    }
                    // Wait a bit for table to be initialized
                    setTimeout(function() {
                        if (typeof transfer_list_table !== 'undefined') {
                            transfer_list_table.draw();
                        }
                    }, 200);
                }, 100);
            });
            
            // Handler for scan_transfer_invoice select change (to open ScanTransferDetailModal)
            // This matches the old version behavior: when invoice is selected, open detail modal
            $(document).off('change', '#scan_transfer_invoice').on('change', '#scan_transfer_invoice', function(e) {
                e.preventDefault();
                var invoice = $('#scan_transfer_invoice option:selected').text();
                $('#scan_transfer_invoice_modal_label').text(invoice);
                $('#scan_transfer_invoice_label').val(invoice);
                // Close ScanTransferModal and open ScanTransferDetailModal
                closeModal('ScanTransferModal');
                setTimeout(function() {
                    if (typeof openModal === 'function') {
                        openModal('ScanTransferDetailModal');
                    }
                    // Wait a bit for table to be initialized
                    setTimeout(function() {
                        if (typeof scan_transfer_list_table !== 'undefined') {
                            scan_transfer_list_table.draw();
                        }
                    }, 200);
                }, 100);
            });
            
            // Handler for cross_invoice select change (to show/hide table)
            // This matches the old version behavior - uses 'd-none' like old version
            $(document).off('change', '#cross_invoice').on('change', '#cross_invoice', function() {
                var invoice = $(this).val();
                if (invoice == '' || invoice == null) {
                    $('#TakeCrossOrdertb').addClass('d-none');
                } else {
                    $('#TakeCrossOrdertb').removeClass('d-none');
                    // Wait a bit for table to be initialized
                    setTimeout(function() {
                        if (typeof take_cross_order_table !== 'undefined') {
                            take_cross_order_table.draw();
                        }
                    }, 200);
                }
            });
            
            // Handler for get_cross_item_btn (Ambil button in TakeCrossOrderModal)
            // This ensures the handler works even if dashboard_js handler doesn't fire
            $(document).off('click', '#get_cross_item_btn').on('click', '#get_cross_item_btn', function() {
                var ptd_id = $(this).attr('data-ptd_id');
                var ptd_qty = $(this).attr('data-ptd_qty');
                swal({
                    title: "Ambil..?",
                    text: "Yakin sudah benar ?",
                    icon: "warning",
                    buttons: [
                        'Batal',
                        'Yakin'
                    ],
                    dangerMode: false,
                }).then(function(isConfirm) {
                    if (isConfirm) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: "POST",
                            data: {
                                _ptd_id: ptd_id,
                                _ptd_qty: ptd_qty
                            },
                            dataType: 'json',
                            url: "{{ url('get_cross_item_status') }}",
                            success: function(r) {
                                if (r.status == '200') {
                                    toast("Berhasil", "Berhasil ambil artikel", "success");
                                    if (typeof take_cross_order_table !== 'undefined') {
                                        take_cross_order_table.draw();
                                    }
                                } else {
                                    toast('Gagal', 'Gagal', 'error');
                                }
                            }
                        });
                        return false;
                    }
                });
            });
            
            // Load storage areas
            if (typeof window.loadStorageAreas === 'function') {
                window.loadStorageAreas();
            }
            
            // Monitor for noConflict calls and restore $
            var checkInterval = setInterval(function() {
                if (typeof jQuery !== 'undefined' && typeof window.$ === 'undefined') {
                    window.$ = jQuery;
                }
            }, 100);
            
            // Stop checking after 5 seconds
            setTimeout(function() {
                clearInterval(checkInterval);
            }, 5000);
        });
    }
    
    // Wait for DOM and jQuery to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            // Wait a bit more for jQuery to be loaded from layout
            setTimeout(initHelperBackup, 200);
        });
    } else {
        // DOM is already ready, wait for jQuery
        setTimeout(initHelperBackup, 200);
    }
})();
</script>

<script>
// Additional initialization after dashboard_js is loaded
// Wait for jQuery to be available
(function() {
    'use strict';
    
    function initAdditional() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initAdditional, 100);
            return;
        }
        
        var $ = jQuery;
        
        // Ensure handler for get_cross_item_btn is attached
        // This ensures the handler works even if dashboard_js handler doesn't fire
        $(document).off('click', '#get_cross_item_btn').on('click', '#get_cross_item_btn', function() {
            var ptd_id = $(this).attr('data-ptd_id');
            var ptd_qty = $(this).attr('data-ptd_qty');
            swal({
                title: "Ambil..?",
                text: "Yakin sudah benar ?",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Yakin'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {
                            _ptd_id: ptd_id,
                            _ptd_qty: ptd_qty
                        },
                        dataType: 'json',
                        url: "{{ url('get_cross_item_status') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                toast("Berhasil", "Berhasil ambil artikel", "success");
                                if (typeof take_cross_order_table !== 'undefined') {
                                    take_cross_order_table.draw();
                                }
                            } else {
                                toast('Gagal', 'Gagal', 'error');
                            }
                        }
                    });
                    return false;
                }
            });
        });
        
        // Close modals with Escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                const modals = ['OutModal', 'ScanInModal', 'ScanInRefundModal', 'KeepOnModal', 'PickOnModal',
                              'TakeCrossOrderModal', 'TransferModal', 'ScanTransferModal', 'ChangePasswordModal',
                              'ScanTransferDetailModal', 'TransferDetailModal', 'OrderListModal', 'binModal', 'TakeTransferItemModal'];
                modals.forEach(function(modalId) {
                    const modal = document.getElementById(modalId);
                    if (modal && !modal.classList.contains('hidden')) {
                        // Use window function if available, otherwise use direct close
                        if (typeof window.closeModal === 'function') {
                            window.closeModal(modalId);
                        } else {
                            modal.classList.add('hidden');
                            modal.setAttribute('aria-hidden', 'true');
                            document.body.classList.remove('overflow-hidden');
                        }
                    }
                });
            }
        });
    }
    
    // Wait for DOM and jQuery to be fully ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initAdditional, 200);
        });
    } else {
        setTimeout(initAdditional, 200);
    }
})();
</script>
@endpush
