
<!-- Modal Voucher -->
<div id="modal-voucher" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                <h3 class="text-lg font-semibold text-gray-900">Tambah Voucher</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="modal-voucher">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <form id="f_add_voucher" class="p-4 md:p-5">
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Voucher</label>
                    <div id="voucher-container">
                        <div class="flex gap-2 mb-3">
                            <input type="text" name="voucher-list[]" class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block p-2.5" placeholder="Kode Voucher" value="">
                            <button type="button" class="add-voucher px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200">+</button>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-2">
                    <button type="button" data-modal-hide="modal-voucher" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900">Batal</button>
                    <button type="submit" class="text-white bg-red-500 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Tambah</button>
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
                <h3 class="text-lg font-semibold text-gray-900">Tambah Diskon</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="modal-discount">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <form id="f_add_total_discount" class="p-4 md:p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="discount-type" class="block mb-2 text-sm font-medium text-gray-900">Tipe Diskon</label>
                        <select id="discount-type" name="discount-type-list" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                            <option value="nominal">Nominal</option>
                            <option value="percentage">Percentage</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Diskon</label>
                        <div id="total-discount-container">
                            <div class="flex gap-2 mb-3">
                                <input type="text" name="total-discount-list[]" class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block p-2.5" placeholder="Diskon" value="">
                                <button type="button" class="add-total-discount px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200">+</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-2">
                    <button type="button" id="total_discount_reset" class="text-white bg-orange-500 hover:bg-orange-600 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-5 py-2.5">Reset</button>
                    <button type="button" data-modal-hide="modal-discount" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900">Batal</button>
                    <button type="submit" class="text-white bg-red-500  hover:bg-red-600  focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{--harus nya disini adalah script gajelas--}}

{{--sampai sini yaaaa--}}

<!-- Modal Customer Detail -->
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

<!-- Modal Customer (Add Customer) - Keep both IDs for compatibility -->
<div id="modal-customer" data-modal-id="choosecustomer" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white"><i class="cft-standard-stroke cft-user mr-2"></i> Add Customer</h3>
                <button type="button" id="close-modal-customer" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="modal-customer">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <form id="f_customer" class="p-4 md:p-5 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                <input type="hidden" id="_mode" name="_mode" />
                <input type="hidden" id="_id" name="_id" />
                <div>
                    {{--                        ini agak gila--}}
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipe Customer</label>
                    {{--                        <select id="ct_id" name="ct_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>--}}
                    {{--                            <option value="">- Pilih -</option>--}}
                    {{--                            @foreach ($data['ct_id'] as $key => $value)--}}
                    {{--                                <option value="{{ $key }}">{{ $value }}</option>--}}
                    {{--                            @endforeach--}}
                    {{--                        </select>--}}
                    <input type="hidden" value="1" id="ct_id" name="ct_id">
                    <input type="text" id="type_cust" name="type_cust" value="GENERAL" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Nama" readonly>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Customer *</label>
                    <input type="text" id="cust_name" name="cust_name" autofocus class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Nama" required>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No Telp</label>
                    <input type="text" id="cust_phone" name="cust_phone" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="No HP">
                </div>
                <div class="flex items-center justify-end space-x-2 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <button type="button" id="cancel-modal-customer" data-modal-hide="modal-customer" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white">Batal</button>
                    <button type="submit" id="save_customer_btn" class="text-white bg-red-500  hover:bg-red-600  focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-500  dark:focus:ring-red-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="payment-offline-popup" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 flex items-center justify-center w-full h-full" data-modal-backdrop="static" data-modal-placement="center">
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <div class="relative bg-white rounded-lg shadow-xl dark:bg-gray-800">
            <!-- Header -->
            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-600 bg-gradient-to-r from-red-500 to-red-500 rounded-t-lg">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Pembayaran
                </h3>
                <button type="button" class="text-white hover:bg-white/20 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors" data-modal-hide="payment-offline-popup" aria-label="Close modal">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <div class="modal-body bg-gray-50 p-6 max-h-[calc(100vh-200px)] overflow-y-auto">
                <!-- Total Bayar Card -->
                <div class="mb-6 bg-gradient-to-r from-red-100 to-red-100 rounded-xl p-5 shadow-md">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="bg-red-500 rounded-lg p-2">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-dark text-sm font-medium">Total Bayar</p>
                                <h4 class="text-dark text-2xl font-bold mt-1"><span id="payment_total"></span></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Pengaturan Pembayaran -->
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Pengaturan Pembayaran
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Down Payment -->
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <label for="dp_checkbox" class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-orange-500 text-white">Down Payment</span>
                                </label>
                                <input type="checkbox" id="dp_checkbox" value="dp" class="w-5 h-5 text-red-600 bg-white border-gray-300 rounded focus:ring-red-500 focus:ring-2 cursor-pointer" />
                            </div>

                            <!-- Metode Pembayaran -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-red-500 text-white mb-2">Metode Pembayaran</span>
                                </label>
                                <div class="flex gap-2">
                                    <select id="payment_option" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 block w-full p-3 shadow-sm">
                                        <option value="">- Pilih -</option>
                                        <option value="one" selected>1 Metode</option>
                                        <option value="two">2 Metode</option>
                                    </select>
                                    <div id="payment_option_parent"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metode Pembayaran 1 -->
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200 border-l-4 border-l-red-500">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-500 text-white text-sm font-bold">1</span>
                            Metode Pembayaran Pertama
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Jenis Pembayaran -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-red-500 text-white mb-2">Jenis Pembayaran</span>
                                </label>
                                <select id="pm_id_offline" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 block w-full p-3 shadow-sm">
                                    <option value="">- Pilih -</option>
                                    @foreach ($data['payment_method'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <div id="pm_id_offline_parent"></div>
                            </div>

                            <!-- Sub Pembayaran -->
                            <div class="space-y-2 hidden" id="sub_payment_offline_content">
                                <label class="block text-sm font-medium text-gray-700">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-red-500 text-white mb-2">Sub Pembayaran</span>
                                </label>
                                <select id="sub_payment_offline" name="sub_payment_offline" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 block w-full p-3 shadow-sm">
                                    <option value="">- Pilih -</option>
                                    <option value="3">On Us</option>
                                    <option value="4">Off Us</option>
                                </select>
                            </div>

                            <!-- Mesin EDC -->
                            <div class="space-y-2 hidden" id="card_provider_content">
                                <label class="block text-sm font-medium text-gray-700">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-red-500 text-white mb-2">Mesin EDC</span>
                                </label>
                                <select id="cp_id" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 block w-full p-3 shadow-sm">
                                    <option value="">- Pilih -</option>
                                    @foreach ($data['cp_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- No. Kartu -->
                            <div class="space-y-2 hidden" id="card_number_label">
                                <label class="block text-sm font-medium text-gray-700">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-red-500 text-white mb-2">No. Kartu</span>
                                </label>
                                <input class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 block w-full p-3 shadow-sm" type="text" placeholder="Masukkan nomor kartu" id="card_number" />
                            </div>

                            <!-- Nama Rekening Pengirim -->
                            <div class="space-y-2 hidden" id="ref_number_label">
                                <label class="block text-sm font-medium text-gray-700">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-red-500 text-white mb-2">Nama Rekening Pengirim</span>
                                </label>
                                <input class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 block w-full p-3 shadow-sm" type="text" placeholder="Masukkan nama rekening" id="ref_number" />
                            </div>

                            <!-- Charge (%) -->
                            <div class="space-y-2 hidden" id="charge_label">
                                <label class="block text-sm font-medium text-gray-700">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-red-500 text-white mb-2">Charge (%)</span>
                                </label>
                                <div class="flex gap-2">
                                    <input class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 block w-full p-3 shadow-sm" type="number" placeholder="%" id="charge" />
                                    <input class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-3 shadow-sm cursor-not-allowed" type="number" placeholder="Total" id="charge_total" readonly />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metode Pembayaran 2 -->
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200 border-l-4 border-l-green-500 hidden" id="payment_method_two_section">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-500 text-white text-sm font-bold">2</span>
                            Metode Pembayaran Kedua
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Jenis Pembayaran 2 -->
                            <div class="space-y-2" id="payment_type_content_two">
                                <label class="block text-sm font-medium text-gray-700">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-green-500 text-white mb-2">Jenis Pembayaran</span>
                                </label>
                                <select id="pm_id_offline_two" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 block w-full p-3 shadow-sm">
                                    <option value="">- Pilih -</option>
                                    @foreach ($data['payment_method'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <div id="pm_id_offline_two_parent"></div>
                            </div>

                            <!-- Mesin EDC 2 -->
                            <div class="space-y-2 hidden" id="card_provider_content_two">
                                <label class="block text-sm font-medium text-gray-700">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-green-500 text-white mb-2">Mesin EDC</span>
                                </label>
                                <select id="cp_id_two" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 block w-full p-3 shadow-sm">
                                    <option value="">- Pilih -</option>
                                    @foreach ($data['cp_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- No. Kartu 2 -->
                            <div class="space-y-2 hidden" id="card_number_label_two">
                                <label class="block text-sm font-medium text-gray-700">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-green-500 text-white mb-2">No. Kartu</span>
                                </label>
                                <input class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 block w-full p-3 shadow-sm" type="text" placeholder="Masukkan nomor kartu" id="card_number_two" />
                            </div>

                            <!-- Nama Rekening Pengirim 2 -->
                            <div class="space-y-2 hidden" id="ref_number_label_two">
                                <label class="block text-sm font-medium text-gray-700">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-green-500 text-white mb-2">Nama Rekening Pengirim</span>
                                </label>
                                <input class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 block w-full p-3 shadow-sm" type="text" placeholder="Masukkan nama rekening" id="ref_number_two" />
                            </div>
                        </div>
                    </div>

                    <!-- Online Mode Fields -->
                    <div id="online_mode" class="hidden bg-white rounded-xl p-5 shadow-sm border border-gray-200 border-l-4 border-l-purple-500">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                            </svg>
                            Pengaturan Online
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Kode Unik</label>
                                <input type="text" placeholder="(isi jika online)" id="unique_code" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 block w-full p-3 shadow-sm" />
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Charge Lain-Lain (+)</label>
                                <input type="text" placeholder="(isi jika online)" id="another_cost" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 block w-full p-3 shadow-sm" />
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Biaya Admin (-)</label>
                                <input type="text" placeholder="(isi jika online)" id="admin_cost" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 block w-full p-3 shadow-sm" />
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Kurir & Ongkos Kirim</label>
                                <div class="flex gap-2">
                                    <select name="cr_id" id="cr_id" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 block flex-1 p-3 shadow-sm">
                                        <option value="">- Pilih Kurir -</option>
                                        @foreach ($data['courier'] as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <input type="number" placeholder="Ongkos Kirim" id="shipping_cost" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 block flex-1 p-3 shadow-sm" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jumlah Pembayaran -->
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Jumlah Pembayaran
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="total_payment_label">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-red-500 text-white mb-2">Jumlah yang dibayar Customer</span>
                                </label>
                                <input type="text" name="number" class="bg-white border-2 border-gray-300 text-gray-900 text-base font-semibold rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 block w-full p-4 shadow-sm" id="total_payment" value="" placeholder="Rp 0">
                            </div>
                            <div class="space-y-2 hidden" id="total_payment_two_label">
                                <label class="block text-sm font-medium text-gray-700">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-green-500 text-white mb-2">Jumlah yang dibayar Customer (Metode 2)</span>
                                </label>
                                <input type="text" name="number" class="bg-white border-2 border-gray-300 text-gray-900 text-base font-semibold rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 block w-full p-4 shadow-sm" id="total_payment_two" value="" placeholder="Rp 0">
                            </div>
                        </div>

                        <!-- Kembalian -->
                        <div class="hidden mt-4" id="return_payment_label">
                            <div class="p-4 flex justify-between items-center bg-gradient-to-r from-red-100 to-red-100 rounded-lg shadow-md">
                                <div class="flex items-center gap-3">
                                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <h5 class="font-bold text-red-500 text-lg mb-0">Kembalian</h5>
                                </div>
                                <h5 class="font-bold text-red-500 text-xl mb-0"><span id="return_payment"></span></h5>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                        <label class="block text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Catatan (Jika ada)
                        </label>
                        <textarea class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 block w-full p-3 shadow-sm resize-none" id="note" rows="3" placeholder="Tambahkan catatan untuk transaksi ini..."></textarea>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-gray-200 dark:border-gray-600">
                    <button type="button" data-modal-hide="payment-offline-popup" class="px-6 py-3 text-gray-700 bg-white hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border-2 border-gray-300 text-sm font-semibold transition-all duration-200 hover:shadow-md">
                        Batal
                    </button>
                    <a href="#" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-red-600 to-red-600 rounded-lg hover:from-red-700 hover:to-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5" id="checkout_btn">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Checkout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Refund/Exchange -->
<div id="RefundExchangeModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Refund / Penukaran [<span id="refund_retur_invoice_label">INV0000000</span>]</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="RefundExchangeModal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5">
                <input id="refund_retur_pt_id" type="hidden" />
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" id="RefundReturtb">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">No</th>
                            <th scope="col" class="px-6 py-3">Artikel</th>
                            <th scope="col" class="px-6 py-3">Tanggal Trx</th>
                            <th scope="col" class="px-6 py-3">Qty</th>
                            <th scope="col" class="px-6 py-3">Price</th>
                            <th scope="col" class="px-6 py-3">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
                <div class="flex items-center justify-end space-x-2 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <button type="button" data-modal-hide="RefundExchangeModal" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white">Selesai</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal DP Exchange -->
<div id="DpExchangeModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Refund / Penukaran [<span id="dp_invoice_label">INV0000000</span>]</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="DpExchangeModal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5">
                <input id="dp_pt_id" type="hidden" />
                <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Item Details</h5>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" id="DpInvoicetb">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">No</th>
                            <th scope="col" class="px-6 py-3">Artikel</th>
                            <th scope="col" class="px-6 py-3">Tanggal Trx</th>
                            <th scope="col" class="px-6 py-3">Qty</th>
                            <th scope="col" class="px-6 py-3">Price</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
                <div class="flex items-center justify-end space-x-2 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <button type="button" data-modal-hide="DpExchangeModal" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white">Selesai</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Input Code -->
<div id="InputCodeModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-[100] justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full" style="z-index: 10000;">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Input Kode</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="InputCodeModal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <form id="f_access" class="p-4 md:p-5">
                <input type="hidden" name="_type" id="_type" value="" />
                <input type="hidden" class="form-control" id="cust_id_num" name="cust_id_num" autocomplete="off" />
                <div class="mb-4">
                    <label for="u_secret_code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Scan Barcode Staff Anda</label>
                    <input type="text" id="u_secret_code" name="u_secret_code" autocomplete="off" autofocus required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500" />
                </div>
                <div class="flex items-center justify-end space-x-2">
                    <button type="button" data-modal-hide="InputCodeModal" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white">Batal</button>
                    <button type="submit" class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">Lanjut Checkout</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Offer (Rating Customer) -->
<div id="OfferModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full" data-modal-backdrop="static" data-keyboard="false">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="p-6 text-center">
                <img src="{{ asset('upload/image/socks.png') }}" alt="Socks" class="mx-auto mb-6 w-32" />
                <h3 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Apakah Customer ingin isi rating ?</h3>
                <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">Jika Ya silahkan input kode akses anda kemudian pilih Ya</p>
                <div class="mb-6">
                    <input type="password" id="free_sock_access_code" autocomplete="off" placeholder="Kode Akses Kasir" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500" />
                </div>
                <div class="flex items-center justify-center gap-3">
                    <button type="button" data-modal-hide="OfferModal" id="free_sock_no_btn" class="text-white bg-red-600 hover:bg-red-500  focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-500  dark:focus:ring-red-800">Tidak</button>
                    <button type="button" id="free_sock_btn" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Ya</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Shift Employee -->
<div id="shiftEmployeeModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Shift Employee</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="shiftEmployeeModal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5">
                <form id="f_shift_customer">
                    <div class="mb-4">
                        <div id="shiftStatus" class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Shift not started</div>
                        <div class="flex gap-2">
                            <button type="button" id="startShiftButton" class="flex-1 text-white bg-red-500  hover:bg-red-600  focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-500  dark:focus:ring-red-800">Start Shift</button>
                            <button type="button" id="stopShiftButton" class="flex-1 text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800 hidden">Stop Shift</button>
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white" id="shift-clock">00:00:00</div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Input Kas -->
<div id="inputKasModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Input Kas</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="inputKasModal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5">
                <form id="f_shift_input_kas">
                    <div class="mb-4">
                        <div id="shiftStatus_inputKas" class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Shift not started</div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white" id="shift-clock_inputKas">00:00:00</div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Product Barcode Modal (Flowbite - Same style as pos_v2 modals) -->
<div id="ProductBarcodeModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full" data-modal-backdrop="static">
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                <h3 class="text-lg font-semibold text-gray-900">
                    Lengkapi Barcode
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="ProductBarcodeModal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5">
                <!-- Search Input -->
                <div class="mb-4">
                    <label for="p_search" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cari Artikel / Barcode</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-3 h-3 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="search" id="p_search" placeholder="Cari artikel / barcode" class="block w-full p-3 pl-10 pr-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500"/>
                        <div id="p_search_loading" class="absolute inset-y-0 right-0 flex items-center pr-3 hidden">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-red-500"></div>
                        </div>
                    </div>
                </div>

                <!-- DataTable Container -->
                <div>
                    <div class="relative">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" id="Ptb">
                            <thead class="text-xs text-gray-900 uppercase bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-gray-900 font-semibold">No</th>
                                <th scope="col" class="px-6 py-4 text-gray-900 font-semibold">Brand</th>
                                <th scope="col" class="px-6 py-4 text-gray-900 font-semibold">Artikel</th>
                                <th scope="col" class="px-6 py-4 text-gray-900 font-semibold">Warna</th>
                                <th scope="col" class="px-6 py-4 text-gray-900 font-semibold">Size</th>
                                <th scope="col" class="px-6 py-4 text-gray-900 font-semibold">Barcode</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700 text-sm">
                            <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-2 p-4 md:p-5 border-t border-gray-200">
                <button type="button" data-modal-hide="ProductBarcodeModal" class="text-gray-800 bg-gray-100 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Shift Detail Modal -->
<div id="modal-shift-detail" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-3xl max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Shift Detail
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="modal-shift-detail">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5 space-y-4">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" id="current-shift-table" style="width: 100% !important;">
                        <thead class="text-xs text-gray-700s uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Payment Method</th>
                            <th scope="col" class="px-6 py-3">Total POS Real Price</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
                <form id="f_laba_input">
                    <div class="mb-4">
                        <label for="laba_shift" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Input Uang Kasir (Cash)</label>
                        <input type="number" id="laba_shift" name="laba_shift"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-1/2 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500"
                               placeholder="Rp."
                               autocomplete="off" />
                    </div>
                    <div class="flex items-center justify-end space-x-2 pt-4 border-t border-gray-200 dark:border-gray-600">
                        <button type="button" data-modal-hide="modal-shift-detail" class="px-4 py-2 text-sm font-medium text-gray-500 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                            End Shift
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>