// POS V2 JavaScript

// Initialize variables (outside document ready so they're accessible globally)
let orderItems = [];
let subtotal = 0;
let shippingCost = 0;
let voucherDiscount = 0;
let totalDiscount = 0;

// Initialize Flowbite modals (will be set in document ready)
let shippingModal, voucherModal, discountModal, paymentModal;

// Global function to force remove ALL backdrops
window.forceRemoveAllBackdrops = function() {
    console.log('🔴 === FORCE REMOVING ALL BACKDROPS ===');
    
    // 1. Remove by ID first
    const paymentBackdrop = document.getElementById('payment-modal-backdrop');
    if (paymentBackdrop) {
        console.log('✅ Removing payment backdrop by ID');
        paymentBackdrop.remove();
    }
    
    // 2. Remove ALL divs with backdrop characteristics
    const allDivs = document.querySelectorAll('body > div');
    console.log('🔍 Checking', allDivs.length, 'divs for backdrops');
    
    let removedCount = 0;
    allDivs.forEach(function(div) {
        const divId = div.getAttribute('id') || '';
        const classes = div.className || '';
        const style = div.getAttribute('style') || '';
        
        // Skip modal elements and important containers
        if (divId.includes('modal-') || divId.includes('toast-') || divId.includes('confirm-') ||
            divId === 'app' || divId === 'root' || divId === 'main-content') {
            return;
        }
        
        // Check if looks like backdrop
        const looksLikeBackdrop = (
            (classes.includes('fixed') && classes.includes('inset-0')) ||
            (style.includes('position: fixed') && style.includes('z-index')) ||
            (classes.includes('z-40') || classes.includes('z-50')) ||
            (style.includes('background-color: rgba'))
        );
        
        if (looksLikeBackdrop) {
            const bgColor = window.getComputedStyle(div).backgroundColor;
            const isTransparent = (bgColor === 'rgba(0, 0, 0, 0)' || bgColor === 'transparent' || bgColor === 'transparent');
            
            if (!isTransparent) {
                console.log('🗑️ REMOVING BACKDROP:', {id: divId || 'no-id', classes: classes, bgColor: bgColor});
                div.remove();
                removedCount++;
            }
        }
    });
    
    console.log('✨ Removed', removedCount, 'backdrop(s)');
};

// Flowbite Toast Helper Function
function showToast(message, type = 'warning') {
    // Type: success, error, warning, info
    const icons = {
        success: '<i class="cft-standard-solid cft-check text-xl"></i>',
        error: '<i class="cft-standard-solid cft-cancel text-xl"></i>',
        warning: '<i class="cft-standard-solid cft-warning text-xl"></i>',
        info: '<i class="cft-standard-solid cft-info text-xl"></i>'
    };
    
    const colors = {
        success: { bg: 'bg-green-100', icon: 'text-green-500', text: 'text-green-800' },
        error: { bg: 'bg-red-100', icon: 'text-red-500', text: 'text-red-800' },
        warning: { bg: 'bg-orange-100', icon: 'text-orange-500', text: 'text-orange-800' },
        info: { bg: 'bg-red-100', icon: 'text-red-500', text: 'text-red-800' }
    };
    
    const color = colors[type] || colors.warning;
    const icon = icons[type] || icons.warning;
    
    // Create toast container if it doesn't exist
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-5 right-5 z-50 space-y-4';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `flex items-center p-4 mb-4 w-full max-w-xs text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800`;
    toast.setAttribute('role', 'alert');
    
    toast.innerHTML = `
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 ${color.icon} ${color.bg} rounded-lg dark:${color.bg} dark:${color.icon}">
            ${icon}
        </div>
        <div class="ml-3 text-sm font-normal ${color.text} dark:text-gray-400">${message.replace(/\n/g, '<br>')}</div>
        <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" data-dismiss-target="#${toastId}" aria-label="Close">
            <span class="sr-only">Close</span>
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </button>
    `;
    
    toastContainer.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.style.transition = 'opacity 0.3s';
            toast.style.opacity = '0';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }
    }, 5000);
    
    // Handle close button
    const closeBtn = toast.querySelector('[data-dismiss-target]');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            toast.style.transition = 'opacity 0.3s';
            toast.style.opacity = '0';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        });
    }
}

// Toast Confirmation Helper Function (replacement for confirm())
function showConfirmToast(message, confirmText = 'Ya', cancelText = 'Batal') {
    return new Promise((resolve) => {
        // Create toast container if it doesn't exist
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'fixed top-5 right-5 z-50 space-y-4';
            document.body.appendChild(toastContainer);
        }
        
        // Create confirmation toast element
        const toastId = 'confirm-toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = 'flex items-center p-4 mb-4 w-full max-w-md text-gray-500 bg-white rounded-lg shadow-lg dark:text-gray-400 dark:bg-gray-800 border border-gray-200';
        toast.setAttribute('role', 'alert');
        
        toast.innerHTML = `
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-orange-500 bg-orange-100 rounded-lg dark:bg-orange-800 dark:text-orange-200">
                <i class="cft-standard-solid cft-warning text-xl"></i>
            </div>
            <div class="ml-3 flex-1">
                <div class="text-sm font-medium text-gray-800 dark:text-gray-300">${message}</div>
                <div class="mt-3 flex gap-2">
                    <button type="button" class="confirm-yes-btn px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                        ${confirmText}
                    </button>
                    <button type="button" class="confirm-cancel-btn px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                        ${cancelText}
                    </button>
                </div>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Handle confirm button
        const confirmBtn = toast.querySelector('.confirm-yes-btn');
        confirmBtn.addEventListener('click', () => {
            toast.style.transition = 'opacity 0.3s';
            toast.style.opacity = '0';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
            resolve(true);
        });
        
        // Handle cancel button
        const cancelBtn = toast.querySelector('.confirm-cancel-btn');
        cancelBtn.addEventListener('click', () => {
            toast.style.transition = 'opacity 0.3s';
            toast.style.opacity = '0';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
            resolve(false);
        });
    });
}

$(document).ready(function() {
    // Debug: Check if jQuery and CSRF token are loaded
    console.log('POS V2 JS loaded');
    console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content'));
    console.log('jQuery version:', $.fn.jquery);
    
    // ===========================================
    // RETUR / EXCHANGE FUNCTIONALITY
    // ===========================================
    
    let selectedTransactionForRetur = null;
    let returItemsData = [];
    
    // Initialize Flowbite modals - wait for Flowbite to be ready
    function initModals() {
        if (typeof Flowbite === 'undefined' || !Flowbite.Modal) {
            setTimeout(initModals, 100);
            return;
        }
        
        const shippingModalEl = document.getElementById('modal-shipping');
        const voucherModalEl = document.getElementById('modal-voucher');
        const discountModalEl = document.getElementById('modal-discount');
        const paymentModalEl = document.getElementById('modal-payment');
        const shiftModalEl = document.getElementById('modal-shift');
        
        try {
            if (shippingModalEl) {
                shippingModal = new Flowbite.Modal(shippingModalEl, {
                    placement: 'center',
                    backdrop: 'dynamic',
                    backdropClasses: 'bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-40'
                });
            }
            if (voucherModalEl) {
                voucherModal = new Flowbite.Modal(voucherModalEl, {
                    placement: 'center',
                    backdrop: 'dynamic',
                    backdropClasses: 'bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-40'
                });
            }
            if (discountModalEl) {
                discountModal = new Flowbite.Modal(discountModalEl, {
                    placement: 'center',
                    backdrop: 'dynamic',
                    backdropClasses: 'bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-40'
                });
            }
            if (paymentModalEl) {
                // Initialize payment modal - sama seperti modal shipping/voucher/discount
                paymentModal = new Flowbite.Modal(paymentModalEl, {
                    placement: 'center',
                    backdrop: 'static',
                    backdropClasses: 'bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-40'
                });
            }
            if (shiftModalEl) {
                window.shiftModal = new Flowbite.Modal(shiftModalEl, {
                    placement: 'center',
                    backdrop: 'dynamic',
                    backdropClasses: 'bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-40'
                });
            }
            console.log('Flowbite modals initialized successfully');
        } catch (error) {
            console.error('Error initializing Flowbite modals:', error);
        }
    }
    
    // Wait for window to fully load including Flowbite
    if (document.readyState === 'complete') {
        initModals();
    } else {
        window.addEventListener('load', function() {
            setTimeout(initModals, 200);
        });
        // Also try immediately
        setTimeout(initModals, 200);
    }
    
    // Update time
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit',
            hour12: false 
        });
        $('#current-time').text(timeString);
    }
    
    setInterval(updateTime, 1000);
    updateTime();
    
    // Retur checkbox toggle
    $('#retur-checkbox').on('change', function() {
        if ($(this).is(':checked')) {
            $('#transaction-id').removeClass('hidden').addClass('block');
        } else {
            $('#transaction-id').addClass('hidden').removeClass('block');
        }
    });
    
    // Search product with autocomplete
    let productSearchTimeout;
    $('#search-product').on('keyup', function() {
        const searchTerm = $(this).val().trim();
        const $autocomplete = $('#product-autocomplete');
        
        clearTimeout(productSearchTimeout);
        
        if (searchTerm.length < 2) {
            $autocomplete.addClass('hidden').removeClass('block').empty();
            return;
        }
        
        productSearchTimeout = setTimeout(function() {
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            if (!csrfToken) {
                console.error('CSRF token not found!');
                $autocomplete.html('<ul class="divide-y divide-gray-200"><li class="p-4 text-red-600">CSRF token not found. Please refresh the page.</li></ul>').removeClass('hidden').addClass('block');
                return;
            }
            $.ajax({
                url: '/search_product_v2',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: {
                    query: searchTerm,
                    type: $('#std_id option:selected').text() || '', // Same as old version
                    _std_id: $('#std_id').val() || '', // Same as old version - use _std_id with underscore
                    _st_id: $('#st_id').val() || '', // Same as old version - use _st_id from select dropdown
                    _token: csrfToken
                },
                success: function(products) {
                    console.log('=== AJAX SUCCESS ===');
                    console.log('Products received:', products);
                    console.log('Products count:', products ? products.length : 0);
                    console.log('Products type:', typeof products);
                    console.log('Is Array:', Array.isArray(products));
                    console.log('Products stringified:', JSON.stringify(products).substring(0, 200));
                    
                    // Handle both array and object responses
                    let productArray = products;
                    if (!Array.isArray(products)) {
                        if (products.data && Array.isArray(products.data)) {
                            productArray = products.data;
                        } else if (products.products && Array.isArray(products.products)) {
                            productArray = products.products;
                            } else {
                                console.error('Unexpected response format:', products);
                                $autocomplete.html('<ul class="divide-y divide-gray-200"><li class="p-4 text-orange-600">Unexpected response format. Check console.</li></ul>').removeClass('hidden').addClass('block');
                                return;
                            }
                    }
                    
                    if (productArray && productArray.length > 0) {
                        let html = '<ul class="divide-y divide-gray-200">';
                        productArray.forEach(function(product) {
                            // Parse bin string to create badges
                            // Format: [02] [0] [TOKO] [0]
                            const binBadges = formatBinToBadges(product.bin);
                            
                            const productImage = product.image || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(product.brand || 'BRAND') + '&background=F74040&color=fff&size=60';
                            const brandName = product.brand || 'BRAND';
                            
                            html += `
                                <li class="product-item p-4 bg-white hover:bg-gray-50 cursor-pointer transition-colors text-sm" 
                                    data-product='${JSON.stringify(product).replace(/'/g, "&#39;")}'>
                                    <div class="flex justify-between items-start gap-3">
                                        <div class="flex items-start gap-3 flex-1">
                                            <img src="${productImage}" alt="${product.name}" class="w-10 h-10 object-cover rounded flex-shrink-0" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(brandName)}&background=F74040&color=fff&size=60'">
                                            <div class="flex-1 min-w-0">
                                                <div class="mb-2 flex items-center gap-2 flex-wrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-orange-500 text-white">${brandName}</span>
                                                    <span class="block text-gray-900 font-semibold">${product.name}</span>
                                                </div>
                                                <div class="flex flex-wrap gap-1 items-center mb-1">
                                                    ${binBadges}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right ml-3 flex-shrink-0">
                                            <div class="mb-1">
                                                <strong class="text-red-600">Rp. ${formatNumber(product.price)}</strong>
                                            </div>
                                            ${product.disc_percent > 0 ? `<small class="text-green-600 block"><i class="fas fa-tag"></i> Diskon ${product.disc_percent}%</small>` : ''}
                                            ${product.disc_rp > 0 ? `<small class="text-green-600 block"><i class="fas fa-money-bill-wave"></i> Rp. ${formatNumber(product.disc_rp)}</small>` : ''}
                                        </div>
                                    </div>
                                </li>
                            `;
                        });
                        html += '</ul>';
                        $autocomplete.html(html).removeClass('hidden').addClass('block');
                    } else {
                        $autocomplete.html('<ul class="divide-y divide-gray-200"><li class="p-4 text-gray-500 text-sm">No products found</li></ul>').removeClass('hidden').addClass('block');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error searching products:', error);
                    console.error('Response:', xhr.responseText);
                    console.error('Status:', xhr.status);
                    console.error('Status Text:', xhr.statusText);
                    
                    let errorMsg = 'Error searching products';
                    if (xhr.status === 419) {
                        errorMsg = 'CSRF token mismatch. Please refresh the page.';
                        // Try to reload CSRF token
                        $.get('/point_of_sale_v2').then(function(html) {
                            var parser = new DOMParser();
                            var doc = parser.parseFromString(html, 'text/html');
                            var newToken = doc.querySelector('meta[name="csrf-token"]').getAttribute('content');
                            $('meta[name="csrf-token"]').attr('content', newToken);
                            console.log('CSRF token refreshed');
                        });
                    } else if (xhr.status === 500) {
                        errorMsg = 'Server error. Please try again.';
                    } else if (xhr.status === 0) {
                        errorMsg = 'Network error. Please check your connection.';
                    } else if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMsg = response.message || errorMsg;
                        } catch(e) {
                            errorMsg = error + ' (Status: ' + xhr.status + ')';
                        }
                    }
                    
                        $autocomplete.html('<ul class="divide-y divide-gray-200"><li class="p-4 text-red-600">' + errorMsg + '</li></ul>').removeClass('hidden').addClass('block');
                }
            });
        }, 300);
    });
    
    // Handle product selection
    $(document).on('click', '.product-item', function() {
        try {
            const productJson = $(this).attr('data-product').replace(/&#39;/g, "'");
            const product = JSON.parse(productJson);
            addProductToOrder(product);
            $('#search-product').val('');
            $('#product-autocomplete').addClass('hidden').removeClass('block').empty();
        } catch(e) {
            console.error('Error parsing product data:', e);
            showToast('Error selecting product. Please try again.', 'error');
        }
    });
    
    // Hide autocomplete when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#search-product, #product-autocomplete').length) {
            $('#product-autocomplete').addClass('hidden').removeClass('block');
        }
        if (!$(e.target).closest('#customer-search, #customer-autocomplete').length) {
            $('#customer-autocomplete').addClass('hidden').removeClass('block');
        }
        if (!$(e.target).closest('#invoice-input, #invoice-autocomplete').length) {
            $('#invoice-autocomplete').addClass('hidden').removeClass('block');
        }
    });
    
    // Search Invoice functionality
    let invoiceSearchTimeout;
    
    $('#invoice-input').on('keyup', function() {
        const query = $(this).val().trim();
        const $autocomplete = $('#invoice-autocomplete');
        
        // Clear previous timeout
        clearTimeout(invoiceSearchTimeout);
        
        if (query.length > 4) {
            // Debounce search
            invoiceSearchTimeout = setTimeout(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                $.ajax({
                    url: '/autocomplete_invoice',
                    method: 'POST',
                    data: {
                        query: query
                    },
                    success: function(data) {
                        if (data && data.trim() !== '') {
                            // Parse the HTML response and convert to modern Tailwind format
                            const $temp = $('<div>').html(data);
                            const invoices = [];
                            
                            $temp.find('li a').each(function() {
                                const $link = $(this);
                                const href = $link.attr('href');
                                const invoiceText = $link.find('span').text().trim();
                                if (invoiceText && href) {
                                    invoices.push({
                                        invoice: invoiceText,
                                        url: href
                                    });
                                }
                            });
                            
                            if (invoices.length > 0) {
                                let html = '<ul class="divide-y divide-gray-200">';
                                invoices.forEach(function(invoice) {
                                    html += `
                                        <li class="invoice-item p-3 hover:bg-gray-50 cursor-pointer transition-colors">
                                            <a href="${invoice.url}" target="_blank" class="flex items-center justify-between text-sm">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                    ${invoice.invoice}
                                                </span>
                                                    <i class="cft-standard-stroke cft-external-link text-gray-400 text-sm"></i>
                                            </a>
                                        </li>
                                    `;
                                });
                                html += '</ul>';
                                $autocomplete.html(html).removeClass('hidden').addClass('block');
                            } else {
                                $autocomplete.html('<ul class="divide-y divide-gray-200"><li class="p-3 text-gray-500 text-sm text-center">Tidak ditemukan</li></ul>').removeClass('hidden').addClass('block');
                            }
                        } else {
                            $autocomplete.html('<ul class="divide-y divide-gray-200"><li class="p-3 text-gray-500 text-sm text-center">Tidak ditemukan</li></ul>').removeClass('hidden').addClass('block');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error searching invoice:', error);
                        $autocomplete.html('<ul class="divide-y divide-gray-200"><li class="p-3 text-red-500 text-sm text-center">Error: ' + error + '</li></ul>').removeClass('hidden').addClass('block');
                    }
                });
            }, 300); // 300ms debounce
        } else {
            $autocomplete.addClass('hidden').removeClass('block');
        }
    });
    
    // Close autocomplete when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#invoice-input, #invoice-autocomplete').length) {
            $('#invoice-autocomplete').addClass('hidden').removeClass('block');
        }
    });
    
    // Add product to order
    function addProductToOrder(product) {
        const brandName = product.brand || 'BRAND';
        const productImage = product.image || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(brandName) + '&background=F74040&color=fff&size=60';
        const fullProductName = product.brand ? '[' + product.brand + '] ' + product.name : product.name;
        
        const item = {
            id: product.id,
            name: fullProductName,
            brand: brandName,
            image: productImage,
            price: parseFloat(product.price) || 0,
            quantity: 1,
            bin: product.bin || 'STORE [1]',
            pl_id: product.pl_id || null,
            discPercent: parseFloat(product.disc_percent) || 0,
            discRp: parseFloat(product.disc_rp) || 0,
            nameset: 0,
            marketplace: 0
        };
        
        // Check if product already exists
        const existingIndex = orderItems.findIndex(i => i.id === item.id && i.pl_id === item.pl_id);
        if (existingIndex >= 0) {
            orderItems[existingIndex].quantity += 1;
        } else {
            orderItems.push(item);
        }
        
        updateOrderDisplay();
        updateSummary();
        updateProductTable();
    }
    
    // Table quantity controls
    window.increaseTableQuantity = function(index) {
        if (orderItems[index]) {
            orderItems[index].quantity += 1;
            updateOrderDisplay();
            updateSummary();
            updateProductTable();
        }
    };
    
    window.decreaseTableQuantity = function(index) {
        if (orderItems[index] && orderItems[index].quantity > 1) {
            orderItems[index].quantity -= 1;
            updateOrderDisplay();
            updateSummary();
            updateProductTable();
        }
    };
    
    window.updateTableQuantity = function(index, value) {
        if (!orderItems[index]) return;
        
        const quantity = parseInt(value) || 0;
        
        // Get selected BIN and its stock quantity
        const $select = $(`select.bin-select[data-item-index="${index}"]`);
        const selectedOption = $select.find('option:selected');
        
        // Check if locations are still loading
        if ($select.find('option').length === 1 && $select.find('option').text() === 'Loading...') {
            showToast('Tunggu sebentar, lokasi sedang dimuat...', 'info');
            const previousQty = orderItems[index].quantity || 1;
            $(`#item_qty${index}`).val(previousQty);
            return false;
        }
        
        const plsQty = parseInt(selectedOption.data('pls-qty')) || 0;
        
        // Validate quantity against stock
        if (quantity > plsQty || quantity < 0) {
            showToast('Melebihi Stok<br>Jumlah item tidak boleh melebihi atau kurang jumlah pada BIN', 'warning');
            // Reset to previous quantity or 1 if invalid
            const previousQty = orderItems[index].quantity || 1;
            $(`#item_qty${index}`).val(previousQty);
            return false;
        }
        
        orderItems[index].quantity = quantity;
        
        // Update subtotal item di table
        updateItemSubtotal(index);
        
        updateOrderDisplay();
        updateSummary();
        updateProductTable();
    };
    
    window.updateNameset = function(index, value) {
        if (!orderItems[index]) return;
        
        const namesetValue = parseFloat(value) || 0;
        
        // Validasi tidak boleh minus (sesuai POS lama)
        if (namesetValue < 0) {
            showToast('Nameset tidak boleh minus', 'warning');
            $(`#nameset_price${index}`).val(orderItems[index].nameset || 0);
            return false;
        }
        
        orderItems[index].nameset = namesetValue;
        
        // Update subtotal item di table (include nameset)
        updateItemSubtotal(index);
        
        // Update summary
        updateSummary();
        updateProductTable();
    };
    
    window.updateMarketplace = function(index, value) {
        if (!orderItems[index]) return;
        
        const marketplaceValue = parseFloat(value) || 0;
        
        // Validasi tidak boleh minus
        if (marketplaceValue < 0) {
            showToast('Marketplace tidak boleh minus', 'warning');
            $(`#marketplace_price${index}`).val(orderItems[index].marketplace || 0);
            return false;
        }
        
        orderItems[index].marketplace = marketplaceValue;
        
        // Update summary
        updateSummary();
    };
    
    // Helper function to update item subtotal in table
    function updateItemSubtotal(index) {
        if (!orderItems[index]) return;
        
        const item = orderItems[index];
        const subtotal = (item.price * item.quantity) - (item.discRp || 0) + (parseFloat(item.nameset) || 0);
        $(`#subtotal_item${index}`).text('Rp. ' + formatNumber(subtotal));
    }
    
    // Update reseller discount percentage
    window.updateResellerDisc = function(index) {
        if (!orderItems[index]) return;
        
        const discountPercent = parseFloat($('#reseller_disc' + index).val()) || 0;
        
        if (discountPercent < 0) {
            showToast('Diskon tidak boleh minus', 'warning');
            $('#reseller_disc' + index).val('');
            return;
        }
        
        orderItems[index].discPercent = discountPercent;
        
        // Calculate discount in rupiah
        const itemPrice = orderItems[index].price;
        const quantity = orderItems[index].quantity;
        const subtotal = itemPrice * quantity;
        const discountRp = (subtotal * discountPercent) / 100;
        
        orderItems[index].discRp = discountRp;
        $('#reseller_disc_number' + index).val(discountRp > 0 ? discountRp.toFixed(0) : '');
        
        // Update subtotal item di table
        updateItemSubtotal(index);
        
        updateSummary();
        updateProductTable();
    };
    
    // Update reseller discount rupiah
    window.updateResellerDiscNumber = function(index) {
        if (!orderItems[index]) return;
        
        const discountRp = parseFloat($('#reseller_disc_number' + index).val()) || 0;
        
        if (discountRp < 0) {
            showToast('Diskon tidak boleh minus', 'warning');
            $('#reseller_disc_number' + index).val('');
            return;
        }
        
        orderItems[index].discRp = discountRp;
        
        // Calculate discount percentage
        const itemPrice = orderItems[index].price;
        const quantity = orderItems[index].quantity;
        const subtotal = itemPrice * quantity;
        
        if (subtotal > 0) {
            const discountPercent = (discountRp / subtotal) * 100;
            orderItems[index].discPercent = discountPercent;
            $('#reseller_disc' + index).val(discountPercent > 0 ? discountPercent.toFixed(2) : '');
        } else {
            orderItems[index].discPercent = 0;
            $('#reseller_disc' + index).val('');
        }
        
        // Update subtotal item di table
        updateItemSubtotal(index);
        
        updateSummary();
        updateProductTable();
    };
    
    // Delete item from table
    window.deleteTableItem = function(index) {
        if (!orderItems[index]) return;
        
        showConfirmToast('Yakin ingin menghapus item ini?', 'Ya, Hapus', 'Batal').then((confirmed) => {
            if (confirmed) {
            orderItems.splice(index, 1);
            updateProductTable();
            updateOrderDisplay();
            updateSummary();
                showToast('Item berhasil dihapus', 'success');
        }
        });
    };
    
    function updateProductTable() {
        $('#product-tbody').empty();
        orderItems.forEach((item, index) => {
            // Check if this is a retur item
            const isRetur = item.isRetur === true;
            const qty = item.qty || item.quantity || 1;
            const actualQty = Math.abs(qty);
            
            // Extract brand from name if format is [BRAND] product name
            let displayName = item.name;
            let brandBadge = '';
            if (item.brand) {
                const brandClass = isRetur ? 'bg-red-700' : 'bg-orange-500';
                brandBadge = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold ${brandClass} text-white mr-2">${item.brand}</span>`;
                displayName = displayName.replace(/^\[.*?\]\s*/, '');
            }
            
            // Styling for retur items
            const rowClass = isRetur ? 'bg-red-50 border-l-4 border-red-500 hover:bg-red-100' : 'bg-white hover:bg-gray-50';
            const textClass = isRetur ? 'text-red-900' : 'text-gray-900';
            const inputBgClass = isRetur ? 'bg-red-100' : 'bg-white';
            const disabledAttr = isRetur ? 'disabled' : '';
            
            // Create BIN select dropdown (disabled for retur)
            let binSelect = '';
            if (isRetur) {
                binSelect = `<span class="text-xs font-semibold text-red-700 px-3 py-2 bg-red-100 rounded-lg border border-red-300">RETUR ITEM</span>`;
            } else {
                binSelect = `<select class="bin-select w-full text-sm bg-green-100 text-green-800 font-semibold border border-green-300 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-green-500 focus:border-green-500" data-pst-id="${item.id}" data-item-index="${index}">`;
            binSelect += `<option value="">Loading...</option>`;
            binSelect += `</select>`;
            }
            
            // Calculate subtotal
            const subtotal = (item.price * actualQty) - (item.disc_number || item.discRp || 0) + (parseFloat(item.nameset) || 0);
            const displaySubtotal = isRetur ? `-Rp. ${formatNumber(subtotal)}` : `Rp. ${formatNumber(subtotal)}`;
            const displayQty = isRetur ? `-${actualQty}` : actualQty;
            
            // Image or initial
            let imageHtml = '';
            if (item.image) {
                imageHtml = `<img src="${item.image}" alt="${item.name}" class="w-10 h-10 object-cover rounded mr-3">`;
            } else {
                const initial = item.name ? item.name.charAt(0).toUpperCase() : 'P';
                const bgColor = isRetur ? 'bg-red-100' : 'bg-gray-200';
                const txtColor = isRetur ? 'text-red-600' : 'text-gray-600';
                imageHtml = `<div class="w-10 h-10 rounded ${bgColor} flex items-center justify-center ${txtColor} font-bold mr-3">${initial}</div>`;
            }
            
            const row = `
                <tr data-product-id="${item.id}" data-pl-id="${item.pl_id || ''}" class="${rowClass} transition-colors">
                    <td class="px-4 py-3 w-50">
                        <div class="flex items-center">
                            ${imageHtml}
                            <div class="flex items-center gap-2 flex-wrap">
                                ${brandBadge}
                                <span class="text-sm ${textClass} font-semibold">${displayName}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 w-180px">
                        ${binSelect}
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" id="reseller_disc${index}" class="w-16 px-2 py-1.5 ${inputBgClass} border border-gray-300 ${textClass} text-sm rounded-lg focus:ring-red-400 focus:border-red-400 text-center" value="${item.disc_percent || item.discPercent || ''}" placeholder="%" min="0" step="0.01" onchange="updateResellerDisc(${index})" ${disabledAttr}>
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" id="reseller_disc_number${index}" class="w-full px-2 py-1.5 ${inputBgClass} border border-gray-300 ${textClass} text-sm rounded-lg focus:ring-red-400 focus:border-red-400" value="${item.disc_number || item.discRp || ''}" placeholder="Rp" min="0" step="0.01" onchange="updateResellerDiscNumber(${index})" ${disabledAttr}>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <input type="text" id="item_qty${index}" class="w-16 text-center text-sm border border-gray-300 rounded-lg px-2 py-1.5 ${inputBgClass} ${textClass} font-semibold focus:ring-red-400 focus:border-red-400" value="${displayQty}" readonly>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <input type="number" id="nameset_price${index}" class="w-24 text-center text-sm border border-gray-300 rounded-lg px-2 py-1.5 ${inputBgClass} ${textClass} focus:ring-red-400 focus:border-red-400" value="${item.nameset || 0}" min="0" onchange="updateNameset(${index}, this.value)" ${disabledAttr}>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <input type="number" id="marketplace_price${index}" class="w-16 text-center text-sm border border-gray-300 rounded-lg px-2 py-1.5 ${inputBgClass} ${textClass} focus:ring-red-400 focus:border-red-400 marketplace-price-input" value="${item.marketplace || 0}" min="0" onchange="updateMarketplace(${index}, this.value)" disabled>
                    </td>
                    <td class="px-4 py-3 text-right text-sm w-28"><strong class="${textClass}">Rp. ${formatNumber(item.price)}</strong></td>
                    <td class="px-4 py-3 text-right text-sm w-28"><strong class="text-red-600 font-bold" id="subtotal_item${index}">${displaySubtotal}</strong></td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" onclick="deleteTableItem(${index})" class="w-8 h-8 flex items-center bg-red-50 justify-center text-red-500 hover:text-red-800 hover:bg-red-100 rounded transition-colors" title="Hapus">
                            <i class="cft-standard-stroke cft-trash text-base"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#product-tbody').append(row);
            
            // Load locations for this product (skip for retur items)
            if (!isRetur) {
            loadLocationsForProduct(item.id, index, item.pl_id);
            }
        });
        
        // Update marketplace field status after table is rendered
        updateMarketplaceFieldStatus();
    }
    
    // Load locations for a product and populate BIN select
    function loadLocationsForProduct(pstId, itemIndex, currentPlId) {
        const stId = $('#st_id').val() || '';
        
        $.ajax({
            url: '/reload_location_by_pst_id',
            method: 'POST',
            data: {
                pst_id: pstId,
                st_id: stId
            },
            success: function(locations) {
                const $select = $(`select.bin-select[data-pst-id="${pstId}"][data-item-index="${itemIndex}"]`);
                $select.empty();
                
                if (locations && locations.length > 0) {
                    locations.forEach(function(loc) {
                        const selected = loc.pl_id == currentPlId ? 'selected' : '';
                        const optionText = `[${loc.pl_code.toUpperCase()}] [${formatNumber(loc.pls_qty)}]`;
                        $select.append(`<option value="${loc.pl_id}" ${selected} data-pl-code="${loc.pl_code}" data-pls-qty="${loc.pls_qty}">${optionText}</option>`);
                    });
                    
                    // Store pls_qty in orderItems after loading locations
                    if (orderItems[itemIndex]) {
                        const selectedOption = $select.find('option:selected');
                        const plsQty = parseInt(selectedOption.data('pls-qty')) || 0;
                        orderItems[itemIndex].pls_qty = plsQty;
                    }
                } else {
                    $select.append('<option value="">No locations available</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading locations:', error);
                const $select = $(`select.bin-select[data-pst-id="${pstId}"][data-item-index="${itemIndex}"]`);
                $select.html('<option value="">Error loading locations</option>');
            }
        });
    }
    
    // Handle BIN select change
    $(document).on('change', '.bin-select', function() {
        const $select = $(this);
        const newPlId = $select.val();
        const itemIndex = $select.data('item-index');
        const pstId = $select.data('pst-id');
        
        if (!orderItems[itemIndex]) return;
        
        // Get selected BIN stock quantity
        const selectedOption = $select.find('option:selected');
        const plCode = selectedOption.data('pl-code');
        const plsQty = parseInt(selectedOption.data('pls-qty')) || 0;
        const currentQuantity = orderItems[itemIndex].quantity || 1;
        
        // Validate quantity against new BIN stock
        if (currentQuantity > plsQty || currentQuantity < 0) {
            showToast('Melebihi Stok<br>Jumlah item tidak boleh melebihi atau kurang jumlah pada BIN', 'warning');
            // Revert to previous selection
            const previousPlId = orderItems[itemIndex].pl_id || '';
            $select.val(previousPlId);
            return false;
        }
        
        // Show warning if TOKO is selected
        if (plCode && plCode.toUpperCase() === 'TOKO') {
                const previousPlId = orderItems[itemIndex].pl_id || '';
            // Revert first
                $select.val(previousPlId);
            
            showConfirmToast('Yakin pilih dari display? Apakah sudah cek barang?', 'Ya, Lanjutkan', 'Batal').then((confirmed) => {
                if (confirmed) {
                    // Update BIN selection
                    $select.val(newPlId);
                    orderItems[itemIndex].pl_id = newPlId;
                    orderItems[itemIndex].pls_qty = plsQty; // Store stock quantity for validation
                    
                    // Update the row's data attribute
                    const $row = $select.closest('tr');
                    $row.attr('data-pl-id', newPlId);
                    
                    // Update summary after BIN change
                    updateSummary();
                }
            });
                return false;
        }
        
        // Update BIN selection
        orderItems[itemIndex].pl_id = newPlId;
        orderItems[itemIndex].pls_qty = plsQty; // Store stock quantity for validation
        
        // Update the row's data attribute
        const $row = $select.closest('tr');
        $row.attr('data-pl-id', newPlId);
        
        // Update summary after BIN change
        updateSummary();
    });
    
    // Update order display
    function updateOrderDisplay() {
        const $orderList = $('#order-items-list');
        $orderList.empty();
        
        if (orderItems.length === 0) {
            $orderList.html('<p class="text-gray-500 text-center text-sm py-3">No items in order</p>');
            return;
        }
        
        orderItems.forEach((item, index) => {
            const isRetur = item.isRetur === true;
            const qty = Math.abs(item.quantity || item.qty || 1);
            const displayQty = isRetur ? `-${qty}` : qty;
            
            // Extract brand from name if format is [BRAND] product name
            let displayName = item.name;
            if (item.brand) {
                displayName = displayName.replace(/^\[.*?\]\s*/, '');
            }
            
            // Use image or initial
            let imageHtml = '';
            if (item.image) {
                imageHtml = `<img src="${item.image}" alt="${item.name}" class="w-9 h-9 object-cover rounded">`;
            } else {
                const initial = item.name ? item.name.charAt(0).toUpperCase() : 'P';
                const bgColor = isRetur ? 'bg-red-100' : 'bg-gray-200';
                const textColor = isRetur ? 'text-red-600' : 'text-gray-600';
                imageHtml = `<div class="w-9 h-9 rounded ${bgColor} flex items-center justify-center ${textColor} font-bold text-sm">${initial}</div>`;
            }
            
            const subtotal = (item.price * qty) - (item.discRp || item.disc_number || 0) + (parseFloat(item.nameset) || 0);
            const displaySubtotal = isRetur ? `-Rp. ${formatNumber(subtotal)}` : `Rp. ${formatNumber(subtotal)}`;
            const bgClass = isRetur ? 'bg-red-50 border-l-4 border-red-500' : 'bg-gray-100';
            const textClass = isRetur ? 'text-red-900' : 'text-gray-900';
            
            const quantityControls = isRetur ? `
                <span class="text-sm font-semibold ${textClass}">${displayQty}</span>
            ` : `
                    <div class="relative flex items-center max-w-[6rem] shadow-xs rounded-base">
                    <button type="button" onclick="decreaseQuantity(${index})" class="text-body bg-white box-border border rounded-r-none border-gray-300 hover:bg-gray-500 hover:text-heading focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-lg text-xs px-2 focus:outline-none h-8">
                            <svg class="w-3 h-3 text-heading" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>
                        </button>
                        <input type="text" data-input-counter class="border-gray-300 h-8 placeholder:text-heading text-center w-full bg-white border-gray-300 py-1.5 placeholder:text-body text-sm" placeholder="999" value="${item.quantity}" />
                        <button type="button" onclick="increaseQuantity(${index})" class="text-body bg-white box-border border rounded-l-none border-gray-300 hover:bg-gray-500 hover:text-heading focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-lg text-xs px-2 focus:outline-none h-8">
                            <svg class="w-3 h-3 text-heading" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/></svg>
                        </button>
                    </div>
            `;
            
            const itemHtml = `
                <div class="flex items-center gap-3 p-4 ${bgClass} rounded-lg">
                    ${imageHtml}
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            ${item.brand ? `<span class="inline-flex items-center px-2 py-0.5 rounded-lg text-2xs font-semibold bg-orange-500 text-white">${item.brand}</span>` : ''}
                            <div class="font-semibold text-sm ${textClass}">${displayName}</div>
                        </div>
                        <div class="text-xs text-gray-600">Qty: ${displayQty}</div>
                    </div>
                    <div class="font-semibold text-red-600 text-sm">${displaySubtotal}</div>
                    ${quantityControls}
                </div>
            `;
            $orderList.append(itemHtml);
        });
        
        $('#item-count').text(`${orderItems.length} Items`);
    }
    
    // Update summary
    function updateSummary() {
        // Hitung subtotal (harga × qty - discount) + nameset
        // Support untuk qty negatif (item retur)
        subtotal = orderItems.reduce((sum, item) => {
            const qty = item.qty || item.quantity || 0; // Bisa negatif untuk retur!
            const itemSubtotal = item.price * qty; // Tetap dikalikan dengan qty (bisa negatif)
            const itemDiscount = item.disc_number || item.discRp || 0;
            const itemNameset = parseFloat(item.nameset) || 0;
            return sum + (itemSubtotal - itemDiscount) + itemNameset;
        }, 0);
        
        // Hitung total nameset (sesuai POS lama)
        const totalNameset = orderItems.reduce((sum, item) => {
            return sum + (parseFloat(item.nameset) || 0);
        }, 0);
        
        // Hitung total marketplace (sesuai POS lama)
        const totalMarketplace = orderItems.reduce((sum, item) => {
            return sum + (parseFloat(item.marketplace) || 0);
        }, 0);
        
        const grandTotal = subtotal + shippingCost - voucherDiscount - totalDiscount;
        
        $('#subtotal').text('Rp. ' + formatNumber(subtotal));
        if (selectedCourierName) {
            $('#shipping-cost').html(' <span class="px-1 py-0.5 text-2xs text-red-500 bg-red-100 rounded-lg mr-2">' + selectedCourierName + '</span>' + 'Rp. ' + formatNumber(shippingCost));
        } else {
            $('#shipping-cost').text('Rp. ' + formatNumber(shippingCost));
        }
        $('#voucher-discount').text('Rp. ' + formatNumber(voucherDiscount));
        $('#total-discount').text('Rp. ' + formatNumber(totalDiscount));
        $('#grand-total').text('Rp. ' + formatNumber(grandTotal));
        
        // Store total nameset and marketplace for later use (if needed in payment modal)
        window.totalNameset = totalNameset;
        window.totalMarketplace = totalMarketplace;
    }
    
    // Format number
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    
    // Quantity controls
    window.increaseQuantity = function(index) {
        orderItems[index].quantity += 1;
        updateOrderDisplay();
        updateSummary();
    };
    
    window.decreaseQuantity = function(index) {
        if (orderItems[index].quantity > 1) {
            orderItems[index].quantity -= 1;
        } else {
            orderItems.splice(index, 1);
        }
        updateOrderDisplay();
        updateSummary();
    };
    
    // Clear all
    $('#clear-all').on('click', function(e) {
        e.preventDefault();
        showConfirmToast('Yakin ingin menghapus semua item?', 'Ya, Hapus Semua', 'Batal').then((confirmed) => {
            if (confirmed) {
            orderItems = [];
            updateOrderDisplay();
            updateSummary();
            updateProductTable();
                showToast('Semua item berhasil dihapus', 'success');
        }
        });
    });
    
    // Variables for shipping and discount
    let selectedCourier = '';
    let selectedCourierName = '';
    
    // Edit summary items - Open Flowbite modals
    $(document).on('click', '.edit-icon-btn', function(e) {
        const $icon = $(this).find('.edit-icon');
        const type = $icon.data('edit');
        
        // Set current values when modal opens
        setTimeout(function() {
            if (type === 'shipping') {
                $('#shipping-amount').val(shippingCost || '');
                if (selectedCourier) {
                    $('#courier').val(selectedCourier);
                }
            }
        }, 100);
    });
    
    // Add/Remove voucher fields
    $(document).on('click', '.add-voucher', function() {
        const newField = `
            <div class="flex gap-2 mb-3">
                <input type="text" name="voucher-list[]" class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block p-2.5" placeholder="Kode Voucher" value="">
                <button type="button" class="remove-voucher px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200">-</button>
            </div>
        `;
        $('#voucher-container').append(newField);
    });
    
    $(document).on('click', '.remove-voucher', function() {
        $(this).closest('.flex').remove();
    });
    
    // Add/Remove discount fields
    $(document).on('click', '.add-total-discount', function() {
        const newField = `
            <div class="flex gap-2 mb-3">
                <input type="text" name="total-discount-list[]" class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block p-2.5" placeholder="Diskon" value="">
                <button type="button" class="remove-total-discount px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200">-</button>
            </div>
        `;
        $('#total-discount-container').append(newField);
    });
    
    $(document).on('click', '.remove-total-discount', function() {
        const discountValue = parseFloat($(this).closest('.flex').find('input').val()) || 0;
        totalDiscount = Math.max(0, totalDiscount - discountValue);
        $(this).closest('.flex').remove();
        updateSummary();
    });
    
    // Reset discount
    $('#total_discount_reset').on('click', function() {
        totalDiscount = 0;
        $('input[name="total-discount-list[]"]').val('');
        updateSummary();
    });
    
    // Handle form submissions
    $('#form-shipping').on('submit', function(e) {
        e.preventDefault();
        const shippingCostValue = parseFloat($('#shipping-amount').val()) || 0;
        const courierValue = $('#courier').val();
        const courierText = $('#courier option:selected').text();
        
        if (courierText === '- Pilih -' || !courierValue) {
            showToast('Silahkan pilih kurir', 'warning');
            return;
        }
        
        shippingCost = shippingCostValue;
        selectedCourier = courierValue;
        selectedCourierName = courierText;
        
        updateSummary();
        
        console.log('=== SHIPPING FORM SUBMITTED - CLOSING MODAL ===');
        
        // Close modal and remove ALL backdrops
        const modalEl = document.getElementById('modal-shipping');
        if (modalEl) {
            // Hide modal first
            if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                const modal = Flowbite.Modal.getInstance(modalEl) || shippingModal;
                if (modal) {
                    console.log('Hiding shipping modal via Flowbite');
                modal.hide();
                }
            }
                modalEl.classList.add('hidden');
                modalEl.setAttribute('aria-hidden', 'true');
            
            // FORCE REMOVE ALL BACKDROPS - with multiple attempts and delays
            console.log('Starting backdrop removal process...');
            window.forceRemoveAllBackdrops();
            setTimeout(function() {
                console.log('Backdrop removal attempt 2');
                window.forceRemoveAllBackdrops();
            }, 100);
            setTimeout(function() {
                console.log('Backdrop removal attempt 3');
                window.forceRemoveAllBackdrops();
            }, 300);
            setTimeout(function() {
                console.log('Backdrop removal attempt 4 (final)');
                window.forceRemoveAllBackdrops();
            }, 600);
        }
    });
    
    $('#form-voucher').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serializeArray();
        const voucherCodes = formData.filter(item => item.name === 'voucher-list[]').map(item => item.value).filter(val => val.trim() !== '');
        
        if (voucherCodes.length === 0) {
            showToast('Silahkan masukkan kode voucher', 'warning');
            return;
        }
        
        // TODO: Implement voucher verification via AJAX
        // For now, just close modal
        console.log('Voucher codes:', voucherCodes);
        console.log('=== VOUCHER FORM SUBMITTED - CLOSING MODAL ===');
        
        // Close modal and remove ALL backdrops
        const modalEl = document.getElementById('modal-voucher');
        if (modalEl) {
            // Hide modal first
            if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                const modal = Flowbite.Modal.getInstance(modalEl) || voucherModal;
                if (modal) {
                    console.log('Hiding voucher modal via Flowbite');
                modal.hide();
                }
            }
                modalEl.classList.add('hidden');
                modalEl.setAttribute('aria-hidden', 'true');
            
            // FORCE REMOVE ALL BACKDROPS - with multiple attempts and delays
            console.log('Starting backdrop removal process...');
            window.forceRemoveAllBackdrops();
            setTimeout(function() {
                window.forceRemoveAllBackdrops();
            }, 100);
            setTimeout(function() {
                window.forceRemoveAllBackdrops();
            }, 300);
            setTimeout(function() {
                window.forceRemoveAllBackdrops();
            }, 600);
        }
    });
    
    $('#form-discount').on('submit', function(e) {
        e.preventDefault();
        
        const discountType = $('#discount-type').val();
        const formData = $(this).serializeArray();
        const discountValues = formData.filter(item => item.name === 'total-discount-list[]').map(item => parseFloat(item.value) || 0);
        
        if (discountValues.length === 0) {
            showToast('Silahkan masukkan nilai diskon', 'warning');
            return;
        }
        
        let totalDiscountValue = 0;
        if (discountType === 'percentage') {
            // Calculate percentage from subtotal
            const percentage = discountValues.reduce((sum, val) => sum + val, 0);
            totalDiscountValue = (subtotal * percentage) / 100;
        } else {
            // Nominal - sum all values
            totalDiscountValue = discountValues.reduce((sum, val) => sum + val, 0);
        }
        
        totalDiscount = totalDiscountValue;
        updateSummary();
        
        console.log('=== DISCOUNT FORM SUBMITTED - CLOSING MODAL ===');
        
        // Close modal and remove ALL backdrops
        const modalEl = document.getElementById('modal-discount');
        if (modalEl) {
            // Hide modal first
            if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                const modal = Flowbite.Modal.getInstance(modalEl) || discountModal;
                if (modal) {
                    console.log('Hiding discount modal via Flowbite');
                modal.hide();
                }
            }
                modalEl.classList.add('hidden');
                modalEl.setAttribute('aria-hidden', 'true');
            
            // FORCE REMOVE ALL BACKDROPS - with multiple attempts and delays
            console.log('Starting backdrop removal process...');
            window.forceRemoveAllBackdrops();
            setTimeout(function() {
                window.forceRemoveAllBackdrops();
            }, 100);
            setTimeout(function() {
                window.forceRemoveAllBackdrops();
            }, 300);
            setTimeout(function() {
                window.forceRemoveAllBackdrops();
            }, 600);
        }
    });
    
    // Pay button - Open Flowbite modal
    $('#pay-btn').on('click', function() {
        if (orderItems.length === 0) {
            showToast('Silakan tambahkan produk terlebih dahulu', 'warning');
            return;
        }
        
        if (!$('#cust_id').val() || $('#cust_id').val() === '') {
            showToast('Silakan pilih customer terlebih dahulu', 'warning');
            return;
        }
        
        // Calculate totals
        const currentSubtotal = subtotal;
        const currentShipping = shippingCost;
        const currentVoucher = voucherDiscount;
        const currentTotalDiscount = totalDiscount;
        const currentGrandTotal = currentSubtotal + currentShipping - currentVoucher - currentTotalDiscount;
        
        // Populate payment modal
        $('#payment_total').text('Rp. ' + formatNumber(currentGrandTotal));
        $('#real_price').val(formatNumber(currentGrandTotal));
        $('#final_total_unique_code').val(formatNumber(currentGrandTotal));
        
        // Reset payment modal fields
        $('#order_code').val('');
        $('#no_resi').val('');
        $('#courier_bayar').val('');
        $('#unique_code').val('0');
        $('#discount_seller').val('0');
        $('#admin_cost').val('0');
        $('#another_cost').val('0');
        $('#dp_checkbox').prop('checked', false).val('0');
        $('#dp_payment').val('0');
        $('#ref_number').val('');
        $('#note').val('');
        $('#no_resi_upload').val('');
        $('#upload-text').html('<span class="font-semibold">Click to upload</span> or drag and drop');
        
        // Recalculate to ensure initial values are correct
        calculateRealPrice();
        
        console.log('=== OPENING PAYMENT MODAL ===');
        
        // MANUAL APPROACH - No Flowbite for backdrop, just show/hide
            const modalEl = document.getElementById('modal-payment');
            if (modalEl) {
            // 1. Create backdrop FIRST
            let backdrop = document.getElementById('payment-modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            backdrop = document.createElement('div');
            backdrop.id = 'payment-modal-backdrop';
            backdrop.style.cssText = 'position: fixed; inset: 0; background: rgba(0, 0, 0, 0.5); z-index: 40; width: 100vw; height: 100vh;';
            backdrop.onclick = function(e) {
                e.stopPropagation();
                e.preventDefault();
                return false;
            };
            document.body.appendChild(backdrop);
            console.log('✅ Backdrop created first');
            
            // 2. Show modal manually
            modalEl.classList.remove('hidden');
            modalEl.removeAttribute('aria-hidden');
            modalEl.style.display = 'flex';
            console.log('✅ Modal shown manually');
            
            // 3. Create Flowbite instance ONLY for internal modal behavior (not backdrop)
                if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                try {
                    const existingModal = Flowbite.Modal.getInstance(modalEl);
                    if (existingModal) {
                        existingModal.destroy();
                    }
                    
                    // Create instance with backdrop disabled
                    paymentModal = new Flowbite.Modal(modalEl, {
                            placement: 'center',
                        backdrop: 'none' // No Flowbite backdrop!
                    });
                    console.log('✅ Flowbite modal instance created (no backdrop)');
                } catch(e) {
                    console.error('Error creating Flowbite modal:', e);
                }
            }
        }
    });
    
    // Payment modal - Close button handlers (custom, no data-modal-hide)
    function closePaymentModal() {
        console.log('=== CLOSING PAYMENT MODAL ===');
            const modalEl = document.getElementById('modal-payment');
            if (modalEl) {
            // Hide modal manually
                        modalEl.classList.add('hidden');
                        modalEl.setAttribute('aria-hidden', 'true');
            modalEl.style.display = 'none';
            console.log('Payment modal hidden manually');
            
            // FORCE REMOVE ALL BACKDROPS
            console.log('Starting backdrop removal process...');
            window.forceRemoveAllBackdrops();
            setTimeout(function() {
                window.forceRemoveAllBackdrops();
            }, 100);
            setTimeout(function() {
                window.forceRemoveAllBackdrops();
            }, 300);
        }
    }
    
    // Close button handlers
    $(document).on('click', '#close-payment-modal, #cancel-payment-modal', function(e) {
        e.preventDefault();
        e.stopPropagation();
        closePaymentModal();
    });
    
    // Payment modal - Unique code handler
    $('#unique_code').on('input', function() {
        calculateRealPrice();
    });
    
    // Payment modal - Calculate real price on input change
    function calculateRealPrice() {
        const grandTotalText = $('#payment_total').text();
        const grandTotal = parseFloat(grandTotalText.replace(/[^\d]/g, '')) || 0;
        const uniqueCode = parseFloat($('#unique_code').val()) || 0;
        const discountSeller = parseFloat($('#discount_seller').val()) || 0;
        const adminCost = parseFloat($('#admin_cost').val()) || 0;
        const anotherCost = parseFloat($('#another_cost').val()) || 0;
        const dpPayment = parseFloat($('#dp_payment').val()) || 0;
        const isDownpayment = $('#dp_checkbox').is(':checked');
        
        let realPrice = grandTotal + uniqueCode - discountSeller - adminCost + anotherCost;
        
        if (isDownpayment && dpPayment > 0) {
            realPrice = dpPayment;
        }
        
        // Ensure real price is not negative
        if (realPrice < 0) {
            realPrice = 0;
        }
        
        $('#real_price').val(formatNumber(realPrice));
        $('#final_total_unique_code').val(formatNumber(grandTotal + uniqueCode));
    }
    
    // Payment modal - Input change handlers
    $('#discount_seller, #admin_cost, #another_cost, #dp_payment').on('input', function() {
        calculateRealPrice();
    });
    
    $('#dp_checkbox').on('change', function() {
        calculateRealPrice();
    });
    
    // Payment modal - File upload handler
    $('#no_resi_upload').on('change', function() {
        const file = this.files[0];
        if (file) {
            if (file.type !== 'application/pdf') {
                showToast('File harus berupa PDF', 'error');
                $(this).val('');
                return;
            }
            $('#upload-text').html(`<span class="font-semibold text-green-600">${file.name}</span>`);
        } else {
            $('#upload-text').html('<span class="font-semibold">Click to upload</span> or drag and drop');
        }
    });
    
    // Payment modal - Save transaction
    $('#save_transaction').on('click', function() {
        if (orderItems.length === 0) {
            showToast('Tidak ada item dalam keranjang', 'error');
            return;
        }
        
        if (!$('#cust_id').val() || $('#cust_id').val() === '') {
            showToast('Silakan pilih customer terlebih dahulu', 'error');
            return;
        }
        
        // Collect all form data
        const formData = new FormData();
        
        // Transaction data
        formData.append('_pm_id', $('#pm_id').val() || '');
        formData.append('sub_payment', $('#sub_payment').val() || '');
        formData.append('_cp_id', $('#cp_id').val() || '');
        formData.append('_std_id', $('#std_id').val() || '');
        formData.append('_cust_id', $('#cust_id').val() || '1');
        formData.append('_sub_cust_id', $('#sub_cust_id').val() || '');
        formData.append('_st_id', $('#st_id').val() || '');
        formData.append('_pt_id_complaint', $('#pt_id_complaint').val() || '');
        formData.append('_exchange', $('#exchange_flag').val() || '');
        formData.append('cross_order', $('#cross_order').val() || '0');
        formData.append('_unique_code', $('#unique_code').val() || '0');
        formData.append('_real_price', $('#real_price').val().replace(/[^\d]/g, '') || '0');
        formData.append('_admin_cost', $('#admin_cost').val() || '0');
        formData.append('_another_cost', $('#another_cost').val() || '0');
        formData.append('_order_code', $('#order_code').val() || '');
        formData.append('_shipping_cost', shippingCost || '0');
        formData.append('_ref_number', $('#ref_number').val() || '');
        formData.append('_total_discount_side', totalDiscount || '0');
        formData.append('_no_resi', $('#no_resi').val() || '');
        formData.append('_cr_id', $('#courier_bayar').val() || '');
        formData.append('_note', $('#note').val() || '');
        formData.append('_discount_seller', $('#discount_seller').val() || '0');
        formData.append('_downpayment', $('#dp_checkbox').is(':checked') ? '1' : '0');
        formData.append('_dp_payment', $('#dp_payment').val() || '0');
        formData.append('voc_pst_id', $('#_voc_pst_id').val() || '');
        formData.append('voc_value', $('#_voc_value').val() || '');
        formData.append('voc_id', $('#_voc_id').val() || '');
        
        // File upload
        const resiFile = $('#no_resi_upload')[0].files[0];
        if (resiFile) {
            formData.append('no_resi_upload', resiFile);
        }
        
        // Order items data
        const orderItemsData = orderItems.map((item, index) => {
            return {
                pst_id: item.pstId,
                pl_id: item.plId,
                quantity: item.quantity,
                nameset: item.nameset || '',
                marketplace: item.marketplace || '',
                disc_percent: item.discPercent || 0,
                disc_rp: item.discRp || 0,
                price: item.price
            };
        });
        formData.append('order_items', JSON.stringify(orderItemsData));
        
        // Disable button during submission
        const $btn = $(this);
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<span class="inline-block animate-spin mr-2">⏳</span> Processing...');
        
        // Submit transaction
        $.ajax({
            url: '/save_transaction',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const result = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (result.status === '200' && result.pt_id) {
                        // Save transaction details for each item
                        let detailPromises = [];
                        let detailIndex = 0;
                        
                        orderItems.forEach((item) => {
                            const detailFormData = new FormData();
                            detailFormData.append('_pt_id', result.pt_id);
                            detailFormData.append('_pt_id_complaint', $('#_pt_id_complaint').val() || '');
                            detailFormData.append('_exchange', $('#_exchange').val() || '');
                            detailFormData.append('_pl_id', item.plId || '');
                            detailFormData.append('_pst_id', item.pstId);
                            detailFormData.append('_plst_id', '');
                            detailFormData.append('_price', item.price);
                            detailFormData.append('_item_qty', item.quantity);
                            detailFormData.append('cross_order', $('#cross_order').val() || '0');
                            detailFormData.append('_discount', item.discPercent || 0);
                            detailFormData.append('_discount_number', item.discRp || 0);
                            detailFormData.append('_sell_price_item', item.price);
                            detailFormData.append('_marketplace_price', item.marketplace || '0');
                            detailFormData.append('_subtotal_item', (item.price * item.quantity) - (item.discRp || 0));
                            detailFormData.append('_nameset_price', item.nameset || '0');
                            detailFormData.append('_final_price', (item.price * item.quantity) - (item.discRp || 0) + (parseFloat(item.nameset) || 0));
                            detailFormData.append('voc_pst_id', $('#_voc_pst_id').val() || '');
                            detailFormData.append('voc_value', $('#_voc_value').val() || '');
                            detailFormData.append('_price_item_discount', item.discRp || 0);
                            
                            const detailPromise = $.ajax({
                                url: '/save_transaction_detail',
                                type: 'POST',
                                data: detailFormData,
                                processData: false,
                                contentType: false
                            });
                            
                            detailPromises.push(detailPromise);
                        });
                        
                        // Wait for all detail saves to complete
                        $.when.apply($, detailPromises).done(function() {
                            showToast('Transaksi berhasil disimpan! Invoice: ' + result.invoice, 'success');
                            
                            // Close payment modal and remove backdrop
                            console.log('=== TRANSACTION SAVED - CLOSING PAYMENT MODAL ===');
                            closePaymentModal();
                            
                            // Reset POS state
                            orderItems = [];
                            shippingCost = 0;
                            voucherDiscount = 0;
                            totalDiscount = 0;
                            selectedCustomer = null;
                            $('#customer-search').val('');
                            $('#cust_id').val('');
                            $('#sub_cust_id').val('');
                            $('#sub-customer-search').val('');
                            updateProductTable();
                            updateSummary();
                            updateOrderDisplay();
                            
                            // Optionally redirect to invoice or print
                            if (result.pt_id) {
                                // You can add redirect to invoice page here
                                // window.location.href = '/invoice/' + result.pt_id;
                            }
                        }).fail(function() {
                            showToast('Transaksi disimpan, tetapi ada error saat menyimpan detail. Silakan cek kembali.', 'warning');
                        });
                    } else {
                        showToast('Gagal menyimpan transaksi: ' + (result.message || 'Unknown error'), 'error');
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    showToast('Error processing response', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr, status, error);
                let errorMsg = 'Gagal menyimpan transaksi';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        errorMsg = errorResponse.message || errorMsg;
                    } catch (e) {
                        errorMsg = xhr.responseText.substring(0, 100);
                    }
                }
                showToast(errorMsg, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Customer search with autocomplete
    let customerSearchTimeout;
    let selectedCustomer = null;
    
    $('#customer-search').on('keyup', function() {
        const query = $(this).val().trim();
        const $autocomplete = $('#customer-autocomplete');
        const divisionType = $('#std_id option:selected').text().toUpperCase();
        
        clearTimeout(customerSearchTimeout);
        
        if (query.length < 3) {
            $autocomplete.addClass('hidden').removeClass('block').empty();
            $('#customer-badge').addClass('hidden').removeClass('flex');
            $('#dropshipper-badge').addClass('hidden').removeClass('flex');
            selectedCustomer = null;
            return;
        }
        
        customerSearchTimeout = setTimeout(function() {
            // Include division type in search untuk filter customer
            $.ajax({
                url: '/autocomplete_customer',
                method: 'POST',
                data: {
                    query: query,
                    type: 'cust',
                    division_type: divisionType // Tambahkan division type untuk filter
                },
                success: function(data) {
                    // Parse and reformat customer autocomplete with badges and smaller text
                    if (data && data.trim() !== '') {
                        const $temp = $('<div>').html(data);
                        const customers = [];
                        
                        $temp.find('li a#add_to_item_list_cust').each(function() {
                            const $link = $(this);
                            const custId = $link.attr('data-id');
                            const custText = $link.text().trim();
                            // Parse: "NAME PHONE [GOLD-ACTIVE]" format
                            const match = custText.match(/^(.+?)\s+([0-9\s\-+]+)\s+\[(.+?)\-(.+?)\]$/);
                            if (match) {
                                customers.push({
                                    id: custId,
                                    name: match[1].trim(),
                                    phone: match[2].trim(),
                                    type: match[3].trim() + ' - ' + match[4].trim()
                                });
                            } else {
                                // Try alternative format: "NAME PHONE [TYPE]"
                                const match2 = custText.match(/^(.+?)\s+([0-9\s\-+]+)\s+\[(.+?)\]$/);
                                if (match2) {
                                    customers.push({
                                        id: custId,
                                        name: match2[1].trim(),
                                        phone: match2[2].trim(),
                                        type: match2[3].trim()
                                    });
                                } else {
                                    // Fallback: split by space
                                    const parts = custText.split(/\s+/);
                                    customers.push({
                                        id: custId,
                                        name: parts[0] || custText,
                                        phone: parts[1] || '',
                                        type: parts.slice(2).join(' ') || ''
                                    });
                                }
                            }
                        });
                        
                        if (customers.length > 0) {
                            let html = '<ul class="divide-y divide-gray-200">';
                            customers.forEach(function(customer) {
                                html += `
                                    <li class="customer-item p-2.5 hover:bg-gray-50 cursor-pointer transition-colors" id="add_to_item_list_cust" data-id="${customer.id}">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1 min-w-0">
                                                <div class="text-xs font-semibold text-gray-900 mb-0.5">${customer.name}</div>
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    ${customer.phone ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">${customer.phone}</span>` : ''}
                                                    ${customer.type ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">${customer.type}</span>` : ''}
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                `;
                            });
                            html += '</ul>';
                            $autocomplete.html(html).removeClass('hidden').addClass('block');
                        } else {
                            $autocomplete.html('<ul class="divide-y divide-gray-200"><li class="p-2.5 text-gray-500 text-xs text-center">Tidak ditemukan</li></ul>').removeClass('hidden').addClass('block');
                        }
                    } else {
                        $autocomplete.html('<ul class="divide-y divide-gray-200"><li class="p-2.5 text-gray-500 text-xs text-center">Tidak ditemukan</li></ul>').removeClass('hidden').addClass('block');
                    }
                },
                error: function() {
                    $autocomplete.html('<ul class="divide-y divide-gray-200"><li class="p-4 text-red-600">Error searching customers</li></ul>').removeClass('hidden').addClass('block');
                }
            });
        }, 300);
    });
    
    // Handle customer selection from autocomplete
    $(document).on('click', '.customer-item, #add_to_item_list_cust', function() {
        const custId = $(this).attr('data-id') || $(this).closest('[data-id]').attr('data-id');
        const $item = $(this);
        
        // Get customer name from the text-xs font-semibold element
        const custName = $item.find('.text-xs.font-semibold').first().text().trim() || $item.text().trim();
        const custPhone = $item.find('.bg-gray-100').first().text().trim() || '';
        const custType = $item.find('.bg-red-100').first().text().trim() || '';
        
        const divisionType = $('#std_id option:selected').text().toUpperCase();
        
        $('#cust_id').val(custId);
        $('#customer-search').val(custName);
        $('#customer-autocomplete').addClass('hidden').removeClass('block').empty();
        
        // Jika division = DROPSHIPPER, tampilkan sebagai dropshipper badge
        if (divisionType === 'DROPSHIPPER') {
            $('#dropshipper-badge .dropshipper-name').text(custName);
            $('#dropshipper-badge').removeClass('hidden').addClass('flex');
            $('#customer-badge').addClass('hidden').removeClass('flex');
        } else {
            // Show customer badge untuk division biasa
        $('#customer-badge .customer-name').text(custName);
        $('#customer-badge .customer-type').text(custType || 'CUSTOMER');
        $('#customer-badge').removeClass('hidden').addClass('flex');
            $('#dropshipper-badge').addClass('hidden').removeClass('flex');
        }
        
        selectedCustomer = {
            id: custId,
            name: custName,
            phone: custPhone,
            status: custType
        };
        
        // Update marketplace field status based on division type
        updateMarketplaceFieldStatus();
    });
    
    // Sub customer search (untuk dropshipper)
    let subCustomerSearchTimeout;
    $('#sub-customer-search').on('keyup', function() {
        const query = $(this).val().trim();
        const $autocomplete = $('#sub-customer-autocomplete');
        
        clearTimeout(subCustomerSearchTimeout);
        
        if (query.length < 3) {
            $autocomplete.addClass('hidden').removeClass('block').empty();
            $('#customer-badge').addClass('hidden').removeClass('flex');
            return;
        }
        
        subCustomerSearchTimeout = setTimeout(function() {
            $.ajax({
                url: '/autocomplete_customer',
                method: 'POST',
                data: {
                    query: query,
                    type: 'cust'
                },
                success: function(data) {
                    // Parse and reformat customer autocomplete
                    if (data && data.trim() !== '') {
                        const $temp = $('<div>').html(data);
                        const customers = [];
                        
                        $temp.find('li a#add_to_item_list_cust').each(function() {
                            const $link = $(this);
                            const custId = $link.attr('data-id');
                            const custText = $link.text().trim();
                            // Parse: "NAME PHONE [TYPE]" format
                            const match = custText.match(/^(.+?)\s+([0-9\s\-+]+)\s+\[(.+?)\]$/);
                            if (match) {
                                customers.push({
                                    id: custId,
                                    name: match[1].trim(),
                                    phone: match[2].trim(),
                                    type: match[3].trim()
                                });
                            } else {
                                const parts = custText.split(/\s+/);
                                customers.push({
                                    id: custId,
                                    name: parts[0] || custText,
                                    phone: parts[1] || '',
                                    type: parts.slice(2).join(' ') || ''
                                });
                            }
                        });
                        
                        if (customers.length > 0) {
                            let html = '<ul class="divide-y divide-gray-200">';
                            customers.forEach(function(customer) {
                                html += `
                                    <li class="sub-customer-item p-2.5 hover:bg-gray-50 cursor-pointer transition-colors" data-id="${customer.id}">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1 min-w-0">
                                                <div class="text-xs font-semibold text-gray-900 mb-0.5">${customer.name}</div>
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    ${customer.phone ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">${customer.phone}</span>` : ''}
                                                    ${customer.type ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">${customer.type}</span>` : ''}
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                `;
                            });
                            html += '</ul>';
                            $autocomplete.html(html).removeClass('hidden').addClass('block');
                        } else {
                            $autocomplete.html('<ul class="divide-y divide-gray-200"><li class="p-2.5 text-gray-500 text-xs text-center">Tidak ditemukan</li></ul>').removeClass('hidden').addClass('block');
                        }
                    } else {
                        $autocomplete.html('<ul class="divide-y divide-gray-200"><li class="p-2.5 text-gray-500 text-xs text-center">Tidak ditemukan</li></ul>').removeClass('hidden').addClass('block');
                    }
                },
                error: function() {
                    $autocomplete.html('<ul class="divide-y divide-gray-200"><li class="p-4 text-red-600">Error searching customers</li></ul>').removeClass('hidden').addClass('block');
                }
            });
        }, 300);
    });
    
    // Handle sub customer selection (untuk dropshipper)
    $(document).on('click', '.sub-customer-item', function() {
        const custId = $(this).attr('data-id');
        const $item = $(this);
        
        const custName = $item.find('.text-xs.font-semibold').first().text().trim() || $item.text().trim();
        const custPhone = $item.find('.bg-gray-100').first().text().trim() || '';
        const custType = $item.find('.bg-blue-100').first().text().trim() || '';
        
        $('#sub_cust_id').val(custId);
        $('#sub-customer-search').val(custName);
        $('#sub-customer-autocomplete').addClass('hidden').removeClass('block').empty();
        
        // Show customer badge untuk sub customer
        $('#customer-badge .customer-name').text(custName);
        $('#customer-badge .customer-type').text(custType || 'CUSTOMER');
        $('#customer-badge').removeClass('hidden').addClass('flex');
    });
    
    // Function to enable/disable marketplace field based on division type (sesuai POS lama)
    // DROPSHIPPER/RESELLER/WHATSAPP/WEBSITE: marketplace DISABLED (menggunakan UNIT/QTY)
    // Division lain: marketplace ENABLED (bisa menggunakan marketplace)
    function updateMarketplaceFieldStatus() {
        const divisionType = $('#std_id option:selected').text().toUpperCase();
        const marketplaceDisabledTypes = ['DROPSHIPPER', 'RESELLER', 'WHATSAPP', 'WEBSITE'];
        const isMarketplaceDisabled = marketplaceDisabledTypes.includes(divisionType);
        
        // Enable/disable all marketplace input fields
        $('.marketplace-price-input').each(function() {
            const index = $(this).attr('id').replace('marketplace_price', '');
            
            if (isMarketplaceDisabled) {
                // Untuk DROPSHIPPER/RESELLER/WHATSAPP/WEBSITE: marketplace DISABLED (menggunakan UNIT/QTY)
                $(this).prop('disabled', true).removeClass('bg-white').addClass('bg-gray-100');
                // Reset value to 0 if disabled
                if (orderItems[index]) {
                    orderItems[index].marketplace = 0;
                    $(this).val(0);
                }
            } else {
                // Untuk division lain: marketplace ENABLED
                $(this).prop('disabled', false).removeClass('bg-gray-100').addClass('bg-white');
            }
        });
    }
    
    // Listen to division type change
    $(document).on('change', '#std_id', function() {
        const stdId = $(this).val();
        const divisionType = $(this).find('option:selected').text().toUpperCase();
        
        // Reset customer fields
        $('#cust_id').val('');
        $('#sub_cust_id').val('');
        $('#customer-search').val('');
        $('#sub-customer-search').val('');
        $('#customer-badge').addClass('hidden').removeClass('flex');
        $('#dropshipper-badge').addClass('hidden').removeClass('flex');
        selectedCustomer = null;
        
        // Clear order items when division changes (sesuai POS lama)
        if (orderItems.length > 0) {
            const previousValue = $(this).data('previous-value') || '';
            const currentValue = stdId;
            // Revert first, then show confirmation
            $(this).val(previousValue);
            
            showConfirmToast('Ubah division akan menghapus semua item di order. Lanjutkan?', 'Ya, Lanjutkan', 'Batal').then((confirmed) => {
                if (confirmed) {
                    $(this).val(currentValue);
                    orderItems = [];
                    updateProductTable();
                    updateOrderDisplay();
                    updateSummary();
                }
            });
            return;
        }
        
        // Store previous value
        $(this).data('previous-value', stdId);
        
        // Show/hide dropshipper field based on division
        if (divisionType === 'DROPSHIPPER') {
            $('#dropship-input-container').removeClass('hidden');
            // Update label dan placeholder untuk input pertama (Dropshipper)
            $('#customer-label').text('Dropshipper');
            $('#customer-search').attr('placeholder', 'Search Dropshipper (min 4 chars)');
        } else {
            $('#dropship-input-container').addClass('hidden');
            // Update label dan placeholder untuk input pertama (Customer biasa)
            $('#customer-label').text('Customer');
            $('#customer-search').attr('placeholder', 'Search Customer (min 4 chars)');
        }
        
        // Reload customer list by division
        if (stdId) {
            reloadCustomerByDivision(stdId);
        }
        
        // Update marketplace field status
        updateMarketplaceFieldStatus();
        updateSummary();
    });
    
    // Function to reload customer list by division (sesuai POS lama)
    function reloadCustomerByDivision(stdId) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.ajax({
            type: "GET",
            dataType: 'html',
            data: {
                _std_id: stdId
            },
            url: "/reload_customer_by_division",
            success: function(response) {
                // Response akan berisi HTML dengan select dropdown
                // Untuk POS V2, kita akan gunakan data untuk autocomplete
                // Tapi kita perlu parse response untuk mendapatkan customer list
                console.log('Customer list reloaded for division:', stdId);
            },
            error: function(xhr, status, error) {
                console.error('Error reloading customer by division:', error);
            }
        });
    }
    
    // Function to load customer detail (reusable)
    function loadCustomerDetail(custId) {
        if (!custId) {
            showToast('Silahkan pilih customer terlebih dahulu', 'warning');
            return;
        }
        
        // Show loading state
        $('#modal-customer-detail').find('[id^="detail-"]').each(function() {
            $(this).html('<span class="text-gray-400 text-xs">Loading...</span>');
        });
        
        // Open modal - use data attributes for Flowbite auto-initialization
        const modalEl = document.getElementById('modal-customer-detail');
        if (modalEl) {
            // Check if Flowbite is available
            if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                try {
                    let modal = Flowbite.Modal.getInstance(modalEl);
                    if (!modal) {
                        modal = new Flowbite.Modal(modalEl);
                    }
                    modal.show();
                } catch(e) {
                    console.error('Error opening modal:', e);
                    // Fallback: use data attributes
                    $(modalEl).removeClass('hidden');
                }
            } else {
                // Fallback: show modal manually
                $(modalEl).removeClass('hidden');
            }
        }
        
        // Fetch customer details
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.ajax({
            url: '/check_customer',
            method: 'POST',
            data: {
                _cust_id: custId
            },
            dataType: 'text', // Force text to handle json_encode response
            success: function(response) {
                // Parse response (controller returns json_encode, so it's a string)
                let data;
                try {
                    data = JSON.parse(response);
                } catch(e) {
                    console.error('Error parsing response:', e, 'Response:', response);
                    showToast('Error parsing response dari server', 'error');
                    if (modalEl) {
                        $(modalEl).addClass('hidden');
                    }
                    return;
                }
                
                console.log('Customer detail response:', data);
                
                if (data.status === '200' || data.status === 200) {
                    // Get customer type name
                    const ctId = data.ct_id;
                    let ctName = '-';
                    if (ctId) {
                        const $ctOption = $('#modal-customer #ct_id option[value="' + ctId + '"], #f_customer #ct_id option[value="' + ctId + '"]');
                        if ($ctOption.length) {
                            ctName = $ctOption.text();
                        } else {
                            ctName = 'ID: ' + ctId;
                        }
                    }
                    
                    // Populate modal fields
                    $('#detail-ct_id').text(ctName);
                    $('#detail-cust_name').text(data.cust_name || '-');
                    $('#detail-cust_store').text(data.cust_store || '-');
                    $('#detail-cust_phone').text(data.cust_phone || '-');
                    $('#detail-cust_email').text(data.cust_email || '-');
                    
                    // Use province/city/subdistrict name if available, otherwise use code (sesuai POS lama)
                    $('#detail-cust_province').text(data.cust_province_name || data.cust_province || '-');
                    $('#detail-cust_city').text(data.cust_city_name || data.cust_city || '-');
                    $('#detail-cust_subdistrict').text(data.cust_subdistrict_name || data.cust_subdistrict || '-');
                    $('#detail-cust_address').text(data.cust_address || '-');
                    
                    // Status badge
                    const isActive = data.cust_token_active == 1 || data.cust_token_active === '1' || data.cust_token_active === 1;
                    const statusHtml = isActive 
                        ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>'
                        : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Non-Active</span>';
                    $('#detail-cust_token_active').html(statusHtml);
                } else {
                    showToast('Gagal memuat detail customer: ' + (data.message || 'Status: ' + data.status), 'error');
                    if (modalEl) {
                        $(modalEl).addClass('hidden');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr, status, error);
                console.error('Response:', xhr.responseText);
                let errorMsg = 'Error memuat detail customer';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        const errorData = JSON.parse(xhr.responseText);
                        errorMsg = errorData.message || errorMsg;
                    } catch(e) {
                        errorMsg = 'HTTP ' + xhr.status + ': ' + (xhr.responseText.substring(0, 100) || error);
                    }
                }
                showToast(errorMsg, 'error');
                if (modalEl) {
                    $(modalEl).addClass('hidden');
                }
            }
        });
    }
    
    // Customer detail button - Show customer details modal
    $(document).on('click', '#customer-detail-btn', function() {
        const custId = $('#cust_id').val();
        loadCustomerDetail(custId);
    });
    
    // Dropshipper detail button - Show dropshipper details modal
    $(document).on('click', '#dropshipper-detail-btn', function() {
        const custId = $('#cust_id').val();
        loadCustomerDetail(custId);
    });
    
    // Dropship mode toggle
    let isDropshipMode = false;
    $('#dropship-btn').on('click', function() {
        isDropshipMode = !isDropshipMode;
        if (isDropshipMode) {
            $('#dropship-input-container').removeClass('hidden');
            $(this).addClass('bg-gray-900').removeClass('bg-gray-700');
            showToast('Mode Dropship aktif', 'info');
        } else {
            $('#dropship-input-container').addClass('hidden');
            $('#sub-customer-search').val('');
            $('#sub_cust_id').val('');
            $(this).removeClass('bg-red-600').addClass('bg-gray-900');
            showToast('Mode Dropship nonaktif', 'info');
        }
    });
    
    // Add new customer form submission
    $('#f_customer').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serializeArray();
        formData.push({name: '_token', value: $('meta[name="csrf-token"]').attr('content')});
        
        $.ajax({
            url: '/save_customer',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.status === '200') {
                    showToast('Customer berhasil ditambahkan!', 'success');
                    const modalEl = document.getElementById('modal-customer');
                    if (modalEl) {
                        if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                            const modal = Flowbite.Modal.getInstance(modalEl) || new Flowbite.Modal(modalEl);
                            modal.hide();
                        } else {
                            modalEl.classList.add('hidden');
                            modalEl.setAttribute('aria-hidden', 'true');
                        }
                    }
                    // Reset form
                    $('#f_customer')[0].reset();
                    // Optionally reload customer list or refresh page
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showToast('Gagal menambahkan customer: ' + (response.message || 'Unknown error'), 'error');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Gagal menambahkan customer!';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showToast(errorMsg, 'error');
            }
        });
    });
    
    // Initialize empty order display
    updateOrderDisplay();
    updateSummary();
    
    // Format bin string to badges
    // Input: "[02] [0] [TOKO] [0]"
    // Output: HTML badges
    function formatBinToBadges(binString) {
        if (!binString || binString.trim() === '') {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">No Location</span>';
        }
        
        // Parse pattern: [CODE] [QTY] [CODE] [QTY] ...
        const regex = /\[([^\]]+)\]\s*\[([^\]]+)\]/g;
        const badges = [];
        let match;
        
        while ((match = regex.exec(binString)) !== null) {
            const locationCode = match[1].trim();
            const quantity = parseInt(match[2]) || 0;
            
            // Determine badge color based on location code and quantity (Tailwind classes)
            // Simple 2-color scheme: green if quantity > 0, red if quantity = 0
            let badgeClass = quantity > 0 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500';
            
            // Create badge with icon
            const icon = quantity > 0 ? '<i class="cft-standard-solid cft-check-round text-green-600 text-sm"></i>' : '<i class="cft-standard-solid cft-cancel text-red-500 text-sm"></i>';
            badges.push(`
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium ${badgeClass}">
                    ${icon} <strong>${locationCode}</strong>: ${formatNumber(quantity)}
                </span>
            `);
        }
        
        return badges.join('');
    }
    
    // CSRF Token setup for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Store selection change handler
    $('#st_id').on('change', function() {
        const selectedStoreId = $(this).val();
        const selectedStoreName = $(this).find('option:selected').text();
        
        if (selectedStoreId && selectedStoreName !== '- Pilih -') {
            // Update store name display
            $('#store-name-display').text(selectedStoreName);
            $('#store-id-display').text('#' + selectedStoreId);
            
            // Reset order items and summary when store changes
            orderItems = [];
            shippingCost = 0;
            voucherDiscount = 0;
            totalDiscount = 0;
            updateProductTable();
            updateSummary();
        } else {
            // Reset to default if no store selected
            $('#store-name-display').text('Store');
            $('#store-id-display').text('#123345');
        }
    });
    
    // ===========================================
    // RETUR EVENT HANDLERS
    // ===========================================
    
    // Retur checkbox handler
    $('#retur-checkbox').on('change', function() {
        if ($(this).is(':checked')) {
            // Show retur search container
            $('#retur-search-container').removeClass('hidden');
            $('#retur-type-badge').html('<span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-semibold">ACTIVE</span>');
            
            // Clear order items with confirmation
            if (orderItems.length > 0) {
                showConfirmToast('Clear semua items untuk aktivasi mode retur?').then((confirmed) => {
                    if (confirmed) {
                        orderItems = [];
                        updateProductTable();
                        updateSummary();
                        updateOrderDisplay();
                        showToast('Mode retur diaktifkan', 'info');
                    } else {
                        $('#retur-checkbox').prop('checked', false);
                        $('#retur-search-container').addClass('hidden');
                        $('#retur-type-badge').html('');
                    }
                });
            } else {
                showToast('Mode retur diaktifkan', 'info');
            }
        } else {
            // Hide retur search container and reset
            $('#retur-search-container').addClass('hidden');
            $('#retur-type-badge').html('');
            $('#transaction-search').val('');
            $('#transaction-list').addClass('hidden').html('');
            $('#transaction-badge').addClass('hidden');
            $('#retur-items-container').addClass('hidden');
            $('#retur-items-list').html('');
            $('#pt_id_complaint').val('');
            $('#exchange_flag').val('');
            selectedTransactionForRetur = null;
            returItemsData = [];
            
            showToast('Mode retur dinonaktifkan', 'info');
        }
    });
    
    // Transaction search with debounce
    let transactionSearchTimeout;
    $('#transaction-search').on('input', function() {
        const searchTerm = $(this).val().trim();
        
        clearTimeout(transactionSearchTimeout);
        
        if (searchTerm.length < 5) {
            $('#transaction-list').addClass('hidden').html('');
            return;
        }
        
        transactionSearchTimeout = setTimeout(function() {
            // AJAX search for transactions
            $.ajax({
                url: '/search_transaction_for_retur',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    search: searchTerm
                },
                success: function(response) {
                    if (response.status === 'success' && response.data.length > 0) {
                        let html = '';
                        response.data.forEach(function(transaction) {
                            html += `
                                <div class="transaction-item p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200" 
                                     data-pt-id="${transaction.id}"
                                     data-invoice="${transaction.pos_invoice}"
                                     data-date="${transaction.created_at}"
                                     data-customer="${transaction.customer_name}"
                                     data-cust-id="${transaction.cust_id || ''}"
                                     data-sub-cust-id="${transaction.sub_cust_id || ''}"
                                     data-std-id="${transaction.std_id || ''}">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-semibold text-sm text-gray-900">${transaction.pos_invoice}</p>
                                            <p class="text-xs text-gray-600">${transaction.customer_name}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">${transaction.created_at}</p>
                                            <p class="text-sm font-semibold text-gray-900">Rp. ${formatNumber(transaction.pos_real_price)}</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        $('#transaction-list').html(html).removeClass('hidden');
                    } else {
                        $('#transaction-list').html('<div class="p-3 text-center text-gray-500 text-sm">Transaksi tidak ditemukan</div>').removeClass('hidden');
                    }
                },
                error: function() {
                    showToast('Gagal mencari transaksi', 'error');
                }
            });
        }, 500);
    });
    
    // Select transaction
    $(document).on('click', '.transaction-item', function() {
        const ptId = $(this).data('pt-id');
        const invoice = $(this).data('invoice');
        const date = $(this).data('date');
        const customer = $(this).data('customer');
        const custId = $(this).data('cust-id');
        const subCustId = $(this).data('sub-cust-id');
        const stdId = $(this).data('std-id');
        
        // Store selected transaction
        selectedTransactionForRetur = {
            id: ptId,
            invoice: invoice,
            date: date,
            customer: customer,
            cust_id: custId,
            sub_cust_id: subCustId,
            std_id: stdId
        };
        
        // Set hidden field
        $('#pt_id_complaint').val(ptId);
        
        // Display selected transaction badge
        $('#selected-transaction-invoice').text(invoice);
        $('#selected-transaction-date').text(`${date} - ${customer}`);
        $('#transaction-badge').removeClass('hidden');
        
        // Hide search list
        $('#transaction-list').addClass('hidden');
        $('#transaction-search').val('');
        
        // Auto-populate division and customer from transaction
        if (stdId) {
            // Set division without clearing customer (for retur)
            const currentStdId = $('#std_id').val();
            if (currentStdId != stdId) {
                $('#std_id').val(stdId);
                // Only reload customer list, don't clear existing data
                reloadCustomerByDivision(stdId);
            }
            
            // Wait a bit then load customer
            setTimeout(() => {
                if (custId) {
                    loadCustomerForRetur(custId, subCustId, customer);
                }
            }, 800);
        }
        
        // Load transaction items
        loadReturItems(ptId);
    });
    
    // Clear selected transaction
    $('#clear-transaction-btn').on('click', function() {
        $('#transaction-badge').addClass('hidden');
        $('#retur-items-container').addClass('hidden');
        $('#retur-items-list').html('');
        $('#pt_id_complaint').val('');
        $('#exchange_flag').val('');
        selectedTransactionForRetur = null;
        returItemsData = [];
        
        // Remove retur items from order
        orderItems = orderItems.filter(item => item.isRetur !== true);
        updateProductTable();
        updateSummary();
        updateOrderDisplay();
    });
    
    // Load items from selected transaction
    function loadReturItems(ptId) {
        $.ajax({
            url: '/get_transaction_items_for_retur',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                pt_id: ptId
            },
            success: function(response) {
                if (response.status === 'success' && response.data.length > 0) {
                    returItemsData = response.data;
                    displayReturItems(response.data);
                } else {
                    showToast('Tidak ada item yang bisa diretur', 'warning');
                }
            },
            error: function() {
                showToast('Gagal memuat item transaksi', 'error');
            }
        });
    }
    
    // Display retur items with checkboxes
    function displayReturItems(items) {
        let html = '';
        items.forEach(function(item, index) {
            // Use initial or image like regular order items
            let imageHtml = '';
            if (item.image) {
                imageHtml = `<img src="/app/assets/media/products/${item.image}" alt="${item.product_name}" class="w-8 h-12 object-cover rounded border border-gray-200">`;
            } else {
                const initial = item.product_name ? item.product_name.charAt(0).toUpperCase() : 'P';
                imageHtml = `<div class="w-12 h-12 rounded border border-gray-200 bg-red-100 flex items-center justify-center text-red-600 font-bold text-lg">${initial}</div>`;
            }
            
            html += `
                <div class="retur-item-card p-3 bg-white border border-gray-200 rounded-lg hover:border-red-300 transition-colors">
                    <div class="flex items-start gap-3">
                        <input type="checkbox" 
                               class="retur-item-checkbox mt-1 w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500" 
                               data-item-index="${index}">
                        ${imageHtml}
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">${item.product_name}</p>
                            <p class="text-xs text-gray-600">${item.brand} - ${item.color} - ${item.size}</p>
                            <div class="flex justify-between items-center mt-2">
                                <div>
                                    <span class="text-xs text-gray-500">Qty:</span>
                                    <input type="number" 
                                           class="retur-qty-input w-16 px-2 py-1 text-xs border border-gray-300 rounded focus:ring-red-500 focus:border-red-500" 
                                           data-item-index="${index}"
                                           value="${item.qty}" 
                                           min="1" 
                                           max="${item.qty}"
                                           disabled>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">Price:</p>
                                    <p class="text-sm font-semibold text-gray-900">Rp. ${formatNumber(item.price)}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        $('#retur-items-list').html(html);
        $('#retur-items-container').removeClass('hidden');
    }
    
    // Enable qty input when checkbox is checked
    $(document).on('change', '.retur-item-checkbox', function() {
        const index = $(this).data('item-index');
        const qtyInput = $(`.retur-qty-input[data-item-index="${index}"]`);
        
        if ($(this).is(':checked')) {
            qtyInput.prop('disabled', false);
            // Add to order items as negative qty
            addReturItemToOrder(index);
        } else {
            qtyInput.prop('disabled', true).val(returItemsData[index].qty);
            // Remove from order items
            removeReturItemFromOrder(index);
        }
    });
    
    // Update qty for retur item
    $(document).on('change', '.retur-qty-input', function() {
        const index = $(this).data('item-index');
        const newQty = parseInt($(this).val());
        const maxQty = returItemsData[index].qty;
        
        if (newQty > maxQty) {
            showToast(`Qty retur tidak boleh melebihi pembelian (max: ${maxQty})`, 'warning');
            $(this).val(maxQty);
            return;
        }
        
        if (newQty < 1) {
            $(this).val(1);
            return;
        }
        
        // Update in order items
        updateReturItemQty(index, newQty);
    });
    
    // Add retur item to order with negative qty
    function addReturItemToOrder(itemIndex) {
        const item = returItemsData[itemIndex];
        const qtyInput = $(`.retur-qty-input[data-item-index="${itemIndex}"]`);
        const qty = parseInt(qtyInput.val()) || item.qty;
        
        // Check if already exists
        const existingIndex = orderItems.findIndex(oi => oi.returItemIndex === itemIndex);
        if (existingIndex !== -1) {
            return; // Already added
        }
        
        // Use image or initial like regular order items
        let imageUrl = '';
        if (item.image) {
            imageUrl = '/app/assets/media/products/' + item.image;
        }
        // Note: initial will be handled in updateOrderDisplay
        
        // Add as negative qty
        orderItems.push({
            id: item.pst_id,
            product_id: item.p_id,
            name: item.product_name,
            brand: item.brand,
            color: item.color,
            size: item.size,
            image: imageUrl, // Can be empty, will use initial
            price: item.price,
            qty: -Math.abs(qty), // NEGATIVE!
            quantity: -Math.abs(qty), // Also set quantity for compatibility
            disc_percent: 0,
            disc_number: 0,
            discRp: 0,
            nameset: 0,
            marketplace: 0,
            isRetur: true,
            returItemIndex: itemIndex,
            plst_id: item.plst_id
        });
        
        // Set exchange flag to true (ada item retur + nanti bisa add item baru)
        $('#exchange_flag').val('true');
        
        updateProductTable();
        updateSummary();
        updateOrderDisplay();
        
        showToast(`Item retur ditambahkan: ${item.product_name}`, 'success');
    }
    
    // Remove retur item from order
    function removeReturItemFromOrder(itemIndex) {
        orderItems = orderItems.filter(item => item.returItemIndex !== itemIndex);
        
        // Check if still has retur items
        const hasReturItems = orderItems.some(item => item.isRetur === true);
        if (!hasReturItems) {
            $('#exchange_flag').val(''); // No retur items, reset flag
        }
        
        updateProductTable();
        updateSummary();
        updateOrderDisplay();
    }
    
    // Update retur item qty
    function updateReturItemQty(itemIndex, newQty) {
        const orderIndex = orderItems.findIndex(item => item.returItemIndex === itemIndex);
        if (orderIndex !== -1) {
            orderItems[orderIndex].qty = -Math.abs(newQty); // Keep negative
            orderItems[orderIndex].quantity = -Math.abs(newQty); // Keep both in sync
            updateProductTable();
            updateSummary();
            updateOrderDisplay();
        }
    }
    
    // Load customer for retur - auto populate customer from selected transaction
    function loadCustomerForRetur(custId, subCustId, customerName) {
        if (custId) {
            // Set customer ID and name
            $('#customer-search').val(customerName);
            $('#cust_id').val(custId);
            
            // Fetch full customer details to get customer type
            $.ajax({
                url: '/check_customer',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _cust_id: custId
                },
                success: function(r) {
                    if (r.status == '200') {
                        // Show customer badge with name and type
                        $('#customer-badge').removeClass('hidden');
                        $('#selected-customer-name').text(r.cust_name);
                        $('#selected-customer-type').text(r.ct_name || 'Customer');
                        
                        // If sub customer exists (for dropshipper)
                        if (subCustId) {
                            $('#sub_cust_id').val(subCustId);
                            // Fetch sub customer name
                            $.ajax({
                                url: '/check_customer',
                                method: 'POST',
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                    _cust_id: subCustId
                                },
                                success: function(subR) {
                                    if (subR.status == '200') {
                                        $('#sub-customer-search').val(subR.cust_name);
                                        $('#dropship-input-container').removeClass('hidden');
                                        $('#dropshipper-badge').removeClass('hidden');
                                        $('#dropshipper-name').text(r.cust_name);
                                    }
                                }
                            });
                        }
                    }
                },
                error: function() {
                    // Fallback if AJAX fails
                    $('#customer-badge').removeClass('hidden');
                    $('#selected-customer-name').text(customerName);
                    $('#selected-customer-type').text('Customer');
                }
            });
        }
    }
    
    // ==================== CLOCK & HEADER BUTTONS ====================
    
    // Real-time clock function
    function updateClock() {
        const now = new Date();
        
        // Format time
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        
        // Format date
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        const dayName = days[now.getDay()];
        const date = now.getDate();
        const monthName = months[now.getMonth()];
        const year = now.getFullYear();
        
        // Update time display (simple format)
        $('#current-time').text(`${hours}:${minutes}:${seconds}`);
        
        // Update tooltip with full date and time
        const tooltip = `${dayName}, ${date} ${monthName} ${year} - ${hours}:${minutes}:${seconds}`;
        $('#clock-container').attr('title', tooltip);
    }
    
    // Update clock immediately and then every second
    updateClock();
    setInterval(updateClock, 1000);
    
    // ==================== SHIFT EMPLOYEE ====================
    
    let shiftInterval = null;
    let shiftStartTime = null;
    
    // Shift Button - Open Modal and Check Status
    $('#shift-btn').on('click', function() {
        // Check current shift status
        const baseUrl = window.location.origin;
        $.ajax({
            url: baseUrl + '/check_user_shift',
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === '200') {
                    // Shift is active
                    $('#start-shift-btn').addClass('hidden');
                    $('#stop-shift-btn').removeClass('hidden');
                    $('#shift-status-badge').removeClass('bg-gray-200 text-gray-700')
                        .addClass('bg-green-100 text-green-700')
                        .text('In Progress');
                    $('#shift-timer-container').removeClass('hidden');
                    
                    // Calculate elapsed time if shift_start is provided
                    if (response.shift_start) {
                        // Parse shift start time - handle various date formats
                        const shiftStart = response.shift_start.replace(' ', 'T'); // Convert "YYYY-MM-DD HH:MM:SS" to ISO format
                        shiftStartTime = new Date(shiftStart);
                        
                        // Verify date is valid
                        if (!isNaN(shiftStartTime.getTime())) {
                            startShiftTimer();
                        } else {
                            console.error('Invalid shift start time:', response.shift_start);
                            $('#shift-timer').text('00:00:00');
                        }
                    }
                } else {
                    // Shift not started
                    $('#start-shift-btn').removeClass('hidden');
                    $('#stop-shift-btn').addClass('hidden');
                    $('#shift-status-badge').removeClass('bg-green-100 text-green-700')
                        .addClass('bg-gray-200 text-gray-700')
                        .text('Not Started');
                    $('#shift-timer-container').addClass('hidden');
                }
                
                // Show modal (using Flowbite)
                if (window.shiftModal) {
                    window.shiftModal.show();
                } else {
                    // Fallback: manually show
                    $('#modal-shift').removeClass('hidden').addClass('flex');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error checking shift status:', xhr.status, error);
                
                // Handle 404 - route not found
                if (xhr.status === 404) {
                    showToast('Fitur shift belum tersedia di server ini', 'warning');
                } else if (xhr.status === 419) {
                    showToast('Session expired. Silakan refresh halaman.', 'error');
                } else {
                    showToast('Gagal memeriksa status shift', 'error');
                }
                
                // Still show modal with default state (not started)
                $('#start-shift-btn').removeClass('hidden');
                $('#stop-shift-btn').addClass('hidden');
                $('#shift-status-badge').removeClass('bg-green-100 text-green-700')
                    .addClass('bg-gray-200 text-gray-700')
                    .text('Not Started');
                $('#shift-timer-container').addClass('hidden');
                
                if (window.shiftModal) {
                    window.shiftModal.show();
                } else {
                    $('#modal-shift').removeClass('hidden').addClass('flex');
                }
            }
        });
    });
    
    // Start Shift Timer
    function startShiftTimer() {
        if (shiftInterval) {
            clearInterval(shiftInterval);
        }
        
        shiftInterval = setInterval(function() {
            if (!shiftStartTime) return;
            
            const now = new Date();
            const diff = now - shiftStartTime;
            
            const hours = Math.floor(diff / 3600000);
            const minutes = Math.floor((diff % 3600000) / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);
            
            const formattedTime = 
                String(hours).padStart(2, '0') + ':' +
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');
            
            $('#shift-timer').text(formattedTime);
        }, 1000);
    }
    
    // Start Shift Button Handler
    $('#start-shift-btn').on('click', function() {
        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Starting...');
        
        const baseUrl = window.location.origin;
        $.ajax({
            url: baseUrl + '/user_start_shift',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Shift started:', response);
                
                // Update UI
                $btn.addClass('hidden').prop('disabled', false).html('<i class="fas fa-play mr-2"></i>Start Shift');
                $('#stop-shift-btn').removeClass('hidden');
                $('#shift-status-badge').removeClass('bg-gray-200 text-gray-700')
                    .addClass('bg-green-100 text-green-700')
                    .text('In Progress');
                $('#shift-timer-container').removeClass('hidden');
                
                // Start timer
                shiftStartTime = new Date();
                startShiftTimer();
                
                showToast('Shift berhasil dimulai', 'success');
            },
            error: function(error) {
                console.error('Error starting shift:', error);
                $btn.prop('disabled', false).html('<i class="fas fa-play mr-2"></i>Start Shift');
                showToast('Gagal memulai shift', 'error');
            }
        });
    });
    
    // Stop Shift Button Handler
    $('#stop-shift-btn').on('click', function() {
        showConfirmToast('Apakah Anda yakin ingin menghentikan shift?').then(function(confirmed) {
            if (!confirmed) return;
            
            const $btn = $('#stop-shift-btn');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Stopping...');
            
            const baseUrl = window.location.origin;
            $.ajax({
                url: baseUrl + '/user_end_shift',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Shift stopped:', response);
                    
                    // Stop timer
                    if (shiftInterval) {
                        clearInterval(shiftInterval);
                        shiftInterval = null;
                    }
                    shiftStartTime = null;
                    
                    // Update UI
                    $btn.addClass('hidden').prop('disabled', false).html('<i class="fas fa-stop mr-2"></i>Stop Shift');
                    $('#start-shift-btn').removeClass('hidden');
                    $('#shift-status-badge').removeClass('bg-green-100 text-green-700')
                        .addClass('bg-gray-200 text-gray-700')
                        .text('Not Started');
                    $('#shift-timer-container').addClass('hidden');
                    $('#shift-timer').text('00:00:00');
                    
                    showToast('Shift berhasil dihentikan', 'success');
                },
                error: function(error) {
                    console.error('Error stopping shift:', error);
                    $btn.prop('disabled', false).html('<i class="fas fa-stop mr-2"></i>Stop Shift');
                    showToast('Gagal menghentikan shift', 'error');
                }
            });
        });
    });
    
    // Close Shift Modal Handler (for X button)
    $('[data-modal-hide="modal-shift"]').on('click', function() {
        if (window.shiftModal) {
            window.shiftModal.hide();
        } else {
            $('#modal-shift').removeClass('flex').addClass('hidden');
        }
    });
    
    // Calculator Button & Dropdown Handler
    let calculatorExpression = '';
    let calculatorDisplay = '0';
    
    $('#calculatorButton').on('click', function(e) {
        e.stopPropagation();
        $('#calculatorDropdown').toggleClass('hidden');
    });
    
    // Close calculator when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#calculatorButton, #calculatorDropdown').length) {
            $('#calculatorDropdown').addClass('hidden');
        }
    });
    
    // Calculator number/operator buttons
    $(document).on('click', '.calc-number, .calc-operator', function() {
        const value = $(this).data('value');
        if (calculatorDisplay === '0' && value !== '.') {
            calculatorDisplay = value;
        } else {
            calculatorDisplay += value;
        }
        calculatorExpression += value;
        $('#calculator-input').text(calculatorDisplay);
    });
    
    // Calculator result button
    $('#calc-result').on('click', function() {
        try {
            // Replace symbols for eval
            let expression = calculatorExpression.replace(/×/g, '*').replace(/÷/g, '/');
            const result = eval(expression);
            calculatorDisplay = String(result);
            calculatorExpression = String(result);
            $('#calculator-input').text(calculatorDisplay);
        } catch (e) {
            $('#calculator-input').text('Error');
            calculatorDisplay = '0';
            calculatorExpression = '';
        }
    });
    
    // Calculator clear button
    $('#calc-clear').on('click', function() {
        calculatorDisplay = '0';
        calculatorExpression = '';
        $('#calculator-input').text(calculatorDisplay);
    });
    
    // Folder Button Handler
    $('#folder-btn').on('click', function() {
        showToast('Fitur Data Folder akan segera tersedia', 'info');
    });
});

