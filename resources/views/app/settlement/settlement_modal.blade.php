<!-- Modal-->
<div class="modal fade" id="SettlementDetailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Transaction Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="modal_content">
                    <div class="d-flex justify-content-between mr-32">
                        <div class="">
                            <div>
                                <p class="text-muted mb-1">Transaction Date</p>
                                <p class="text-dark font-weight-bold h8" id="transaction_date">01 Jan 2024, 12:00</p>
                            </div>
                            <div>
                                <p class="text-muted mb-1">Outlet</p>
                                <p class="text-dark font-weight-bold h8" id="store_name">JEZ MALANG</p>
                            </div>
                        </div>
                        <div class="">
                            <div>
                                <p class="text-muted mb-1">Receipt Number</p>
                                <p class="text-dark font-weight-bold h8" id="receipt_number">INV03123823787</p>
                            </div>
                            <div>
                                <p class="text-muted mb-1">Status</p>
                                <button class="btn-sm" id="trx_status">PENDING</button>
                            </div>
                        </div>
                        <div class="">
                            <div>
                                <p class="text-muted mb-1">Order Number</p>
                                <p class="text-dark font-weight-bold h8" id="order_number">25255247509877</p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h6>Payment Information</h6>
                    <div class="mb-3">
                        <p class="text-muted mb-1">Settlement Status</p>
                        <span class="btn-sm" id="payment_status">Partially Paid (Outstanding)</span>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">Outstanding Balance</p>
                        <p class="text-danger font-weight-bold h8" id="outstanding_balance">Rp 200.000</p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="flex-fill pr-3">
                            <div class="mb-3">
                                <p class="text-muted mb-1">Payment Method</p>
                                <p class="font-weight-bold h8" id="payment_method_1">EDC BRI</p>
                            </div>
                            <div class="mb-3">
                                <p class="text-muted mb-1">Sub Payment</p>
                                <p class="font-weight-bold h8" id="sub_payment_method_1">ON US</p>
                            </div>
                            <div class="mb-3">
                                <p class="text-muted mb-1">Amount</p>
                                <p class="font-weight-bold h8" id="payment_amount_1">Rp 500.000</p>
                            </div>
                        </div>
                        <div class="flex-fill pl-3">
                            <div class="mb-3">
                                <p class="text-muted mb-1">Payment Method</p>
                                <p class="font-weight-bold h8" id="payment_method_2">EDC BCA</p>
                            </div>
                            <div class="mb-3">
                                <p class="text-muted mb-1">Sub Payment</p>
                                <p class="font-weight-bold h8" id="sub_payment_method_2">OFF US</p>
                            </div>
                            <div class="mb-3">
                                <p class="text-muted mb-1">Amount</p>
                                <p class="font-weight-bold h8" id="payment_amount_2">Rp 300.000</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 mr-64">
                        <p class="text-muted mb-1">Notes</p>
                        <p class="font-weight-bold h8" id="note">Notes </p>
                    </div>

                    <hr>
                    <h6>Item Sales</h6>
                    <table class="table table-hover table-checkable" id="SettlementItemsTable">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th class="text-dark">Article ID</th>
                                <th class="text-dark">Name</th>
                                <th class="text-dark">SKU</th>
                                <th class="text-dark">Qty</th>
                                <th class="text-dark">Price</th>
                                <th class="text-dark">Nameset</th>
                                <th class="text-dark">Discount</th>
                                <th class="text-dark">Net Sales</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <hr>
                    <h6>Financial Summary</h6>
                    <div class="d-flex justify-content-between">
                        <div class="flex-fill pr-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Down Payment</span>
                                <span class="font-weight-bold" id="down_payment">Rp 200.000</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Gross Sales</span>
                                <span class="font-weight-bold" id="gross_sales">Rp 850.000</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Discount</span>
                                <span class="font-weight-bold text-danger" id="total_discount">- Rp 25.000</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Net Sales</span>
                                <span class="font-weight-bold" id="net_sales">Rp 825.000</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Total Payments</span>
                                <span class="font-weight-bold" id="total_payment">Rp 625.000</span>
                            </div>
                        </div>
                        <div class="flex-fill pl-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">COGS</span>
                                <span class="font-weight-bold" id="cogs">Rp 420.000</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Seller Voucher</span>
                                <span class="font-weight-bold" id="seller_voucher">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Admin Fees</span>
                                <span class="font-weight-bold" id="total_admin_fee">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Outstanding Balance</span>
                                <span class="font-weight-bold text-danger" id="outstanding_balance_summary">Rp
                                    200.000</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Dana Cair</span>
                                <span class="font-weight-bold" id="total_dana_cair">Rp 802.500</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Gross Margin</span>
                                <span class="font-weight-bold text-success" id="gross_margin">Rp 405.000</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Margin %</span>
                                <span class="font-weight-bold text-success" id="margin_percentage">49.09%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="ml-auto">
                    <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">Close</button>
                    <a type="a" class="btn btn-primary" id="btn_print_receipt" target="_blank">Print</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->
