<!DOCTYPE html>
<html lang="en" data-page="offline_pos_v2">
<head>
    <meta charset="utf-8" />
    <title>JEZ SYSTEM | Offline POS V2</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Flowbite CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.3.0/dist/flowbite.min.css" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS - Use same CSS as pos_v2 -->
    <link href="{{ asset('pos_v2/css/style.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app/assets/fonts/style.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app/assets/fonts/style-solid.css') }}" rel="stylesheet" type="text/css" />
    
    @include('app.offline_pos_v2.offline_pos_v2_css')

</head>
<body>
    <!-- Header -->
    @include('app.offline_pos_v2.offline_pos_v2_header')

    <!-- Main Content -->
    <div class="bg-body min-h-screen p-5">
        <div class="mx-0">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                <!-- Left Side - Product Area (Same as pos_v2) -->
                <div class="lg:col-span-9">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                        <!-- Search Bar (1 baris: Product, Barcode, Invoice) -->
                        <div class="mb-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                                <!-- Product Search -->
                                <div class="relative col-span-2">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                                        </div>
                                        <input type="text" id="product_name_input" placeholder="Search Product (min 3 chars)" autocomplete="off" class="block w-full p-3 ps-9 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-400 focus:border-red-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500"/>
                                        <div id="itemList" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-80 overflow-y-auto hidden divide-y divide-gray-200"></div>
                                    </div>
                                </div>
                                <!-- Barcode Input -->
                                <div class="relative col-span-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="cft-standard-stroke cft-qr-code text-gray-500 text-sm"></i>
                                    </div>
                                    <input type="text" id="barcode_input" placeholder="Barcode" autocomplete="off" class="block w-full p-3 ps-9 bg-green-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-400 focus:border-red-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500"/>
                                </div>
                                <!-- Invoice Input -->
                                <div class="relative col-span-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="cft-standard-stroke cft-receipt text-gray-500 text-sm"></i>
                                    </div>
                                    <input type="text" id="invoice_input" placeholder="Search Invoice (min 5 chars)" autocomplete="off" class="block w-full p-3 ps-9 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-400 focus:border-red-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500"/>
                                    <div id="invoice-autocomplete" class="absolute z-50 w-full min-w-20vw mt-1 bg-white border border-gray-200 rounded-lg shadow-sm max-h-60 overflow-y-auto hidden"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden Inputs -->
                        <input type="hidden" id="_pt_id" value=""/>
                        <input type="hidden" id="_pt_id_complaint" value=""/>
                        <input type="hidden" id="_exchange" value=""/>
                        <input type="hidden" id="cross_order" value="0"/>
                        <input type="hidden" value="{{ Auth::user()->u_name }}" id="u_name">
                        <input type="hidden" value="{{ $data['pst_custom']->id ?? '' }}" id="pst_custom">
                        <input type="hidden" value="{{ $data['psc_custom']->id ?? '' }}" id="psc_custom">
                        <input type="hidden" value="{{ $data['pl_custom']->id ?? '' }}" id="pl_custom">
                        <input type="hidden" value="{{ $data['store']->st_name ?? '' }}" id="store_name">

                        <div class="overflow-x-auto max-h-[calc(100vh-300px)]">
                            <input type="hidden" id="total_row" value="0"/>
                            <table id="orderTable" class="w-full overflow-x-auto text-xs text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 sticky top-0 z-10">
                                    <tr>
                                        <th scope="col" class="px-5 py-3">Product</th>
                                        <th scope="col" class="px-4 py-3">Stock</th>
                                        <th scope="col" class="px-4 py-3 text-center">Qty</th>
                                        <th scope="col" class="px-4 py-3 text-center">Discount Type</th>
                                        <th scope="col" class="px-4 py-3 text-center">Disc (%)</th>
                                        <th scope="col" class="px-4 py-3 text-center min-w-[150px]">Disc (Rp)</th>
                                        <th scope="col" class="px-4 py-3 text-center min-w-[150px]">Nameset</th>
                                        <th scope="col" class="px-4 py-3 text-right">Harga Bandrol</th>
                                        <th scope="col" class="px-4 py-3 text-right">Harga Jual</th>
                                        <th scope="col" class="px-4 py-3 text-right">Diskon Toko / Pcs</th>
                                        <th scope="col" class="px-4 py-3 text-right">Sub Total</th>
                                        <th scope="col" class="px-4 py-3 text-center"></th>
                                    </tr>
                                </thead>
                                <tbody id="orderTableBody" class="bg-white divide-y divide-gray-200">
                                    <!-- Table rows will be inserted here by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Voucher Information (Hidden) -->
                        <div id="voucher_information" class="hidden">
                            <input type="hidden" id="_voucher_value"/>
                            <input type="hidden" id="_voc_pst_id"/>
                            <input type="hidden" id="_voc_value"/>
                            <input type="hidden" id="_voc_disc_value"/>
                            <input type="hidden" id="_voc_total_disc_value">
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <div class="bg-white rounded-lg shadow-md p-5 sticky top-24 max-h-[calc(100vh-120px)] overflow-y-auto">
                        <div class="bg-red-500 text-white p-4 rounded-lg mb-5">
                            <div class="flex justify-between items-center">
                                <div class="flex items-start gap-2">
                                    <i class="fas fa-store pt-1"></i>
                                    <div>
                                        <span class="font-semibold block" id="store-name-display">{{ $data['store']->st_name ?? 'Store' }}</span>
                                        @if($data['store'] && $data['store']->st_address)
                                            <span class="text-xs opacity-90">{{ $data['store']->st_address }}</span>
                                        @endif
                                </div>
                                </div>
                                <div class="flex flex-col items-end ">
                                    <span class="bg-white/20 px-3 py-1 rounded-full text-xs font-semibold" id="store-id-display">#{{ $data['store']->id ?? '123345' }}</span>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold mt-1" id="shift-status-badge">Shift not started</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-5">
                            <h6 class="text-sm font-semibold text-gray-700 mb-3 uppercase">Customer Information</h6>
                            <div class="relative mb-2">
                                <input type="hidden" id="cust_id" value="1"/>
                                <div class="flex gap-1">
                                    <div class="relative w-full">
                                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <i class="cft-standard-stroke cft-user text-gray-500 text-sm"></i>
                                        </div>
                                        <input type="text" id="cust_id_label" placeholder="Search Customer (min 4 chars)" autocomplete="off" class="block w-full p-3 ps-9 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-400 focus:border-red-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500"/>
                                        <script>
                                            // Event listener untuk mengubah +62 atau 62 menjadi 08
                                            document.getElementById('cust_id_label').addEventListener('input', function (e) {
                                                let inputText = e.target.value;

                                                // Jika awalan +62, ubah menjadi 08
                                                if (inputText.startsWith('+62')) {
                                                    e.target.value = '08' + inputText.substring(3); // Ganti +62 di awal dengan 08
                                                }
                                                // Jika awalan 62, ubah menjadi 08
                                                else if (inputText.startsWith('62')) {
                                                    e.target.value = '08' + inputText.substring(2); // Ganti 62 di awal dengan 08
                                                }
                                            });
                                        </script>
                                        <div id="itemListCust" class="absolute z-50 w-full min-w-25vw mt-1 bg-white border border-gray-200 rounded-lg shadow-sm max-h-60 overflow-y-auto hidden"></div>
                                    </div>
                                    <button data-tooltip-target="tooltip-addcustomer" data-tooltip-style="light" class="px-3.5 py-2 bg-red-500 text-white border border-red-500 rounded-lg hover:bg-red-600 transition-colors" type="button" id="add-customer-btn" data-modal-target="modal-customer" data-modal-toggle="modal-customer">
                                        <i class="cft-standard-stroke cft-user-add text-white"></i>
                                    </button>
                                    <div id="tooltip-addcustomer" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-heading bg-neutral-primary-medium border border-default rounded-base shadow-xs opacity-0 tooltip">
                                        Add Customer
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-red-100 text-red-500 px-4 py-3 rounded-lg flex items-center gap-2 hidden" id="customer-badge">
                                <span class="customer-name font-semibold text-sm w-3/5"></span>
                                <div class="w-2/5 flex items-center justify-end gap-1">
                                <span class="customer-type bg-red-400 text-white px-1.5 py-0.5 rounded-lg text-xs w-full"></span>
                                    <i class="cft-standard-solid cft-info text-red-500 text-xl cursor-pointer hover:text-red-700 transition-colors" id="customer-detail-btn" data-modal-target="modal-customer-detail" data-modal-toggle="modal-customer-detail"></i>
                                </div>
                            </div>
                            
                            <input type="hidden" id="free_sock_customer_mode" value=""/>
                            <div class="hidden mt-2 p-3 bg-blue-50 rounded-lg" id="waiting_customer_label">
                                <span class="text-sm">Menunggu customer isi rating...</span>
                                <button class="ml-2 text-red-500 hover:text-red-700" id="cancel_rating_btn">
                                    <i class="fas fa-times"></i>
                                </button>
                                </div>
                            <div class="hidden mt-2 p-3 bg-blue-50 rounded-lg" id="free_sock_customer_panel">
                                <label class="text-sm font-semibold text-gray-700">RATING CUSTOMER</label>
                                <input type="text" id="free_sock_customer_label" class="w-full mt-2 px-3 py-2 border rounded-lg" value=""/>
                                <input type="hidden" id="free_sock_customer_id" value=""/>
                                <input type="hidden" id="free_sock_customer_ur_id" value=""/>
                            </div>
                        </div>

                        <div class="mb-5 pb-5 border-b border-gray-200">
                            <!-- <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="retur-checkbox" class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500">
                                        <label for="retur-checkbox" class="ml-2 text-sm font-semibold text-gray-700 uppercase">Retur/Exchange</label>
                                    </div>
                                    <span class="text-xs text-red-500" id="retur-type-badge"></span>
                                </div>
                                <button class="px-2 py-1 bg-cyan-500 hover:bg-cyan-600 text-white rounded-md transition-colors text-xs" id="product_barcode_btn">
                                    Lengkapi Barcode
                                </button>
                            </div> -->
                            
                            <div id="retur-search-container" class="hidden">
                                <label for="transaction-search" class="block mb-1 text-xs font-semibold text-gray-500">
                                    Search Transaction (Invoice / Order Number)
                                </label>
                                <div class="relative mb-3">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="cft-standard-stroke cft-receipt text-gray-500 text-sm"></i>
                                    </div>
                                    <input type="text" id="transaction-search" 
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-3 ps-9" 
                                        placeholder="INV... / ORD... (min 5 chars)"
                                        autocomplete="off">
                                    <div id="transaction-list" class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden"></div>
                                </div>
                                
                                <!-- Selected Transaction Badge -->
                                <div id="transaction-badge" class="hidden mb-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="text-xs text-red-600 font-semibold">Selected Transaction:</span>
                                            <p class="text-sm font-bold text-red-700" id="selected-transaction-invoice"></p>
                                            <p class="text-xs text-gray-600" id="selected-transaction-date"></p>
                                        </div>
                                        <button type="button" id="clear-transaction-btn" class="text-red-600 hover:text-red-800">
                                            <i class="cft-standard-solid cft-cancel text-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Retur Items List -->
                                <div id="retur-items-container" class="hidden">
                                    <label class="block mb-2 text-sm font-medium text-gray-900">
                                        <i class="cft-standard-stroke cft-box text-gray-700"></i>
                                        Select Items to Return
                                    </label>
                                    <div id="retur-items-list" class="space-y-2 max-h-48 overflow-y-auto bg-gray-50 p-3 rounded-lg border border-gray-200">
                                        <!-- Retur items will be loaded here -->
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hidden fields untuk backend -->
                            <input type="hidden" id="pt_id_complaint" value="">
                            <input type="hidden" id="exchange_flag" value="">
                            
                            <!-- Refund/Penukaran Button -->
                            <!-- <button class="w-full mt-3 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors font-medium" id="reload_refund_list">
                                Refund / Penukaran
                            </button>
                            <div id="refund_reload" class="mt-2"></div>
                            <button class="w-full mt-3 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors" id="product_barcode_btn">
                                Lengkapi Barcode
                            </button> -->
                        </div>

                        <!-- Order Details (Same as pos_v2) -->
                        <div class="mb-5 max-h-60 overflow-y-auto">
                            <div class="flex justify-between items-center mb-3">
                                <h6 class="text-sm font-semibold text-gray-700 uppercase">Order Details</h6>
                                <div class="flex items-center gap-2">
                                    <span class="bg-gray-900 text-white px-2 py-1 rounded text-xs font-semibold" id="item-count">0 Items</span>
                                    <a href="#" class="text-red-600 text-xs hover:underline" id="clear-all">Clear All</a>
                                </div>
                            </div>
                            <div id="order-items-list" class="space-y-2">
                                <!-- Order items will be added here -->
                            </div>
                        </div>

                        <!-- Summary (Same as offline pos lama) -->
                        <div class="bg-gray-100 p-4 rounded-lg mb-5">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm text-gray-700">Total Harga</span>
                                <span class="text-sm font-semibold" id="total_price_side">Rp. 0</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm text-gray-700">Total Nameset</span>
                                <span class="text-sm font-semibold" id="total_nameset_side">Rp. 0</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm text-gray-700 flex items-center gap-1">
                                    Voucher
                                    <button type="button" data-modal-target="modal-voucher" data-modal-toggle="modal-voucher" class="edit-icon-btn ml-auto">
                                        <i class="cft-standard-stroke cft-edit text-red-600 cursor-pointer hover:text-red-800 text-xs" data-edit="voucher"></i>
                                    </button>
                                </span>
                                <span class="text-sm font-semibold" id="voucher_total_value_side">Rp. 0</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-gray-700 flex items-center gap-1">
                                    Total Discount
                                    <button type="button" data-modal-target="modal-discount" data-modal-toggle="modal-discount" class="edit-icon-btn ml-auto">
                                        <i class="cft-standard-stroke cft-edit text-red-600 cursor-pointer hover:text-red-800 text-xs" data-edit="discount"></i>
                                    </button>
                                </span>
                                <span class="text-sm font-semibold" id="total_discount_value_side">Rp. 0</span>
                            </div>
                            <div class="flex justify-between items-center pt-3 mt-2 border-t-2 border-red-600">
                                <span class="text-base font-bold text-red-600">TOTAL</span>
                                <span class="text-base font-bold text-red-600" id="total_final_price_side">Rp. 0</span>
                            </div>
                        </div>

                        <!-- Pay Button (Same as pos_v2) -->
                        <button type="button" data-modal-target="payment-offline-popup" data-modal-toggle="payment-offline-popup" class="text-base w-full bg-red-500 hover:bg-red-500  text-white font-bold py-4 rounded-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg" id="payment_btn">
                            BAYAR
                        </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loader">
        <div class="loading-content">
            <img src="{{ asset('pos/jez.gif') }}" alt="loading" class="w-48 mx-auto bg-white p-4 rounded-lg shadow-lg">
            <div class="loading-text">Loading<span class="dots">...</span></div>
        </div>
    </div>

    <!-- Modals with Flowbite Style (Same as point_of_sale_v2) -->
    @include('app.offline_pos_v2.offline_pos_v2_modal')

    <!-- Inline debug for voucher (temporary) -->
    @include('app.offline_pos_v2.offline_pos_new_v2_js')

{{--        @include('app.offline_pos_v2.offline_pos_v2_js')--}}

</body>
</html>
