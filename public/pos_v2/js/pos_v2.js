// POS V2 JavaScript

// Initialize variables (outside document ready so they're accessible globally)
let orderItems = [];
let subtotal = 0;
let shippingCost = 0;
let voucherDiscount = 0;
let totalDiscount = 0;

// Initialize Flowbite modals (will be set in document ready)
let shippingModal, voucherModal, discountModal;

// Flowbite Toast Helper Function
function showToast(message, type = 'warning') {
    // Type: success, error, warning, info
    const icons = {
        success: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>',
        error: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>',
        warning: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8.257 3.099c.366-.446.998-.446 1.364 0l6.857 8.333c.36.438.03 1.068-.682 1.068H2.682c-.712 0-1.042-.63-.682-1.068l6.857-8.333zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-4a1 1 0 00-1 1v3a1 1 0 002 0v-3a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>',
        info: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'
    };
    
    const colors = {
        success: { bg: 'bg-green-100', icon: 'text-green-500', text: 'text-green-800' },
        error: { bg: 'bg-red-100', icon: 'text-red-500', text: 'text-red-800' },
        warning: { bg: 'bg-yellow-100', icon: 'text-yellow-500', text: 'text-yellow-800' },
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

$(document).ready(function() {
    // Debug: Check if jQuery and CSRF token are loaded
    console.log('POS V2 JS loaded');
    console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content'));
    console.log('jQuery version:', $.fn.jquery);
    
    // Initialize Flowbite modals - wait for Flowbite to be ready
    function initModals() {
        if (typeof Flowbite === 'undefined' || !Flowbite.Modal) {
            setTimeout(initModals, 100);
            return;
        }
        
        const shippingModalEl = document.getElementById('modal-shipping');
        const voucherModalEl = document.getElementById('modal-voucher');
        const discountModalEl = document.getElementById('modal-discount');
        
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
                                $autocomplete.html('<ul class="divide-y divide-gray-200"><li class="p-4 text-yellow-600">Unexpected response format. Check console.</li></ul>').removeClass('hidden').addClass('block');
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
            nameset: 1,
            marketplace: 1
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
        updateOrderDisplay();
        updateSummary();
        updateProductTable();
    };
    
    window.updateNameset = function(index, value) {
        if (orderItems[index]) {
            orderItems[index].nameset = parseInt(value) || 0;
        }
    };
    
    window.updateMarketplace = function(index, value) {
        if (orderItems[index]) {
            orderItems[index].marketplace = parseInt(value) || 0;
        }
    };
    
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
        
        updateSummary();
        updateProductTable();
    };
    
    // Delete item from table
    window.deleteTableItem = function(index) {
        if (!orderItems[index]) return;
        
        if (confirm('Yakin ingin menghapus item ini?')) {
            orderItems.splice(index, 1);
            updateProductTable();
            updateOrderDisplay();
            updateSummary();
        }
    };
    
    function updateProductTable() {
        $('#product-tbody').empty();
        orderItems.forEach((item, index) => {
            // Extract brand from name if format is [BRAND] product name
            let displayName = item.name;
            let brandBadge = '';
            if (item.brand) {
                brandBadge = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-orange-500 text-white mr-2">${item.brand}</span>`;
                displayName = displayName.replace(/^\[.*?\]\s*/, '');
            }
            
            // Create BIN select dropdown
            let binSelect = `<select class="bin-select w-full text-sm bg-green-100 text-green-800 font-semibold border border-green-300 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-green-500 focus:border-green-500" data-pst-id="${item.id}" data-item-index="${index}">`;
            binSelect += `<option value="">Loading...</option>`;
            binSelect += `</select>`;
            
            const row = `
                <tr data-product-id="${item.id}" data-pl-id="${item.pl_id || ''}" class="bg-white hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 w-50">
                        <div class="flex items-center">
                            <img src="${item.image}" alt="${item.name}" class="w-10 h-10 object-cover rounded mr-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                ${brandBadge}
                                <span class="text-sm text-gray-900 font-semibold">${displayName}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 w-180px">
                        ${binSelect}
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" id="reseller_disc${index}" class="w-16 px-2 py-1.5 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-400 focus:border-red-400 text-center" value="${item.discPercent || ''}" placeholder="%" min="0" step="0.01" onchange="updateResellerDisc(${index})">
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" id="reseller_disc_number${index}" class="w-full px-2 py-1.5 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-400 focus:border-red-400" value="${item.discRp || ''}" placeholder="Rp" min="0" step="0.01" onchange="updateResellerDiscNumber(${index})">
                    </td>
                    <td class="px-4 py-3 text-center">
                        <input type="number" id="item_qty${index}" class="w-16 text-center text-sm border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-red-400 focus:border-red-400" value="${item.quantity}" min="1" onchange="updateTableQuantity(${index}, this.value)">
                    </td>
                    <td class="px-4 py-3 text-center">
                        <input type="number" class="w-16 text-center text-sm border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-red-400 focus:border-red-400" value="${item.nameset}" min="0" onchange="updateNameset(${index}, this.value)">
                    </td>
                    <td class="px-4 py-3 text-center">
                        <input type="number" class="w-16 text-center text-sm border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-red-400 focus:border-red-400" value="${item.marketplace}" min="0" onchange="updateMarketplace(${index}, this.value)">
                    </td>
                    <td class="px-4 py-3 text-right text-sm w-28"><strong class="text-gray-900">Rp. ${formatNumber(item.price)}</strong></td>
                    <td class="px-4 py-3 text-right text-sm w-28"><strong class="text-red-600" id="subtotal_item${index}">Rp. ${formatNumber((item.price * item.quantity) - (item.discRp || 0))}</strong></td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" onclick="deleteTableItem(${index})" class="w-8 h-8 flex items-center bg-red-50 justify-center text-red-500 hover:text-red-800 hover:bg-red-100 rounded transition-colors" title="Hapus">
                            <i class="cft-standard-stroke cft-trash text-base"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#product-tbody').append(row);
            
            // Load locations for this product
            loadLocationsForProduct(item.id, index, item.pl_id);
        });
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
            if (!confirm('Yakin pilih dari display? Apakah sudah cek barang?')) {
                // Revert to previous selection
                const previousPlId = orderItems[itemIndex].pl_id || '';
                $select.val(previousPlId);
                return false;
            }
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
            // Extract brand from name if format is [BRAND] product name
            let displayName = item.name;
            if (item.brand) {
                displayName = displayName.replace(/^\[.*?\]\s*/, '');
            }
            
            const itemHtml = `
                <div class="flex items-center gap-3 p-3 bg-gray-100 rounded-lg">
                    <img src="${item.image}" alt="${item.name}" class="w-9 h-9 object-cover rounded">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            ${item.brand ? `<span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-orange-500 text-white">${item.brand}</span>` : ''}
                            <div class="font-semibold text-sm text-gray-900">${displayName}</div>
                        </div>
                        <div class="text-xs text-gray-600">Qty: ${item.quantity}</div>
                    </div>
                    <div class="font-semibold text-red-600 text-sm">Rp. ${formatNumber(item.price * item.quantity)}</div>
                    <div class="relative flex items-center max-w-[6rem] shadow-xs rounded-base">
                        <button type="button" onclick="decreaseQuantity(${index})" class="text-body bg-white box-border border rounded-r-none border-gray-300  hover:bg-gray-500 hover:text-heading focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-lg text-xs px-2 focus:outline-none h-8">
                            <svg class="w-3 h-3 text-heading" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>
                        </button>
                        <input type="text" data-input-counter class="border-gray-300 h-8 placeholder:text-heading text-center w-full bg-white border-gray-300 py-1.5 placeholder:text-body text-sm" placeholder="999" value="${item.quantity}" />
                        <button type="button" onclick="increaseQuantity(${index})" class="text-body bg-white box-border border rounded-l-none border-gray-300 hover:bg-gray-500 hover:text-heading focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-lg text-xs px-2 focus:outline-none h-8">
                            <svg class="w-3 h-3 text-heading" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/></svg>
                        </button>
                    </div>
                </div>
            `;
            $orderList.append(itemHtml);
        });
        
        $('#item-count').text(`${orderItems.length} Items`);
    }
    
    // Update summary
    function updateSummary() {
        subtotal = orderItems.reduce((sum, item) => {
            const itemSubtotal = item.price * item.quantity;
            const itemDiscount = item.discRp || 0;
            return sum + (itemSubtotal - itemDiscount);
        }, 0);
        
        const grandTotal = subtotal + shippingCost - voucherDiscount - totalDiscount;
        
        $('#subtotal').text('Rp. ' + formatNumber(subtotal));
        if (selectedCourierName) {
            $('#shipping-cost').html('Rp. ' + formatNumber(shippingCost) + ' <small class="text-gray-500">(' + selectedCourierName + ')</small>');
        } else {
            $('#shipping-cost').text('Rp. ' + formatNumber(shippingCost));
        }
        $('#voucher-discount').text('Rp. ' + formatNumber(voucherDiscount));
        $('#total-discount').text('Rp. ' + formatNumber(totalDiscount));
        $('#grand-total').text('Rp. ' + formatNumber(grandTotal));
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
        if (confirm('Are you sure you want to clear all items?')) {
            orderItems = [];
            updateOrderDisplay();
            updateSummary();
            updateProductTable();
        }
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
        
        // Close modal
        const modalEl = document.getElementById('modal-shipping');
        if (modalEl) {
            if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                const modal = Flowbite.Modal.getInstance(modalEl) || new Flowbite.Modal(modalEl);
                modal.hide();
            } else {
                modalEl.classList.add('hidden');
                modalEl.setAttribute('aria-hidden', 'true');
            }
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
        
        // Close modal
        const modalEl = document.getElementById('modal-voucher');
        if (modalEl) {
            if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                const modal = Flowbite.Modal.getInstance(modalEl) || new Flowbite.Modal(modalEl);
                modal.hide();
            } else {
                modalEl.classList.add('hidden');
                modalEl.setAttribute('aria-hidden', 'true');
            }
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
        
        // Close modal
        const modalEl = document.getElementById('modal-discount');
        if (modalEl) {
            if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                const modal = Flowbite.Modal.getInstance(modalEl) || new Flowbite.Modal(modalEl);
                modal.hide();
            } else {
                modalEl.classList.add('hidden');
                modalEl.setAttribute('aria-hidden', 'true');
            }
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
        
        // Show payment modal
        const modalEl = document.getElementById('modal-payment');
        if (modalEl) {
            if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                let modal = Flowbite.Modal.getInstance(modalEl);
                if (!modal) {
                    modal = new Flowbite.Modal(modalEl);
                }
                modal.show();
            } else {
                // Fallback: use data attributes or direct show
                modalEl.classList.remove('hidden');
                modalEl.setAttribute('aria-hidden', 'false');
            }
        }
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
        formData.append('_pt_id_complaint', $('#_pt_id_complaint').val() || '');
        formData.append('_exchange', $('#_exchange').val() || '');
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
                            
                            // Close payment modal
                            const modalEl = document.getElementById('modal-payment');
                            if (modalEl) {
                                if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                                    const modal = Flowbite.Modal.getInstance(modalEl);
                                    if (modal) {
                                        modal.hide();
                                    } else {
                                        modalEl.classList.add('hidden');
                                        modalEl.setAttribute('aria-hidden', 'true');
                                    }
                                } else {
                                    modalEl.classList.add('hidden');
                                    modalEl.setAttribute('aria-hidden', 'true');
                                }
                            }
                            
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
        
        clearTimeout(customerSearchTimeout);
        
        if (query.length < 3) {
            $autocomplete.addClass('hidden').removeClass('block').empty();
            $('#customer-badge').addClass('hidden').removeClass('flex');
            selectedCustomer = null;
            return;
        }
        
        customerSearchTimeout = setTimeout(function() {
            $.ajax({
                url: '/autocomplete_customer',
                method: 'POST',
                data: {
                    query: query,
                    type: 'cust'
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
        
        $('#cust_id').val(custId);
        $('#customer-search').val(custName);
        $('#customer-autocomplete').addClass('hidden').removeClass('block').empty();
        
        // Show customer badge
        $('#customer-badge .customer-name').text(custName);
        $('#customer-badge .customer-type').text(custType || 'CUSTOMER');
        $('#customer-badge').removeClass('hidden').addClass('flex');
        
        selectedCustomer = {
            id: custId,
            name: custName,
            phone: custPhone,
            status: custType
        };
    });
    
    // Customer detail button - Show customer details modal
    $(document).on('click', '#customer-detail-btn', function() {
        const custId = $('#cust_id').val();
        
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
                    $('#detail-cust_province').text(data.cust_province || '-');
                    $('#detail-cust_city').text(data.cust_city || '-');
                    $('#detail-cust_subdistrict').text(data.cust_subdistrict || '-');
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
    
    // Sub Customer Search (Dropshipper)
    let subCustomerSearchTimeout;
    $('#sub-customer-search').on('keyup', function() {
        const query = $(this).val().trim();
        const $autocomplete = $('#sub-customer-autocomplete');
        
        clearTimeout(subCustomerSearchTimeout);
        
        if (query.length >= 4) {
            subCustomerSearchTimeout = setTimeout(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                $.ajax({
                    url: '/autocomplete_customer',
                    method: 'POST',
                    data: {
                        query: query,
                        std_id: $('#std_id').val(),
                        type: 'DROPSHIPPER'
                    },
                    success: function(data) {
                        // Parse and reformat sub customer autocomplete with badges and smaller text
                        if (data && data.trim() !== '') {
                            const $temp = $('<div>').html(data);
                            const subCustomers = [];
                            
                            $temp.find('li a#add_to_item_list_cust, li a#add_to_item_list_sub_cust, li a.sub-customer-item').each(function() {
                                const $link = $(this);
                                const custId = $link.attr('data-id') || $link.data('id');
                                const custText = $link.text().trim();
                                // Parse: "NAME PHONE [GOLD-ACTIVE]" format
                                const match = custText.match(/^(.+?)\s+([0-9\s\-+]+)\s+\[(.+?)\-(.+?)\]$/);
                                if (match) {
                                    subCustomers.push({
                                        id: custId,
                                        name: match[1].trim(),
                                        phone: match[2].trim(),
                                        type: match[3].trim() + ' - ' + match[4].trim()
                                    });
                                } else {
                                    // Try alternative format: "NAME PHONE [TYPE]"
                                    const match2 = custText.match(/^(.+?)\s+([0-9\s\-+]+)\s+\[(.+?)\]$/);
                                    if (match2) {
                                        subCustomers.push({
                                            id: custId,
                                            name: match2[1].trim(),
                                            phone: match2[2].trim(),
                                            type: match2[3].trim()
                                        });
                                    } else {
                                        // Fallback: split by space
                                        const parts = custText.split(/\s+/);
                                        subCustomers.push({
                                            id: custId,
                                            name: parts[0] || custText,
                                            phone: parts[1] || '',
                                            type: parts.slice(2).join(' ') || ''
                                        });
                                    }
                                }
                            });
                            
                            if (subCustomers.length > 0) {
                                let html = '<ul class="divide-y divide-gray-200">';
                                subCustomers.forEach(function(customer) {
                                    html += `
                                        <li class="sub-customer-item p-2.5 hover:bg-gray-50 cursor-pointer transition-colors" data-id="${customer.id}" data-name="${customer.name}">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-xs font-semibold text-gray-900 mb-0.5">${customer.name}</div>
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        ${customer.phone ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">${customer.phone}</span>` : ''}
                                                        ${customer.type ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-700">${customer.type}</span>` : ''}
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
                        $autocomplete.html('<ul class="divide-y divide-gray-200"><li class="p-4 text-red-600">Error loading sub customers</li></ul>').removeClass('hidden').addClass('block');
                    }
                });
            }, 300);
        } else {
            $autocomplete.addClass('hidden').removeClass('block').empty();
        }
    });
    
    // Handle sub customer selection
    $(document).on('click', '.sub-customer-item', function() {
        const subCustId = $(this).data('id');
        const subCustName = $(this).data('name');
        $('#sub_cust_id').val(subCustId);
        $('#sub-customer-search').val(subCustName);
        $('#sub-customer-autocomplete').addClass('hidden').removeClass('block').empty();
        showToast('Sub customer dipilih: ' + subCustName, 'success');
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
});

