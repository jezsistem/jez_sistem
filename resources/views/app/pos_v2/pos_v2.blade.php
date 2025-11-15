<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>JEZ SYSTEM | Point Of Sale V2</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Flowbite CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.3.0/dist/flowbite.min.css" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link href="{{ asset('pos_v2/css/style.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app/assets/fonts/style.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app/assets/fonts/style-solid.css') }}" rel="stylesheet" type="text/css" />
</head>
<body>
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-5 z-50 rounded-lg m-5 border border-gray-100">
        <div class="mx-8 py-4">
            <div class="flex items-center justify-between">
                <div class="mr-12">
                    <img src="{{ asset('logo/POS.png') }}" alt="JEZ POS" class="h-11 w-auto">
                </div>
                <div class="w-5/6 flex items-center gap-4">
                    <div class="bg-cyan rounded-lg text-white px-3 py-1 font-semibold text-sm flex items-center gap-2">
                        <i class="cft-standard-stroke cft-clock text-white text-2xl"></i>
                        <span id="current-time text-lg">09:52:21</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" id="std_id" name="std_id">
                            <option value="">Division</option>
                            @foreach ($data['std_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <select class="bg-red-50 border border-red-200 text-gray-900 text-sm rounded-lg focus:ring-red-300 focus:border-red-300 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" id="st_id" name="st_id">
                            <option value="">Store</option>
                            @foreach ($data['st_id'] as $key => $value)
                                <option value="{{ $key }}" {{ $data['store'] && $data['store']->id == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-4 relative">
                    <button class="w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 transition-colors" title="Shopping Bag">
                        <i class="cft-standard-stroke cft-shopping-bag text-gray-600 text-2xl"></i>
                    </button>
                    <button class="w-10 h-10 rounded-lg bg-clock hover:bg-clock-dark flex items-center justify-center text-gray-600 transition-colors" title="Document">
                        <i class="cft-standard-stroke cft-clock-square text-white text-2xl"></i>
                    </button>
                    <!-- Calculator Button -->
                    <div class="relative">
                        <button id="calculatorButton" class="w-10 h-10 rounded-lg bg-calculator hover:bg-calculator-dark flex items-center justify-center text-gray-600 transition-colors" title="Calculator">
                            <i class="cft-standard-stroke cft-calculator text-white text-2xl"></i>
                        </button>
                        <!-- Calculator Dropdown -->
                        <div id="calculatorDropdown" class="hidden absolute right-0 mt-2 z-50 bg-white rounded-lg shadow-xl w-72 p-4 border border-gray-200">
                            <div class="calculator-container">
                                <div class="mb-3">
                                    <div id="calculator-input" class="w-full h-16 bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-right text-2xl font-semibold text-gray-900 overflow-x-auto flex items-center justify-end">
                                        0
                                    </div>
                                </div>
                                <div class="grid grid-cols-4 gap-2">
                                    <!-- Operators Row -->
                                    <button class="calc-btn calc-operator bg-blue-100 hover:bg-blue-200 text-blue-700 font-semibold" data-value="+">+</button>
                                    <button class="calc-btn calc-operator bg-blue-100 hover:bg-blue-200 text-blue-700 font-semibold" data-value="-">-</button>
                                    <button class="calc-btn calc-operator bg-blue-100 hover:bg-blue-200 text-blue-700 font-semibold" data-value="×">×</button>
                                    <button class="calc-btn calc-operator bg-blue-100 hover:bg-blue-200 text-blue-700 font-semibold" data-value="÷">÷</button>
                                    
                                    <!-- Numbers Row 1 -->
                                    <button class="calc-btn calc-number bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold" data-value="7">7</button>
                                    <button class="calc-btn calc-number bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold" data-value="8">8</button>
                                    <button class="calc-btn calc-number bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold" data-value="9">9</button>
                                    <button class="calc-btn calc-result bg-blue-600 hover:bg-blue-700 text-white font-bold row-span-4" id="calc-result">=</button>
                                    
                                    <!-- Numbers Row 2 -->
                                    <button class="calc-btn calc-number bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold" data-value="4">4</button>
                                    <button class="calc-btn calc-number bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold" data-value="5">5</button>
                                    <button class="calc-btn calc-number bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold" data-value="6">6</button>
                                    
                                    <!-- Numbers Row 3 -->
                                    <button class="calc-btn calc-number bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold" data-value="1">1</button>
                                    <button class="calc-btn calc-number bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold" data-value="2">2</button>
                                    <button class="calc-btn calc-number bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold" data-value="3">3</button>
                                    
                                    <!-- Numbers Row 4 -->
                                    <button class="calc-btn calc-number bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold col-span-2" data-value="0">0</button>
                                    <button class="calc-btn calc-number bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold" data-value=".">.</button>
                                </div>
                                <div class="mt-2">
                                    <button class="calc-btn calc-clear w-full bg-red-500 hover:bg-red-500 text-white font-semibold py-2 rounded-lg" id="calc-clear">Clear (C)</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button id="dropdownUserButton" data-dropdown-toggle="dropdownUser" class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center overflow-hidden transition-colors">
                        @php
                            $userName = $data['user']->u_name ?? 'User';
                            $fallbackUrl = 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=F74040&color=fff';
                            $logoPath = file_exists(public_path('logo/logo.png')) ? asset('logo/logo.png') : $fallbackUrl;
                        @endphp
                        <img src="{{ $logoPath }}" alt="User" class="w-10 h-10 rounded-full object-cover" onerror="this.onerror=null; this.src='{{ $fallbackUrl }}'">
                    </button>
                    <!-- Dropdown menu -->
                    <div id="dropdownUser" class="hidden z-10 bg-white divide-y divide-gray-100 rounded-lg shadow w-44">
                        <div class="px-4 py-3 text-sm text-gray-900">
                            <div class="font-medium">{{ $data['user']->u_name ?? 'User' }}</div>
                        </div>
                        <ul class="py-2 text-sm text-gray-700">
                            <li>
                                <a href="{{ route('logout') }}" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="bg-body min-h-screen p-5">
        <div class="mx-0">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                <!-- Left Side - Product Area -->
                <div class="lg:col-span-9">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                        <!-- Search Bar -->
                        <div class="mb-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                                <div class="md:col-span-3 relative">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                                        </div>
                                        <input type="text" id="search-product" placeholder="Search Product" autocomplete="off" class="block w-full p-3 ps-9 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-400 focus:border-red-400 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"/>
                                        <div id="product-autocomplete" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-sm max-h-80 overflow-y-auto hidden hover:bg-red-100"></div>
                                    </div>
                                </div>
                                <div class="md:col-span-1 relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="cft-standard-stroke cft-qr-code text-gray-500 text-sm"></i>
                                    </div>
                                    <input type="text" id="invoice-input" placeholder="Search Invoice (min 5 chars)" autocomplete="off" class="block w-full p-3 ps-9 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-400 focus:border-red-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"/>
                                    <div id="invoice-autocomplete" class="absolute z-50 w-full min-w-25vw mt-1 bg-white border border-gray-200 rounded-lg shadow-sm max-h-60 overflow-y-auto hidden"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Product Table -->
                        <div class="overflow-x-auto max-h-[calc(100vh-300px)]">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400" id="product-table">
                                <thead class="text-sm text-gray-700 uppercase bg-gray-100 sticky top-0 z-10">
                                    <tr>
                                        <th scope="col" class="px-5 py-3">Product</th>
                                        <th scope="col" class="px-4 py-3">BIN</th>
                                        <th scope="col" class="px-4 py-3 text-center">Disc (%)</th>
                                        <th scope="col" class="px-4 py-3 text-center">Disc (Rp)</th>
                                        <th scope="col" class="px-4 py-3 text-center">Unit</th>
                                        <th scope="col" class="px-4 py-3 text-center">Nameset</th>
                                        <th scope="col" class="px-4 py-3 text-center">Marketplace</th>
                                        <th scope="col" class="px-4 py-3 text-right">Harga</th>
                                        <th scope="col" class="px-4 py-3 text-right">Sub Total</th>
                                        <th scope="col" class="px-4 py-3 text-center"></th>
                                    </tr>
                                </thead>
                                <tbody id="product-tbody" class="bg-white divide-y divide-gray-200">
                                    <!-- Products will be added here dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Order Sidebar -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-lg shadow-md p-5 sticky top-24 max-h-[calc(100vh-120px)] overflow-y-auto">
                        <!-- Store Info -->
                        <div class="bg-red-500 text-white p-4 rounded-lg mb-5">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-store"></i>
                                    <span class="font-semibold" id="store-name-display">{{ $data['store']->st_name ?? 'Store' }}</span>
                                </div>
                                <span class="bg-white/20 px-3 py-1 rounded-full text-xs font-semibold" id="store-id-display">#{{ $data['store']->id ?? '123345' }}</span>
                            </div>
                        </div>

                        <!-- Hidden Inputs -->
                        <input type="hidden" id="_pt_id" value="">
                        <input type="hidden" id="_pt_id_complaint" value="">
                        <input type="hidden" id="_exchange" value="">
                        <input type="hidden" id="cross_order" value="">
                        <input type="hidden" id="_voc_pst_id" value="">
                        <input type="hidden" id="_voc_value" value="">
                        <input type="hidden" id="_voc_id" value="">

                        <!-- Customer Information -->
                        <div class="mb-5">
                            <h6 class="text-sm font-semibold text-gray-700 mb-3  uppercase">Customer Information</h6>
                            <div class="relative mb-2">
                                <input type="hidden" id="cust_id" value="">
                                <input type="hidden" id="sub_cust_id" value="">
                                <div class="flex gap-1">
                                    <div class="relative w-full">
                                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <i class="cft-standard-stroke cft-user text-gray-500 text-sm"></i>
                                        </div>
                                        <input type="text" id="customer-search" placeholder="Search Customer (min 4 chars)" autocomplete="off" class="block w-full p-3 ps-9 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-400 focus:border-red-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"/>
                                        <div id="customer-autocomplete" class="absolute z-50 w-full min-w-25vw mt-1 bg-white border border-gray-200 rounded-lg shadow-sm max-h-60 overflow-y-auto hidden"></div>
                                    </div>
                                    <button data-tooltip-target="tooltip-addcustomer" data-tooltip-style="light" class="px-3.5 py-2 bg-red-500 text-white border border-red-500 rounded-lg hover:bg-red-600 transition-colors" type="button" id="add-customer-btn" data-modal-target="modal-customer" data-modal-toggle="modal-customer">
                                        <i class="cft-standard-stroke cft-user-add text-white"></i>
                                    </button>
                                    <div id="tooltip-addcustomer" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-heading bg-neutral-primary-medium border border-default rounded-base shadow-xs opacity-0 tooltip">
                                        Add Customer
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                    <button data-tooltip-target="tooltip-dropshipper" data-tooltip-style="light" class="px-3.5 py-2 bg-gray-700 text-white border border-gray-900 rounded-lg hover:bg-gray-800 focus:bg-gray-800" type="button" id="dropship-btn">
                                        <i class="cft-standard-stroke cft-ship-box-2 text-white"></i>
                                    </button>
                                    <div id="tooltip-dropshipper" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-heading bg-neutral-primary-medium border border-default rounded-base shadow-xs opacity-0 tooltip">
                                        Search Dropshipper
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>

                                </div>
                            </div>
                            <!-- Dropship Input (hidden by default) -->
                            <div class="relative mb-2 hidden" id="dropship-input-container">
                                <div class="relative w-full">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="cft-standard-stroke cft-user text-gray-500 text-sm"></i>
                                    </div>
                                    <input type="text" id="sub-customer-search" placeholder="Search Dropshipper (min 4 chars)" autocomplete="off" class="block w-full p-3 ps-9 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-400 focus:border-red-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"/>
                                    <div id="sub-customer-autocomplete" class="absolute z-50 w-full min-w-25vw mt-1 bg-white border border-gray-200 rounded-lg shadow-sm max-h-60 overflow-y-auto hidden"></div>
                                </div>
                            </div>
                            <div class="bg-red-100 text-red-500 px-4 py-3 rounded-lg flex items-center gap-2 hidden" id="customer-badge">
                                <span class="customer-name font-semibold text-sm w-3/5"></span>
                                <div class="w-2/5 flex items-center justify-end gap-1">
                                <span class="customer-type bg-red-400 text-white px-1.5 py-0.5 rounded-lg text-xs w-full"></span>
                                    <i class="cft-standard-solid cft-info text-red-500 text-xl cursor-pointer hover:text-red-700 transition-colors" id="customer-detail-btn" data-modal-target="modal-customer-detail" data-modal-toggle="modal-customer-detail"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Retur Section -->
                        <div class="mb-5 pb-5 border-b border-gray-200">
                            <div class="flex items-center mb-2">
                                <input type="checkbox" id="retur-checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                <label for="retur-checkbox" class="ml-2 text-sm text-gray-700">Retur</label>
                            </div>
                            <input type="text" class="hidden w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mt-2" id="transaction-id" placeholder="Masukkan ID Transaksi">
                        </div>

                        <!-- Order Details -->
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

                        <!-- Summary -->
                        <div class="bg-gray-100 p-4 rounded-lg mb-5">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm text-gray-700">Sub Total</span>
                                <span class="text-sm font-semibold" id="subtotal">Rp. 0</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm text-gray-700 flex items-center gap-1">
                                    Ongkos Kirim
                                    <button type="button" data-modal-target="modal-shipping" data-modal-toggle="modal-shipping" class="edit-icon-btn ml-auto">
                                        <i class="cft-standard-stroke cft-edit text-red-600 cursor-pointer hover:text-red-800 text-xs" data-edit="shipping"></i>
                                    </button>
                                </span>
                                <span class="text-sm font-semibold" id="shipping-cost">Rp. 0</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm text-gray-700 flex items-center gap-1">
                                    Voucher
                                    <button type="button" data-modal-target="modal-voucher" data-modal-toggle="modal-voucher" class="edit-icon-btn ml-auto">
                                        <i class="cft-standard-stroke cft-edit text-red-600 cursor-pointer hover:text-red-800 text-xs" data-edit="voucher"></i>
                                    </button>
                                </span>
                                <span class="text-sm font-semibold" id="voucher-discount">Rp. 0</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-gray-700 flex items-center gap-1">
                                    Total Discount
                                    <button type="button" data-modal-target="modal-discount" data-modal-toggle="modal-discount" class="edit-icon-btn ml-auto">
                                        <i class="cft-standard-stroke cft-edit text-red-600 cursor-pointer hover:text-red-800 text-xs" data-edit="discount"></i>
                                    </button>
                                </span>
                                <span class="text-sm font-semibold" id="total-discount">Rp. 0</span>
                            </div>
                            <div class="flex justify-between items-center pt-3 mt-2 border-t-2 border-red-600">
                                <span class="text-base font-bold text-red-600">TOTAL</span>
                                <span class="text-base font-bold text-red-600" id="grand-total">Rp. 0</span>
                            </div>
                        </div>

                        <!-- Pay Button -->
                        <button class="text-base w-full bg-red-500 hover:bg-red-700 text-white font-bold py-4 rounded-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg" id="pay-btn">
                            BAYAR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ongkos Kirim -->
    <div id="modal-shipping" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">Tambah Ongkir</h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="modal-shipping">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <form id="form-shipping" class="p-4 md:p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="courier" class="block mb-2 text-sm font-medium text-gray-900">Kurir</label>
                            <select id="courier" name="courier" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="">- Pilih -</option>
                                @foreach ($data['courier'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="shipping-amount" class="block mb-2 text-sm font-medium text-gray-900">Total Ongkir</label>
                            <input type="number" id="shipping-amount" name="shipping-amount" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Total ongkir" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="flex items-center justify-end space-x-2">
                        <button type="button" data-modal-hide="modal-shipping" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900">Batal</button>
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Voucher -->
    <div id="modal-voucher" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-blue-600">Tambah Voucher</h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="modal-voucher">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <form id="form-voucher" class="p-4 md:p-5">
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Voucher</label>
                        <div id="voucher-container">
                            <div class="flex gap-2 mb-3">
                                <input type="text" name="voucher-list[]" class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5" placeholder="Kode Voucher" value="">
                                <button type="button" class="add-voucher px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end space-x-2">
                        <button type="button" data-modal-hide="modal-voucher" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900">Batal</button>
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Total Discount -->
    <div id="modal-discount" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-blue-600">Tambah Diskon</h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="modal-discount">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <form id="form-discount" class="p-4 md:p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="discount-type" class="block mb-2 text-sm font-medium text-gray-900">Tipe Diskon</label>
                            <select id="discount-type" name="discount-type-list" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="nominal">Nominal</option>
                                <option value="percentage">Percentage</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Diskon</label>
                            <div id="total-discount-container">
                                <div class="flex gap-2 mb-3">
                                    <input type="text" name="total-discount-list[]" class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5" placeholder="Diskon" value="">
                                    <button type="button" class="add-total-discount px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end space-x-2">
                        <button type="button" id="total_discount_reset" class="text-white bg-yellow-500 hover:bg-yellow-600 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5">Reset</button>
                        <button type="button" data-modal-hide="modal-discount" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900">Batal</button>
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Customer Detail Modal -->
    <div id="modal-customer-detail" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full" data-modal-backdrop="static">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Customer Details</h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="modal-customer-detail">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-4 md:p-5">
                    <div class="grid gap-4 mb-4 grid-cols-2">
                        <div class="col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipe Customer</label>
                            <div class="text-sm text-gray-700 dark:text-gray-300" id="detail-ct_id">-</div>
                        </div>
                        <div class="col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Customer</label>
                            <div class="text-sm text-gray-700 dark:text-gray-300 font-semibold" id="detail-cust_name">-</div>
                        </div>
                        <div class="col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Toko</label>
                            <div class="text-sm text-gray-700 dark:text-gray-300" id="detail-cust_store">-</div>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No Telp</label>
                            <div class="text-sm text-gray-700 dark:text-gray-300" id="detail-cust_phone">-</div>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                            <div class="text-sm text-gray-700 dark:text-gray-300" id="detail-cust_email">-</div>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Provinsi</label>
                            <div class="text-sm text-gray-700 dark:text-gray-300" id="detail-cust_province">-</div>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kota</label>
                            <div class="text-sm text-gray-700 dark:text-gray-300" id="detail-cust_city">-</div>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kecamatan</label>
                            <div class="text-sm text-gray-700 dark:text-gray-300" id="detail-cust_subdistrict">-</div>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                            <div class="text-sm" id="detail-cust_token_active">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">-</span>
                            </div>
                        </div>
                        <div class="col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat</label>
                            <div class="text-sm text-gray-700 dark:text-gray-300" id="detail-cust_address">-</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end">
                        <button type="button" data-modal-hide="modal-customer-detail" class="text-white bg-gray-600 hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Modal -->
    <div id="modal-customer" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add Customer</h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="modal-customer">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <form id="f_customer" class="p-4 md:p-5">
                    <input type="hidden" id="_mode" name="_mode" value="add">
                    <input type="hidden" id="_id" name="_id" value="">
                    <div class="grid gap-4 mb-4 grid-cols-2">
                        <div class="col-span-2">
                            <label for="ct_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipe Customer *</label>
                            <select id="ct_id" name="ct_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                <option value="">- Pilih -</option>
                                @foreach ($data['ct_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="cust_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Customer *</label>
                            <input type="text" id="cust_name" name="cust_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Nama" required>
                        </div>
                        <div class="col-span-2">
                            <label for="cust_store" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Toko</label>
                            <input type="text" id="cust_store" name="cust_store" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Isi jika dropshipper">
                        </div>
                        <div class="col-span-2">
                            <label for="cust_phone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No Telp</label>
                            <input type="text" id="cust_phone" name="cust_phone" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="No HP">
                        </div>
                        <div class="col-span-2">
                            <label for="cust_email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                            <input type="email" id="cust_email" name="cust_email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Email">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label for="cust_province" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Provinsi *</label>
                            <select id="cust_province" name="cust_province" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                <option value="">- Pilih -</option>
                                @foreach ($data['cust_province'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label for="cust_city" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kota *</label>
                            <select id="cust_city" name="cust_city" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                <option value="">- Pilih -</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="cust_subdistrict" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kecamatan *</label>
                            <select id="cust_subdistrict" name="cust_subdistrict" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                <option value="">- Pilih -</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="cust_address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat</label>
                            <textarea id="cust_address" name="cust_address" rows="3" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Alamat"></textarea>
                        </div>
                    </div>
                    <div class="flex items-center justify-end">
                        <button type="submit" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="modal-payment" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-4xl max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pembayaran</h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="modal-payment">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-4 md:p-5 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                    <!-- Order Info -->
                    <div class="grid gap-4 grid-cols-2">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. Pesanan</label>
                            <input type="text" id="order_code" placeholder="INVxxxxx" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. Resi (Cross Order MP)</label>
                            <input type="text" id="no_resi" placeholder="2504xxx / 5772xxxx" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>
                        <div id="courier_content">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kurir</label>
                            <select id="courier_bayar" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">- Pilih -</option>
                                @foreach ($data['courier'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Upload Resi -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Upload Resi File (PDF)</label>
                        <div class="flex items-center justify-center w-full">
                            <label for="no_resi_upload" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400" id="upload-text"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">PDF only</p>
                                </div>
                                <input id="no_resi_upload" type="file" accept="application/pdf" class="hidden" />
                            </label>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="grid gap-4 grid-cols-2 border-t pt-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Total</label>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white" id="payment_total">Rp. 0</div>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Downpayment</label>
                            <div class="flex items-center">
                                <input type="checkbox" id="dp_checkbox" value="0" onchange="this.value = this.checked ? 1 : 0" class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500">
                                <label for="dp_checkbox" class="ml-2 text-sm text-gray-900 dark:text-white">Aktifkan Downpayment</label>
                            </div>
                        </div>
                        <div id="payment_type_content">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipe Pembayaran</label>
                            <select id="pm_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @foreach($data['payment_method'] as $key => $value)
                                    @if (strtolower($value) == 'cash')
                                        <option value="{{ $key }}" selected>{{ $value }}</option>
                                    @else
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div id="sub_payment_type" class="hidden">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sub Pembayaran</label>
                            <select id="sub_payment" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">- Pilih -</option>
                                <option value="1">CASH</option>
                                <option value="2">COD</option>
                            </select>
                        </div>
                        <div id="card_provider_content">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rek Tujuan (Jika WA/Web)</label>
                            <select id="cp_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">- Pilih -</option>
                                @foreach($data['cp_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="ref_number_label">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode Referensi</label>
                            <input type="text" id="ref_number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>
                        <div id="marketplace_total_tr" class="hidden">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Total Harga Marketplace</label>
                            <input type="text" id="marketplace_side" readonly class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div id="marketplace_selisih_tr" class="hidden">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Selisih (Harga Jual dan Marketplace)</label>
                            <input type="text" id="marketplace_sell_price" readonly class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode Unik</label>
                            <input type="text" id="unique_code" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Total Bayar (Total + Kode Unik)</label>
                            <input type="text" id="final_total_unique_code" readonly class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Diskon Penjual (-)</label>
                            <input type="number" id="discount_seller" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Biaya Admin (-)</label>
                            <input type="text" id="admin_cost" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Biaya Lain-Lain (+)</label>
                            <input type="text" id="another_cost" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nominal DP (Jika ada)</label>
                            <input type="text" id="dp_payment" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>
                        <div class="col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white font-bold text-red-600">Harga Total</label>
                            <input type="text" id="real_price" readonly class="bg-red-50 border-2 border-red-300 text-red-900 text-lg font-bold rounded-lg block w-full p-2.5">
                        </div>
                    </div>

                    <!-- Note -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Catatan (Jika ada)</label>
                        <textarea id="note" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter Note"></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-2 pt-4 border-t">
                        <button type="button" data-modal-hide="modal-payment" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 border border-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white">Batal</button>
                        <button type="button" id="save_transaction" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Checkout</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flowbite JS -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.3.0/dist/flowbite.min.js"></script>
    <script src="{{ asset('pos_v2/js/pos_v2.js') }}"></script>
</body>
</html>

