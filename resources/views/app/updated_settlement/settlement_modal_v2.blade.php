<!-- Settlement Detail Modal -->
<div id="SettlementDetailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-7xl shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Transaction Details</h3>
            <button type="button" onclick="closeSettlementDetail()" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="mt-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Transaction Date</p>
                    <p class="text-base font-semibold text-gray-900" id="transaction_date">-</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Receipt Number</p>
                    <p class="text-base font-semibold text-gray-900" id="receipt_number">-</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Order Number</p>
                    <p class="text-base font-semibold text-gray-900" id="order_number">-</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Outlet</p>
                    <p class="text-base font-semibold text-gray-900" id="store_name">-</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Status</p>
                    <span id="trx_status" class="px-2 py-1 text-xs rounded">-</span>
                </div>
            </div>

            <hr class="my-6">

            <h6 class="text-base font-semibold text-gray-900 mb-4">Payment Information</h6>
            <div class="mb-4">
                <p class="text-sm text-gray-500 mb-1">Settlement Status</p>
                <span id="payment_status" class="px-2 py-1 text-xs rounded">-</span>
            </div>
            <div class="mb-4">
                <p class="text-sm text-gray-500 mb-1">Outstanding Balance</p>
                <p class="text-base font-semibold text-red-500" id="outstanding_balance">Rp 0</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Payment Method</p>
                    <p class="text-base font-semibold text-gray-900" id="payment_method_1">-</p>
                    <p class="text-sm text-gray-500 mb-1 mt-2">Sub Payment</p>
                    <p class="text-base font-semibold text-gray-900" id="sub_payment_method_1">-</p>
                    <p class="text-sm text-gray-500 mb-1 mt-2">Amount</p>
                    <p class="text-base font-semibold text-gray-900" id="payment_amount_1">Rp 0</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Payment Method</p>
                    <p class="text-base font-semibold text-gray-900" id="payment_method_2">-</p>
                    <p class="text-sm text-gray-500 mb-1 mt-2">Sub Payment</p>
                    <p class="text-base font-semibold text-gray-900" id="sub_payment_method_2">-</p>
                    <p class="text-sm text-gray-500 mb-1 mt-2">Amount</p>
                    <p class="text-base font-semibold text-gray-900" id="payment_amount_2">Rp 0</p>
                </div>
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-500 mb-1">Notes Transaction</p>
                <p class="text-base font-semibold text-gray-900" id="note">-</p>
            </div>
            <div class="mb-4">
                <p class="text-sm text-gray-500 mb-1">Notes Down Payment</p>
                <p class="text-base font-semibold text-gray-900" id="note_dp">-</p>
            </div>
            <div class="mb-4">
                <p class="text-sm text-gray-500 mb-1">Notes Settlement</p>
                <textarea name="note_settlement" id="note_settlement" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"></textarea>
            </div>

            <hr class="my-6">

            <h6 class="text-base font-semibold text-gray-900 mb-4">Item Sales</h6>
            <div class="overflow-x-auto mb-6">
                <table class="w-full text-sm text-left text-gray-500" id="SettlementItemsTable">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-3">Article ID</th>
                            <th scope="col" class="px-3 py-3">Name</th>
                            <th scope="col" class="px-3 py-3">SKU</th>
                            <th scope="col" class="px-3 py-3">Qty</th>
                            <th scope="col" class="px-3 py-3">Price</th>
                            <th scope="col" class="px-3 py-3">Nameset</th>
                            <th scope="col" class="px-3 py-3">Discount</th>
                            <th scope="col" class="px-3 py-3">Net Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8" class="px-3 py-4 text-center text-gray-500">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <hr class="my-6">

            <h6 class="text-base font-semibold text-gray-900 mb-4">Financial Summary</h6>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-500">Down Payment</span>
                        <span class="text-base font-semibold text-gray-900" id="down_payment">Rp 0</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-500">Gross Sales</span>
                        <span class="text-base font-semibold text-gray-900" id="gross_sales">Rp 0</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-500">Total Discount</span>
                        <span class="text-base font-semibold text-red-500" id="total_discount">- Rp 0</span>
                    </div>
                    <hr class="my-2">
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-500">Net Sales</span>
                        <span class="text-base font-semibold text-gray-900" id="net_sales">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Total Payments</span>
                        <span class="text-base font-semibold text-gray-900" id="total_payment">Rp 0</span>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-500">COGS</span>
                        <span class="text-base font-semibold text-gray-900" id="cogs">Rp 0</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-500">Seller Voucher</span>
                        <span class="text-base font-semibold text-gray-900" id="seller_voucher">Rp 0</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-500">Total Admin Fees</span>
                        <span class="text-base font-semibold text-gray-900" id="total_admin_fee">Rp 0</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-500">Outstanding Balance</span>
                        <span class="text-base font-semibold text-red-500" id="outstanding_balance_summary">Rp 0</span>
                    </div>
                    <hr class="my-2">
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-500">Total Dana Cair</span>
                        <span class="text-base font-semibold text-gray-900" id="dana_cair">Rp 0</span>
                    </div>
                    <hr class="my-2">
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-500">Gross Margin</span>
                        <span class="text-base font-semibold text-green-600" id="gross_margin">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Margin %</span>
                        <span class="text-base font-semibold text-green-600" id="margin_percentage_detail">0%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
            <button type="button" onclick="closeSettlementDetail()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Close</button>
            <a type="a" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" id="btn_print_receipt" target="_blank">Print</a>
        </div>
    </div>
</div>
