@php
    $totalPayment = 0;
    $total_tf_bri = 0;
    $total_tf_bca = 0;
    $total_edc_bni = 0;
    $total_edc_bri = 0;
    $total_edc_bca = 0;
@endphp

<div class="container bg-white p-6 rounded-lg shadow-md max-w-2xl mx-auto">
    <h2 class="text-xl font-weight-bold mb-4">SHIFT DETAILS</h2>
    <div class="row row-cols-2 gap-4 mb-6">
        <div class="font-weight-medium col">Name</div>
        <div class="col" id>{{ $data['name'] }}</div>
        <div class="font-weight-medium col">Access</div>
        <div class="col">{{ $data['st_name'] }}</div>
        <div class="font-weight-medium col">Starting Shift</div>
        <div class="col">{{ $data['start_time'] }}</div>
        <div class="font-weight-medium col">Ending Shift</div>
        <div class="col">{{ $data['end_time'] }}</div>
    </div>
    <h3 class="text-lg font-weight-bold mb-4">ORDER DETAILS</h3>
    <div class="row row-cols-2 gap-4 mb-6">
        <div class="font-weight-medium col">Sold Items</div>
        <div class="col d-flex justify-content-between align-items-center">
            <span>{{ $total_sold_items }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="w-4 h-4" id="userShiftDetailSoldBtn">
                <path d="m9 18 6-6-6-6"></path>
            </svg>
        </div>
        <hr/>
        <hr/>
        <div class="font-weight-medium col">Refunded Items</div>
        <div class="col d-flex justify-content-between align-items-center">
            <span>{{ $total_refund_items }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="w-4 h-4" id="userShiftDetailRefundBtn">
                <path d="m9 18 6-6-6-6"></path>
            </svg>
        </div>
        <hr/>
        <hr/>
    </div>

    {{--  CASH  --}}
    @if($cashMethods != null)
        <h3 class="text-lg font-weight-bold mb-4">Cash</h3>
        <div class="row row-cols-2 gap-4 mb-6">
            <div class="font-weight-medium col">Cash</div>
            <div class="col d-flex justify-content-between align-items-center">
                @php
                    $totalPayment = number_format($cashMethods->total_pos_payment + $cashMethods->total_pos_payment_partials);
                @endphp

                <span>Rp. {{ $totalPayment }}</span>
            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">Cash Refunds</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. {{ number_format($cashMethods->total_pos_payment_refund) }}</span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">Expected Cash Payment</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. {{ number_format($cashMethods->total_pos_payment_expected) }}</span>

            </div>
            <hr/>
            <hr/>
        </div>
    @endif

    {{--  EDC BCA  --}}
    @if($bcaMethods != null)
        <h3 class="text-lg font-weight-bold mb-4">EDC BCA</h3>
        <div class="row row-cols-2 gap-4 mb-6">
            <div class="font-weight-medium col">BCA</div>
            <div class="col d-flex justify-content-between align-items-center">
                @php
                    $total_edc_bca = number_format($bcaMethods->total_pos_payment + $bcaMethods->total_pos_payment_partials);
                @endphp
                <span>Rp. {{ $total_edc_bca }}</span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">EDC Refunds</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. {{ number_format($bcaMethods->total_pos_payment_refund) }}</span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">Expected EDC Payment</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. {{ number_format($bcaMethods->total_pos_payment_expected) }}</span>

            </div>
            <hr/>
            <hr/>
        </div>
    @endif

    {{--  EDC BrI  --}}
    @if($briMethods != null)
        <h3 class="text-lg font-weight-bold mb-4">EDC BRI</h3>
        <div class="row row-cols-2 gap-4 mb-6">
            <div class="font-weight-medium col">BRI</div>
            <div class="col d-flex justify-content-between align-items-center">
                @php
                    $total_edc_bri = number_format($briMethods->total_pos_payment + $briMethods->total_pos_payment_partials);
                @endphp
                <span>Rp. {{ $total_edc_bri }}</span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">EDC Refunds</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. {{ number_format($briMethods->total_pos_payment_refund) }}</span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">Expected EDC Payment</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. {{ number_format($briMethods->total_pos_payment_expected) }}</span>

            </div>
            <hr/>
            <hr/>
        </div>
    @endif

    {{--  EDC BNI  --}}
    @if($bniMethods != null)
        <h3 class="text-lg font-weight-bold mb-4">EDC BNI</h3>
        <div class="row row-cols-2 gap-4 mb-6">
            <div class="font-weight-medium col">BRI</div>
            <div class="col d-flex justify-content-between align-items-center">
                @php
                    $total_edc_bni = number_format($bniMethods->total_pos_payment + $bniMethods->total_pos_payment_partials);
                @endphp
                <span>Rp. {{ $total_edc_bni }}</span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">EDC Refunds</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. {{ number_format($bniMethods->total_pos_payment_refund) }}</span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">Expected EDC Payment</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. {{ number_format($bniMethods->total_pos_payment_expected) }}</span>

            </div>
            <hr/>
            <hr/>
        </div>
    @endif

    {{--   TRANSFER BCA  --}}
    @if($transferBca != null)
        <h3 class="text-lg font-weight-bold mb-4">TRANSFER BCA</h3>
        <div class="row row-cols-2 gap-4 mb-6">
            <div class="font-weight-medium col">TRANSFER BCA</div>
            <div class="col d-flex justify-content-between align-items-center">
                @php
                    $total_tf_bca = number_format($transferBca->total_pos_payment + $transferBca->total_pos_payment_partials);
                @endphp
                <span>Rp. {{ $total_tf_bca }}</span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">TRANSFER BCA Refunds</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. {{number_format($transferBca->total_pos_payment_refund) }}</span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">Expected Transfer Payment</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. {{number_format($transferBca->total_pos_payment_expected) }}</span>

            </div>
            <hr/>
            <hr/>
        </div>
    @endif

    {{--   TRANSFER BRI  --}}
    @if($transferBri != null)
        <h3 class="text-lg font-weight-bold mb-4">TRANSFER BRI</h3>
        <div class="row row-cols-2 gap-4 mb-6">
            <div class="font-weight-medium col">TRANSFER BRI</div>
            <div class="col d-flex justify-content-between align-items-center">
                @php
                    $total_tf_bri = number_format($transferBri->total_pos_payment + $transferBri->total_pos_payment_partials);
                @endphp
                <span>Rp. {{ $total_tf_bri }}</span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">TRANSFER BRI Refunds</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. {{ number_format($transferBri->total_pos_payment_refund) }}</span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">Expected Transfer Payment</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. {{ number_format($transferBri->total_pos_payment_expected) }}</span>

            </div>
            <hr/>
            <hr/>
        </div>
    @endif

    {{--   TRANSFER BNI  --}}

    @php
        $total_partials = 0;
    @endphp

    @if($transferBni != null)
        <h3 class="text-lg font-weight-bold mb-4">TRANSFER BNI</h3>
        <div class="row row-cols-2 gap-4 mb-6">
            <div class="font-weight-medium col">TRANSFER BRI</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. {{ number_format($transferBni->total_pos_payment + $transferBni->total_pos_payment_partials) }}</span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">TRANSFER BNI Refunds</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>{{ number_format($transferBni->total_pos_payment_refund) }}</span>

            </div>
            <hr/>
            <hr/>
            <div class="font-weight-medium col">Expected Transfer Payment</div>
            <div class="col d-flex justify-content-between align-items-center">
                <span>Rp. {{ number_format($transferBni->total_pos_payment_expected) }}</span>
            </div>
            <hr/>
            <hr/>
        </div>
    @endif



    @php
        $total_expected = $totalPayment;
    @endphp
    <h3 class="text-lg font-weight-bold mb-4">Total</h3>
    <div class="row row-cols-2 gap-4 mb-6">
        <div class="font-weight-med ium col">Total Expected</div>
        <div class="col d-flex justify-content-between align-items-center">
            <span>Rp. {{ number_format($total_expected_payment) }}</span>
        </div>
        <hr/>
        <hr/>
        <div class="font-weight-medium col">Total Actual</div>
        <div class="col d-flex justify-content-between align-items-center">
            <span>Rp. {{ number_format($total_actual_payment + $total_payment_two) }}</span>
        </div>
        <hr/>
        <hr/>
        <div class="font-weight-medium col">Total Actual Ending Cash</div>
        <div class="col d-flex justify-content-between align-items-center">
            <span>Rp. {{ number_format($data['laba_shift']) }}</span>
        </div>
        <hr/>
        <hr/>
        <div class="font-weight-medium col">Difference</div>
        <div class="col d-flex justify-content-between align-items-center">
            <span>Rp. {{ number_format($total_expected_payment - $total_actual_payment) }}</span>
            {{--            <span>Rp. {{ number_format($total_actual_payment - $total_expected_payment) }}</span>--}}
        </div>
        <hr/>
        <hr/>
        <div class="font-weight-medium col">GAP Cash (Ending Cash - Actual Cash)</div>
        <div class="col d-flex justify-content-between align-items-center">
            {{--            <span>Rp. {{ number_format($total_expected_payment - $total_actual_payment) }}</span>--}}
            <span>Rp. {{ number_format($data['laba_shift'] - $total_expected_payment) }}</span>
        </div>
        <hr/>
        <hr/>

    </div>
</div>