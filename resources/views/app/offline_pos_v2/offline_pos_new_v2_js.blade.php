<!-- jQuery (CDN with local fallback) -->




<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Flowbite JS - Load before other scripts -->
<script src="https://cdn.jsdelivr.net/npm/flowbite@2.3.0/dist/flowbite.min.js"></script>

{{--ini yang dari modal tadi yaaaa --}}
<script>
    try {
        console.log('voucher-inline-debug loaded');
        document.addEventListener('click', function(e) {
            var t = e.target;
            if (t.closest && t.closest('.add-voucher')) {
                console.log('inline: add-voucher clicked');
            }
            if (t.closest && t.closest('.add-total-discount')) {
                console.log('inline: add-total-discount clicked');
            }
        });

        // Attach direct handlers to existing buttons (non-delegated) so clicks always append
        function attachDirectAddHandlers() {
            document.querySelectorAll('.add-voucher').forEach(function(btn) {
                btn.removeEventListener('click', btn._inlineVocHandler);
                btn._inlineVocHandler = function(ev) {
                    ev.preventDefault();
                    console.log('direct: add-voucher clicked');
                    var container = document.querySelector('#voucher-container');
                    if (!container) return;
                    var wrapper = document.createElement('div');
                    wrapper.className = 'flex gap-2 mb-3';
                    wrapper.innerHTML = '<input type="text" name="voucher-list[]" class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block p-2.5" placeholder="Kode Voucher" value="">' +
                        '<button type="button" class="add-voucher px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200">+</button>' +
                        '<button type="button" class="remove-voucher px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200">-</button>';
                    container.appendChild(wrapper);
                    // re-run to attach handler to the new + button as well
                    attachDirectAddHandlers();
                };
                btn.addEventListener('click', btn._inlineVocHandler);
            });

            document.querySelectorAll('.add-total-discount').forEach(function(btn) {
                btn.removeEventListener('click', btn._inlineDiscHandler);
                btn._inlineDiscHandler = function(ev) {
                    ev.preventDefault();
                    console.log('direct: add-total-discount clicked');
                    var container = document.querySelector('#total-discount-container');
                    if (!container) return;
                    var wrapper = document.createElement('div');
                    wrapper.className = 'flex gap-2 mb-3';
                    wrapper.innerHTML = '<input type="text" name="total-discount-list[]" class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block p-2.5" placeholder="Diskon" value="">' +
                        '<button type="button" class="add-total-discount px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200">+</button>' +
                        '<button type="button" class="remove-total-discount px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200">-</button>';
                    container.appendChild(wrapper);
                    attachDirectAddHandlers();
                };
                btn.addEventListener('click', btn._inlineDiscHandler);
            });
        }
        attachDirectAddHandlers();

        // Delegated remove handlers so dynamically added rows can be removed
        document.addEventListener('click', function(e) {
            try {
                var rem = e.target.closest && e.target.closest('.remove-voucher');
                if (rem) {
                    e.preventDefault();
                    var row = rem.closest('.flex.gap-2.mb-3') || rem.closest('.flex');
                    if (row) row.remove();
                    return;
                }

                var rem2 = e.target.closest && e.target.closest('.remove-total-discount');
                if (rem2) {
                    e.preventDefault();
                    var row2 = rem2.closest('.flex.gap-2.mb-3') || rem2.closest('.flex');
                    if (row2) row2.remove();
                    return;
                }
            } catch (err) {
                console.error('remove handler error', err);
            }
        });
        // Duplicate JSON-based submit handler removed; the FormData-based submit handler
        // below (attached later) will handle voucher submission to avoid preflight 405 errors.
    } catch (e) {
        console.error('voucher-inline-debug error', e);
    }
</script>
{{--sampai sini yaaa--}}
<!-- Wait for Flowbite to be ready -->
<script>
    // Ensure Flowbite is loaded before proceeding
    (function() {
        let flowbiteCheckCount = 0;
        const maxChecks = 50; // 5 seconds max wait

        function checkFlowbite() {
            if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                console.log('Flowbite is ready');
                return;
            }

            flowbiteCheckCount++;
            if (flowbiteCheckCount < maxChecks) {
                setTimeout(checkFlowbite, 100);
            } else {
                console.error('Flowbite failed to load after 5 seconds');
            }
        }

        checkFlowbite();
    })();
</script>

<!-- PAYMENT MODAL HANDLER - Simple version -->
<script>
    console.log('💳 Simple payment modal handler loaded');

    // Simple helper functions for number formatting
    function replaceComma(val) {
        return parseFloat(String(val).replace(/\./g, '').replace(/,/g, ''));
    }

    function addCommas(num) {
        if (!num && num !== 0) return '0';
        const intNum = Math.round(num);
        return intNum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('💳 Setting up simple payment modal handler...');

        const paymentBtn = document.getElementById('payment_btn');
        const paymentTotal = document.getElementById('payment_total');
        const totalFinalPrice = document.getElementById('total_final_price_side');

        if (paymentBtn && paymentTotal && totalFinalPrice) {
            paymentBtn.addEventListener('click', function() {
                const totalText = totalFinalPrice.textContent || '0';
                const cleanTotal = String(totalText).replace('Rp. ', '').trim();
                const numericTotal = replaceComma(cleanTotal);
                const formatted = addCommas(numericTotal);
                paymentTotal.textContent = 'Rp. ' + formatted;
                console.log('✅ Payment total set to: Rp. ' + formatted);
            });
        }

        // CRITICAL: Add payment_option change handler here in blade.php
        console.log('💳💳💳 Registering payment_option handler in blade.php...');

        // Function to handle payment option change
        function handlePaymentOptionChangeBlade(value) {
            console.log('💳💳💳💳💳 Payment option changed in blade.php! Value:', value);

            const section2 = document.getElementById('payment_method_two_section');
            const paymentType2 = document.getElementById('payment_type_content_two');
            const totalPayment2Label = document.getElementById('total_payment_two_label');
            const totalPayment2 = document.getElementById('total_payment_two');
            const returnPaymentLabel = document.getElementById('return_payment_label');

            // Clear fields
            const pmIdOffline = document.getElementById('pm_id_offline');
            const pmIdOfflineTwo = document.getElementById('pm_id_offline_two');
            if (pmIdOffline) pmIdOffline.value = '';
            if (pmIdOfflineTwo) pmIdOfflineTwo.value = '';

            const totalPayment = document.getElementById('total_payment');
            const totalPaymentTwo = document.getElementById('total_payment_two');
            if (totalPayment) totalPayment.value = '';
            if (totalPaymentTwo) totalPaymentTwo.value = '';

            if (value === 'two') {
                console.log('💳💳💳 Showing payment method 2 section');
                if (section2) {
                    section2.classList.remove('hidden');
                    console.log('✅ Section 2 shown - classes:', section2.className);
                } else {
                    console.error('❌ Section 2 element not found!');
                }
                if (paymentType2) paymentType2.classList.remove('hidden');
                if (totalPayment2Label) totalPayment2Label.classList.remove('hidden');
                if (totalPayment2) totalPayment2.classList.remove('hidden');
                if (returnPaymentLabel) returnPaymentLabel.classList.add('hidden');
            } else {
                console.log('💳💳💳 Hiding payment method 2 section');
                if (section2) {
                    section2.classList.add('hidden');
                    console.log('✅ Section 2 hidden');
                }
                if (paymentType2) paymentType2.classList.add('hidden');
                if (totalPayment2Label) totalPayment2Label.classList.add('hidden');
                if (totalPayment2) totalPayment2.classList.add('hidden');
                if (returnPaymentLabel) returnPaymentLabel.classList.remove('hidden');
            }
        }

        // Method 1: Event delegation
        document.addEventListener('change', function(e) {
            if (e.target && e.target.id === 'payment_option') {
                handlePaymentOptionChangeBlade(e.target.value);
            }
        });

        // Method 2: Direct binding when element exists (with retry)
        function bindPaymentOptionHandler() {
            const paymentOption = document.getElementById('payment_option');
            if (paymentOption) {
                console.log('💳 Payment option element found, binding handler directly');
                paymentOption.removeEventListener('change', paymentOption._bladeHandler);
                paymentOption._bladeHandler = function() {
                    handlePaymentOptionChangeBlade(this.value);
                };
                paymentOption.addEventListener('change', paymentOption._bladeHandler);
                console.log('✅ Direct handler bound to payment_option');
            } else {
                console.log('💳 Payment option not found yet, will retry...');
                setTimeout(bindPaymentOptionHandler, 500);
            }
        }

        // Try binding immediately
        bindPaymentOptionHandler();

        // Handler to close InputCodeModal
        function closeInputCodeModal() {
            const inputCodeModal = document.getElementById('InputCodeModal');
            if (inputCodeModal) {
                inputCodeModal.classList.add('hidden');
                inputCodeModal.setAttribute('aria-hidden', 'true');
                inputCodeModal.style.display = 'none';

                // Remove backdrop
                const backdrop = document.getElementById('input-code-modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }

                // Restore body scroll
                document.body.style.overflow = '';
            }
        }

        // Bind close handlers for InputCodeModal
        document.addEventListener('click', function(e) {
            if (e.target && (
                e.target.closest('[data-modal-hide="InputCodeModal"]') ||
                e.target.getAttribute('data-modal-hide') === 'InputCodeModal'
            )) {
                e.preventDefault();
                closeInputCodeModal();
            }
        });

        // Also bind when payment button is clicked (modal opens)
        if (paymentBtn) {
            paymentBtn.addEventListener('click', function() {
                setTimeout(function() {
                    console.log('💳 Re-binding all payment handlers after modal opens');
                    bindPaymentOptionHandler();

                    // Bind pm_id_offline handler for auto-fill and field visibility
                    const pmIdOffline = document.getElementById('pm_id_offline');
                    if (pmIdOffline) {
                        pmIdOffline.removeEventListener('change', pmIdOffline._bladeHandler);
                        pmIdOffline._bladeHandler = function() {
                            const label = this.options[this.selectedIndex].text;
                            const paymentTotal = document.getElementById('payment_total');
                            const totalPayment = document.getElementById('total_payment');

                            console.log('💳 pm_id_offline changed to:', label);

                            // Show/hide fields based on payment method
                            const cardProvider = document.getElementById('card_provider_content');
                            const cardNumber = document.getElementById('card_number_label');
                            const refNumber = document.getElementById('ref_number_label');
                            const chargeLabel = document.getElementById('charge_label');
                            const subPayment = document.getElementById('sub_payment_offline_content');

                            if (label === 'DEBIT CARD' || label === 'CREDIT CARD') {
                                if (cardProvider) cardProvider.classList.remove('hidden');
                                if (cardNumber) cardNumber.classList.remove('hidden');
                                if (refNumber) refNumber.classList.remove('hidden');
                                if (chargeLabel) {
                                    if (label === 'CREDIT CARD') {
                                        chargeLabel.classList.remove('hidden');
                                    } else {
                                        chargeLabel.classList.add('hidden');
                                    }
                                }
                                if (subPayment) subPayment.classList.add('hidden');
                            } else if (label.includes('EDC')) {
                                if (subPayment) subPayment.classList.remove('hidden');
                                if (cardNumber) cardNumber.classList.add('hidden');
                                if (cardProvider) cardProvider.classList.add('hidden');
                                if (refNumber) refNumber.classList.add('hidden');
                                if (chargeLabel) chargeLabel.classList.add('hidden');
                            } else if (label === 'CASH') {
                                if (cardProvider) cardProvider.classList.add('hidden');
                                if (cardNumber) cardNumber.classList.add('hidden');
                                if (refNumber) refNumber.classList.add('hidden');
                                if (subPayment) subPayment.classList.add('hidden');
                                if (chargeLabel) chargeLabel.classList.add('hidden');
                            } else {
                                // TRANSFER, QRIS, etc
                                if (refNumber) refNumber.classList.remove('hidden');
                                if (cardNumber) cardNumber.classList.add('hidden');
                                if (cardProvider) cardProvider.classList.add('hidden');
                                if (subPayment) subPayment.classList.add('hidden');
                                if (chargeLabel) chargeLabel.classList.add('hidden');
                            }

                            // Auto-fill payment amount
                            if (paymentTotal && totalPayment && label !== 'CASH' && label !== '- Pilih -') {
                                const paymentText = paymentTotal.textContent || '0';
                                const cleanPayment = String(paymentText).replace('Rp. ', '').trim();
                                const numericPayment = replaceComma(cleanPayment);

                                // Set value and trigger input event for formatter
                                totalPayment.value = numericPayment.toString();
                                const inputEvent = new Event('input', { bubbles: true });
                                totalPayment.dispatchEvent(inputEvent);

                                console.log('💳 Auto-filled payment amount:', numericPayment);
                            } else if (label === 'CASH') {
                                if (totalPayment) totalPayment.value = '';
                            }
                        };
                        pmIdOffline.addEventListener('change', pmIdOffline._bladeHandler);
                        console.log('✅ pm_id_offline handler bound (auto-fill + field visibility)');
                    }

                    // Bind pm_id_offline_two handler
                    const pmIdOfflineTwo = document.getElementById('pm_id_offline_two');
                    if (pmIdOfflineTwo) {
                        pmIdOfflineTwo.removeEventListener('change', pmIdOfflineTwo._bladeHandler);
                        pmIdOfflineTwo._bladeHandler = function() {
                            const label = this.options[this.selectedIndex].text;
                            console.log('💳 pm_id_offline_two changed to:', label);

                            // Show/hide fields for payment method 2
                            const cardProviderTwo = document.getElementById('card_provider_content_two');
                            const cardNumberTwo = document.getElementById('card_number_label_two');
                            const refNumberTwo = document.getElementById('ref_number_label_two');

                            if (label === 'DEBIT CARD') {
                                if (cardProviderTwo) cardProviderTwo.classList.remove('hidden');
                                if (cardNumberTwo) cardNumberTwo.classList.remove('hidden');
                                if (refNumberTwo) refNumberTwo.classList.remove('hidden');
                            } else if (label === 'CASH') {
                                if (cardProviderTwo) cardProviderTwo.classList.add('hidden');
                                if (cardNumberTwo) cardNumberTwo.classList.add('hidden');
                                if (refNumberTwo) refNumberTwo.classList.add('hidden');
                            } else {
                                // TRANSFER, QRIS, etc
                                if (refNumberTwo) refNumberTwo.classList.remove('hidden');
                                if (cardNumberTwo) cardNumberTwo.classList.add('hidden');
                                if (cardProviderTwo) cardProviderTwo.classList.add('hidden');
                            }
                        };
                        pmIdOfflineTwo.addEventListener('change', pmIdOfflineTwo._bladeHandler);
                        console.log('✅ pm_id_offline_two handler bound');
                    }

                    // Bind total_payment input handler for return calculation
                    const totalPayment = document.getElementById('total_payment');
                    if (totalPayment) {
                        // Currency formatter
                        totalPayment.removeEventListener('input', totalPayment._currencyFormatter);
                        totalPayment._currencyFormatter = function(e) {
                            const input = e.target;
                            let value = input.value;
                            value = value.replace(/\D/g, '');
                            const formattedValue = new Intl.NumberFormat('en-US', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(value);
                            input.value = formattedValue.replace('IDR', 'Rp');
                        };
                        totalPayment.addEventListener('input', totalPayment._currencyFormatter);

                        // Return payment calculator
                        totalPayment.removeEventListener('keyup', totalPayment._returnCalculator);
                        totalPayment._returnCalculator = function(e) {
                            const paymentTotal = document.getElementById('payment_total');
                            const returnPayment = document.getElementById('return_payment');
                            const returnPaymentLabel = document.getElementById('return_payment_label');
                            const totalPaymentTwo = document.getElementById('total_payment_two');
                            const paymentOption = document.getElementById('payment_option');

                            if (!paymentTotal) return;

                            let totalPaymentValue = this.value.replace(/Rp|,|\./g, '').trim();
                            const totalPriceText = paymentTotal.textContent || '0';
                            const cleanTotalPrice = String(totalPriceText).replace('Rp. ', '').trim();
                            const numericTotalPrice = replaceComma(cleanTotalPrice);

                            const method = paymentOption ? paymentOption.value : 'one';

                            if (method === 'two') {
                                // 2 Metode: calculate total_payment_two
                                if (totalPaymentTwo) {
                                    if (totalPaymentValue === '') {
                                        totalPaymentTwo.value = numericTotalPrice.toString();
                                    } else {
                                        const payment1 = parseFloat(totalPaymentValue.replace(/\./g, ''));
                                        const payment2 = numericTotalPrice - payment1;
                                        totalPaymentTwo.value = payment2.toString();
                                    }
                                }
                                if (returnPaymentLabel) returnPaymentLabel.classList.add('hidden');
                            } else {
                                // 1 Metode: calculate return payment
                                if (totalPaymentValue === '') {
                                    if (returnPayment) returnPayment.textContent = '';
                                    if (returnPaymentLabel) returnPaymentLabel.classList.add('hidden');
                                } else {
                                    const payment1 = parseFloat(totalPaymentValue.replace(/\./g, ''));
                                    const returnAmount = payment1 - numericTotalPrice;

                                    if (returnAmount >= 0) {
                                        if (returnPayment) {
                                            returnPayment.textContent = 'Rp. ' + addCommas(returnAmount.toString());
                                        }
                                        if (returnPaymentLabel) returnPaymentLabel.classList.remove('hidden');
                                    } else {
                                        if (returnPayment) returnPayment.textContent = '';
                                        if (returnPaymentLabel) returnPaymentLabel.classList.add('hidden');
                                    }
                                }
                            }
                        };
                        totalPayment.addEventListener('keyup', totalPayment._returnCalculator);
                        console.log('✅ total_payment handlers bound (formatter + return calculator)');
                    }

                    // Bind total_payment_two input handler
                    const totalPaymentTwo = document.getElementById('total_payment_two');
                    if (totalPaymentTwo) {
                        // Currency formatter for payment 2
                        totalPaymentTwo.removeEventListener('input', totalPaymentTwo._currencyFormatter);
                        totalPaymentTwo._currencyFormatter = function(e) {
                            const input = e.target;
                            let value = input.value;
                            value = value.replace(/\D/g, '');
                            const formattedValue = new Intl.NumberFormat('en-US', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(value);
                            input.value = formattedValue.replace('IDR', 'Rp');
                        };
                        totalPaymentTwo.addEventListener('input', totalPaymentTwo._currencyFormatter);
                        console.log('✅ total_payment_two formatter bound');
                    }

                    // Bind checkout button handler
                    const checkoutBtn = document.getElementById('checkout_btn');
                    if (checkoutBtn) {
                        checkoutBtn.removeEventListener('click', checkoutBtn._bladeHandler);
                        checkoutBtn._bladeHandler = function(e) {
                            e.preventDefault();
                            console.log('💳💳💳 Checkout button clicked!');

                            // Get all payment values
                            const paymentOption = document.getElementById('payment_option');
                            const paymentMethod = document.getElementById('pm_id_offline');
                            const paymentMethodTwo = document.getElementById('pm_id_offline_two');
                            const paymentTotal = document.getElementById('payment_total');
                            const totalPayment = document.getElementById('total_payment');
                            const totalPaymentTwo = document.getElementById('total_payment_two');
                            const cpId = document.getElementById('cp_id');
                            const cpIdTwo = document.getElementById('cp_id_two');

                            if (!paymentOption || !paymentMethod || !paymentTotal) {
                                console.error('❌ Required payment elements not found');
                                return false;
                            }

                            const paymentOptionValue = paymentOption.value;
                            const paymentOptionLabel = paymentOption.options[paymentOption.selectedIndex].text;
                            const paymentMethodValue = paymentMethod.value;
                            const paymentMethodLabel = paymentMethod.options[paymentMethod.selectedIndex].text;
                            const paymentMethodTwoValue = paymentMethodTwo ? paymentMethodTwo.value : '';
                            const paymentMethodTwoLabel = paymentMethodTwo ? paymentMethodTwo.options[paymentMethodTwo.selectedIndex].text : '';

                            const paymentTotalText = paymentTotal.textContent || '0';
                            const cleanTotal = String(paymentTotalText).replace('Rp. ', '').trim();
                            const numericTotal = replaceComma(cleanTotal);

                            let totalPaymentValue = totalPayment ? totalPayment.value.replace(/Rp|,|\./g, '').trim() : '';
                            totalPaymentValue = totalPaymentValue ? parseFloat(totalPaymentValue.replace(/\./g, '')) : 0;

                            const totalPaymentTwoValue = totalPaymentTwo ? totalPaymentTwo.value : '';

                            // Validation
                            if (numericTotal > 0) {
                                if (paymentOptionValue === '') {
                                    if (typeof swal !== 'undefined') {
                                        swal("Metode Pembayaran", "Silahkan pilih metode pembayaran", "warning");
                                    } else {
                                        alert("Silahkan pilih metode pembayaran");
                                    }
                                    return false;
                                }

                                if (paymentOptionValue !== '' && paymentMethodValue === '') {
                                    if (typeof swal !== 'undefined') {
                                        swal("Jenis Pembayaran", "Silahkan pilih jenis pembayaran", "warning");
                                    } else {
                                        alert("Silahkan pilih jenis pembayaran");
                                    }
                                    return false;
                                }

                                if (paymentOptionLabel !== '2 Metode' && paymentMethodLabel === 'CASH' && totalPaymentValue < numericTotal) {
                                    if (typeof swal !== 'undefined') {
                                        swal("Jumlah Dibayar", "Silahkan periksa jumlah dibayar", "warning");
                                    } else {
                                        alert("Jumlah dibayar kurang dari total bayar");
                                    }
                                    return false;
                                }

                                if ((paymentMethodLabel === 'DEBIT CARD' || paymentMethodLabel === 'CREDIT CARD') && (!cpId || cpId.value === '')) {
                                    if (typeof swal !== 'undefined') {
                                        swal("Penyedia Kartu", "Silahkan pilih penyedia kartu", "warning");
                                    } else {
                                        alert("Silahkan pilih penyedia kartu");
                                    }
                                    return false;
                                }

                                if (paymentOptionLabel === '2 Metode' && paymentMethodTwoValue === '') {
                                    if (typeof swal !== 'undefined') {
                                        swal("Jenis Pembayaran Dua", "Silahkan pilih jenis pembayaran kedua", "warning");
                                    } else {
                                        alert("Silahkan pilih jenis pembayaran kedua");
                                    }
                                    return false;
                                }

                                if ((paymentMethodTwoLabel === 'DEBIT CARD' || paymentMethodTwoLabel === 'CREDIT CARD') && (!cpIdTwo || cpIdTwo.value === '')) {
                                    if (typeof swal !== 'undefined') {
                                        swal("Penyedia Kartu Kedua", "Silahkan pilih penyedia kartu kedua", "warning");
                                    } else {
                                        alert("Silahkan pilih penyedia kartu kedua");
                                    }
                                    return false;
                                }

                                if (paymentMethodTwoValue !== '' && totalPaymentValue === 0) {
                                    if (typeof swal !== 'undefined') {
                                        swal("Jumlah Dibayar Pertama", "Silahkan masukkan jumlah dibayar pertama", "warning");
                                    } else {
                                        alert("Silahkan masukkan jumlah dibayar pertama");
                                    }
                                    return false;
                                }

                                if (paymentMethodTwoValue !== '' && (!totalPaymentTwo || totalPaymentTwo.value === '')) {
                                    if (typeof swal !== 'undefined') {
                                        swal("Jumlah Dibayar Kedua", "Silahkan masukkan jumlah dibayar kedua", "warning");
                                    } else {
                                        alert("Silahkan masukkan jumlah dibayar kedua");
                                    }
                                    return false;
                                }
                            }

                            // Close payment modal first
                            const paymentModal = document.getElementById('payment-offline-popup');
                            if (paymentModal) {
                                console.log('💳 Closing payment modal...');
                                paymentModal.classList.add('hidden');
                                paymentModal.setAttribute('aria-hidden', 'true');
                            }

                            // Show InputCodeModal
                            console.log('💳 Opening InputCodeModal...');
                            const inputCodeModal = document.getElementById('InputCodeModal');
                            if (inputCodeModal) {
                                console.log('💳 InputCodeModal element found');
                                console.log('💳 Modal current classes:', inputCodeModal.className);
                                console.log('💳 Modal current display:', window.getComputedStyle(inputCodeModal).display);

                                // Always try manual show first as primary method
                                console.log('💳 Showing modal manually...');
                                inputCodeModal.classList.remove('hidden');
                                inputCodeModal.setAttribute('aria-hidden', 'false');
                                inputCodeModal.style.display = 'flex';
                                inputCodeModal.style.zIndex = '10000';
                                inputCodeModal.style.position = 'fixed';
                                inputCodeModal.style.top = '0';
                                inputCodeModal.style.left = '0';
                                inputCodeModal.style.right = '0';
                                inputCodeModal.style.bottom = '0';

                                // Add backdrop with higher z-index
                                let backdrop = document.getElementById('input-code-modal-backdrop');
                                if (!backdrop) {
                                    backdrop = document.createElement('div');
                                    backdrop.id = 'input-code-modal-backdrop';
                                    backdrop.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:9999;';
                                    document.body.appendChild(backdrop);
                                    console.log('✅ Backdrop created');
                                }

                                // Prevent body scroll
                                document.body.style.overflow = 'hidden';

                                console.log('✅ InputCodeModal shown manually');
                                console.log('💳 Modal display after show:', window.getComputedStyle(inputCodeModal).display);
                                console.log('💳 Modal visibility:', window.getComputedStyle(inputCodeModal).visibility);
                                console.log('💳 Modal z-index:', window.getComputedStyle(inputCodeModal).zIndex);

                                // Also try Flowbite if available (as secondary)
                                if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                                    try {
                                        let modal = Flowbite.Modal.getInstance(inputCodeModal);
                                        if (!modal) {
                                            modal = new Flowbite.Modal(inputCodeModal, {
                                                backdrop: 'static',
                                                closable: true
                                            });
                                        }
                                        // Don't call show() again, just ensure it's initialized
                                        console.log('💳 Flowbite Modal instance ready');
                                    } catch (err) {
                                        console.warn('⚠️ Flowbite Modal error (ignored, using manual):', err);
                                    }
                                }

                                // Focus on secret code input
                                setTimeout(() => {
                                    const secretCodeInput = document.getElementById('u_secret_code');
                                    if (secretCodeInput) {
                                        secretCodeInput.focus();
                                        console.log('✅ Focused on secret code input');
                                    } else {
                                        console.error('❌ Secret code input not found!');
                                    }
                                }, 300);
                            } else {
                                console.error('❌ InputCodeModal not found in DOM!');
                            }
                        };
                        checkoutBtn.addEventListener('click', checkoutBtn._bladeHandler);
                        console.log('✅ checkout_btn handler bound');
                    }

                }, 300);
            });
        }

        console.log('✅ Simple payment modal handler setup complete');
    });
</script>

<script>
    (function() {
        const modalId = 'shiftEmployeeModal';
        const modal = document.getElementById(modalId);

        function manualShowModal(el) {
            if (!el) return;
            el.classList.remove('hidden');
            el.setAttribute('aria-hidden', 'false');
            el.style.display = 'flex';
            document.body.classList.add('overflow-hidden');
            if (!document.getElementById('manual-modal-backdrop')) {
                const bd = document.createElement('div');
                bd.id = 'manual-modal-backdrop';
                bd.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:49';
                document.body.appendChild(bd);
            }
        }

        function manualHideModal(el) {
            if (!el) return;
            el.classList.add('hidden');
            el.setAttribute('aria-hidden', 'true');
            el.style.display = 'none';
            document.body.classList.remove('overflow-hidden');
            const bd = document.getElementById('manual-modal-backdrop');
            if (bd) bd.remove();
        }

        function attachModalCloseHandlers() {
            if (!modal) return;
            modal.querySelectorAll('[data-modal-hide]').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    try {
                        // Try Flowbite hide first
                        if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                            const inst = Flowbite.Modal.getInstance(modal);
                            if (inst && typeof inst.hide === 'function') {
                                inst.hide();
                                return;
                            }
                        }
                    } catch (err) {
                        // ignore
                    }
                    manualHideModal(modal);
                });
            });
        }

        // Attach click on shift button(s) to open modal
        function attachShiftOpen() {
            ['shift-btn','shift-employee-btn'].forEach(function(id) {
                const btn = document.getElementById(id);
                if (btn) {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        try {
                            if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                                const inst = Flowbite.Modal.getInstance(modal);
                                if (inst && typeof inst.show === 'function') {
                                    inst.show();
                                    return;
                                } else {
                                    // create instance if needed
                                    try { new Flowbite.Modal(modal); } catch(e) {}
                                    const inst2 = Flowbite.Modal.getInstance(modal);
                                    if (inst2 && typeof inst2.show === 'function') {
                                        inst2.show();
                                        return;
                                    }
                                }
                            }
                        } catch(err) {
                            // fallback to manual
                        }
                        manualShowModal(modal);
                    });
                }
            });
        }

        // Start/Stop shift logic (UI-first; optional AJAX)
        let shiftInterval = null;
        let shiftStartTs = null;

        function formatElapsed(ms) {
            const total = Math.max(0, Math.floor(ms / 1000));
            const h = String(Math.floor(total / 3600)).padStart(2, '0');
            const m = String(Math.floor((total % 3600) / 60)).padStart(2, '0');
            const s = String(total % 60).padStart(2, '0');
            return `${h}:${m}:${s}`;
        }

        function updateClockDisplay() {
            const el = document.getElementById('shift-clock');
            if (!el || !shiftStartTs) return;
            el.textContent = formatElapsed(Date.now() - shiftStartTs);
        }

        function startShift() {
            try { window.__shiftActionInFlight = Date.now(); } catch(e) {}
            // UI updates
            document.getElementById('startShiftButton')?.classList.add('hidden');
            document.getElementById('stopShiftButton')?.classList.remove('hidden');
            document.getElementById('shiftStatus') && (document.getElementById('shiftStatus').textContent = 'Shift In Progress');
            document.getElementById('shift-status-badge') && (document.getElementById('shift-status-badge').textContent = 'Shift In Progress');

            shiftStartTs = Date.now();
            updateClockDisplay();
            if (shiftInterval) clearInterval(shiftInterval);
            shiftInterval = setInterval(updateClockDisplay, 1000);

            // Hide the modal immediately (prefer Flowbite, fallback to manual)
            try {
                if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                    const inst = Flowbite.Modal.getInstance(modal);
                    if (inst && typeof inst.hide === 'function') {
                        inst.hide();
                    } else {
                        try { new Flowbite.Modal(modal); } catch(e) {}
                        const inst2 = Flowbite.Modal.getInstance(modal);
                        if (inst2 && typeof inst2.hide === 'function') inst2.hide();
                        else manualHideModal(modal);
                    }
                } else {
                    manualHideModal(modal);
                }
            } catch (err) {
                try { manualHideModal(modal); } catch(e){}
            }

            // Notify server; reload after success (tolerate errors)
            try {
                if (window.jQuery) {
                    var __shiftRequestStart = Date.now();
                    $.ajax({
                        url: '/user_start_shift',
                        method: 'POST',
                        data: { _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function() {
                            try{
                                var latency = Date.now() - __shiftRequestStart;
                                window.__shiftActionTimeoutMs = Math.max(2000, Math.min(10000, latency * 2 + 500));
                            } catch(e){}
                            try{ window.__shiftActionInFlight = null; }catch(e){}
                            console.log('Shift started (server ok)');
                            location.reload();
                        },
                        error: function() {
                            try{
                                var latency = Date.now() - __shiftRequestStart;
                                window.__shiftActionTimeoutMs = Math.max(2000, Math.min(10000, latency * 2 + 500));
                            } catch(e){}
                            try{ window.__shiftActionInFlight = null; }catch(e){}
                            console.warn('Start shift server call failed (ignored)');
                            setTimeout(function(){ location.reload(); }, 500);
                        }
                    });
                    return;
                }
            } catch(e) {
                // ignore
            }

            // If no jQuery/ajax available, reload after short delay
            setTimeout(function(){ location.reload(); }, 700);
        }

        function stopShift() {
            document.getElementById('startShiftButton')?.classList.remove('hidden');
            document.getElementById('stopShiftButton')?.classList.add('hidden');
            document.getElementById('shiftStatus') && (document.getElementById('shiftStatus').textContent = 'Shift not started');
            document.getElementById('shift-status-badge') && (document.getElementById('shift-status-badge').textContent = 'Shift not started');

            if (shiftInterval) {
                clearInterval(shiftInterval);
                shiftInterval = null;
            }
            shiftStartTs = null;
            const el = document.getElementById('shift-clock'); if (el) el.textContent = '00:00:00';

            // Optional: notify server; tolerate errors
            try {
                if (window.jQuery) {
                    var __shiftRequestStart = Date.now();
                    $.ajax({
                        url: '/user_end_shift',
                        method: 'POST',
                        data: { _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function() {
                            try{
                                var latency = Date.now() - __shiftRequestStart;
                                window.__shiftActionTimeoutMs = Math.max(2000, Math.min(10000, latency * 2 + 500));
                            } catch(e){}
                            try{ window.__shiftActionInFlight = null; }catch(e){}
                            console.log('Shift stopped (server ok)');
                        },
                        error: function() {
                            try{
                                var latency = Date.now() - __shiftRequestStart;
                                window.__shiftActionTimeoutMs = Math.max(2000, Math.min(10000, latency * 2 + 500));
                            } catch(e){}
                            try{ window.__shiftActionInFlight = null; }catch(e){}
                            console.warn('Stop shift server call failed (ignored)');
                        }
                    });
                }
            } catch(e) {}

            // After stopping, show the end-shift / input modal if present.
            try {
                // Prefer old InputLabaShift id if exists, else show v2 shift detail modal
                const preferIds = ['InputLabaShift', 'modal-shift-detail', 'shiftDetailModal'];
                let shown = false;
                for (const id of preferIds) {
                    const el = document.getElementById(id);
                    if (!el) continue;
                    try {
                        if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                            try { new Flowbite.Modal(el); } catch(e) {}
                            const inst = Flowbite.Modal.getInstance(el);
                            if (inst && typeof inst.show === 'function') { inst.show(); shown = true; break; }
                        }
                    } catch(e) {}
                    // jQuery/Bootstrap fallback
                    try { if (window.jQuery && jQuery.fn.modal) { jQuery('#' + id).modal('show'); shown = true; break; } } catch(e) {}
                    // Manual fallback
                    try { manualShowModal(el); shown = true; break; } catch(e) {}
                }

                // Try to redraw tables if available (safe guards)
                try { if (typeof product !== 'undefined' && product && typeof product.draw === 'function') product.draw(false); } catch(e) {}
                try { if (typeof detail_shift !== 'undefined' && detail_shift && typeof detail_shift.draw === 'function') detail_shift.draw(); } catch(e) {}
            } catch(e) {
                console.warn('Error showing end-shift modal or redrawing tables', e);
            }
        }

        function attachShiftButtons() {
            const startBtn = document.getElementById('startShiftButton');
            const stopBtn = document.getElementById('stopShiftButton');
            if (startBtn) {
                startBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    startShift();
                });
            }
            if (stopBtn) {
                stopBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    stopShift();
                });
            }
        }

        // Init now
        try { attachModalCloseHandlers(); } catch(e) {}
        try { attachShiftOpen(); } catch(e) {}
        try { attachShiftButtons(); } catch(e) {}

        // Expose for debugging
        function markShiftStartedFromServer(shiftStart) {
            try {
                document.getElementById('startShiftButton')?.classList.add('hidden');
                document.getElementById('stopShiftButton')?.classList.remove('hidden');
                document.getElementById('shiftStatus') && (document.getElementById('shiftStatus').textContent = 'Shift In Progress');
                document.getElementById('shift-status-badge') && (document.getElementById('shift-status-badge').textContent = 'Shift In Progress');

                // If server provided a start time (HH:MM), initialize clock based on today's date
                if (shiftStart && typeof shiftStart === 'string' && shiftStart.match(/^[0-9]{1,2}:[0-9]{2}$/)) {
                    const parts = shiftStart.split(':');
                    const hh = parseInt(parts[0], 10) || 0;
                    const mm = parseInt(parts[1], 10) || 0;
                    const d = new Date();
                    d.setHours(hh, mm, 0, 0);
                    shiftStartTs = d.getTime();
                    try { updateClockDisplay(); } catch(e) {}
                    if (shiftInterval) clearInterval(shiftInterval);
                    shiftInterval = setInterval(updateClockDisplay, 1000);
                }
            } catch (e) {
                console.warn('markShiftStartedFromServer error', e);
            }
        }

        window.__manualShiftHelpers = {
            manualShowModal, manualHideModal, startShift, stopShift, markShiftStartedFromServer
        };
    })();
    var f = document.getElementById('f_add_voucher');
    if (f) {
        f.addEventListener('submit', function(ev) {
            ev.preventDefault();
            console.log('inline: f_add_voucher submit');

            try {
                // Build FormData compatible with server's expected `formData` (serializeArray)
                var formData = new FormData();
                var inputs = f.querySelectorAll('input[name="voucher-list[]"]');
                var sa = [];
                inputs.forEach(function(inp) {
                    sa.push({ name: 'voucher-list[]', value: inp.value || '' });
                });
                // Append as indexed array of objects to mimic jQuery.serializeArray()
                sa.forEach(function(obj, idx) {
                    formData.append('formData[' + idx + '][name]', obj.name);
                    formData.append('formData[' + idx + '][value]', obj.value);
                });

                // Also include 'item' as JSON string so server can parse array/object
                formData.append('item', JSON.stringify(window.shoes_voucher_temp || []));

                // CSRF token
                var tokenMeta = document.querySelector('meta[name="csrf-token"]');
                if (tokenMeta) formData.append('_token', tokenMeta.getAttribute('content'));

                console.log('inline: sending verify-vouchers-v2 payload', { formDataArray: sa, item: window.shoes_voucher_temp || [] });

                fetch("{{ url('verify-vouchers-v2') }}", {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                }).then(function(resp) {
                    return resp.text().then(function(txt) {
                        console.log('inline: verify-vouchers-v2 response status', resp.status, txt);
                        var parsed = null;
                        try { parsed = JSON.parse(txt); } catch (e) { /* not json */ }

                        if (resp.ok) {
                            // success - show toast and close modal
                            var message = (parsed && parsed.message) ? parsed.message : 'Voucher verified';
                            if (typeof window.showToast === 'function') window.showToast('success', message);
                            else if (typeof window.toast === 'function') window.toast('success', message);
                            else {
                                // fallback simple toast
                                var t = document.createElement('div');
                                t.textContent = message;
                                t.style.position = 'fixed';
                                t.style.right = '20px';
                                t.style.top = '20px';
                                t.style.background = '#16a34a';
                                t.style.color = '#fff';
                                t.style.padding = '10px 14px';
                                t.style.borderRadius = '6px';
                                document.body.appendChild(t);
                                setTimeout(function(){ t.remove(); }, 3500);
                            }
                            // close modal if Flowbite/manual close supported
                            var hideBtn = document.querySelector('[data-modal-hide="modal-voucher"]');
                            if (hideBtn) hideBtn.click();
                        } else {
                            var errMsg = (parsed && parsed.message) ? parsed.message : txt || 'Verify failed';
                            if (typeof window.showToast === 'function') window.showToast('error', errMsg);
                            else if (typeof window.toast === 'function') window.toast('error', errMsg);
                            else {
                                var t2 = document.createElement('div');
                                t2.textContent = errMsg;
                                t2.style.position = 'fixed';
                                t2.style.right = '20px';
                                t2.style.top = '20px';
                                t2.style.background = '#dc2626';
                                t2.style.color = '#fff';
                                t2.style.padding = '10px 14px';
                                t2.style.borderRadius = '6px';
                                document.body.appendChild(t2);
                                setTimeout(function(){ t2.remove(); }, 4500);
                            }
                        }
                    });
                }).catch(function(err) {
                    console.error('inline: fetch error', err);
                    if (typeof window.showToast === 'function') window.showToast('error', 'Network error');
                });
            } catch (err) {
                console.error('inline: prepare fetch error', err);
            }
        });
    } else {
        console.warn('inline: f_add_voucher form not found');
    }
</script>

<script>
    // Wait for jQuery to be ready
    (function() {
        window.convertBootstrapToFlowbite = function($container) {
            $container.find('ul.dropdown-menu, ul').each(function() {
                const $ul = $(this);
                if (!$ul.hasClass('divide-y')) {
                    $ul.removeClass('dropdown-menu form-control').addClass('divide-y divide-gray-200');
                }
            });

            // Convert Bootstrap buttons to Flowbite buttons (Same styling as pos_v2)
            $container.find('a.btn, button.btn, a[id="add_to_item_list"]').each(function() {
                const $btn = $(this);
                const classes = $btn.attr('class') || '';
                const id = $btn.attr('id') || '';

                // Remove Bootstrap classes
                $btn.removeClass('btn btn-sm btn-inventory btn-primary btn-lg btn-info col-12');

                // Add Flowbite classes based on original style (matching pos_v2)
                if (classes.includes('btn-inventory') || classes.includes('btn-primary') || id === 'add_to_item_list') {
                    $btn.addClass('block w-full px-4 py-3 text-left text-sm font-medium text-gray-900 hover:bg-gray-50 cursor-pointer transition-colors');
                } else if (classes.includes('btn-info')) {
                    $btn.addClass('inline-block px-3 py-1.5 text-xs font-semibold text-white bg-blue-500 rounded-lg mr-2 mb-2');
                } else {
                    $btn.addClass('block w-full px-4 py-3 text-left text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer transition-colors');
                }
            });

            // Convert Bootstrap spans to Flowbite badges (Same as pos_v2)
            $container.find('span.btn-lg, span[class*="btn-"]').each(function() {
                const $span = $(this);
                const classes = $span.attr('class') || '';
                $span.removeClass('btn-lg btn-primary btn-info');

                if (classes.includes('btn-primary')) {
                    $span.addClass('inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-orange-500 text-white mr-1');
                } else if (classes.includes('btn-info')) {
                    $span.addClass('inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-blue-500 text-white mr-1');
                }
            });
        };

        // Override fadeIn/fadeOut for itemList to use Flowbite classes
        // IMPORTANT: This must run BEFORE offline_pos_js is loaded
        $(document).ready(function() {
            const originalFadeIn = $.fn.fadeIn;
            const originalFadeOut = $.fn.fadeOut;

            $.fn.fadeIn = function(speed, callback) {
                const elementId = this.attr('id');

                if (elementId === 'itemList') {
                    // For product search - ensure it shows and stays visible
                    console.log('fadeIn called for itemList');
                    this.removeClass('hidden').addClass('block');
                    // Force display to ensure visibility
                    this.css('display', 'block');
                    // Remove converted class to allow re-conversion if needed
                    this.removeClass('converted');
                    // HTML conversion is handled in html() override
                    if (callback) callback();
                    return this;
                } else if (elementId === 'itemListCust') {
                    // For customer search - handled by customer search script
                    this.removeClass('hidden').addClass('block');
                    if (callback) callback();
                    return this;
                }
                return originalFadeIn.apply(this, arguments);
            };

            $.fn.fadeOut = function(speed, callback) {
                const elementId = this.attr('id');

                if (elementId === 'itemList') {
                    // Only fadeOut if itemList is empty or has no meaningful content
                    const htmlContent = this.html().trim();
                    const hasContent = htmlContent.length > 100; // More than just loading/error message

                    console.log('fadeOut called for itemList, hasContent:', hasContent, 'htmlLength:', htmlContent.length);

                    if (!hasContent) {
                        // Only hide if no content
                        this.addClass('hidden').removeClass('block');
                        if (callback) callback();
                        return this;
                    } else {
                        // Don't hide if there's content - keep it visible
                        console.log('Preventing fadeOut for itemList with content');
                        if (callback) callback();
                        return this;
                    }
                } else if (elementId === 'itemListCust') {
                    this.addClass('hidden').removeClass('block');
                    if (callback) callback();
                    return this;
                }
                return originalFadeOut.apply(this, arguments);
            };

            // Intercept html() method for itemList to convert Bootstrap to Flowbite
            const originalHtml = $.fn.html;
            let lastLargeContent = null; // Store last large content to prevent clearing

            $.fn.html = function(content) {
                const elementId = this.attr('id');

                if (elementId === 'itemList') {
                    if (content !== undefined && content !== null && content !== '') {
                        const contentLength = typeof content === 'string' ? content.length : 0;
                        const currentHtml = this.html();
                        const currentLength = currentHtml.length;

                        console.log('=== itemList.html() called ===');
                        console.log('New content length:', contentLength);
                        console.log('Current HTML length:', currentLength);

                        // Prevent clearing large content with small content
                        if (currentLength > 1000 && contentLength < 200) {
                            console.warn('⚠️ Preventing HTML clear: current has', currentLength, 'chars, new has', contentLength, 'chars');
                            // Don't clear - keep existing content
                            return this;
                        }

                        // Only process if content is substantial (not empty/error message)
                        if (contentLength > 100) {
                            // Store large content
                            lastLargeContent = content;

                            // Remove converted class to allow re-conversion
                            this.removeClass('converted');

                            // Set HTML first (original behavior)
                            const result = originalHtml.apply(this, arguments);

                            // Ensure itemList is visible immediately and stays visible
                            this.removeClass('hidden').addClass('block').css('display', 'block');

                            // Then convert after HTML is set
                            const self = this;
                            setTimeout(function() {
                                // Double-check visibility and content
                                const currentHtml = self.html();
                                if (currentHtml.length < 100) {
                                    console.warn('⚠️ itemList content was cleared after setting, restoring...');
                                    // Restore if we have stored content
                                    if (lastLargeContent && lastLargeContent.length > 100) {
                                        console.log('Restoring content, length:', lastLargeContent.length);
                                        originalHtml.call(self, lastLargeContent);
                                        self.removeClass('hidden').addClass('block').css('display', 'block');
                                    }
                                    return;
                                }

                                if (!self.hasClass('block')) {
                                    self.removeClass('hidden').addClass('block').css('display', 'block');
                                }

                                // Find all product items
                                const $items = self.find('li a#add_to_item_list');
                                console.log('Found product items in itemList:', $items.length);

                                if ($items.length === 0) {
                                    console.warn('No product items found. HTML preview:', currentHtml.substring(0, 300));
                                    // Still keep it visible if there's content
                                    if (currentHtml.length > 100) {
                                        self.removeClass('hidden').addClass('block').css('display', 'block');
                                    }
                                    return;
                                }

                                $items.each(function() {
                                    const $link = $(this);
                                    const $li = $link.parent('li');

                                    // Skip if already converted
                                    if ($li.hasClass('product-item')) {
                                        return;
                                    }

                                    // Convert to Flowbite product item structure
                                    $li.addClass('product-item p-4 bg-white hover:bg-gray-50 cursor-pointer transition-colors text-sm');
                                    $li.removeClass('btn btn-sm btn-inventory col-12');

                                    // Extract product data from data attributes
                                    const pName = $link.attr('data-p_name') || '';
                                    const bin = $link.attr('data-bin') || '';
                                    const sellPrice = $link.attr('data-sell_price') || '0';
                                    const productImage = $link.attr('data-image') || '';
                                    const brandName = $link.attr('data-brand') || '';

                                    // Parse pName to extract display name and brand if not in data attributes
                                    let displayName = pName;
                                    let extractedBrand = brandName;

                                    if (!extractedBrand && pName) {
                                        // Extract brand from pName format: [BRAND] ARTICLE NAME COLOR [SIZE]
                                        const brandMatch = pName.match(/^\[([^\]]+)\]/);
                                        if (brandMatch) {
                                            extractedBrand = brandMatch[1];
                                            // Remove brand and article from display name
                                            displayName = pName.replace(/^\[[^\]]+\]\s*\d+\s*/, '').replace(/\s*\[[^\]]+\]\s*$/, '').trim();
                                        }
                                    }

                                    // Create product image URL with fallback
                                    const imageUrl = productImage && productImage.trim() !== ''
                                        ? productImage
                                        : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(extractedBrand || 'BRAND') + '&background=F74040&color=fff&size=60';

                                    // Create brand badge
                                    const brandBadge = extractedBrand
                                        ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-orange-500 text-white">${extractedBrand}</span>`
                                        : '';

                                    // Create Flowbite-styled product item
                                    const binBadges = window.formatBinToBadges ? window.formatBinToBadges(bin) : '';
                                    const formattedPrice = window.formatNumber ? window.formatNumber(parseFloat(sellPrice)) : sellPrice;

                                    // Replace link content with formatted content (matching point_of_sale_v2 structure)
                                    $link.html(`
                                            <div class="flex justify-between items-start gap-3">
                                                <div class="flex items-start gap-3 flex-1">
                                                    <img src="${imageUrl}" alt="${displayName}" class="w-10 h-10 object-cover rounded flex-shrink-0" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(extractedBrand || 'BRAND')}&background=F74040&color=fff&size=60'">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="mb-2 flex items-center gap-2 flex-wrap">
                                                            ${brandBadge}
                                                            <span class="block text-gray-900 font-semibold">${displayName}</span>
                                                        </div>
                                                        <div class="flex flex-wrap gap-1 items-center mb-1">
                                                            ${binBadges}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-right ml-3 flex-shrink-0">
                                                    <div class="mb-1">
                                                        <strong class="text-red-600">Rp. ${formattedPrice}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        `);
                                });

                                // Mark as converted and ensure visibility
                                self.addClass('converted');
                                self.removeClass('hidden').addClass('block').css('display', 'block');
                                console.log('✅ itemList conversion complete, items:', self.find('.product-item').length);
                            }, 200);

                            return result;
                        } else {
                            // Small content (loading/error) - only set if current is also small
                            if (currentLength < 200) {
                                return originalHtml.apply(this, arguments);
                            } else {
                                console.log('Ignoring small content, keeping existing large content');
                                return this;
                            }
                        }
                    } else if (content === '' || content === null) {
                        // Empty content - only allow clearing if current is also small
                        const currentHtml = this.html();
                        if (currentHtml.length < 200) {
                            console.log('itemList.html() called with empty content - clearing (current is small)');
                            return originalHtml.apply(this, arguments);
                        } else {
                            console.warn('⚠️ Preventing clear: current has', currentHtml.length, 'chars');
                            return this;
                        }
                    }
                } else if (elementId === 'itemListCust') {
                    // Customer list conversion is handled by customer search script
                    // Just ensure Flowbite structure - don't convert here to avoid duplication
                }

                return originalHtml.apply(this, arguments);
            };

            // Format number helper (same as pos_v2) - Define BEFORE html() override
            window.formatNumber = function(num) {
                if (num === null || num === undefined || isNaN(num)) return '0';
                return new Intl.NumberFormat('id-ID').format(num || 0);
            };

            window.formatBinToBadges = function(binString) {
                if (!binString || binString.trim() === '') {
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">No Location</span>';
                }

                const regex = /\[([^\]]+)\]\s*\[([^\]]+)\]/g;
                const badges = [];
                let match;
                let hasMatch = false;

                while ((match = regex.exec(binString)) !== null) {
                    hasMatch = true;
                    const locationCode = match[1].trim();
                    const quantity = parseInt(match[2]) || 0;

                    // Skip if location code is "0" or empty
                    if (locationCode === '0' || locationCode === '') {
                        continue;
                    }

                    // Determine badge color based on location code and quantity
                    let badgeClass = quantity > 0 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500';

                    // Create badge with icon
                    const icon = quantity > 0 ? '<i class="cft-standard-solid cft-check-round text-green-600 text-sm"></i>' : '<i class="cft-standard-solid cft-cancel text-red-500 text-sm"></i>';
                    badges.push(`
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium ${badgeClass}">
                                ${icon} <strong>${locationCode}</strong>: ${window.formatNumber(quantity)}
                            </span>
                        `);
                }

                // If no matches found, try to parse as simple format or return default
                if (!hasMatch && binString.trim() !== '') {
                    // Try to extract location and quantity from any format
                    const simpleMatch = binString.match(/\[([^\]]+)\]/);
                    if (simpleMatch) {
                        return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">${simpleMatch[1]}</span>`;
                    }
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">No Location</span>';
                }

                return badges.length > 0 ? badges.join('') : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">No Location</span>';
            };
        });
    })();
</script>
<script>
    $(document).ready(function() {
        window.__shiftActionInFlight = null;
        window.__shiftActionTimeoutMs = 3000;
        function checkShiftStatus() {
            try {
                const timeoutMs = (window.__shiftActionTimeoutMs && typeof window.__shiftActionTimeoutMs === 'number') ? window.__shiftActionTimeoutMs : 3000;
                if (window.__shiftActionInFlight && (Date.now() - window.__shiftActionInFlight) < timeoutMs) {
                    return;
                }
            } catch(e) {}

            $.ajax({
                url: '/check_user_shift',
                method: 'GET',
                success: function(response) {
                    const $badge = $('#shift-status-badge');
                    if ($badge.length) {
                        if (response && response.status === '200') {
                            $badge.text('Shift In Progress').removeClass('bg-yellow-500').addClass('bg-green-500');
                            try {
                                if (window.__manualShiftHelpers && typeof window.__manualShiftHelpers.markShiftStartedFromServer === 'function') {
                                    window.__manualShiftHelpers.markShiftStartedFromServer(response.shift_start || response.shiftStart || '');
                                }
                            } catch(e) { console.warn('markShiftStartedFromServer failed', e); }
                        } else {
                            $badge.text('Shift not started').removeClass('bg-green-500').addClass('bg-yellow-500');
                            try {
                                document.getElementById('startShiftButton')?.classList.remove('hidden');
                                document.getElementById('stopShiftButton')?.classList.add('hidden');
                                document.getElementById('shiftStatus') && (document.getElementById('shiftStatus').textContent = 'Shift not started');
                            } catch(e) {}
                        }
                    }
                },
                error: function() {
                    const $badge = $('#shift-status-badge');
                    if ($badge.length) {
                        $badge.text('Shift not started').addClass('bg-yellow-500');
                    }
                }
            });
        }

        setTimeout(checkShiftStatus, 500);

        $(document).on('click', '#shift-btn, #shift-employee-btn', function() {
            setTimeout(checkShiftStatus, 500);
        });
    });
</script>

<script>
    $(document).ready(function() {
        setTimeout(function() {
            const totalPaymentEl = document.getElementById('total_payment');
            if (totalPaymentEl && !totalPaymentEl.hasAttribute('data-listener-added')) {
                totalPaymentEl.addEventListener('input', function(e) {
                    const input = e.target;
                    let value = input.value;
                    value = value.replace(/\D/g, '');
                    if (value) {
                        const formatted = new Intl.NumberFormat('id-ID').format(value);
                        input.value = 'Rp. ' + formatted;
                    }
                });
                totalPaymentEl.setAttribute('data-listener-added', 'true');
            }
        }, 1000);
    });
</script>

<script>
    $(document).ready(function() {
        $('#cust_id_label').off('keyup focus change');

        let customerSearchTimeout;
        $('#cust_id_label').on('keyup', function() {
            const query = $(this).val().trim();
            const $autocomplete = $('#itemListCust');

            clearTimeout(customerSearchTimeout);

            if (query.length < 3) {
                $autocomplete.addClass('hidden').removeClass('block').empty();
                $('#customer-badge').addClass('hidden').removeClass('flex');
                return;
            }

            customerSearchTimeout = setTimeout(function() {
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
                        type: 'cust',
                        division_type: $('#std_id option:selected').text().toUpperCase()
                    },
                    success: function(data) {
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
                                // Check if query looks like a phone number
                                const isPhoneLike = /^[\+]?[0-9\s\-]+$/.test(query) && query.replace(/[\s\-]/g, '').length >= 8;
                                let html = '<ul class="divide-y divide-gray-200">';
                                if (isPhoneLike) {
                                    html += `
                                            <li class="p-2.5 text-center">
                                                <button type="button" id="add_new_customer" data-modal-target="modal-customer" data-modal-toggle="modal-customer" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-red-700 hover:bg-red-900 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m-6 0H6"></path>
                                                    </svg>
                                                    Tambah Customer Baru (${query})
                                                </button>
                                            </li>
                                        `;
                                } else {
                                    html += '<li class="p-2.5 text-gray-500 text-xs text-center">Tidak ditemukan</li>';
                                }
                                html += '</ul>';
                                $autocomplete.html(html).removeClass('hidden').addClass('block');
                            }
                        } else {
                            $autocomplete.html('<ul class="divide-y divide-gray-200"><li class="p-2.5 text-gray-500 text-xs text-center">Tidak ditemukan</li></ul>').removeClass('hidden').addClass('block');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error searching customers:', error, xhr);
                        let errorMsg = 'Error searching customers';
                        if (xhr.status === 419) {
                            errorMsg = 'CSRF token mismatch. Please refresh the page.';
                        } else if (xhr.status === 500) {
                            errorMsg = 'Server error. Please try again.';
                        }
                        $autocomplete.html('<ul class="divide-y divide-gray-200"><li class="p-4 text-red-600">' + errorMsg + '</li></ul>').removeClass('hidden').addClass('block');
                    }
                });
            }, 300);
        });

        // Handle customer selection
        $(document).on('click', '.customer-item, #add_to_item_list_cust', function() {
            const custId = $(this).attr('data-id') || $(this).closest('[data-id]').attr('data-id');
            const $item = $(this);

            const custName = $item.find('.text-xs.font-semibold').first().text().trim() || $item.text().trim();
            const custPhone = $item.find('.bg-gray-100').first().text().trim() || '';
            const custType = $item.find('.bg-red-100').first().text().trim() || '';

            $('#cust_id').val(custId);
            $('#cust_id_label').val(custName);
            $('#itemListCust').addClass('hidden').removeClass('block').empty();

            // Show customer badge
            $('#customer-badge .customer-name').text(custName);
            $('#customer-badge .customer-type').text(custType || 'CUSTOMER');
            $('#customer-badge').removeClass('hidden').addClass('flex');

            // Show posContent
            $('#posContent').show();
        });

        // Function to load customer detail into modal (compatible with old POS)
        function loadCustomerDetail(custId) {
            if (!custId) {
                if (typeof showToast === 'function') {
                    showToast('Silahkan pilih customer terlebih dahulu', 'warning');
                }
                return;
            }

            // Set loading state for all detail fields
            $('#modal-customer-detail').find('[id^="detail-"]').each(function() {
                $(this).html('<span class="text-gray-400 text-xs">Loading...</span>');
            });

            // Open modal (prefer Flowbite, fallback to manual)
            const modalEl = document.getElementById('modal-customer-detail');
            if (modalEl) {
                if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                    try {
                        let modal = Flowbite.Modal.getInstance(modalEl);
                        if (!modal) modal = new Flowbite.Modal(modalEl);
                        modal.show();
                    } catch (e) {
                        $(modalEl).removeClass('hidden');
                    }
                } else {
                    $(modalEl).removeClass('hidden');
                }
            }

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            $.ajax({
                url: '/check_customer',
                method: 'POST',
                data: { _cust_id: custId },
                dataType: 'text',
                success: function(response) {
                    let data;
                    try {
                        data = JSON.parse(response);
                    } catch (err) {
                        console.error('Error parsing customer detail response', err, response);
                        if (typeof showToast === 'function') showToast('Error parsing response dari server', 'error');
                        if (modalEl) $(modalEl).addClass('hidden');
                        return;
                    }

                    if (data && (data.status === '200' || data.status === 200)) {
                        const ctId = data.ct_id || data.ctid || null;
                        let ctName = '-';
                        if (ctId) {
                            const $opt = $('#modal-customer #ct_id option[value="' + ctId + '"]');
                            if ($opt.length) ctName = $opt.text(); else ctName = 'ID: ' + ctId;
                        }

                        $('#detail-ct_id').text(ctName);
                        $('#detail-cust_name').text(data.cust_name || '-');
                        $('#detail-cust_store').text(data.cust_store || '-');
                        $('#detail-cust_phone').text(data.cust_phone || '-');
                        $('#detail-cust_email').text(data.cust_email || '-');
                        $('#detail-cust_province').text(data.cust_province_name || data.cust_province || '-');
                        $('#detail-cust_city').text(data.cust_city_name || data.cust_city || '-');
                        $('#detail-cust_subdistrict').text(data.cust_subdistrict_name || data.cust_subdistrict || '-');
                        $('#detail-cust_address').text(data.cust_address || '-');

                        const isActive = (data.cust_token_active == 1 || data.cust_token_active === '1');
                        const statusHtml = isActive ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>' : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Non-Active</span>';
                        $('#detail-cust_token_active').html(statusHtml);
                    } else {
                        if (typeof showToast === 'function') showToast('Gagal memuat detail customer', 'error');
                        if (modalEl) $(modalEl).addClass('hidden');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error loading customer detail:', xhr, status, error);
                    const modalEl2 = document.getElementById('modal-customer-detail');
                    if (typeof showToast === 'function') showToast('Error memuat detail customer', 'error');
                    if (modalEl2) $(modalEl2).addClass('hidden');
                }
            });
        }

        // Open customer detail modal when info button is clicked
        $(document).on('click', '#customer-detail-btn', function() {
            const custId = $('#cust_id').val() || '';
            loadCustomerDetail(custId);
        });
    });
</script>

<!-- Product Search Script (Using JSON endpoint like point_of_sale_v2) -->
<script>
    $(document).ready(function() {
        // Override product search handler from offline_pos_js
        // Unbind existing handlers first to prevent duplication
        $('#product_name_input').off('keyup focus change');

        // Product Search with autocomplete - using JSON endpoint (like point_of_sale_v2)
        let productSearchTimeout;
        $('#product_name_input').on('keyup', function() {
            const query = $(this).val().trim();
            const $autocomplete = $('#itemList');

            clearTimeout(productSearchTimeout);

            if (query.length < 2) {
                $autocomplete.addClass('hidden').removeClass('block').empty();
                return;
            }

            productSearchTimeout = setTimeout(function() {
                const csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: '/search_product_offline_v2',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        query: query,
                        type: $('#std_id option:selected').text() || '',
                        _item_type: $('#item_type').val() || 'store',
                        _std_id: $('#std_id').val() || '',
                        _st_id: $('#st_id').val() || '',
                        _token: csrfToken
                    },
                    success: function(products) {
                        console.log('=== Product Search Response (JSON) ===');
                        console.log('Products received:', products);
                        console.log('Products count:', products ? products.length : 0);

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
                                const binBadges = window.formatBinToBadges ? window.formatBinToBadges(product.bin) : '';

                                const productImage = product.image || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(product.brand || 'BRAND') + '&background=F74040&color=fff&size=60';
                                const brandName = product.brand || 'BRAND';

                                // Build data attributes for offline_pos_js compatibility
                                const dataAttrs = `
                                        data-pst_id="${product.id}"
                                        data-psc_id="${product.psc_id || ''}"
                                        data-pl_id="${product.pl_id || ''}"
                                        data-pls_qty="${product.pls_qty || 0}"
                                        data-plst_id="${product.plst_id || ''}"
                                        data-sell_price="${product.price}"
                                        data-sell_price_discount="${product.disc_rp || 0}"
                                        data-bandrol="${product.bandrol || product.price}"
                                        data-ps_qty="${product.qty || 0}"
                                        data-p_name="[${brandName}] ${product.article_id || ''} ${product.name}"
                                        data-bin="${product.bin}"
                                        data-brand="${brandName}"
                                        data-image="${product.image || ''}"
                                        data-b1g1_id="${product.b1g1_id || ''}"
                                        data-b1g1_price="${product.b1g1_price || ''}"
                                    `;

                                html += `
                                        <li class="product-item p-4 bg-white hover:bg-gray-50 cursor-pointer transition-colors text-sm"
                                            data-product='${JSON.stringify(product).replace(/'/g, "&#39;")}'>
                                            <a id="add_to_item_list" class="block" ${dataAttrs}>
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
                                                            <strong class="text-red-600">Rp. ${window.formatNumber ? window.formatNumber(product.price) : product.price}</strong>
                                                        </div>
                                                        ${product.disc_percent > 0 ? `<small class="text-green-600 block"><i class="fas fa-tag"></i> Diskon ${product.disc_percent}%</small>` : ''}
                                                        ${product.disc_rp > 0 ? `<small class="text-green-600 block"><i class="fas fa-money-bill-wave"></i> Rp. ${window.formatNumber ? window.formatNumber(product.disc_rp) : product.disc_rp}</small>` : ''}
                                                    </div>
                                                </div>
                                            </a>
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

                        let errorMsg = 'Error searching products';
                        if (xhr.status === 419) {
                            errorMsg = 'CSRF token mismatch. Please refresh the page.';
                        } else if (xhr.status === 500) {
                            errorMsg = 'Server error. Please try again.';
                        } else if (xhr.status === 0) {
                            errorMsg = 'Network error. Please check your connection.';
                        }

                        $autocomplete.html('<ul class="divide-y divide-gray-200"><li class="p-4 text-red-600">' + errorMsg + '</li></ul>').removeClass('hidden').addClass('block');
                    }
                });
            }, 300);
        });
    });
</script>

<!-- Product Search Handler - Ensure it works with offline_pos_js -->
<script>
    // This must run BEFORE offline_pos_js to prevent it from using autocomplete_by_waiting
    // AND to prevent it from adding Bootstrap rows to the table
    (function() {
        function blockOfflinePosHandler() {
            if (typeof jQuery !== 'undefined') {
                // 1. Block autocomplete_by_waiting AJAX calls
                const originalJQueryAjax = jQuery.ajax;
                jQuery.ajax = function(options) {
                    // Block autocomplete_by_waiting calls from offline_pos_js (not from our custom handler)
                    if (options && options.url && (String(options.url).includes('autocomplete_by_waiting'))) {
                        const isFromCustomHandler = options.data && (options.data._custom_handler === true || options.data._custom_handler === 'true');
                        if (!isFromCustomHandler) {
                            // Block - return fake promise
                            return {
                                done: function() { return this; },
                                fail: function() { return this; },
                                always: function() { return this; },
                                then: function() { return this; },
                                catch: function() { return this; }
                            };
                        }
                    }
                    return originalJQueryAjax.apply(this, arguments);
                };

                // 2. Block append/after to orderTable to prevent Bootstrap rows from appearing
                const originalAppend = jQuery.fn.append;
                const originalAfter = jQuery.fn.after;

                jQuery.fn.append = function() {
                    // Block if appending to orderTable (Bootstrap rows)
                    if (this.is('#orderTableBody, #orderTable tbody') ||
                        this.closest('#orderTable, #orderTableBody').length) {
                        console.log('🚫 Blocked append to orderTable (will use updateProductTable instead)');
                        return this; // Don't append
                    }
                    return originalAppend.apply(this, arguments);
                };

                jQuery.fn.after = function() {
                    if (this.is('#orderTable tr, #orderTableBody tr') ||
                        this.closest('#orderTable, #orderTableBody').length) {
                        return this;
                    }
                    return originalAfter.apply(this, arguments);
                };

                (function() {
                    function clearTableOnLoad() {
                        const $tbody = jQuery('#orderTableBody, #orderTable tbody');
                        if ($tbody.length && $tbody.children().length > 0) {
                            console.log('🧹 Clearing table body on page load to remove Bootstrap rows');
                            $tbody.empty();
                        }
                    }
                    // Try multiple times to catch rows added at different times
                    clearTableOnLoad();
                    setTimeout(clearTableOnLoad, 100);
                    setTimeout(clearTableOnLoad, 500);
                    setTimeout(clearTableOnLoad, 1000);
                    jQuery(document).ready(function() {
                        clearTableOnLoad();
                        setTimeout(clearTableOnLoad, 100);
                        setTimeout(clearTableOnLoad, 500);
                    });
                })();
            } else {
                setTimeout(blockOfflinePosHandler, 50);
            }
        }
        blockOfflinePosHandler();
    })();
</script>

<!-- REMOVED: jQuery.ajax interceptor for autocomplete_by_waiting - not needed anymore -->
<!-- We now use search_product_offline_v2 (JSON endpoint) instead of autocomplete_by_waiting (HTML) -->

<!-- Flowbite Toast Helper Function (same style as pos_v2) -->
<script>
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
            info: { bg: 'bg-blue-100', icon: 'text-blue-500', text: 'text-blue-800' }
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
</script>

<!-- Include JS from Offline POS V2 (duplicated and modified for v2) -->
@include('app.offline_pos_v2.offline_pos_v2_js')

<script>
    $(document).ready(function() {
        // Wait a bit for offline_pos_js to attach handlers, then unbind them
        setTimeout(function() {
            console.log('🔧 Unbinding offline_pos_js handler for product_name_input');

            // Unbind all handlers from product_name_input multiple times to be sure
            $('#product_name_input').off('keyup');
            $('#product_name_input').off('keyup focus change');
            $('#product_name_input').off(); // Unbind all

            // Also unbind using jQuery directly
            jQuery('#product_name_input').off('keyup');
            jQuery('#product_name_input').off('keyup focus change');
            jQuery('#product_name_input').off();

            console.log('✅ offline_pos_js handler unbound');

            // Also prevent any future handlers from being attached
            const originalOn = jQuery.fn.on;
            jQuery.fn.on = function(events, selector, data, handler) {
                // Block keyup handler for product_name_input
                if (this.length > 0 && this[0] && this[0].id === 'product_name_input') {
                    if (events === 'keyup' || (typeof events === 'string' && events.includes('keyup'))) {
                        console.log('🚫 Blocking keyup handler attachment to product_name_input');
                        return this;
                    }
                }
                // Also check by selector
                if (this.selector === '#product_name_input') {
                    if (events === 'keyup' || (typeof events === 'string' && events.includes('keyup'))) {
                        console.log('🚫 Blocking keyup handler attachment to product_name_input (by selector)');
                        return this;
                    }
                }
                return originalOn.apply(this, arguments);
            };

            // Re-attach our custom handler to ensure it's the only one
            console.log('🔄 Re-attaching custom product search handler');

            // Re-attach custom handler (copy from earlier script)
            let productSearchTimeout;
            $('#product_name_input').on('keyup', function() {
                const query = $(this).val().trim();
                const $autocomplete = $('#itemList');

                clearTimeout(productSearchTimeout);

                if (query.length < 3) {
                    $autocomplete.addClass('hidden').removeClass('block').empty();
                    return;
                }

                productSearchTimeout = setTimeout(function() {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: '{{ url("search_product_offline_v2") }}',
                        method: 'POST',
                        data: {
                            query: query,
                            type: $('#std_id option:selected').text() || '',
                            _item_type: $('#item_type').val() || 'store',
                            _std_id: $('#std_id').val() || '',
                            _st_id: $('#st_id').val() || ''
                        },
                        success: function(products) {
                            // Handle JSON response (like point_of_sale_v2)
                            console.log('=== AJAX SUCCESS (JSON) ===');
                            console.log('Products received:', products);
                            console.log('Products count:', products ? products.length : 0);

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
                                    // Parse bin string to create badges (like point_of_sale_v2)
                                    const binBadges = window.formatBinToBadges ? window.formatBinToBadges(product.bin) : '';

                                    const productImage = product.image || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(product.brand || 'BRAND') + '&background=F74040&color=fff&size=60';
                                    const brandName = product.brand || 'BRAND';

                                    // Build data attributes for compatibility with offline_pos_js
                                    const dataAttrs = `
                                            data-pst_id="${product.id || product.pst_id || ''}"
                                            data-pl_id="${product.pl_id || ''}"
                                            data-pls_qty="${product.pls_qty || 0}"
                                            data-plst_id="${product.plst_id || ''}"
                                            data-psc_id="${product.psc_id || ''}"
                                            data-sell_price="${product.price || 0}"
                                            data-bandrol="${product.bandrol || product.price || 0}"
                                            data-p_name="[${brandName}] ${product.article_id || ''} ${product.name}"
                                            data-bin="${product.bin || ''}"
                                            data-brand="${brandName}"
                                            data-image="${product.image || ''}"
                                            data-b1g1_id="${product.b1g1_id || ''}"
                                            data-b1g1_price="${product.b1g1_price || ''}"
                                        `;

                                    html += `
                                            <li class="product-item p-4 bg-white hover:bg-gray-50 cursor-pointer transition-colors text-sm"
                                                data-product='${JSON.stringify(product).replace(/'/g, "&#39;")}'>
                                                <a id="add_to_item_list" class="block" ${dataAttrs}>
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
                                                                <strong class="text-red-600">Rp. ${window.formatNumber ? window.formatNumber(product.price) : product.price}</strong>
                                                            </div>
                                                            ${product.disc_percent > 0 ? `<small class="text-green-600 block"><i class="fas fa-tag"></i> Diskon ${product.disc_percent}%</small>` : ''}
                                                            ${product.disc_rp > 0 ? `<small class="text-green-600 block"><i class="fas fa-money-bill-wave"></i> Rp. ${window.formatNumber ? window.formatNumber(product.disc_rp) : product.disc_rp}</small>` : ''}
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                        `;
                                });
                                html += '</ul>';
                                $autocomplete.html(html).removeClass('hidden').addClass('block');
                                console.log('✅ Autocomplete displayed with', productArray.length, 'products');
                            } else {
                                $autocomplete.html('<ul class="divide-y divide-gray-200"><li class="p-4 text-gray-500 text-sm text-center">Tidak ditemukan</li></ul>').removeClass('hidden').addClass('block');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error searching products:', error, xhr);
                            let errorMsg = 'Error searching products';
                            if (xhr.status === 419) {
                                errorMsg = 'CSRF token mismatch. Please refresh the page.';
                            } else if (xhr.status === 500) {
                                errorMsg = 'Server error. Please try again.';
                            }
                            $autocomplete.html('<ul class="divide-y divide-gray-200"><li class="p-4 text-red-600">' + errorMsg + '</li></ul>').removeClass('hidden').addClass('block');
                        }
                    });
                }, 300);
            });

            console.log('✅ Custom product search handler re-attached');
        }, 1000); // Increased delay to ensure offline_pos_js has loaded
    });
</script>

<script>
    // Initialize orderItems array FIRST (outside document ready so it's accessible globally)
    window.orderItems = [];

    // Format number function (like point_of_sale_v2) - defined BEFORE document.ready
    function formatNumber(num) {
        if (typeof num === 'undefined' || num === null || isNaN(num)) return '0';
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Function to update product table from orderItems array (EXACTLY like point_of_sale_v2)
    // IMPORTANT: Defined BEFORE document.ready so it's available when handler is called
    window.updateProductTable = function() {
        console.log('🔄 updateProductTable called, building table from orderItems array');

        // Find all possible tbody elements
        const $tbody1 = jQuery('#orderTableBody');
        const $tbody2 = jQuery('#orderTable tbody');
        const $tbody = $tbody1.length ? $tbody1 : $tbody2;

        if (!$tbody.length) {
            console.warn('⚠️ orderTableBody not found');
            return;
        }

        jQuery('#orderTable tbody tr').remove();
        jQuery('#orderTableBody tr').remove();
        jQuery('#orderTable tr[data-list-item]').remove();
        jQuery('#orderTableBody tr[data-list-item]').remove();
        jQuery('#orderTable tr[id^="orderList"]').remove();
        jQuery('#orderTableBody tr[id^="orderList"]').remove();

        // Also clear tbody directly
        $tbody.empty();

        console.log('✅ All old rows removed, tbody cleared');

        if (!window.orderItems || window.orderItems.length === 0) {
            console.log('⚠️ orderItems array is empty, table cleared');
            return;
        }

        console.log('📦 Building table from', window.orderItems.length, 'items in orderItems array');

        // Build table rows from orderItems array (EXACTLY like point_of_sale_v2)
        window.orderItems.forEach((item, index) => {
            // Check if this is a retur item FIRST
            const isRetur = item.isRetur === true;

            // For retur items, keep qty negative. For regular items, use absolute value
            const actualQty = item.quantity || item.qty || 1;
            const qty = isRetur ? actualQty : Math.abs(actualQty); // Keep negative for retur, abs for regular

            // Extract brand from name if format is [BRAND] product name (EXACTLY like point_of_sale_v2)
            let displayName = item.name || '';
            let brandBadge = '';
            const brandName = item.brand || '';

            // Brand badge styling for retur items (red) vs regular items (orange)
            if (brandName) {
                const brandClass = isRetur ? 'bg-red-500 ' : 'bg-orange-500';
                brandBadge = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold ${brandClass} text-white mr-2">${brandName}</span>`;
                // Remove brand from display name if it's in brackets
                displayName = displayName.replace(/^\[.*?\]\s*/, '');
            }

            // Image or initial (EXACTLY like point_of_sale_v2)
            let imageHtml = '';
            const productImage = item.image || '';

            if (productImage && productImage.trim() !== '') {
                // Use product image
                imageHtml = `<img src="${productImage}" alt="${displayName}" class="w-10 h-10 object-cover rounded mr-3" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(brandName || 'P')}&background=F74040&color=fff&size=60'">`;
            } else {
                // Use initial with retur styling (red) vs regular (gray)
                const initial = displayName ? displayName.charAt(0).toUpperCase() : (brandName ? brandName.charAt(0).toUpperCase() : 'P');
                const bgColor = isRetur ? 'bg-red-100' : 'bg-red-500';
                const txtColor = isRetur ? 'text-red-600' : 'text-white';
                imageHtml = `<div class="w-10 h-10 rounded ${bgColor} flex items-center justify-center ${txtColor} font-bold mr-3 p-4 text-sm">${initial}</div>`;
            }

            // Debug log (only for first few items to avoid spam)
            if (index < 3) {
                console.log(`📦 Building row ${index + 1}:`, {
                    name: displayName.substring(0, 30),
                    brand: brandName,
                    hasImage: !!productImage,
                    image: productImage ? productImage.substring(0, 50) : 'no image'
                });
            }

            // Get values from item
            const itemType = item.item_type || 'store'; // Get item_type from item
            const isWaiting = itemType === 'waiting';
            const isB1G1 = item.isB1G1 || itemType === 'b1g1';
            const qtyForCalc = Math.abs(item.quantity) || 0;
            const availableStockVal = isWaiting ? 1 : (item.pls_qty || 0); // available stock from product / location
            const remainingStock = (availableStockVal || 0) - qtyForCalc; // can be negative
            const stockDisplay = (remainingStock < 0) ? ('-' + Math.abs(remainingStock)) : remainingStock;
            const stockTextClass = (remainingStock < 0) ? 'text-red-600 font-semibold' : 'text-gray-700';
            const discPercent = item.disc_percent || item.discPercent || 0;
            const discRp = item.disc_number || item.discRp || 0;
            const nameset = item.nameset || 0;
            const bandrol = item.bandrol || item.price || 0;
            const price = item.price || 0;
            const discountNormal = bandrol - price; // Discount normal = bandrol - sell_price

            // Calculate subtotal - use actualQty (which is negative for retur) for calculation
            // Same as point_of_sale_v2: support untuk qty negatif (item retur)
            const subtotal = item.subtotal || (price * actualQty) - discRp + parseFloat(nameset);
            // Note: actualQty is already negative for retur items, so subtotal will be negative

            // Display values for retur items (negative) vs regular items (positive)
            const displaySubtotal = isRetur ? `-Rp. ${formatNumber(Math.abs(subtotal))}` : `Rp. ${formatNumber(subtotal)}`;
            const displayQty = isRetur ? `-${Math.abs(actualQty)}` : Math.abs(actualQty);

            // B1G1 class for styling
            const b1g1Class = isB1G1 ? 'b1g1_mode' : '';
            // Retur class for styling (red background or border) - Same as point_of_sale_v2
            const returClass = isRetur ? 'bg-red-50 border-l-4 border-l-red-500 hover:bg-red-100' : 'bg-white hover:bg-gray-50';
            const textClass = isRetur ? 'text-red-900' : 'text-gray-900';
            const inputBgClass = isRetur ? 'bg-red-100' : 'bg-white';

            // Build row HTML - SAME structure for all modes (WAITING, STORE, B1G1) - 11 columns like offline pos lama
            // All columns should appear even for retur items
            let row = '';
            // All modes (WAITING, STORE, B1G1) use the same 11 columns structure like offline pos lama
            row = `
                    <tr data-list-item data-product-id="${item.id || ''}" data-pl-id="${item.pl_id || ''}" data-plst-id="${item.plst_id || ''}" class="${returClass} transition-colors pos_item_list ${b1g1Class}" id="orderList${item.index || index}">
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                ${imageHtml}
                                <div class="flex items-center gap-2 flex-wrap">
                                    ${brandBadge}
                                    <span class="text-xs ${textClass} font-semibold">${displayName}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center text-xs"><span class="${stockTextClass}">${stockDisplay}</span></td>
                        <td class="px-4 py-3 text-center">
                            <input type="number" id="item_qty${item.index || index}" class="w-16 text-center text-xs border ${isRetur ? 'border-red-300' : 'border-gray-300'} rounded-lg px-2 py-1.5 ${inputBgClass} ${textClass} font-semibold focus:ring-red-400 focus:border-red-400" value="${displayQty}" onchange="if(typeof changeQty === 'function') changeQty(${item.index || index}, ${item.id || ''}, ${availableStockVal})">
                        </td>
                        <td class="px-4 py-3">
                            <select class="w-full px-2 py-1.5 ${inputBgClass} border border-gray-300 ${textClass} text-xs rounded-lg focus:ring-red-400 focus:border-red-400" id="discount_selection${item.index || index}" data-sellPrice="${price}" onchange="if(typeof handleSelectChange === 'function') handleSelectChange(${item.index || index}, this)" ${isRetur ? 'disabled' : ''}>
                                <option value="0">Discount Extra</option>
                                <option value="1">Discount Promo</option>
                            </select>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <input type="number" id="discount_percentage${item.index || index}" class="w-16 px-2 py-1.5 ${inputBgClass} border border-gray-300 ${textClass} text-xs rounded-lg focus:ring-red-400 focus:border-red-400 text-center" value="${discPercent}" placeholder="%" min="0" step="0.01" onchange="if(typeof changeDiscountPercentage === 'function') changeDiscountPercentage(${item.index || index}, ${item.id || ''}, ${availableStockVal})" ${isRetur ? 'disabled' : ''}>
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" id="discount_number${item.index || index}" class="w-24 px-2 py-1.5 ${inputBgClass} border border-gray-300 ${textClass} text-xs rounded-lg focus:ring-red-400 focus:border-red-400" value="${discRp}" placeholder="Rp" min="0" onchange="if(typeof changeDiscountNumber === 'function') changeDiscountNumber(${item.index || index}, ${item.id || ''}, ${availableStockVal})" ${isRetur ? 'disabled' : ''}>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <input type="number" id="nameset_price${item.index || index}" class="w-24 text-center text-xs border border-gray-300 rounded-lg px-2 py-1.5 ${inputBgClass} ${textClass} focus:ring-red-400 focus:border-red-400" value="${nameset}" placeholder="Rp" min="0" onchange="if(typeof namesetPrice === 'function') namesetPrice(${item.index || index})" ${isRetur ? 'disabled' : ''}>
                        </td>
                        <td class="px-4 py-3 text-right text-xs"><strong class="${textClass}">Rp. ${formatNumber(bandrol)}</strong></td>
                        <td class="px-4 py-3 text-right text-xs"><strong class="${textClass}">Rp. ${formatNumber(price)}</strong></td>
                        <td class="px-4 py-3 text-right text-xs"><strong class="${textClass}">Rp. ${formatNumber(discountNormal)}</strong></td>
                        <td class="px-4 py-3 text-right text-xs"><strong class="text-red-600 font-bold" id="subtotal_item${item.index || index}">${displaySubtotal}</strong></td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" onclick="deleteItem(${item.id || ''}, ${price}, ${item.index || index}, ${item.pl_id || ''}, ${item.plst_id || ''}, ${bandrol})" class="w-8 h-8 flex items-center bg-red-50 justify-center text-red-500 hover:text-red-800 hover:bg-red-100 rounded transition-colors" title="Hapus">
                                <i class="cft-standard-stroke cft-trash text-base"></i>
                            </button>
                        </td>
                    </tr>
                `;
            $tbody.append(row);
        });

        console.log('✅ Table rebuilt from orderItems array with', window.orderItems.length, 'items');

        // Auto-update totals after table is rebuilt
        if (typeof window.updateTotalHarga === 'function') {
            window.updateTotalHarga();
        }
        if (typeof window.updateTotalNameset === 'function') {
            window.updateTotalNameset();
        }
        if (typeof window.updateTotalDiskon === 'function') {
            window.updateTotalDiskon();
        }
        if (typeof window.updateGrandTotal === 'function') {
            window.updateGrandTotal();
        }
    };

    // Simple deleteItem function for waiting cart restore
    window.deleteItem = function(pst_id, price, index, pl_id, plst_id, bandrol) {
        if (!confirm('Apakah Anda yakin ingin menghapus item ini?')) {
            return;
        }

        jQuery.ajax({
            type: 'POST',
            url: "{{ url('change_waiting_status') }}",
            dataType: 'json',
            data: {
                _pst_id: pst_id,
                _pl_id: pl_id,
                _mode: 'delete',
                _item_type: 'store',
                _plst_id: plst_id,
                _token: jQuery('meta[name="csrf-token"]').attr('content')
            },
            success: function(r) {
                if (r.status == '200') {
                    // Find the item and reduce quantity
                    if (window.orderItems) {
                        const itemIndex = window.orderItems.findIndex(item => item.index === index);
                        if (itemIndex >= 0) {
                            const item = window.orderItems[itemIndex];
                            item.quantity = (parseInt(item.quantity) || 1) - 1;

                            // Remove the used plst_id from the array
                            if (item.plst_ids && item.plst_ids.length > 0) {
                                item.plst_ids.shift(); // Remove first plst_id
                                if (item.plst_ids.length > 0) {
                                    item.plst_id = item.plst_ids[0]; // Update current plst_id
                                }
                            }

                            if (item.quantity <= 0) {
                                // Remove item completely if quantity is 0
                                window.orderItems.splice(itemIndex, 1);
                                jQuery('#orderList' + index).remove();
                            } else {
                                // Update the quantity display
                                jQuery('#item_qty' + index).val(item.quantity);
                                // Rebuild table to update calculations
                                if (typeof window.updateProductTable === 'function') {
                                    window.updateProductTable();
                                }
                            }
                        }
                    }
                    if (typeof updateGrandTotal === 'function') updateGrandTotal();
                    if (typeof toast === 'function') {
                        toast('Dihapus', 'Item berhasil dihapus', 'success');
                    } else {
                        alert('Item berhasil dihapus');
                    }
                } else {
                    if (typeof toast === 'function') {
                        toast('Gagal', 'Gagal menghapus item', 'error');
                    } else {
                        alert('Gagal menghapus item');
                    }
                }
            },
            error: function() {
                if (typeof toast === 'function') {
                    toast('Error', 'Terjadi kesalahan', 'error');
                } else {
                    alert('Error menghapus item');
                }
            }
        });
    };

    // Update Total Harga (from price_tag_item * qty for all items)
    window.updateTotalHarga = function() {
        let total = 0;

        if (!window.orderItems || window.orderItems.length === 0) {
            jQuery('#total_price_side').text('0');
            return;
        }

        window.orderItems.forEach(function(item) {
            const bandrol = parseFloat(item.bandrol) || parseFloat(item.price) || 0;
            // Support untuk qty negatif (item retur) - sama seperti point_of_sale_v2
            const qty = parseInt(item.quantity) || parseInt(item.qty) || 0; // Bisa negatif untuk retur!
            total += bandrol * qty; // Tetap dikalikan dengan qty (bisa negatif untuk retur)
        });

        jQuery('#total_price_side').text(formatNumber(total));
        console.log('💰 Total Harga updated:', total, '(includes retur items with negative qty)');
    };

    // Update Total Nameset (sum of all nameset values)
    window.updateTotalNameset = function() {
        let total = 0;

        if (!window.orderItems || window.orderItems.length === 0) {
            jQuery('#total_nameset_side').text('0');
            return;
        }

        window.orderItems.forEach(function(item) {
            const nameset = parseFloat(item.nameset) || 0;
            total += nameset;
        });

        jQuery('#total_nameset_side').text(formatNumber(total));
        console.log('🏷️ Total Nameset updated:', total);
    };

    // Update Total Discount (discount_normal * qty + discount_number for all items)
    window.updateTotalDiskon = function() {
        let totalDiscNormal = 0;
        let totalDiscField = 0;

        if (!window.orderItems || window.orderItems.length === 0) {
            jQuery('#total_discount_value_side').text('0');
            return;
        }

        window.orderItems.forEach(function(item) {
            const bandrol = parseFloat(item.bandrol) || parseFloat(item.price) || 0;
            const price = parseFloat(item.price) || 0;
            // Support untuk qty negatif (item retur) - sama seperti point_of_sale_v2
            const qty = parseInt(item.quantity) || parseInt(item.qty) || 0; // Bisa negatif untuk retur!

            // Discount normal = bandrol - sell_price (per item)
            const discountNormal = bandrol - price;
            totalDiscNormal += discountNormal * qty; // Tetap dikalikan dengan qty (bisa negatif untuk retur)

            // Discount field (discount_number)
            const discNumber = parseFloat(item.disc_number) || parseFloat(item.discRp) || 0;
            totalDiscField += discNumber;
        });

        const totalDiscount = totalDiscNormal + totalDiscField;
        jQuery('#total_discount_value_side').text(formatNumber(totalDiscount));
        console.log('💸 Total Discount updated:', totalDiscount, '(normal:', totalDiscNormal, '+ field:', totalDiscField, ')');
    };

    // Update Grand Total (total_harga + total_nameset - voucher - total_discount)
    window.updateGrandTotal = function() {
        let grandTotal = 0;

        // Get values and convert to numbers (remove commas and parse)
        const replaceComma = function(str) {
            if (typeof str === 'string') {
                return str.replace(/,/g, '').replace(/\./g, '');
            }
            return str || 0;
        };

        const totalHarga = parseFloat(replaceComma(jQuery('#total_price_side').text())) || 0;
        const totalNameset = parseFloat(replaceComma(jQuery('#total_nameset_side').text())) || 0;
        const totalVoucher = parseFloat(replaceComma(jQuery('#voucher_total_value_side').text())) || 0;
        const totalDiscount = parseFloat(replaceComma(jQuery('#total_discount_value_side').text())) || 0;

        // Grand total calculation (same as offline pos lama)
        grandTotal = totalHarga + totalNameset - totalVoucher - totalDiscount;

        // Set to DOM with formatting
        jQuery('#total_final_price_side').text(formatNumber(grandTotal));
        console.log('🎯 Grand Total updated:', grandTotal, '(harga:', totalHarga, '+ nameset:', totalNameset, '- voucher:', totalVoucher, '- discount:', totalDiscount, ')');
    };

    // Load waiting cart from server (check_waiting_for_checkout) and populate window.orderItems
    window.loadWaitingCartFromServer = function() {
        if (window.cartRestored || window.isRestoringCart) {
            console.log('Cart already restored or restoring, skipping');
            return;
        }
        window.isRestoringCart = true;
        try {
            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                type: 'POST',
                url: "{{ url('check_waiting_for_checkout_json') }}",
                dataType: 'json',
                success: function(data) {
                    try {
                        var items = [];
                        console.log('📦 Processing', data.pos_data.length, 'cart items from server');
                        if (data.pos_data && Array.isArray(data.pos_data)) {
                            data.pos_data.forEach(function(item, idx) {
                                console.log('📦 Processing item', idx + 1, ':', item.p_name, 'qty:', item.quantity);
                                var pst_id = item.pst_id;
                                var name = (item.p_name || '') + (item.br_name ? ' ' + item.br_name : '') + (item.sz_name ? ' ' + item.sz_name : '') + (item.p_color ? ' ' + item.p_color : '');
                                var price = parseFloat(item.ps_sell_price) || parseFloat(item.p_sell_price) || 0;
                                var pls_qty = parseInt(item.pls_qty) || 0;
                                var pl_id = item.pl_id;
                                var plst_id = item.plst_id;
                                var qty = parseInt(item.quantity) || 1;
                                var plst_ids = Array.isArray(item.plst_ids) ? item.plst_ids : [plst_id];

                                if (pst_id) {
                                    items.push({
                                        id: pst_id,
                                        name: name.trim(),
                                        price: price,
                                        quantity: qty,
                                        pls_qty: pls_qty,
                                        pl_id: pl_id,
                                        plst_id: plst_ids[0], // Use first plst_id for delete
                                        plst_ids: plst_ids, // Store all plst_ids
                                        bandrol: price,
                                        index: idx + 1
                                    });
                                    console.log('✅ Added item to orderItems:', name.trim(), 'qty:', qty);
                                }
                            });
                        }

                        if (items.length > 0) {
                            window.orderItems = items;
                            window.cartRestored = true;
                            window.isRestoringCart = false;
                            console.log('✅ Loaded', items.length, 'waiting items from server JSON into orderItems');
                            if (typeof window.updateProductTable === 'function') {
                                window.updateProductTable();
                            }
                            if (typeof updateGrandTotal === 'function') updateGrandTotal();
                        } else {
                            window.isRestoringCart = false;
                            console.log('ℹ️ No waiting items found on server');
                        }
                    } catch (err) {
                        window.isRestoringCart = false;
                        console.error('Error parsing waiting-for-checkout JSON:', err);
                    }
                },
                error: function(err) {
                    window.isRestoringCart = false;
                    console.error('Failed to load waiting items from server:', err);
                }
            });
        } catch (e) {
            window.isRestoringCart = false;
            console.error('loadWaitingCartFromServer error', e);
        }
    };

    $(document).ready(function() {
        // Intercept click on #add_to_item_list to add product to orderItems array (like point_of_sale_v2)
        // This runs BEFORE offline_pos_js handler (because we register it first)
        // IMPORTANT: Don't preventDefault() - let the event continue to offline_pos_js handler for AJAX call
        jQuery(document).on('click', '#add_to_item_list', function(e) {
            console.log('📦 #add_to_item_list clicked - intercepting to add to orderItems');

            // Get product data from multiple sources
            const $link = jQuery(this);
            const $productItem = $link.closest('.product-item');

            // Try to get from data-product attribute on parent
            let product = null;
            if ($productItem.length && $productItem.attr('data-product')) {
                try {
                    const productJson = $productItem.attr('data-product').replace(/&#39;/g, "'");
                    product = JSON.parse(productJson);
                    console.log('✅ Got product from data-product attribute');
                } catch(err) {
                    console.error('Error parsing data-product:', err);
                }
            }

            // If not found, try to get from data-* attributes on the link itself
            if (!product) {
                const pstId = $link.attr('data-pst_id') || $link.attr('data-pst-id') || '';
                const pName = $link.attr('data-p_name') || '';
                const sellPrice = $link.attr('data-sell_price') || '0';
                const plId = $link.attr('data-pl_id') || '';
                const plsQty = $link.attr('data-pls_qty') || '0';
                const plstId = $link.attr('data-plst_id') || '';
                const pscId = $link.attr('data-psc_id') || '';
                const bandrol = $link.attr('data-bandrol') || sellPrice;
                const brand = $link.attr('data-brand') || '';
                const image = $link.attr('data-image') || '';
                const bin = $link.attr('data-bin') || '';

                if (pstId && pName) {
                    product = {
                        id: pstId,
                        pst_id: pstId,
                        name: pName,
                        brand: brand,
                        image: image,
                        price: parseFloat(sellPrice) || 0,
                        pl_id: plId,
                        pls_qty: parseFloat(plsQty) || 0,
                        plst_id: plstId,
                        psc_id: pscId,
                        bandrol: parseFloat(bandrol) || 0,
                        bin: bin,
                        disc_percent: 0,
                        disc_rp: 0
                    };
                    console.log('✅ Got product from data-* attributes');
                }
            }

            if (product) {
                console.log('📦 Product data:', product);

                // Get current item_type from select
                const itemType = jQuery('#item_type').val() || 'store';
                console.log('📋 Current item_type:', itemType);

                // Add to orderItems array (like point_of_sale_v2)
                const brandName = product.brand || '';
                const productImage = product.image || '';
                const productName = product.name || '';
                const fullProductName = brandName ? '[' + brandName + '] ' + productName : productName;

                // Determine B1G1 mode
                const b1g1Id = product.b1g1_id || $link.attr('data-b1g1_id') || '';
                const b1g1Price = product.b1g1_price || $link.attr('data-b1g1_price') || '';
                const isB1G1 = (itemType === 'b1g1' || (b1g1Id && b1g1Price));

                const item = {
                    id: product.id || product.pst_id || '',
                    name: fullProductName,
                    brand: brandName,
                    image: productImage,
                    price: parseFloat(product.price) || 0,
                    quantity: 1,
                    bin: product.bin || 'STORE [1]',
                    pl_id: product.pl_id || null,
                    pls_qty: product.pls_qty || 0,
                    plst_id: product.plst_id || null,
                    discPercent: parseFloat(product.disc_percent) || 0,
                    discRp: parseFloat(product.disc_rp) || 0,
                    disc_percent: parseFloat(product.disc_percent) || 0,
                    disc_number: parseFloat(product.disc_rp) || 0,
                    nameset: 0,
                    marketplace: 0,
                    bandrol: parseFloat(product.bandrol) || parseFloat(product.price) || 0,
                    psc_id: product.psc_id || null,
                    item_type: itemType, // Store item_type for table rendering
                    b1g1_id: b1g1Id,
                    b1g1_price: parseFloat(b1g1Price) || 0,
                    isB1G1: isB1G1
                };

                // Check if product already exists
                const existingIndex = window.orderItems.findIndex(i => i.id === item.id && i.pl_id === item.pl_id);
                if (existingIndex >= 0) {
                    // Allow increasing quantity even if it exceeds available stock.
                    // Show a warning but do not block the action (user wants to sell even when stock not recorded).
                    const existingItem = window.orderItems[existingIndex];
                    const existingQty = parseInt(existingItem.quantity) || 0;
                    const availableStock = parseInt(existingItem.pls_qty || item.pls_qty || 0) || 0;
                    window.orderItems[existingIndex].quantity = existingQty + 1;
                    if ((existingQty + 1) > availableStock) {
                        if (typeof showToast === 'function') {
                            showToast('Melebihi Stok: stok akan menjadi negatif di cart', 'warning');
                        } else if (typeof swal !== 'undefined') {
                            swal('Melebihi Stok', 'Stok akan menjadi negatif di cart', 'warning');
                        }
                        console.log('⚠️ Increased qty beyond available stock (allowed)');
                    } else {
                        console.log('✅ Updated existing item quantity');
                    }
                } else {
                    // Allow adding item even if available stock is zero or undefined.
                    // Set index and push regardless of stock; warn if stock is 0.
                    const availableStockNew = parseInt(item.pls_qty || 0) || 0;
                    item.index = window.orderItems.length;
                    window.orderItems.push(item);
                    if (availableStockNew <= 0) {
                        if (typeof showToast === 'function') {
                            showToast('Produk ditambahkan meskipun stok 0 — cart akan menunjukkan jumlah negatif', 'warning');
                        } else if (typeof swal !== 'undefined') {
                            swal('Stok 0', 'Produk ditambahkan meskipun stok 0 — cart akan menunjukkan jumlah negatif', 'warning');
                        }
                        console.log('⚠️ Added new item with zero stock (allowed)');
                    } else {
                        console.log('✅ Added new item to orderItems array');
                    }
                }

                console.log('✅ orderItems array now has', window.orderItems.length, 'items');

                // Persist item to database
                console.log('📡 Sending AJAX to persist item:', {
                    _pst_id: product.id || product.pst_id || '',
                    _pl_id: product.pl_id || '',
                    _sell_price: parseFloat(product.price) || 0
                });
                jQuery.ajax({
                    type: 'POST',
                    url: "{{ url('change_waiting_status') }}",
                    dataType: 'json',
                    data: {
                        _pst_id: product.id || product.pst_id || '',
                        _pl_id: product.pl_id || '',
                        _mode: 'add',
                        _item_type: 'store',
                        _sell_price: parseFloat(product.price) || 0,
                        _token: jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(r) {
                        console.log('📡 AJAX response:', r);
                        if (r.status == '200') {
                            console.log('✅ Item persisted to database');
                            // Update the item with the returned plst_id if available
                            if (r.plst_id && item) {
                                item.plst_id = r.plst_id;
                            }
                        } else {
                            console.error('❌ Failed to persist item to database, status:', r.status);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Error persisting item to database:', error, xhr.responseText);
                    }
                });

                // Hide autocomplete list after product is selected (multiple methods to ensure it's hidden)
                const $itemList = jQuery('#itemList');
                if ($itemList.length) {
                    $itemList.addClass('hidden').removeClass('block');
                    $itemList.css('display', 'none');
                    $itemList.fadeOut(0); // Instant fadeOut
                    $itemList.empty(); // Also clear content
                    console.log('✅ Autocomplete list hidden after product selection');
                }

                // Clear product search input
                jQuery('#product_name_input').val('');

                // IMPORTANT: After adding to orderItems, immediately update table
                // Don't wait for AJAX - update table now, then AJAX will validate on backend
                setTimeout(function() {
                    console.log('🔄 Immediately updating table after adding to orderItems');
                    console.log('📦 orderItems count:', window.orderItems ? window.orderItems.length : 0);
                    console.log('🔍 updateProductTable exists:', typeof window.updateProductTable === 'function');
                    if (typeof window.updateProductTable === 'function') {
                        if (window.orderItems && window.orderItems.length > 0) {
                            console.log('✅ Calling updateProductTable() now...');
                            window.updateProductTable();
                        } else {
                            console.warn('⚠️ orderItems is empty, cannot update table');
                        }
                    } else {
                        console.error('❌ updateProductTable function not found!');
                    }
                    if (typeof window.updateOrderDisplay === 'function') {
                        window.updateOrderDisplay();
                    }
                    // Update all totals
                    if (typeof window.updateTotalHarga === 'function') {
                        window.updateTotalHarga();
                    }
                    if (typeof window.updateTotalNameset === 'function') {
                        window.updateTotalNameset();
                    }
                    if (typeof window.updateTotalDiskon === 'function') {
                        window.updateTotalDiskon();
                    }
                    if (typeof window.updateGrandTotal === 'function') {
                        window.updateGrandTotal();
                    }
                }, 50);

                // Mark that we've added product to array
                // AJAX will still run for backend validation, but table is already updated
            } else {
                console.warn('⚠️ Could not get product data from click, will sync from table after AJAX');
            }
        });

        const originalJQueryAjax = jQuery.ajax;
        jQuery.ajax = function(options) {
            if (options && options.url && (String(options.url).includes('change_waiting_status')) && !options.isCartRestore) {
                const originalSuccess = options.success;
                options.success = function(data) {
                    const $tbody = jQuery('#orderTableBody, #orderTable tbody');
                    $tbody.empty();

                    if (originalSuccess) {
                        const originalAppend = jQuery.fn.append;
                        const originalAfter = jQuery.fn.after;

                        jQuery.fn.append = function() {
                            if (this.is('#orderTableBody, #orderTable tbody') ||
                                this.closest('#orderTable, #orderTableBody').length) {
                                return this;
                            }
                            return originalAppend.apply(this, arguments);
                        };

                        jQuery.fn.after = function() {
                            if (this.is('#orderTable tr, #orderTableBody tr') ||
                                this.closest('#orderTable, #orderTableBody').length) {
                                return this;
                            }
                            return originalAfter.apply(this, arguments);
                        };

                        originalSuccess.apply(this, arguments);

                        // Restore original functions
                        jQuery.fn.append = originalAppend;
                        jQuery.fn.after = originalAfter;

                        // Clear any rows that might have been added
                        $tbody.empty();
                    }

                    // Immediately rebuild table from orderItems array (no delay)
                    // Use setTimeout to ensure orderItems is fully updated
                    setTimeout(function() {
                        console.log('🔄 AJAX interceptor: Rebuilding table from orderItems array');
                        console.log('📦 orderItems count:', window.orderItems ? window.orderItems.length : 0);
                        if (typeof window.updateProductTable === 'function' && window.orderItems && window.orderItems.length > 0) {
                            window.updateProductTable();
                        }
                        if (typeof window.updateOrderDisplay === 'function') {
                            window.updateOrderDisplay();
                        }
                        if (typeof updateGrandTotal === 'function') {
                            updateGrandTotal();
                        }
                    }, 50);
                };
            }
            return originalJQueryAjax.apply(this, arguments);
        };

        // REMOVED: Duplicate updateProductTable function - already defined before document.ready (line ~1532)
    });
</script>

<!-- Discount and Nameset Functions (like point_of_sale_v2) -->
<script>
    // REMOVED: window.orderItems initialization - already defined earlier (line ~1533)

    // REMOVED: Duplicate updateProductTable function - using the one defined earlier (line 1731)
    // The first definition is more complete with logging and error handling

    // Function to update discount percentage (like point_of_sale_v2)
    // Update orderItems array when discount percentage changes
    window.changeDiscountPercentage = function(index, pstId, stock) {
        if (!window.orderItems || !window.orderItems[index]) return;

        const discPercent = parseFloat(jQuery(`#discount_percentage${index}`).val()) || 0;
        const item = window.orderItems[index];
        const price = item.price || 0;
        const qty = Math.abs(item.quantity || 1);

        // Calculate discount in rupiah
        const subtotal = price * qty;
        const discRp = (subtotal * discPercent) / 100;

        // Update orderItems array
        item.disc_percent = discPercent;
        item.discPercent = discPercent;
        item.disc_number = discRp;
        item.discRp = discRp;

        // Update discount number input
        jQuery(`#discount_number${index}`).val(discRp > 0 ? discRp.toFixed(0) : '');

        // Rebuild table and update totals
        if (typeof window.updateProductTable === 'function') {
            window.updateProductTable();
        }
        if (typeof window.updateOrderDisplay === 'function') {
            window.updateOrderDisplay();
        }
        // Update all totals
        if (typeof window.updateTotalHarga === 'function') {
            window.updateTotalHarga();
        }
        if (typeof window.updateTotalNameset === 'function') {
            window.updateTotalNameset();
        }
        if (typeof window.updateTotalDiskon === 'function') {
            window.updateTotalDiskon();
        }
        if (typeof window.updateGrandTotal === 'function') {
            window.updateGrandTotal();
        }
    };

    // Function to update discount number (like point_of_sale_v2)
    window.changeDiscountNumber = function(index, pstId, stock) {
        if (!window.orderItems || !window.orderItems[index]) return;

        const discRp = parseFloat(jQuery(`#discount_number${index}`).val()) || 0;
        const item = window.orderItems[index];
        const price = item.price || 0;
        const qty = Math.abs(item.quantity || 1);

        // Calculate discount percentage
        const subtotal = price * qty;
        const discPercent = subtotal > 0 ? (discRp / subtotal) * 100 : 0;

        // Update orderItems array
        item.disc_number = discRp;
        item.discRp = discRp;
        item.disc_percent = discPercent;
        item.discPercent = discPercent;

        // Update discount percentage input
        jQuery(`#discount_percentage${index}`).val(discPercent > 0 ? discPercent.toFixed(2) : '');

        // Rebuild table and update totals
        if (typeof window.updateProductTable === 'function') {
            window.updateProductTable();
        }
        if (typeof window.updateOrderDisplay === 'function') {
            window.updateOrderDisplay();
        }
        // Update all totals
        if (typeof window.updateTotalHarga === 'function') {
            window.updateTotalHarga();
        }
        if (typeof window.updateTotalNameset === 'function') {
            window.updateTotalNameset();
        }
        if (typeof window.updateTotalDiskon === 'function') {
            window.updateTotalDiskon();
        }
        if (typeof window.updateGrandTotal === 'function') {
            window.updateGrandTotal();
        }
    };

    // Function to update quantity (works with orderItems array)
    window.changeQty = function(index, pstId, stock) {
        if (!window.orderItems || !window.orderItems[index]) return;

        const newQty = parseInt(jQuery(`#item_qty${index}`).val()) || 1;
        const item = window.orderItems[index];

        // B1G1 validation (from offline_pos_js)
        if (item.isB1G1 || item.item_type === 'b1g1') {
            // Count total B1G1 items
            let b1g1TotalQty = 0;
            window.orderItems.forEach(function(i) {
                if (i.isB1G1 || i.item_type === 'b1g1') {
                    b1g1TotalQty += Math.abs(parseInt(i.quantity) || 1);
                }
            });

            if (b1g1TotalQty > 2) {
                jQuery(`#item_qty${index}`).val(item.quantity || 1);
                if (typeof swal !== 'undefined') {
                    swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs', 'warning');
                }
                return false;
            }

            if (newQty > 2) {
                jQuery(`#item_qty${index}`).val(item.quantity || 1);
                if (typeof swal !== 'undefined') {
                    swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs', 'warning');
                }
                return false;
            }
        }

        // Stock guard: previously blocked exceeding stock. Now allow exceeding stock
        // but show a warning so cashier knows stock will go negative in cart.
        const availableStock = (typeof stock !== 'undefined' && stock !== null) ? parseInt(stock) : null;
        if (availableStock !== null && !isNaN(availableStock)) {
            if (Math.abs(newQty) > availableStock) {
                if (typeof showToast === 'function') {
                    showToast('Melebihi Stok: cart akan menunjukkan stok negatif', 'warning');
                } else if (typeof swal !== 'undefined') {
                    swal('Melebihi Stok', 'Cart akan menunjukkan stok negatif', 'warning');
                }
                console.log('⚠️ User set qty beyond available stock; allowing and showing warning');
                // do not revert; allow change so offline workflow can proceed
            }
        }

        // Update orderItems array
        item.quantity = Math.abs(newQty) || 1;

        // Rebuild table and update totals
        if (typeof window.updateProductTable === 'function') {
            window.updateProductTable();
        }
        if (typeof window.updateOrderDisplay === 'function') {
            window.updateOrderDisplay();
        }
        // Update all totals
        if (typeof window.updateTotalHarga === 'function') {
            window.updateTotalHarga();
        }
        if (typeof window.updateTotalNameset === 'function') {
            window.updateTotalNameset();
        }
        if (typeof window.updateTotalDiskon === 'function') {
            window.updateTotalDiskon();
        }
        if (typeof window.updateGrandTotal === 'function') {
            window.updateGrandTotal();
        }
    };

    // Function to update nameset (like point_of_sale_v2)
    window.namesetPrice = function(index) {
        if (!window.orderItems || !window.orderItems[index]) return;

        const namesetValue = parseFloat(jQuery(`#nameset_price${index}`).val()) || 0;

        // Validasi tidak boleh minus
        if (namesetValue < 0) {
            if (typeof swal !== 'undefined') {
                swal('Minus', 'Nameset tidak boleh minus', 'warning');
            }
            jQuery(`#nameset_price${index}`).val(window.orderItems[index].nameset || 0);
            return false;
        }

        // Update orderItems array
        window.orderItems[index].nameset = namesetValue;

        // Rebuild table and update totals
        if (typeof window.updateProductTable === 'function') {
            window.updateProductTable();
        }
        if (typeof window.updateOrderDisplay === 'function') {
            window.updateOrderDisplay();
        }
        // Update all totals
        if (typeof window.updateTotalHarga === 'function') {
            window.updateTotalHarga();
        }
        if (typeof window.updateTotalNameset === 'function') {
            window.updateTotalNameset();
        }
        if (typeof window.updateTotalDiskon === 'function') {
            window.updateTotalDiskon();
        }
        if (typeof window.updateGrandTotal === 'function') {
            window.updateGrandTotal();
        }
    };
</script>

<!-- Sidebar Order List and Quantity Controls -->
<script>
    $(document).ready(function() {
        // REMOVED: styleTableRow and styleAllRows functions - not needed anymore
        // We build table directly from orderItems array with correct styling via updateProductTable()

        // Function to update sidebar order list (like point_of_sale_v2) - using orderItems array
        window.updateOrderDisplay = function() {
            console.log('🔄 updateOrderDisplay called');

            // Rebuild table from orderItems array

            const $orderList = jQuery('#order-items-list');
            if (!$orderList.length) {
                console.warn('⚠️ order-items-list not found');
                return;
            }

            $orderList.empty();

            if (!window.orderItems || window.orderItems.length === 0) {
                $orderList.html('<p class="text-gray-500 text-center text-sm py-3">No items in order</p>');
                const $itemCount = jQuery('#item-count');
                if ($itemCount.length) {
                    $itemCount.text('0 Items');
                }
                return;
            }


            window.orderItems.forEach((item, index) => {
                const qty = Math.abs(item.quantity || 1);
                const displayQty = qty;

                let displayName = item.name || '';
                if (item.brand) {
                    displayName = displayName.replace(/^\[.*?\]\s*/, '');
                }

                let imageHtml = '';
                const productImage = item.image || '';
                if (productImage && productImage.trim() !== '' && !productImage.includes('ui-avatars.com')) {

                    imageHtml = `<img src="${productImage}" alt="${displayName}" class="w-9 h-9 object-cover rounded mr-3" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(brandName || 'P')}&background=F74040&color=fff&size=60'">`;

                } else {
                    const initial = displayName ? displayName.charAt(0).toUpperCase() : (item.brand ? item.brand.charAt(0).toUpperCase() : 'P');
                    imageHtml = `<div class="w-9 h-9 p-3 rounded bg-red-500 flex items-center justify-center text-white font-bold text-sm">${initial}</div>`;
                }

                const subtotal = item.subtotal || (item.price * qty) + (parseFloat(item.nameset) || 0);
                const displaySubtotal = `Rp. ${window.formatNumber ? window.formatNumber(subtotal) : subtotal.toLocaleString('id-ID')}`;
                const bgClass = 'bg-gray-100';
                const textClass = 'text-gray-900';

                // Quantity controls (like point_of_sale_v2)
                const quantityControls = `
                        <div class="relative flex items-center max-w-[6rem] shadow-xs rounded-base">
                            <button type="button" onclick="window.decreaseQuantity(${index})" class="text-body bg-white box-border border rounded-r-none border-gray-300 hover:bg-gray-500 hover:text-heading focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-lg text-xs px-2 focus:outline-none h-8">
                                <svg class="w-3 h-3 text-heading" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>
                            </button>
                            <input type="text" data-input-counter class="border-gray-300 h-8 placeholder:text-heading text-center w-full bg-white border-gray-300 py-1.5 placeholder:text-body text-sm" placeholder="999" value="${qty}" onchange="window.updateQuantityFromSidebar(${index}, this.value)" />
                            <button type="button" onclick="window.increaseQuantity(${index})" class="text-body bg-white box-border border rounded-l-none border-gray-300 hover:bg-gray-500 hover:text-heading focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-lg text-xs px-2 focus:outline-none h-8">
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

            const $itemCount = jQuery('#item-count');
            if ($itemCount.length) {
                $itemCount.text(`${window.orderItems.length} Items`);
            }

            console.log('✅ Sidebar updated with', window.orderItems.length, 'items');
        };

        // Quantity controls (like point_of_sale_v2)
        window.increaseQuantity = function(index) {
            if (window.orderItems[index]) {
                const newQty = window.orderItems[index].quantity + 1;
                // Use updateQuantityFromSidebar to ensure all updates happen
                window.updateQuantityFromSidebar(index, newQty);
            }
        };

        window.decreaseQuantity = function(index) {
            if (window.orderItems[index]) {
                if (window.orderItems[index].quantity > 1) {
                    const newQty = window.orderItems[index].quantity - 1;
                    // Use updateQuantityFromSidebar to ensure all updates happen
                    window.updateQuantityFromSidebar(index, newQty);
                } else {
                    // Remove item
                    window.orderItems.splice(index, 1);

                    // Rebuild table
                    if (typeof window.updateProductTable === 'function') {
                        window.updateProductTable();
                    }

                    // Update sidebar
                    if (typeof window.updateOrderDisplay === 'function') {
                        window.updateOrderDisplay();
                    }

                    // Update all totals
                    if (typeof window.updateTotalHarga === 'function') {
                        window.updateTotalHarga();
                    }
                    if (typeof window.updateTotalNameset === 'function') {
                        window.updateTotalNameset();
                    }
                    if (typeof window.updateTotalDiskon === 'function') {
                        window.updateTotalDiskon();
                    }
                    if (typeof window.updateGrandTotal === 'function') {
                        window.updateGrandTotal();
                    }
                }
            }
        };

        window.updateQuantityFromSidebar = function(index, newQty) {
            const qty = parseInt(newQty) || 1;
            if (window.orderItems[index] && qty > 0) {
                // B1G1 validation (same as changeQty)
                const item = window.orderItems[index];
                if (item.isB1G1 || item.item_type === 'b1g1') {
                    // Count total B1G1 items
                    let b1g1TotalQty = 0;
                    window.orderItems.forEach(function(i) {
                        if (i.isB1G1 || i.item_type === 'b1g1') {
                            b1g1TotalQty += Math.abs(parseInt(i.quantity) || 1);
                        }
                    });

                    if (b1g1TotalQty > 2) {
                        jQuery(`#item_qty${index}`).val(item.quantity || 1);
                        if (typeof swal !== 'undefined') {
                            swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs', 'warning');
                        }
                        // Revert sidebar input
                        updateOrderDisplay();
                        return false;
                    }

                    if (qty > 2) {
                        if (typeof swal !== 'undefined') {
                            swal('1 Invoice 1 B1G1', 'Silahkan checkout item diinvoice yang baru apabila lebih dari 2pcs', 'warning');
                        }
                        // Revert sidebar input
                        updateOrderDisplay();
                        return false;
                    }
                }

                // Update orderItems array
                window.orderItems[index].quantity = qty;

                // Rebuild table to reflect quantity change
                if (typeof window.updateProductTable === 'function') {
                    window.updateProductTable();
                }

                // Update sidebar display
                if (typeof window.updateOrderDisplay === 'function') {
                    window.updateOrderDisplay();
                }

                // Update all totals
                if (typeof window.updateTotalHarga === 'function') {
                    window.updateTotalHarga();
                }
                if (typeof window.updateTotalNameset === 'function') {
                    window.updateTotalNameset();
                }
                if (typeof window.updateTotalDiskon === 'function') {
                    window.updateTotalDiskon();
                }
                if (typeof window.updateGrandTotal === 'function') {
                    window.updateGrandTotal();
                }

                console.log('✅ Quantity updated from sidebar:', qty, 'for item index:', index);
            }
        };


        // Update order display initially - with multiple attempts
        function updateOrderDisplayNow() {
            // Rebuild table from orderItems array (removes old Bootstrap rows)
            if (typeof window.updateProductTable === 'function') {
                window.updateProductTable();
            }
            if (typeof window.updateOrderDisplay === 'function') {
                console.log('⏰ Calling updateOrderDisplay from timeout');
                window.updateOrderDisplay();
            }
        }

        // Try multiple times with increasing delays
        setTimeout(updateOrderDisplayNow, 500);
        setTimeout(updateOrderDisplayNow, 1000);
        setTimeout(updateOrderDisplayNow, 2000);
        setTimeout(updateOrderDisplayNow, 3000);
        setTimeout(updateOrderDisplayNow, 5000);

        // Also try when window loads
        jQuery(window).on('load', function() {
            setTimeout(updateOrderDisplayNow, 1000);
            setTimeout(updateOrderDisplayNow, 3000);
            setTimeout(updateOrderDisplayNow, 5000);
        });

        // Clear all button handler (like point_of_sale_v2)
        jQuery(document).on('click', '#clear-all', function(e) {
            e.preventDefault();

            if (confirm('Yakin ingin menghapus semua item dari keranjang?')) {
                // Clear orderItems array (like point_of_sale_v2)
                window.orderItems = [];

                // Remove all product rows from table (try multiple selectors)
                jQuery('#orderTable tbody tr[data-list-item]').remove();
                jQuery('#orderTableBody tr[data-list-item]').remove();
                jQuery('#orderTable tbody tr[id^="orderList"]').remove();
                jQuery('#orderTableBody tr[id^="orderList"]').remove();

                jQuery('#orderTable tbody tr, #orderTableBody tr').each(function() {
                    const $row = jQuery(this);
                    if ($row.find('th').length === 0 && $row.find('td').length > 3) {
                        $row.remove();
                    }
                });

                // Reset total row counter
                const $totalRow = jQuery('#total_row');
                if ($totalRow.length) {
                    $totalRow.val(0);
                }

                // Update sidebar (will show empty message)
                if (typeof window.updateOrderDisplay === 'function') {
                    window.updateOrderDisplay();
                }

                // Clear any summary values if needed
                jQuery('#total_item_side').text('0');
                jQuery('#total_price_side').text('0');
                jQuery('#total_final_price_side').text('0');
                jQuery('#total_nameset_side').text('0');

                // Also call updateGrandTotal if it exists (from offline_pos_js)
                if (typeof updateGrandTotal === 'function') {
                    updateGrandTotal();
                }

                console.log('✅ Cleared all items (orderItems array and table)');
            }
        });

        // REMOVED: MutationObserver - not needed anymore
        // We handle table rendering directly from orderItems array via updateProductTable()

        // Watch for changes in qty and subtotal to update sidebar
        jQuery(document).on('change', '#orderTable input[id^="item_qty"], #orderTable input[id^="discount_percentage"], #orderTable input[id^="discount_number"], #orderTable input[id^="nameset_price"]', function() {
            const $input = jQuery(this);
            const inputId = $input.attr('id') || '';

            // If nameset_price changed, call namesetPrice function (from offline_pos_js)
            if (inputId.includes('nameset_price')) {
                const indexMatch = inputId.match(/nameset_price(\d+)/);
                if (indexMatch && typeof namesetPrice === 'function') {
                    const index = indexMatch[1];
                    namesetPrice(index);
                } else if (typeof updateGrandTotal === 'function') {
                    // Fallback: just update grand total
                    updateGrandTotal();
                }
            }

            // Rebuild table and update sidebar
            setTimeout(function() {
                if (typeof window.updateProductTable === 'function' && window.orderItems && window.orderItems.length > 0) {
                    window.updateProductTable();
                }
                if (typeof window.updateOrderDisplay === 'function') {
                    window.updateOrderDisplay();
                }
            }, 300);
        });

        // Also watch for input events (for real-time updates)
        jQuery(document).on('input', '#orderTable input[id^="nameset_price"]', function() {
            const $input = jQuery(this);
            const inputId = $input.attr('id') || '';
            const indexMatch = inputId.match(/nameset_price(\d+)/);

            if (indexMatch && typeof namesetPrice === 'function') {
                const index = indexMatch[1];
                // Debounce the call
                clearTimeout(window.namesetTimeout);
                window.namesetTimeout = setTimeout(function() {
                    namesetPrice(index);
                }, 500);
            }
        });

        // ============================================
        // BARCODE FUNCTIONALITY (Same as offline pos lama)
        // ============================================
        jQuery(document).on('change', '#barcode_input', function(e) {
            e.preventDefault();
            const inpBarcode = jQuery(this).val().trim();
            if (!inpBarcode) return;

            const type = jQuery('#std_id option:selected').text() || '';
            const item_type = jQuery('#item_type option:selected').val() || 'store';
            const std_id = jQuery('#std_id').val() || '';
            const barcode = inpBarcode.replace(/(\r\n|\n|\r)/gm, '');

            jQuery('#barcode_input').val('');

            // Check if customer is selected
            if (!jQuery('#cust_id').val() || jQuery('#cust_id').val() === '') {
                if (typeof swal !== 'undefined') {
                    swal('Customer', 'Pilih customer terlebih dahulu', 'warning');
                }
                return false;
            }

            // If item_type is 'store', check waiting status first
            if (item_type === 'store') {
                jQuery.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url: "{{ url('has_waiting_status') }}",
                    method: "GET",
                    data: { barcode: barcode },
                    dataType: "json",
                    success: function(r) {
                        if (r.status == '200') {
                            if (confirm(r.message)) {
                                processBarcodeScan(barcode, type, item_type, std_id);
                            }
                        } else {
                            processBarcodeScan(barcode, type, item_type, std_id);
                        }
                    },
                    error: function() {
                        processBarcodeScan(barcode, type, item_type, std_id);
                    }
                });
            } else {
                processBarcodeScan(barcode, type, item_type, std_id);
            }
        });

        // Process barcode scan (adds product to orderItems array)
        function processBarcodeScan(barcode, type, item_type, std_id) {
            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                url: "{{ url('pos_barcode_scan') }}",
                method: "POST",
                dataType: "json",
                data: {
                    barcode: barcode,
                    type: type,
                    _item_type: item_type,
                    _std_id: std_id
                },
                beforeSend: function() {
                    jQuery('#barcode_input').prop('disabled', true);
                },
                success: function(r) {
                    if (r.status == '200') {
                        // Add product to orderItems array (same as product search)
                        const product = {
                            id: r.pst_id,
                            pst_id: r.pst_id,
                            name: r.p_name,
                            brand: r.br_name || '',
                            image: r.p_image ? "{{ asset('upload') }}/" + r.p_image : '',
                            price: parseFloat(r.sell_price) || 0,
                            pl_id: r.pl_id || null,
                            pls_qty: parseFloat(r.pls_qty) || 0,
                            plst_id: r.plst_id || null,
                            psc_id: r.psc_id || null,
                            bandrol: parseFloat(r.bandrol) || parseFloat(r.sell_price) || 0,
                            bin: `[${r.pl_code || 'STORE'}] [${r.pls_qty || 0}]`,
                            disc_percent: 0,
                            disc_rp: 0,
                            b1g1_id: r.b1g1_id || '',
                            b1g1_price: parseFloat(r.b1g1_price) || 0
                        };

                        // Get current item_type
                        const currentItemType = jQuery('#item_type').val() || 'store';
                        const isB1G1 = (currentItemType === 'b1g1' || (product.b1g1_id && product.b1g1_price));

                        const item = {
                            id: product.id,
                            name: product.brand ? '[' + product.brand + '] ' + product.name : product.name,
                            brand: product.brand,
                            image: product.image,
                            price: product.price,
                            quantity: 1,
                            bin: product.bin,
                            pl_id: product.pl_id,
                            pls_qty: product.pls_qty,
                            plst_id: product.plst_id,
                            discPercent: 0,
                            discRp: 0,
                            disc_percent: 0,
                            disc_number: 0,
                            nameset: 0,
                            marketplace: 0,
                            bandrol: product.bandrol,
                            psc_id: product.psc_id,
                            item_type: currentItemType,
                            b1g1_id: product.b1g1_id,
                            b1g1_price: product.b1g1_price,
                            isB1G1: isB1G1
                        };

                        // Check if product already exists
                        const existingIndex = window.orderItems.findIndex(i => i.id === item.id && i.pl_id === item.pl_id);
                        if (existingIndex >= 0) {
                            window.orderItems[existingIndex].quantity += 1;
                        } else {
                            item.index = window.orderItems.length;
                            window.orderItems.push(item);
                        }

                        // Update table and totals
                        if (typeof window.updateProductTable === 'function') {
                            window.updateProductTable();
                        }
                        if (typeof window.updateOrderDisplay === 'function') {
                            window.updateOrderDisplay();
                        }
                        if (typeof window.updateTotalHarga === 'function') {
                            window.updateTotalHarga();
                        }
                        if (typeof window.updateTotalNameset === 'function') {
                            window.updateTotalNameset();
                        }
                        if (typeof window.updateTotalDiskon === 'function') {
                            window.updateTotalDiskon();
                        }
                        if (typeof window.updateGrandTotal === 'function') {
                            window.updateGrandTotal();
                        }

                        if (typeof toast !== 'undefined') {
                            toast('Ditambah', 'Item berhasil ditambah', 'success');
                        }
                    } else {
                        if (typeof toast !== 'undefined') {
                            toast('Tidak Ditemukan', 'Barcode tidak ditemukan', 'warning');
                        }
                    }
                },
                complete: function() {
                    jQuery('#barcode_input').prop('disabled', false).focus();
                }
            });
        }

        // ============================================
        // INVOICE FUNCTIONALITY (Same as point_of_sale_v2)
        // ============================================
        let invoiceSearchTimeout;
        jQuery('#invoice_input').on('keyup', function() {
            const query = jQuery(this).val().trim();
            const $autocomplete = jQuery('#invoice-autocomplete');

            clearTimeout(invoiceSearchTimeout);

            if (query.length > 4) {
                invoiceSearchTimeout = setTimeout(function() {
                    jQuery.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    jQuery.ajax({
                        url: "{{ url('autocomplete_invoice_offline') }}",
                        method: "POST",
                        data: { query: query },
                        success: function(data) {
                            if (data && data.trim() !== '') {
                                // Parse the HTML response and convert to modern Tailwind format (same as pos_v2)
                                const $temp = jQuery('<div>').html(data);
                                const invoices = [];

                                $temp.find('li a').each(function() {
                                    const $link = jQuery(this);
                                    const href = $link.attr('href');
                                    const invoiceText = $link.find('span').text().trim() || $link.text().trim();
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

        // Click outside to hide invoice autocomplete
        jQuery(document).on('click', function(e) {
            if (!jQuery(e.target).closest('#invoice_input, #invoice-autocomplete').length) {
                jQuery('#invoice-autocomplete').addClass('hidden').removeClass('block');
            }
        });

        // ============================================
        // RETUR FUNCTIONALITY (Same as pos_v2)
        // ============================================
        // Retur checkbox handler
        jQuery('#retur-checkbox').on('change', function() {
            const isChecked = jQuery(this).is(':checked');
            const $returContainer = jQuery('#retur-search-container');
            const $returBadge = jQuery('#retur-type-badge');

            if (isChecked) {
                $returContainer.removeClass('hidden');
                $returBadge.text('ACTIVE');
                jQuery('#_exchange').val('true');
            } else {
                $returContainer.addClass('hidden');
                $returBadge.text('');
                jQuery('#_exchange').val('');
                jQuery('#_pt_id_complaint').val('');
                jQuery('#transaction-badge').addClass('hidden');
                jQuery('#retur-items-container').addClass('hidden');
                jQuery('#transaction-search').val('');
            }
        });

        // Transaction search handler (Same as point_of_sale_v2)
        let transactionSearchTimeout;
        jQuery('#transaction-search').on('input', function() {
            const searchTerm = jQuery(this).val().trim();
            const $transactionList = jQuery('#transaction-list');

            clearTimeout(transactionSearchTimeout);

            if (searchTerm.length < 5) {
                $transactionList.addClass('hidden').html('');
                return;
            }

            transactionSearchTimeout = setTimeout(function() {
                jQuery.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url: "{{ url('search_transaction_for_retur') }}",
                    method: "POST",
                    data: {
                        _token: jQuery('meta[name="csrf-token"]').attr('content'),
                        search: searchTerm
                    },
                    success: function(response) {
                        if (response.status === 'success' && response.data && response.data.length > 0) {
                            let html = '';
                            response.data.forEach(function(transaction) {
                                const formatNumber = function(num) {
                                    if (num === null || num === undefined || isNaN(num)) return '0';
                                    return new Intl.NumberFormat('id-ID').format(num || 0);
                                };

                                html += `
                                        <div class="transaction-item p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200"
                                             data-pt-id="${transaction.id}"
                                             data-invoice="${transaction.pos_invoice}"
                                             data-date="${transaction.created_at}"
                                             data-customer="${transaction.customer_name || ''}"
                                             data-cust-id="${transaction.cust_id || ''}"
                                             data-sub-cust-id="${transaction.sub_cust_id || ''}"
                                             data-std-id="${transaction.std_id || ''}">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <p class="font-semibold text-sm text-gray-900">${transaction.pos_invoice}</p>
                                                    <p class="text-xs text-gray-600">${transaction.customer_name || ''}</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-xs text-gray-500">${transaction.created_at || ''}</p>
                                                    <p class="text-sm font-semibold text-gray-900">Rp. ${formatNumber(transaction.pos_real_price || 0)}</p>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                            });
                            $transactionList.html(html).removeClass('hidden');
                        } else {
                            $transactionList.html('<div class="p-3 text-center text-gray-500 text-sm">Transaksi tidak ditemukan</div>').removeClass('hidden');
                        }
                    },
                    error: function() {
                        $transactionList.html('<div class="p-3 text-center text-red-500 text-sm">Error searching transactions</div>').removeClass('hidden');
                    }
                });
            }, 500);
        });

        // Transaction item click handler (Same as point_of_sale_v2)
        jQuery(document).on('click', '.transaction-item', function() {
            const ptId = jQuery(this).data('pt-id') || jQuery(this).attr('data-pt-id');
            const invoice = jQuery(this).data('invoice') || jQuery(this).attr('data-invoice');
            const date = jQuery(this).data('date') || jQuery(this).attr('data-date');
            const customer = jQuery(this).data('customer') || jQuery(this).attr('data-customer') || '';
            const custId = jQuery(this).data('cust-id') || jQuery(this).attr('data-cust-id') || '';
            const subCustId = jQuery(this).data('sub-cust-id') || jQuery(this).attr('data-sub-cust-id') || '';
            const stdId = jQuery(this).data('std-id') || jQuery(this).attr('data-std-id') || '';

            // Set hidden field
            jQuery('#_pt_id_complaint').val(ptId);

            // Display selected transaction badge
            jQuery('#selected-transaction-invoice').text(invoice);
            jQuery('#selected-transaction-date').text(`${date}${customer ? ' - ' + customer : ''}`);
            jQuery('#transaction-badge').removeClass('hidden');

            // Hide search list
            jQuery('#transaction-list').addClass('hidden');
            jQuery('#transaction-search').val('');

            // Auto-populate division and customer from transaction (Same as point_of_sale_v2)
            if (stdId) {
                const currentStdId = jQuery('#std_id').val();
                if (currentStdId != stdId) {
                    jQuery('#std_id').val(stdId);
                    // Reload customer list by division
                    reloadCustomerByDivision(stdId);
                }

                // Wait a bit then load customer
                setTimeout(function() {
                    if (custId) {
                        loadCustomerForRetur(custId, subCustId, customer);
                    }
                }, 800);
            }

            // Load transaction items
            loadReturItems(ptId);
        });

        // Load items from selected transaction (Same as point_of_sale_v2)
        function loadReturItems(ptId) {
            if (!ptId) {
                console.warn('⚠️ No pt_id provided for loadReturItems');
                return;
            }

            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: "{{ url('get_transaction_items_for_retur') }}",
                method: 'POST',
                data: {
                    _token: jQuery('meta[name="csrf-token"]').attr('content'),
                    pt_id: ptId
                },
                success: function(response) {
                    if (response.status === 'success' && response.data && response.data.length > 0) {
                        // Store retur items data globally
                        window.returItemsData = response.data;
                        displayReturItems(response.data);
                    } else {
                        if (typeof toast !== 'undefined') {
                            toast('Tidak ada item', 'Tidak ada item yang bisa diretur', 'warning');
                        }
                        jQuery('#retur-items-container').addClass('hidden');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading retur items:', error);
                    if (typeof toast !== 'undefined') {
                        toast('Gagal', 'Loading item transaksi gagal', 'danger');
                    }
                }
            });
        }

        // Display retur items with checkboxes (Same as point_of_sale_v2)
        function displayReturItems(items) {
            if (!items || items.length === 0) {
                jQuery('#retur-items-list').html('<div class="p-3 text-center text-gray-500 text-sm">Tidak ada item</div>');
                return;
            }

            let html = '';
            items.forEach(function(item, index) {
                // Use image or initial like regular order items
                let imageHtml = '';
                if (item.image) {
                    imageHtml = `<img src="{{ asset('upload') }}/${item.image}" alt="${item.product_name || ''}" class="w-12 h-12 object-cover rounded border border-gray-200" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(item.product_name || 'P')}&background=F74040&color=fff&size=60'">`;
                } else {
                    const initial = item.product_name ? item.product_name.charAt(0).toUpperCase() : 'P';
                    imageHtml = `<div class="w-12 h-12 rounded border border-gray-200 bg-red-100 flex items-center justify-center text-red-600 font-bold text-lg">${initial}</div>`;
                }

                const formatNumber = function(num) {
                    if (num === null || num === undefined || isNaN(num)) return '0';
                    return new Intl.NumberFormat('id-ID').format(num || 0);
                };

                html += `
                        <div class="retur-item-card p-3 bg-white border border-gray-200 rounded-lg hover:border-red-300 transition-colors">
                            <div class="flex items-start gap-3">
                                <input type="checkbox"
                                       class="retur-item-checkbox mt-1 w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500"
                                       data-item-index="${index}">
                                ${imageHtml}
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">${item.product_name || ''}</p>
                                    <p class="text-xs text-gray-600">${item.brand || ''} - ${item.color || ''} - ${item.size || ''}</p>
                                    <div class="flex justify-between items-center mt-2">
                                        <div>
                                            <span class="text-xs text-gray-500">Qty:</span>
                                            <input type="number"
                                                   class="retur-qty-input w-16 px-2 py-1 text-xs border border-gray-300 rounded focus:ring-red-500 focus:border-red-500"
                                                   data-item-index="${index}"
                                                   value="${item.qty || 1}"
                                                   min="1"
                                                   max="${item.qty || 1}"
                                                   disabled>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">Price:</p>
                                            <p class="text-sm font-semibold text-gray-900">Rp. ${formatNumber(item.price || 0)}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
            });

            jQuery('#retur-items-list').html(html);
            jQuery('#retur-items-container').removeClass('hidden');
        }

        // Load customer for retur (Same as point_of_sale_v2)
        function loadCustomerForRetur(custId, subCustId, customerName) {
            if (!custId) return;

            // Set customer ID and name
            jQuery('#cust_id_label').val(customerName);
            jQuery('#cust_id').val(custId);

            // Fetch full customer details to get customer type
            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: "{{ url('check_customer') }}",
                method: 'POST',
                data: {
                    _token: jQuery('meta[name="csrf-token"]').attr('content'),
                    _cust_id: custId
                },
                success: function(r) {
                    if (r.status == '200') {
                        // Show customer badge with name and type
                        jQuery('#customer-badge').removeClass('hidden');
                        jQuery('.customer-name').text(r.cust_name);
                        jQuery('.customer-type').text(r.ct_name || 'Customer');

                        // If sub customer exists (for dropshipper)
                        if (subCustId) {
                            jQuery('#sub_cust_id').val(subCustId);
                            // Fetch sub customer name
                            jQuery.ajax({
                                url: "{{ url('check_customer') }}",
                                method: 'POST',
                                data: {
                                    _token: jQuery('meta[name="csrf-token"]').attr('content'),
                                    _cust_id: subCustId
                                },
                                success: function(subR) {
                                    if (subR.status == '200') {
                                        jQuery('#sub-customer-search').val(subR.cust_name);
                                        jQuery('#dropship-input-container').removeClass('hidden');
                                        jQuery('#dropshipper-badge').removeClass('hidden');
                                        jQuery('.dropshipper-name').text(r.cust_name);
                                    }
                                }
                            });
                        }
                    }
                },
                error: function() {
                    // Fallback if AJAX fails
                    jQuery('#customer-badge').removeClass('hidden');
                    jQuery('.customer-name').text(customerName);
                    jQuery('.customer-type').text('Customer');
                }
            });
        }

        // Reload customer by division (Same as point_of_sale_v2)
        function reloadCustomerByDivision(stdId) {
            if (!stdId) return;

            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                type: "GET",
                dataType: 'html',
                data: {
                    _std_id: stdId
                },
                url: "{{ url('reload_customer_by_division') }}",
                success: function(response) {
                    console.log('Customer list reloaded for division:', stdId);
                },
                error: function(xhr, status, error) {
                    console.error('Error reloading customer by division:', error);
                }
            });
        }

        // Enable qty input when checkbox is checked (Same as point_of_sale_v2)
        jQuery(document).on('change', '.retur-item-checkbox', function() {
            const index = jQuery(this).data('item-index');
            const qtyInput = jQuery(`.retur-qty-input[data-item-index="${index}"]`);

            if (jQuery(this).is(':checked')) {
                qtyInput.prop('disabled', false);
                // Add to order items as negative qty
                addReturItemToOrder(index);
            } else {
                qtyInput.prop('disabled', true).val(window.returItemsData[index].qty);
                // Remove from order items
                removeReturItemFromOrder(index);
            }
        });

        // Update qty for retur item (Same as point_of_sale_v2)
        jQuery(document).on('change', '.retur-qty-input', function() {
            const index = jQuery(this).data('item-index');
            const newQty = parseInt(jQuery(this).val());
            const maxQty = window.returItemsData[index].qty;

            if (newQty > maxQty) {
                if (typeof swal !== 'undefined') {
                    swal('Qty Melebihi Pembelian', `Qty retur tidak boleh melebihi pembelian (max: ${maxQty})`, 'warning');
                }
                jQuery(this).val(maxQty);
                return;
            }

            if (newQty < 1) {
                jQuery(this).val(1);
                return;
            }

            // Update in order items
            updateReturItemQty(index, newQty);
        });

        // Add retur item to order with negative qty (Same as point_of_sale_v2)
        function addReturItemToOrder(itemIndex) {
            if (!window.returItemsData || !window.returItemsData[itemIndex]) {
                console.warn('⚠️ returItemsData not available');
                return;
            }

            const item = window.returItemsData[itemIndex];
            const qtyInput = jQuery(`.retur-qty-input[data-item-index="${itemIndex}"]`);
            const qty = parseInt(qtyInput.val()) || item.qty;

            // Check if already exists
            const existingIndex = window.orderItems.findIndex(oi => oi.returItemIndex === itemIndex);
            if (existingIndex !== -1) {
                return; // Already added
            }

            // Use image or initial like regular order items
            let imageUrl = '';
            if (item.image) {
                imageUrl = "{{ asset('upload') }}/" + item.image;
            }

            // Add as negative qty
            const returItem = {
                id: item.pst_id,
                product_id: item.p_id,
                name: item.product_name,
                brand: item.brand || '',
                color: item.color || '',
                size: item.size || '',
                image: imageUrl,
                price: parseFloat(item.price) || 0,
                qty: -Math.abs(qty), // NEGATIVE!
                quantity: -Math.abs(qty), // Also set quantity for compatibility
                disc_percent: 0,
                disc_number: 0,
                discRp: 0,
                nameset: 0,
                marketplace: 0,
                bandrol: parseFloat(item.price) || 0,
                isRetur: true,
                returItemIndex: itemIndex,
                plst_id: item.plst_id || null,
                item_type: 'waiting' // Retur items are usually waiting type
            };

            returItem.index = window.orderItems.length;
            window.orderItems.push(returItem);

            // Set exchange flag to true
            jQuery('#exchange_flag').val('true');

            // Update table and totals
            if (typeof window.updateProductTable === 'function') {
                window.updateProductTable();
            }
            if (typeof window.updateOrderDisplay === 'function') {
                window.updateOrderDisplay();
            }
            if (typeof window.updateTotalHarga === 'function') {
                window.updateTotalHarga();
            }
            if (typeof window.updateTotalNameset === 'function') {
                window.updateTotalNameset();
            }
            if (typeof window.updateTotalDiskon === 'function') {
                window.updateTotalDiskon();
            }
            if (typeof window.updateGrandTotal === 'function') {
                window.updateGrandTotal();
            }

            if (typeof toast !== 'undefined') {
                toast('Ditambahkan', `Item retur ditambahkan: ${item.product_name}`, 'success');
            }
        }

        // Remove retur item from order (Same as point_of_sale_v2)
        function removeReturItemFromOrder(itemIndex) {
            window.orderItems = window.orderItems.filter(item => item.returItemIndex !== itemIndex);

            // Check if still has retur items
            const hasReturItems = window.orderItems.some(item => item.isRetur === true);
            if (!hasReturItems) {
                jQuery('#exchange_flag').val(''); // No retur items, reset flag
            }

            // Update table and totals
            if (typeof window.updateProductTable === 'function') {
                window.updateProductTable();
            }
            if (typeof window.updateOrderDisplay === 'function') {
                window.updateOrderDisplay();
            }
            if (typeof window.updateTotalHarga === 'function') {
                window.updateTotalHarga();
            }
            if (typeof window.updateTotalNameset === 'function') {
                window.updateTotalNameset();
            }
            if (typeof window.updateTotalDiskon === 'function') {
                window.updateTotalDiskon();
            }
            if (typeof window.updateGrandTotal === 'function') {
                window.updateGrandTotal();
            }
        }

        // Update retur item qty (Same as point_of_sale_v2)
        function updateReturItemQty(itemIndex, newQty) {
            const existingIndex = window.orderItems.findIndex(oi => oi.returItemIndex === itemIndex);
            if (existingIndex !== -1) {
                window.orderItems[existingIndex].qty = -Math.abs(newQty);
                window.orderItems[existingIndex].quantity = -Math.abs(newQty);

                // Update table and totals
                if (typeof window.updateProductTable === 'function') {
                    window.updateProductTable();
                }
                if (typeof window.updateOrderDisplay === 'function') {
                    window.updateOrderDisplay();
                }
                if (typeof window.updateTotalHarga === 'function') {
                    window.updateTotalHarga();
                }
                if (typeof window.updateTotalNameset === 'function') {
                    window.updateTotalNameset();
                }
                if (typeof window.updateTotalDiskon === 'function') {
                    window.updateTotalDiskon();
                }
                if (typeof window.updateGrandTotal === 'function') {
                    window.updateGrandTotal();
                }
            }
        }

        // Initialize returItemsData array
        window.returItemsData = [];

        // Clear transaction button (Same as point_of_sale_v2)
        jQuery('#clear-transaction-btn').on('click', function() {
            jQuery('#transaction-badge').addClass('hidden');
            jQuery('#retur-items-container').addClass('hidden');
            jQuery('#retur-items-list').html('');
            jQuery('#_pt_id_complaint').val('');
            jQuery('#exchange_flag').val('');
            jQuery('#transaction-search').val('');

            // Remove retur items from order
            if (window.orderItems) {
                window.orderItems = window.orderItems.filter(item => item.isRetur !== true);

                // Update table and totals
                if (typeof window.updateProductTable === 'function') {
                    window.updateProductTable();
                }
                if (typeof window.updateOrderDisplay === 'function') {
                    window.updateOrderDisplay();
                }
                if (typeof window.updateTotalHarga === 'function') {
                    window.updateTotalHarga();
                }
                if (typeof window.updateTotalNameset === 'function') {
                    window.updateTotalNameset();
                }
                if (typeof window.updateTotalDiskon === 'function') {
                    window.updateTotalDiskon();
                }
                if (typeof window.updateGrandTotal === 'function') {
                    window.updateGrandTotal();
                }
            }

            // Clear retur items data
            window.returItemsData = [];
        });

        // Click outside to hide transaction list
        jQuery(document).on('click', function(e) {
            if (!jQuery(e.target).closest('#transaction-search, #transaction-list').length) {
                jQuery('#transaction-list').addClass('hidden').removeClass('block');
            }
        });

        // Watch for changes in subtotal spans (when calculated by backend)
        subtotalObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' || mutation.type === 'characterData') {
                    setTimeout(function() {
                        if (typeof window.updateOrderDisplay === 'function') {
                            window.updateOrderDisplay();
                        }
                    }, 200);
                }
            });
        });

        // Observe all existing subtotal spans
        setTimeout(function() {
            jQuery('#orderTable span.subtotal_item').each(function() {
                subtotalObserver.observe(this, { childList: true, characterData: true, subtree: true });
            });
        }, 1000);
    });
</script>

<script>
    // Convert Bootstrap modals to Flowbite on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure all modals are hidden by default
        document.querySelectorAll('.modal, [id*="modal"], [id*="Modal"]').forEach(function(modal) {
            if (!modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
            }
            // Remove Bootstrap show class if exists
            modal.classList.remove('show', 'fade', 'in');
            // Remove inline display styles
            if (modal.style.display === 'block' || modal.style.display === '') {
                modal.style.display = 'none';
            }
        });

        // Convert Bootstrap modal attributes to Flowbite
        document.querySelectorAll('[data-toggle="modal"]').forEach(function(trigger) {
            const target = trigger.getAttribute('data-target');
            if (target) {
                const modalId = target.replace('#', '');
                trigger.setAttribute('data-modal-target', modalId);
                trigger.setAttribute('data-modal-toggle', modalId);
                trigger.removeAttribute('data-toggle');
                trigger.removeAttribute('data-target');
            }
        });

        // Convert Bootstrap modal close buttons
        document.querySelectorAll('[data-dismiss="modal"]').forEach(function(closeBtn) {
            const modal = closeBtn.closest('.modal');
            if (modal) {
                const modalId = modal.id;
                if (modalId) {
                    closeBtn.setAttribute('data-modal-hide', modalId);
                    closeBtn.removeAttribute('data-dismiss');
                }
            }
        });

        // Convert Bootstrap modals to Flowbite structure
        document.querySelectorAll('.modal').forEach(function(modal) {
            // Ensure modal is hidden by default
            if (!modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
            }
            modal.classList.remove('show', 'fade', 'in');
            if (modal.style.display === 'block' || modal.style.display === '') {
                modal.style.display = 'none';
            }

            // Ensure modal has proper Flowbite structure
            if (!modal.hasAttribute('tabindex')) {
                modal.setAttribute('tabindex', '-1');
            }
            if (!modal.hasAttribute('aria-hidden')) {
                modal.setAttribute('aria-hidden', 'true');
            }

            // Add Flowbite classes if not present
            if (!modal.classList.contains('overflow-y-auto')) {
                modal.classList.add('overflow-y-auto', 'overflow-x-hidden', 'fixed', 'top-0', 'right-0', 'left-0', 'z-50', 'justify-center', 'items-center', 'w-full', 'md:inset-0', 'h-[calc(100%-1rem)]', 'max-h-full');
            }

            // Convert modal-dialog to Flowbite structure
            const modalDialog = modal.querySelector('.modal-dialog');
            if (modalDialog) {
                if (!modalDialog.classList.contains('relative')) {
                    modalDialog.classList.add('relative', 'p-4', 'w-full', 'max-w-2xl', 'max-h-full');
                }
                // Move modal-content classes to modal-dialog if needed
                const modalContent = modalDialog.querySelector('.modal-content');
                if (modalContent) {
                    if (!modalContent.classList.contains('relative')) {
                        modalContent.classList.add('relative', 'bg-white', 'rounded-lg', 'shadow', 'dark:bg-gray-800');
                    }
                }
            } else {
                // If no modal-dialog, ensure modal-content has proper structure
                const modalContent = modal.querySelector('.modal-content');
                if (modalContent && !modalContent.classList.contains('relative')) {
                    modalContent.classList.add('relative', 'p-4', 'w-full', 'max-w-2xl', 'max-h-full', 'bg-white', 'rounded-lg', 'shadow', 'dark:bg-gray-800');
                }
            }

            // Convert modal-header to Flowbite structure
            const modalHeader = modal.querySelector('.modal-header');
            if (modalHeader) {
                if (!modalHeader.classList.contains('flex')) {
                    modalHeader.classList.add('flex', 'items-center', 'justify-between', 'p-4', 'md:p-5', 'border-b', 'rounded-t', 'dark:border-gray-600');
                }
                // Convert close button
                const closeBtn = modalHeader.querySelector('.close, [data-dismiss="modal"]');
                if (closeBtn) {
                    closeBtn.setAttribute('data-modal-hide', modal.id);
                    closeBtn.removeAttribute('data-dismiss');
                    closeBtn.classList.add('text-gray-400', 'bg-transparent', 'hover:bg-gray-200', 'hover:text-gray-900', 'rounded-lg', 'text-sm', 'w-8', 'h-8', 'ms-auto', 'inline-flex', 'justify-center', 'items-center', 'dark:hover:bg-gray-600', 'dark:hover:text-white');
                }
            }

            // Convert modal-body to Flowbite structure
            const modalBody = modal.querySelector('.modal-body');
            if (modalBody) {
                if (!modalBody.classList.contains('p-4')) {
                    modalBody.classList.add('p-4', 'md:p-5');
                }
            }

            // Convert modal-footer to Flowbite structure
            const modalFooter = modal.querySelector('.modal-footer');
            if (modalFooter) {
                if (!modalFooter.classList.contains('flex')) {
                    modalFooter.classList.add('flex', 'items-center', 'justify-end', 'space-x-2', 'p-4', 'md:p-5', 'border-t', 'border-gray-200', 'dark:border-gray-600');
                }
                // Convert footer buttons
                modalFooter.querySelectorAll('button[data-dismiss="modal"]').forEach(function(btn) {
                    btn.setAttribute('data-modal-hide', modal.id);
                    btn.removeAttribute('data-dismiss');
                    if (!btn.classList.contains('px-4')) {
                        btn.classList.add('px-4', 'py-2', 'text-sm', 'font-medium', 'text-gray-500', 'bg-white', 'rounded-lg', 'border', 'border-gray-200', 'hover:bg-gray-100', 'hover:text-gray-900');
                    }
                });
            }

            // Initialize Flowbite modal - Only if Flowbite is available
            if (!modal.hasAttribute('data-flowbite-initialized') && typeof Flowbite !== 'undefined') {
                try {
                    new Flowbite.Modal(modal, {
                        backdrop: 'static',
                        closable: true
                    });
                    modal.setAttribute('data-flowbite-initialized', 'true');
                } catch(e) {
                    // Silently fail - modal will be initialized when needed
                }
            }
        });

        // Initialize all modals after a delay to ensure Flowbite is loaded
        // Wait for Flowbite to be fully ready (same approach as pos_v2.js)
        let retryCount = 0;
        const maxRetries = 50; // Max 5 seconds (50 * 100ms)
        function initializeFlowbiteModals() {
            if (typeof Flowbite === 'undefined' || !Flowbite.Modal) {
                retryCount++;
                if (retryCount < maxRetries) {
                    setTimeout(initializeFlowbiteModals, 100);
                } else {
                    console.warn('Flowbite.Modal not available after max retries, skipping initialization');
                }
                return;
            }

            console.log('Flowbite.Modal is available, initializing modals...');

            // List of all modal IDs to initialize
            const modalIds = [
                'modal-customer',
                'modal-customer-detail',
                'modal-voucher',
                'modal-discount',
                'payment-offline-popup',
                'RefundExchangeModal',
                'DpExchangeModal',
                'InputCodeModal',
                'OfferModal',
                'shiftEmployeeModal',
                'inputKasModal',
                'ProductBarcodeModal',
                'modal-shift-detail',
                'InputCodeModal'
            ];

            // Initialize each modal
            modalIds.forEach(function(modalId) {
                const modal = document.getElementById(modalId);
                if (modal && !modal.hasAttribute('data-flowbite-initialized')) {
                    try {
                        new Flowbite.Modal(modal, {
                            backdrop: 'static',
                            closable: true
                        });
                        modal.setAttribute('data-flowbite-initialized', 'true');
                        console.log('Modal initialized:', modalId);
                    } catch(e) {
                        console.warn('Error initializing modal ' + modalId + ':', e);
                    }
                }
            });

            // Also initialize any other modals found in DOM
            document.querySelectorAll('[id*="modal"], [id*="Modal"]').forEach(function(modal) {
                if (modal && !modal.hasAttribute('data-flowbite-initialized') && modalIds.indexOf(modal.id) === -1) {
                    try {
                        new Flowbite.Modal(modal, {
                            backdrop: 'static',
                            closable: true
                        });
                        modal.setAttribute('data-flowbite-initialized', 'true');
                        console.log('Additional modal initialized:', modal.id);
                    } catch(e) {
                        console.warn('Error initializing additional modal ' + modal.id + ':', e);
                    }
                }
            });

            console.log('All modals initialization completed');
        }

        // Start initialization after a delay - wait longer for Flowbite to load
        // Wait for both DOM and Flowbite to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(initializeFlowbiteModals, 1000);
            });
        } else {
            setTimeout(initializeFlowbiteModals, 1000);
        }
    });

    // Phone number handler (Same as offline pos lama) - Wait for DOM
    jQuery(document).ready(function() {
        // Wait a bit for modal to be in DOM
        setTimeout(function() {
            const custPhoneInput = document.getElementById('cust_phone');
            if (custPhoneInput) {
                // Ketika input fokus, tambahkan awalan 08 jika kosong
                custPhoneInput.addEventListener('focus', function(e) {
                    if (e.target.value === '') {
                        e.target.value = '08';
                    }
                });

                // Ketika input berubah, pastikan awalan selalu 08
                custPhoneInput.addEventListener('input', function(e) {
                    let phoneNumber = e.target.value;
                    // Jika awalan bukan 08, tambahkan awalan 08 dan hapus karakter non-numerik
                    if (!phoneNumber.startsWith('08')) {
                        e.target.value = '08' + phoneNumber.replace(/[^0-9]/g, '').substring(2);
                    }
                });
            }
        }, 500);
    });

    // Modal compatibility - Convert Bootstrap modal calls to Flowbite
    jQuery(document).ready(function() {
        // Override jQuery modal to use Flowbite
        const originalModal = jQuery.fn.modal;
        jQuery.fn.modal = function(action) {
            const modalId = this.attr('id');
            let modalElement = document.getElementById(modalId);

            // Handle choosecustomer -> modal-customer mapping
            if (modalId === 'choosecustomer' && !modalElement) {
                modalElement = document.getElementById('modal-customer');
            }

            if (modalElement && typeof Flowbite !== 'undefined') {
                if (action === 'show') {
                    let modal = Flowbite.Modal.getInstance(modalElement);
                    if (!modal) {
                        modal = new Flowbite.Modal(modalElement, {
                            backdrop: modalElement.hasAttribute('data-modal-backdrop') ? modalElement.getAttribute('data-modal-backdrop') : 'static',
                            closable: true
                        });
                    }
                    modal.show();
                } else if (action === 'hide') {
                    const modal = Flowbite.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    } else {
                        modalElement.classList.add('hidden');
                    }
                }
                return this;
            }
            // Fallback to original if Flowbite not available or element not found
            return originalModal.apply(this, arguments);
        };

        // Convert data-toggle="modal" and data-target to Flowbite
        jQuery(document).on('click', '[data-toggle="modal"]', function(e) {
            e.preventDefault();
            const target = jQuery(this).data('target') || jQuery(this).attr('data-target');
            if (target) {
                const modalId = target.replace('#', '');
                const modalElement = document.getElementById(modalId);
                if (modalElement && typeof Flowbite !== 'undefined') {
                    let modal = Flowbite.Modal.getInstance(modalElement);
                    if (!modal) {
                        modal = new Flowbite.Modal(modalElement);
                    }
                    modal.show();
                }
            }
        });

        // Convert data-dismiss="modal" to Flowbite
        jQuery(document).on('click', '[data-dismiss="modal"]', function(e) {
            e.preventDefault();
            const modalElement = jQuery(this).closest('.modal, [id*="modal"], [id*="Modal"]')[0];
            if (modalElement && typeof Flowbite !== 'undefined') {
                const modal = Flowbite.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                } else {
                    modalElement.classList.add('hidden');
                }
            }
        });

        // Ensure elements using Flowbite-style attributes also work when Flowbite isn't initialized
        jQuery(document).on('click', '[data-modal-toggle], [data-modal-target]', function(e) {
            e.preventDefault();
            const $el = jQuery(this);
            const modalId = $el.attr('data-modal-target') || $el.attr('data-modal-toggle') || $el.data('modal-target') || $el.data('modal-toggle');
            if (!modalId) return;
            const modalEl = document.getElementById(modalId);
            if (!modalEl) return;

            // Prefer Flowbite if available
            if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                try {
                    let inst = Flowbite.Modal.getInstance(modalEl);
                    if (!inst) inst = new Flowbite.Modal(modalEl, { backdrop: modalEl.getAttribute('data-modal-backdrop') || 'static', closable: true });
                    inst.show();

                    // Special handling for customer modal
                    if (modalId === 'modal-customer') {
                        $('#_mode').val('add');
                        $('#cust_phone').val($('#cust_id_label').val());
                        $('#itemListCust').addClass('hidden').removeClass('block').empty();
                    }
                    return;
                } catch (err) {
                    console.warn('Flowbite modal show failed for', modalId, err);
                }
            }

            // Manual fallback: ensure flex centering classes, show modal and backdrop
            modalEl.classList.remove('hidden');
            modalEl.classList.add('flex', 'items-center', 'justify-center');
            modalEl.style.display = 'flex';
            modalEl.setAttribute('aria-hidden', 'false');

            // Special handling for customer modal
            if (modalId === 'modal-customer') {
                $('#_mode').val('add');
                $('#cust_phone').val($('#cust_id_label').val());
                $('#itemListCust').addClass('hidden').removeClass('block').empty();
            }

            // Add a simple backdrop to mimic modal behavior if not present
            if (!document.getElementById('manual-modal-backdrop')) {
                const bd = document.createElement('div');
                bd.id = 'manual-modal-backdrop';
                bd.className = 'fixed inset-0 bg-black bg-opacity-50 z-40';
                bd.addEventListener('click', function() {
                    // clicking backdrop hides any manual modal
                    document.querySelectorAll('.modal, [id*="modal"], [id*="Modal"]').forEach(function(m) {
                        if (!m.hasAttribute('data-flowbite-initialized')) {
                            m.classList.add('hidden');
                            m.classList.remove('flex', 'items-center', 'justify-center');
                            m.style.display = 'none';
                            m.setAttribute('aria-hidden', 'true');
                        }
                    });
                    bd.remove();
                });
                document.body.appendChild(bd);
            }
        });

        // Handle data-modal-hide as a fallback for manual hiding
        jQuery(document).on('click', '[data-modal-hide]', function(e) {
            e.preventDefault();
            const target = jQuery(this).attr('data-modal-hide') || jQuery(this).data('modal-hide') || jQuery(this).data('modalHide');
            if (!target) {
                const modalElement = jQuery(this).closest('.modal, [id*="modal"], [id*="Modal"]')[0];
                // Defensive: ensure Flowbite and Modal API exist before calling
                if (modalElement && typeof Flowbite !== 'undefined' && Flowbite && Flowbite.Modal && typeof Flowbite.Modal.getInstance === 'function') {
                    try {
                        let modal = Flowbite.Modal.getInstance(modalElement);
                        if (!modal && typeof Flowbite.Modal === 'function') {
                            modal = new Flowbite.Modal(modalElement, { backdrop: modalElement.getAttribute('data-modal-backdrop') || 'static', closable: true });
                        }
                        if (modal && typeof modal.hide === 'function') {
                            modal.hide();
                            return;
                        }
                    } catch (err) {
                        console.warn('Flowbite modal hide failed (auto-create):', err);
                    }
                }
                if (modalElement) {
                    modalElement.classList.add('hidden');
                    modalElement.classList.remove('flex', 'items-center', 'justify-center');
                    modalElement.style.display = 'none';
                    modalElement.setAttribute('aria-hidden', 'true');
                }
                return;
            }

            const modalEl = document.getElementById(target);
            if (!modalEl) return;
            if (typeof Flowbite !== 'undefined' && Flowbite && Flowbite.Modal && typeof Flowbite.Modal.getInstance === 'function') {
                try {
                    let modal = Flowbite.Modal.getInstance(modalEl);
                    if (!modal && typeof Flowbite.Modal === 'function') {
                        modal = new Flowbite.Modal(modalEl, { backdrop: modalEl.getAttribute('data-modal-backdrop') || 'static', closable: true });
                    }
                    if (modal && typeof modal.hide === 'function') {
                        modal.hide();
                        return;
                    }
                } catch (err) {
                    console.warn('Flowbite modal hide failed (auto-create):', err);
                }
            }
            modalEl.classList.add('hidden');
            modalEl.classList.remove('flex', 'items-center', 'justify-center');
            modalEl.style.display = 'none';
            modalEl.setAttribute('aria-hidden', 'true');
            // Remove manual backdrop if present
            const bd = document.getElementById('manual-modal-backdrop');
            if (bd) bd.remove();
        });
    });
</script>

<!-- Debug Product Search Script -->
<script>
    $(document).ready(function() {
        // Wait for offline_pos_js to load
        setTimeout(function() {
            // Debug: Log when product search is triggered
            $('#product_name_input').on('keyup', function() {
                const query = $(this).val().trim();
                console.log('=== Product Input Keyup ===');
                console.log('Query:', query, 'Length:', query.length);

                if (query.length > 2) {
                    // Check if itemList exists
                    const $itemList = $('#itemList');
                    console.log('itemList check:', {
                        exists: $itemList.length > 0,
                        visible: $itemList.hasClass('block'),
                        hidden: $itemList.hasClass('hidden'),
                        display: $itemList.css('display')
                    });
                }
            });

            // Monitor itemList for changes
            const itemListElement = document.getElementById('itemList');
            if (itemListElement) {
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'childList') {
                            console.log('=== itemList Mutation ===');
                            console.log('Children added:', mutation.addedNodes.length);
                            console.log('Children removed:', mutation.removedNodes.length);
                            const $itemList = $('#itemList');
                            console.log('itemList state:', {
                                htmlLength: $itemList.html().length,
                                children: $itemList.children().length,
                                visible: $itemList.hasClass('block'),
                                hidden: $itemList.hasClass('hidden')
                            });

                            // Force visibility if content exists
                            const htmlLength = $itemList.html().length;
                            if (htmlLength > 100 && $itemList.hasClass('hidden')) {
                                console.log('Forcing itemList to be visible, htmlLength:', htmlLength);
                                $itemList.removeClass('hidden').addClass('block').css('display', 'block');
                            } else if (htmlLength > 100) {
                                // Content exists but might be getting cleared - prevent it
                                console.log('itemList has content, preventing clear, htmlLength:', htmlLength);
                            }
                        }
                    });
                });

                observer.observe(itemListElement, {
                    childList: true,
                    subtree: true
                });
            }
        }, 1500);
    });
</script>

<script>
    // Real-time Clock (matching pos_v2 style)
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');

        document.getElementById('current-time').textContent = `${hours}:${minutes}:${seconds}`;

        // Tooltip for full date/time
        const clockContainer = document.getElementById('clock-container');
        if (clockContainer) {
            clockContainer.title = now.toLocaleString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }
    }

    setInterval(updateClock, 1000);
    updateClock();

    // Phone number formatting for customer input - Check if element exists
    const custIdLabel = document.getElementById('cust_id_label');
    if (custIdLabel) {
        custIdLabel.addEventListener('input', function (e) {
            let inputText = e.target.value;
            if (inputText.startsWith('+62')) {
                e.target.value = '08' + inputText.substring(3);
            } else if (inputText.startsWith('62')) {
                e.target.value = '08' + inputText.substring(2);
            }
        });
    }

    // Show posContent after customer is selected (adjust based on original logic) - Check if element exists
    const checkCustomerBtn = document.getElementById('check_customer');
    if (checkCustomerBtn) {
        checkCustomerBtn.addEventListener('click', function() {
            const posContent = document.getElementById('posContent');
            if (posContent) {
                posContent.style.display = 'block';
            }
        });
    }

    // Calculator functionality (matching pos_v2)
    let calculatorExpression = '';
    let calculatorDisplay = '0';
    let justCalculated = false;

    $('#calculatorButton').on('click', function (e) {
        e.stopPropagation();
        $('#calculatorDropdown').toggleClass('hidden');
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#calculatorButton, #calculatorDropdown').length) {
            $('#calculatorDropdown').addClass('hidden');
        }
    });

    $('.calc-btn').on('click', function () {
        const value = String($(this).data('value')); // 🔥 FIX UTAMA
        const operators = ['+', '-', '×', '÷'];

        // CLEAR
        if (value === 'C') {
            calculatorExpression = '';
            calculatorDisplay = '0';
            justCalculated = false;
            $('#calculator-input').text('0');
            return;
        }

        // EQUALS
        if (value === '=') {
            if (!calculatorExpression) return;

            try {
                const exp = calculatorExpression
                    .replace(/×/g, '*')
                    .replace(/÷/g, '/');

                const result = Function(`return ${exp}`)();
                calculatorDisplay = String(result);
                calculatorExpression = calculatorDisplay;
                justCalculated = true;

                $('#calculator-input').text(calculatorDisplay);
            } catch {
                $('#calculator-input').text('Error');
                calculatorExpression = '';
                calculatorDisplay = '0';
                justCalculated = false;
            }
            return;
        }

        // OPERATOR
        if (operators.includes(value)) {
            if (!calculatorExpression) return;

            const lastChar = calculatorExpression.slice(-1);
            calculatorExpression = operators.includes(lastChar)
                ? calculatorExpression.slice(0, -1) + value
                : calculatorExpression + value;

            calculatorDisplay = calculatorExpression;
            justCalculated = false;
            $('#calculator-input').text(calculatorDisplay);
            return;
        }

        // DECIMAL
        if (value === '.') {
            const lastNumber = calculatorExpression
                .split(/[\+\-\×\÷]/)
                .pop();
            if (lastNumber.includes('.')) return;
        }

        // NUMBER
        if (!isNaN(value)) {
            if (justCalculated) {
                calculatorExpression = value;
                calculatorDisplay = value;
                justCalculated = false;
            } else if (calculatorDisplay === '0') {
                calculatorExpression = value;
                calculatorDisplay = value;
            } else {
                calculatorExpression += value;
                calculatorDisplay += value;
            }

            $('#calculator-input').text(calculatorDisplay);
        }
    });

    // Optional: Add custom amount button
    // $('#add_custom_amount').on('click', function () {
    //     console.log('Final Value:', calculatorDisplay);
    //     // contoh:
    //     // $('#amount').val(calculatorDisplay);
    //     $('#calculatorDropdown').addClass('hidden');
    // });

    // Add custom amount from calculator
    $('#add_custom_amount').on('click', function() {
        const amount = calculatorDisplay === '0' ? '' : calculatorDisplay;
        // This will be handled by offline_pos_js
        if (typeof addCustomAmount === 'function') {
            addCustomAmount(amount);
        }
        $('#calculatorDropdown').addClass('hidden');
    });

    // Function to add custom amount to cart
    window.addCustomAmount = function(amount) {
        if (!amount || isNaN(amount) || parseFloat(amount) <= 0) {
            if (typeof showToast === 'function') {
                showToast('Masukkan jumlah yang valid', 'warning');
            }
            return;
        }

        const customAmount = parseFloat(amount);

        // Create custom item
        const item = {
            id: 'custom-amount-' + Date.now(), // Unique ID
            name: 'Custom Amount',
            brand: '',
            image: '',
            price: customAmount,
            quantity: 1,
            bin: 'CUSTOM',
            pl_id: null,
            pls_qty: 999999, // Unlimited stock for custom
            plst_id: null,
            discPercent: 0,
            discRp: 0,
            disc_percent: 0,
            disc_number: 0,
            nameset: 0,
            marketplace: 0,
            bandrol: customAmount,
            psc_id: null,
            item_type: 'normal',
            b1g1_id: null,
            b1g1_price: 0,
            isB1G1: false
        };

        // Set index
        item.index = window.orderItems.length;

        // Add to orderItems
        window.orderItems.push(item);

        // Update table
        window.updateProductTable();

        // Show success message
        if (typeof showToast === 'function') {
            showToast('Custom amount ditambahkan ke keranjang', 'success');
        }

        console.log('✅ Added custom amount to cart:', customAmount);
    };

    // Fix DataTables initialization after modal is shown
    document.addEventListener('DOMContentLoaded', function() {
        // Wait for offline_pos_js to load, then fix DataTables
        setTimeout(function() {
            // Re-initialize DataTables for current-shift-table if it exists
            if ($.fn.DataTable && $('#current-shift-table').length && !$.fn.DataTable.isDataTable('#current-shift-table')) {
                try {
                    var detail_shift = $('#current-shift-table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: '{{ route("current-shift.data") }}',
                            type: 'GET',
                            error: function(xhr, error, thrown) {
                                console.error('DataTables AJAX error:', error, thrown);
                                console.error('Response:', xhr.responseText);
                            }
                        },
                        columns: [{
                            data: 'pm_name',
                            name: 'pm_name'
                        }, {
                            data: 'total_pos_real_price',
                            name: 'total_pos_real_price',
                            render: function(data, type, row) {
                                if (typeof formatRupiahDetail === 'function') {
                                    return formatRupiahDetail(data);
                                }
                                return new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR'
                                }).format(data || 0);
                            }
                        }],
                        order: [[0, 'asc']],
                        paging: false,
                        searching: false,
                        info: false
                    });
                } catch(e) {
                    console.error('DataTables initialization error:', e);
                }
            }

            // Fix modal triggers for Flowbite
            $('#folder-btn, #folder-data-btn').on('click', function() {
                const modal = document.getElementById('modal-shift-detail');
                if (modal) {
                    const flowbiteModal = new Flowbite.Modal(modal);
                    flowbiteModal.show();
                    // Re-initialize DataTables when modal is shown
                    setTimeout(function() {
                        const table = $('#current-shift-table');
                        if (table.length) {
                            // Destroy existing DataTable if exists
                            if ($.fn.DataTable.isDataTable('#current-shift-table')) {
                                table.DataTable().destroy();
                            }

                            // Initialize new DataTable
                            try {
                                table.DataTable({
                                    processing: true,
                                    serverSide: true,
                                    ajax: {
                                        url: '{{ route("current-shift.data") }}',
                                        type: 'GET',
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        error: function(xhr, error, thrown) {
                                            console.error('DataTables AJAX error:', {
                                                status: xhr.status,
                                                error: error,
                                                thrown: thrown,
                                                response: xhr.responseText
                                            });
                                            // Show user-friendly error
                                            table.find('tbody').html('<tr><td colspan="2" class="text-center text-red-500">Error loading data. Please try again.</td></tr>');
                                        }
                                    },
                                    columns: [{
                                        data: 'pm_name',
                                        name: 'pm_name',
                                        defaultContent: '-'
                                    }, {
                                        data: 'total_pos_real_price',
                                        name: 'total_pos_real_price',
                                        defaultContent: 0,
                                        render: function(data, type, row) {
                                            if (type === 'display' || type === 'type') {
                                                if (typeof formatRupiahDetail === 'function') {
                                                    return formatRupiahDetail(data);
                                                }
                                                return new Intl.NumberFormat('id-ID', {
                                                    style: 'currency',
                                                    currency: 'IDR',
                                                    minimumFractionDigits: 0
                                                }).format(data || 0);
                                            }
                                            return data || 0;
                                        }
                                    }],
                                    order: [[0, 'asc']],
                                    paging: false,
                                    searching: false,
                                    info: false,
                                    language: {
                                        processing: 'Loading...',
                                        emptyTable: 'No data available'
                                    }
                                });
                            } catch(e) {
                                console.error('DataTables initialization error:', e);
                            }
                        }
                    }, 500);
                }
            });

            // Function to close customer modal
            function closeCustomerModal() {
                const modalElement = document.getElementById('modal-customer');
                if (!modalElement) {
                    return;
                }

                // Try to use Flowbite modal instance first
                if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                    const flowbiteModal = Flowbite.Modal.getInstance(modalElement);
                    if (flowbiteModal) {
                        try {
                            flowbiteModal.hide();
                        } catch(err) {
                            console.error('Error hiding modal with Flowbite:', err);
                        }
                    }
                }

                // Always ensure modal is hidden (even if Flowbite hide was called)
                modalElement.classList.add('hidden');
                modalElement.setAttribute('aria-hidden', 'true');
                modalElement.style.display = 'none';

                // Remove backdrop elements that Flowbite creates
                const backdrops = document.querySelectorAll('.fixed.inset-0.z-40.bg-gray-900, [data-modal-backdrop="static"]');
                backdrops.forEach(function(backdrop) {
                    backdrop.remove();
                });

                // Remove manual backdrop if exists
                const manualBackdrop = document.getElementById('manual-modal-backdrop');
                if (manualBackdrop) {
                    manualBackdrop.remove();
                }

                // Also remove body classes that Flowbite might add
                document.body.classList.remove('overflow-hidden');
            }

            // Ensure close button exists in modal header
            function ensureCloseButtonExists() {
                const modalElement = document.getElementById('modal-customer');
                if (!modalElement) {
                    return;
                }

                const header = modalElement.querySelector('.flex.items-center.justify-between');
                if (!header) {
                    return;
                }

                // Check if close button exists
                let closeBtn = document.getElementById('close-modal-customer');
                if (!closeBtn) {
                    // Recreate close button if it doesn't exist
                    closeBtn = document.createElement('button');
                    closeBtn.type = 'button';
                    closeBtn.id = 'close-modal-customer';
                    closeBtn.className = 'text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white';
                    closeBtn.setAttribute('data-modal-hide', 'modal-customer');
                    closeBtn.innerHTML = `
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        `;
                    header.appendChild(closeBtn);
                } else {
                    // Remove hidden class if it exists
                    closeBtn.classList.remove('hidden');
                }
            }

            // Check close button when modal is shown
            // $(document).on('click', '#add-customer-btn', function() {
            //     setTimeout(ensureCloseButtonExists, 100);
            // });

            // Fix customer modal close buttons - ensure they work properly
            $(document).on('click', '#close-modal-customer, #cancel-modal-customer, [data-modal-hide="modal-customer"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeCustomerModal();
            });

            // Ensure province and city change handlers work (event delegation for dynamically loaded modal)
            $(document).on('change', '#cust_province', function() {
                var province = jQuery(this).val();
                console.log('Province changed:', province);
                if (province) {
                    // Try to call reloadCity function (from offline_pos_v2_js)
                    if (typeof reloadCity === 'function') {
                        console.log('Using reloadCity function');
                        reloadCity(province);
                    } else if (typeof window.reloadCity === 'function') {
                        console.log('Using window.reloadCity function');
                        window.reloadCity(province);
                    } else {
                        // Fallback: direct AJAX call
                        console.log('Using fallback AJAX for reloadCity');
                        jQuery.ajax({
                            type: "GET",
                            data: { _province: province },
                            dataType: 'html',
                            url: "{{ url('reload_city') }}",
                            success: function(r) {
                                jQuery('#cust_city').html(r);
                                console.log('City options loaded');
                            },
                            error: function(xhr, status, error) {
                                console.error('Error loading city:', error);
                            }
                        });
                    }
                } else {
                    // Reset city and subdistrict if province is cleared
                    jQuery('#cust_city').html('<option value="">- Pilih -</option>');
                    jQuery('#cust_subdistrict').html('<option value="">- Pilih -</option>');
                }
            });

            $(document).on('change', '#cust_city', function() {
                var city = jQuery(this).val();
                console.log('City changed:', city);
                if (city) {
                    // Try to call reloadSubdistrict function (from offline_pos_v2_js)
                    if (typeof reloadSubdistrict === 'function') {
                        console.log('Using reloadSubdistrict function');
                        reloadSubdistrict(city);
                    } else if (typeof window.reloadSubdistrict === 'function') {
                        console.log('Using window.reloadSubdistrict function');
                        window.reloadSubdistrict(city);
                    } else {
                        // Fallback: direct AJAX call
                        console.log('Using fallback AJAX for reloadSubdistrict');
                        jQuery.ajax({
                            type: "GET",
                            data: { _city: city },
                            dataType: 'html',
                            url: "{{ url('reload_subdistrict') }}",
                            success: function(r) {
                                jQuery('#cust_subdistrict').html(r);
                                console.log('Subdistrict options loaded');
                            },
                            error: function(xhr, status, error) {
                                console.error('Error loading subdistrict:', error);
                            }
                        });
                    }
                } else {
                    // Reset subdistrict if city is cleared
                    jQuery('#cust_subdistrict').html('<option value="">- Pilih -</option>');
                }
            });

            // Auto prefix 08 for phone number (same as old version)
            $(document).on('focus', '#cust_phone', function(e) {
                if (e.target.value === '') {
                    e.target.value = '08';
                }
            });

            $(document).on('input', '#cust_phone', function(e) {
                let phoneNumber = e.target.value;
                if (!phoneNumber.startsWith('08')) {
                    e.target.value = '08' + phoneNumber.replace(/[^0-9]/g, '').substring(2);
                }
            });

            // Fix shift employee button - ensure modal shows
            $(document).on('click', '#shift-btn, #shift-employee-btn', function(e) {
                e.preventDefault();
                // Handler akan di-handle oleh offline_pos_js, tapi kita perlu convert modal
                setTimeout(function() {
                    const modals = ['shiftEmployeeModal', 'shift_on', 'shift_off', 'InputLabaShift', 'shiftDetailModal'];
                    modals.forEach(function(modalId) {
                        const modal = document.getElementById(modalId);
                        if (modal) {
                            if (!modal.hasAttribute('data-flowbite-initialized')) {
                                modal.classList.add('hidden');
                                modal.setAttribute('tabindex', '-1');
                                modal.setAttribute('aria-hidden', 'true');
                                if (!modal.classList.contains('overflow-y-auto')) {
                                    modal.classList.add('overflow-y-auto', 'overflow-x-hidden', 'fixed', 'top-0', 'right-0', 'left-0', 'z-50', 'justify-center', 'items-center', 'w-full', 'md:inset-0', 'h-[calc(100%-1rem)]', 'max-h-full');
                                }
                                try {
                                    new Flowbite.Modal(modal, {
                                        backdrop: 'static',
                                        closable: true
                                    });
                                    modal.setAttribute('data-flowbite-initialized', 'true');
                                } catch(e) {
                                    console.warn('Modal init warning for', modalId, e);
                                }
                            }
                        }
                    });
                }, 200);
            });

            // Fix product barcode button - convert Bootstrap modal to Flowbite
            $(document).on('click', '#product_barcode_btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const modalElement = document.getElementById('ProductBarcodeModal');
                if (!modalElement) {
                    console.error('ProductBarcodeModal not found');
                    return;
                }

                // Function to initialize or reload DataTable
                function reloadProductTable() {
                    const $ptb = jQuery('#Ptb');
                    if ($ptb.length === 0) {
                        console.warn('Table #Ptb not found');
                        return;
                    }

                    // Check if DataTable is initialized
                    let productTable = null;
                    if (typeof window.product !== 'undefined' && window.product) {
                        productTable = window.product;
                    } else if (typeof product !== 'undefined' && product) {
                        productTable = product;
                    } else {
                        // Try to get existing DataTable instance
                        try {
                            productTable = $ptb.DataTable();
                        } catch(err) {
                            // DataTable not initialized, need to initialize it
                            console.log('Initializing Product DataTable...');
                            if (typeof initializeProductDataTable === 'function') {
                                initializeProductDataTable();
                                productTable = window.product;
                            } else {
                                console.warn('initializeProductDataTable function not found');
                                return;
                            }
                        }
                    }

                    if (productTable && typeof productTable.draw === 'function') {
                        console.log('Reloading product DataTable...');
                        productTable.draw(false);
                    } else {
                        console.warn('Product DataTable not available, retrying...');
                        setTimeout(function() {
                            if (typeof window.product !== 'undefined' && window.product && window.product.draw) {
                                window.product.draw(false);
                            } else if (typeof initializeProductDataTable === 'function') {
                                initializeProductDataTable();
                            }
                        }, 500);
                    }
                }

                // Check if Flowbite.Modal is available, but don't block if not
                let useFlowbite = false;
                if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                    useFlowbite = true;
                } else {
                    console.warn('Flowbite.Modal is not available, using manual modal display');
                }

                // Show modal (with or without Flowbite)
                if (useFlowbite) {
                    let flowbiteModal = Flowbite.Modal.getInstance(modalElement);

                    if (!flowbiteModal) {
                        try {
                            flowbiteModal = new Flowbite.Modal(modalElement, {
                                backdrop: true, // Enable backdrop
                                closable: true,
                                placement: 'center'
                            });
                            modalElement.setAttribute('data-flowbite-initialized', 'true');
                        } catch(err) {
                            console.error('Error creating Flowbite modal:', err);
                            useFlowbite = false;
                        }
                    }

                    if (useFlowbite && flowbiteModal) {
                        modalElement.classList.remove('hidden');
                        modalElement.setAttribute('aria-hidden', 'false');
                        try {
                            // Set backdrop to static so it shows
                            flowbiteModal._config.backdrop = 'static';
                            flowbiteModal.show();
                        } catch(err) {
                            console.error('Error showing Flowbite modal:', err);
                            useFlowbite = false;
                        }
                    }
                }

                // Fallback to manual display if Flowbite failed
                if (!useFlowbite) {
                    modalElement.classList.remove('hidden');
                    modalElement.setAttribute('aria-hidden', 'false');
                    modalElement.style.display = 'flex';
                }

                // Always initialize DataTable after modal is shown
                setTimeout(function() {
                    console.log('Initializing Product DataTable after modal opened...');

                    // Try multiple ways to access the function
                    let initFunc = null;
                    if (typeof window.initializeProductDataTable === 'function') {
                        initFunc = window.initializeProductDataTable;
                    } else if (typeof initializeProductDataTable === 'function') {
                        initFunc = initializeProductDataTable;
                    }

                    // Fallback: initialize directly if function not found
                    if (!initFunc && typeof jQuery !== 'undefined' && jQuery.fn.DataTable) {
                        console.log('Function not found, initializing DataTable directly...');
                        const $ptb = jQuery('#Ptb');
                        if ($ptb.length > 0) {
                            // Destroy existing instance if any
                            try {
                                if (window.product) {
                                    window.product.destroy();
                                }
                            } catch(e) {
                                console.warn('Error destroying existing DataTable:', e);
                            }

                            // Initialize DataTable
                            window.product = $ptb.DataTable({
                                destroy: true,
                                processing: true,
                                serverSide: true,
                                responsive: true,
                                language: {
                                    processing: '<div class="w-full flex items-center justify-center p-4"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-500"></div><span class="ml-2 text-gray-700">Loading</span></div>',
                                    emptyTable: 'Tidak ada data',
                                    zeroRecords: 'Tidak ada data yang sesuai',
                                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                                    infoFiltered: '(disaring dari _MAX_ total data)',
                                    search: 'Cari:',
                                    lengthMenu: 'Tampilkan _MENU_ data',
                                    paginate: {
                                        first: 'Pertama',
                                        last: 'Terakhir',
                                        next: 'Selanjutnya',
                                        previous: 'Sebelumnya'
                                    }
                                },
                                dom: '<"flex flex-wrap items-center justify-between mb-4"<"flex items-center"l>>rt<"flex flex-wrap items-center justify-between mt-4"<"flex items-center"i><"flex items-center"p>>',
                                // Hide default DataTable search (we use custom search input)
                                searching: false,
                                ajax: {
                                    url: "{{ url('scan_adjustment_product_datatables_v2') }}",
                                    type: 'GET',
                                    beforeSend: function() {
                                        // Show loading indicator
                                        jQuery('#p_search_loading').removeClass('hidden');
                                    },
                                    data: function(d) {
                                        d.search = jQuery('#p_search').val() || '';
                                        console.log('DataTable AJAX request with search:', d.search);
                                    },
                                    complete: function() {
                                        // Hide loading indicator when request completes
                                        jQuery('#p_search_loading').addClass('hidden');
                                    },
                                    dataSrc: function(json) {
                                        console.log('DataTable AJAX response:', json);
                                        if (json && json.data) {
                                            return json.data;
                                        }
                                        return [];
                                    },
                                    error: function(xhr, error, thrown) {
                                        console.error('DataTable AJAX error:', {
                                            status: xhr.status,
                                            statusText: xhr.statusText,
                                            error: error,
                                            thrown: thrown,
                                            responseText: xhr.responseText
                                        });
                                        if (typeof showToast === 'function') {
                                            showToast('Gagal memuat data produk: ' + error, 'error');
                                        }
                                    }
                                },
                                columns: [
                                    {
                                        data: 'DT_RowIndex',
                                        name: 'DT_RowIndex',
                                        title: 'No',
                                        searchable: false,
                                        orderable: false,
                                        className: 'px-6 py-3 text-center text-gray-900 whitespace-nowrap'
                                    },
                                    {
                                        data: 'br_name',
                                        name: 'br_name',
                                        title: 'Brand',
                                        className: 'px-6 py-3 text-gray-900 whitespace-nowrap'
                                    },
                                    {
                                        data: 'p_name',
                                        name: 'p_name',
                                        title: 'Artikel',
                                        className: 'px-6 py-3 text-gray-900 whitespace-nowrap'
                                    },
                                    {
                                        data: 'p_color',
                                        name: 'p_color',
                                        title: 'Warna',
                                        className: 'px-6 py-3 text-gray-900 whitespace-nowrap'
                                    },
                                    {
                                        data: 'sz_name',
                                        name: 'sz_name',
                                        title: 'Size',
                                        className: 'px-6 py-3 text-gray-900 whitespace-nowrap'
                                    },
                                    {
                                        data: 'ps_barcode_show',
                                        name: 'ps_barcode',
                                        title: 'Barcode',
                                        className: 'px-6 py-3'
                                    }
                                ],
                                columnDefs: [
                                    {
                                        targets: 0,
                                        className: 'px-6 py-3 text-center text-gray-900 whitespace-nowrap'
                                    },
                                    {
                                        targets: [1, 2, 3, 4],
                                        className: 'px-6 py-3 text-gray-900 whitespace-nowrap'
                                    },
                                    {
                                        targets: 5,
                                        className: 'px-6 py-3'
                                    }
                                ],
                                order: [[1, 'asc']],
                                pageLength: 10,
                                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                                drawCallback: function(settings) {
                                    // Add Flowbite table styling after draw
                                    jQuery('#Ptb tbody tr').removeClass('odd even').addClass('bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
                                    jQuery('#Ptb tbody td').addClass('px-6 py-3 text-gray-900 dark:text-white');
                                    // Style barcode input fields
                                    jQuery('#Ptb .input_barcode_field').addClass('w-3/4 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white');
                                }
                            });

                            // Attach search handler after DataTable is initialized
                            jQuery('#p_search').off('keyup input').on('keyup input', function() {
                                const searchValue = jQuery(this).val();
                                const $loading = jQuery('#p_search_loading');
                                $loading.removeClass('hidden');

                                if (window.product && typeof window.product.draw === 'function') {
                                    console.log('Search triggered, calling draw:', searchValue);
                                    window.product.one('draw', function() {
                                        $loading.addClass('hidden');
                                    });
                                    window.product.draw(false);
                                }
                            });

                            console.log('Product DataTable initialized directly');
                        } else {
                            console.error('Table #Ptb not found!');
                        }
                    } else {
                        console.error('jQuery DataTable not available!');
                    }

                    if (initFunc) {
                        initFunc();
                    }
                }, 500);
            });

            // Handle close button for payment modal
            jQuery(document).on('click', '#close-payment-modal', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const paymentModal = document.getElementById('payment-offline-popup');
                if (paymentModal) {
                    if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                        const modal = Flowbite.Modal.getInstance(paymentModal);
                        if (modal) {
                            try {
                                modal.hide();
                            } catch (err) {
                                console.error('Error hiding payment modal:', err);
                            }
                        }
                    }
                    paymentModal.classList.add('hidden');
                }
            });                // Override Bootstrap modal calls for shift modals to use Flowbite
            const originalModalShow = $.fn.modal;
            $(document).on('DOMNodeInserted', function() {
                // Re-initialize modals that might be added dynamically
                setTimeout(function() {
                    const shiftModals = ['shiftEmployeeModal', 'shift_on', 'shift_off', 'InputLabaShift', 'shiftDetailModal'];
                    shiftModals.forEach(function(modalId) {
                        const modal = document.getElementById(modalId);
                        if (modal && !modal.hasAttribute('data-flowbite-initialized')) {
                            modal.classList.add('hidden');
                            modal.setAttribute('tabindex', '-1');
                            modal.setAttribute('aria-hidden', 'true');
                            if (!modal.classList.contains('overflow-y-auto')) {
                                modal.classList.add('overflow-y-auto', 'overflow-x-hidden', 'fixed', 'top-0', 'right-0', 'left-0', 'z-50', 'justify-center', 'items-center', 'w-full', 'md:inset-0', 'h-[calc(100%-1rem)]', 'max-h-full');
                            }
                            try {
                                new Flowbite.Modal(modal, {
                                    backdrop: 'static',
                                    closable: true
                                });
                                modal.setAttribute('data-flowbite-initialized', 'true');
                            } catch(e) {
                                console.warn('Shift modal init warning for', modalId, e);
                            }
                        }
                    });
                }, 100);
            });

            // Fix reload_refund_list button - load refund list from backend
            $('#reload_refund_list').on('click', function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/reload_refund_offline',
                    method: 'GET',
                    success: function(data) {
                        $('#refund_reload').html(data);
                    },
                    error: function() {
                        console.error('Error loading refund list');
                    }
                });
            });

            // Fix product search - ensure itemList shows/hides correctly
            $(document).on('click', function(e) {
                // Hide itemList when clicking outside
                if (!$(e.target).closest('#product_name_input, #itemList, #barcode_input, #invoice_input, #cust_id_label, #itemListCust, #transaction-search, #transaction-list').length && !$(e.target).is('#add_new_customer') && !$(e.target).closest('#add_new_customer').length) {
                    $('#itemList').addClass('hidden').removeClass('block');
                    $('#itemListCust').addClass('hidden').removeClass('block');
                    $('#transaction-list').addClass('hidden');
                }
            });

            // REMOVED: Duplicate retur checkbox handler and transaction search handler - already handled above (line 2842-2930)

            // Compatibility layer: Make Bootstrap modal functions work with Flowbite
            window.$ = window.$ || jQuery;
            if (window.$) {
                // Override Bootstrap modal functions to use Flowbite
                const originalModal = $.fn.modal;
                $.fn.modal = function(action) {
                    const modalId = this.attr('id') || this.data('target')?.replace('#', '');
                    if (!modalId) return this;

                    const modalElement = document.getElementById(modalId);
                    if (!modalElement) return this;

                    // Ensure modal is hidden by default and has Flowbite structure
                    if (!modalElement.classList.contains('hidden')) {
                        modalElement.classList.add('hidden');
                    }
                    modalElement.classList.remove('show', 'fade', 'in');
                    if (modalElement.style.display === 'block') {
                        modalElement.style.display = 'none';
                    }

                    // Add Flowbite structure if not present
                    if (!modalElement.hasAttribute('tabindex')) {
                        modalElement.setAttribute('tabindex', '-1');
                    }
                    if (!modalElement.hasAttribute('aria-hidden')) {
                        modalElement.setAttribute('aria-hidden', 'true');
                    }
                    if (!modalElement.classList.contains('overflow-y-auto')) {
                        modalElement.classList.add('overflow-y-auto', 'overflow-x-hidden', 'fixed', 'top-0', 'right-0', 'left-0', 'z-50', 'justify-center', 'items-center', 'w-full', 'md:inset-0', 'h-[calc(100%-1rem)]', 'max-h-full');
                    }

                    if (!modalElement.hasAttribute('data-flowbite-initialized')) {
                        try {
                            new Flowbite.Modal(modalElement, {
                                backdrop: 'static',
                                closable: true
                            });
                            modalElement.setAttribute('data-flowbite-initialized', 'true');
                        } catch(e) {
                            console.warn('Modal init warning for', modalId, e);
                        }
                    }

                    const flowbiteModal = new Flowbite.Modal(modalElement);

                    if (action === 'show' || action === 'toggle' || action === undefined) {
                        flowbiteModal.show();
                    } else if (action === 'hide') {
                        flowbiteModal.hide();
                    }

                    return this;
                };

                // Fix data-toggle="modal" triggers
                $(document).on('click', '[data-toggle="modal"]', function(e) {
                    e.preventDefault();
                    const target = $(this).data('target');
                    if (target) {
                        const targetId = target.replace('#', '');
                        const modal = document.getElementById(targetId);
                        if (modal && typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                            let flowbiteModal = Flowbite.Modal.getInstance(modal);
                            if (!flowbiteModal) {
                                try {
                                    flowbiteModal = new Flowbite.Modal(modal);
                                    modal.setAttribute('data-flowbite-initialized', 'true');
                                } catch(err) {
                                    console.warn('Modal init error:', err);
                                    return;
                                }
                            }
                            flowbiteModal.show();
                        }
                    }
                });

                // Fix data-dismiss="modal" buttons
                $(document).on('click', '[data-dismiss="modal"]', function(e) {
                    e.preventDefault();
                    const modal = $(this).closest('.modal, [id*="modal"], [id*="Modal"]');
                    if (modal.length) {
                        const modalId = modal.attr('id');
                        if (modalId && typeof Flowbite !== 'undefined' && Flowbite.Modal) {
                            const modalElement = document.getElementById(modalId);
                            if (modalElement) {
                                const flowbiteModal = Flowbite.Modal.getInstance(modalElement);
                                if (flowbiteModal) {
                                    flowbiteModal.hide();
                                } else {
                                    modalElement.classList.add('hidden');
                                }
                            }
                        }
                    }
                });
            }
        }, 1500); // Wait 1.5 seconds for offline_pos_js to fully load
    });
</script>

<script>
    // Override original POS functions that interfere with v2
    window.reloadWaitingForCheckout = function() {
        console.log('🚫 reloadWaitingForCheckout blocked - using v2 cart system');
        return false;
    };

    // Auto-restore cart from server on page load
    setTimeout(function() {
        if (typeof window.loadWaitingCartFromServer === 'function') {
            console.log('🔁 Auto-restoring waiting cart from server on page load...');
            window.loadWaitingCartFromServer();
        }
    }, 1500); // Increased delay to ensure all other scripts have loaded
</script>