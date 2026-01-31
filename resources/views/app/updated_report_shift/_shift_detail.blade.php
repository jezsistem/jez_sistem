@php
    $totalPayment = 0;
    $total_tf_bri = 0;
    $total_tf_bca = 0;
    $total_edc_bni = 0;
    $total_edc_bri = 0;
    $total_edc_bca = 0;
    $total_qris = 0;
@endphp

<div class="max-w-5xl mx-auto p-6 space-y-6">
    <!-- Shift Details Card -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 shadow-md border border-blue-100">
        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-clock mr-3 text-blue-600"></i>
            Shift Details
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="text-sm text-gray-500 mb-1">Nama</div>
                <div class="text-lg font-semibold text-gray-900">{{ $data['name'] }}</div>
            </div>
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="text-sm text-gray-500 mb-1">Toko</div>
                <div class="text-lg font-semibold text-gray-900">{{ $data['st_name'] }}</div>
            </div>
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="text-sm text-gray-500 mb-1">Mulai Shift</div>
                <div class="text-lg font-semibold text-gray-900">{{ $data['start_time'] }}</div>
            </div>
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="text-sm text-gray-500 mb-1">Selesai Shift</div>
                <div class="text-lg font-semibold text-gray-900">{{ $data['end_time'] ?? '-' }}</div>
            </div>
        </div>
    </div>

    <!-- Order Details Card -->
    <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200">
        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-shopping-cart mr-3 text-green-600"></i>
            Order Details
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-5 border border-green-200 hover:shadow-md transition-shadow cursor-pointer" id="soldItemsCard">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-600 mb-1">Sold Items</div>
                        <div class="text-3xl font-bold text-green-700">{{ number_format($total_sold_items) }}</div>
                    </div>
                    <button id="userShiftDetailSoldBtn" class="bg-green-600 text-white p-3 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            <div class="bg-gradient-to-br from-red-50 to-rose-50 rounded-lg p-5 border border-red-200 hover:shadow-md transition-shadow cursor-pointer" id="refundItemsCard">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-600 mb-1">Refunded Items</div>
                        <div class="text-3xl font-bold text-red-700">{{ number_format($total_refund_items) }}</div>
                    </div>
                    <button id="userShiftDetailRefundBtn" class="bg-red-500 text-white p-3 rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods Section -->
    <div class="space-y-4">
        <!-- Cash -->
        @if($cashMethods != null)
            @php
                $totalPayment = number_format($cashMethods->total_pos_payment + $cashMethods->total_pos_payment_partials);
            @endphp
            <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl p-6 shadow-md border border-yellow-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-money-bill-wave mr-3 text-yellow-600"></i>
                    Cash
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">Done Transactions</div>
                        <div class="text-xl font-bold text-gray-900">Rp. {{ $totalPayment }}</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">Cash Refunds</div>
                        <div class="text-xl font-bold text-red-500">Rp. {{ number_format($cashMethods->total_pos_payment_refund) }}</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">Expected Cash Payment</div>
                        <div class="text-xl font-bold text-blue-600">Rp. {{ number_format($cashMethods->total_pos_payment_expected) }}</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- QRIS -->
        @if($qris != null)
            @php
                $total_qris = number_format($qris->total_pos_payment + $qris->total_pos_payment_partials);
            @endphp
            <div class="bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl p-6 shadow-md border border-purple-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-qrcode mr-3 text-purple-600"></i>
                    QRIS Payment
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">Done Transactions</div>
                        <div class="text-xl font-bold text-gray-900">Rp. {{ $total_qris }}</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">QRIS Refunds</div>
                        <div class="text-xl font-bold text-red-500">Rp. {{ number_format($qris->total_pos_payment_refund) }}</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">Expected QRIS Payment</div>
                        <div class="text-xl font-bold text-blue-600">Rp. {{ number_format($qris->total_pos_payment_expected) }}</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- EDC BCA -->
        @if($bcaMethods != null)
            @php
                $total_edc_bca = number_format($bcaMethods->total_pos_payment + $bcaMethods->total_pos_payment_partials);
            @endphp
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl p-6 shadow-md border border-blue-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-credit-card mr-3 text-blue-600"></i>
                    EDC BCA
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">Done Transactions</div>
                        <div class="text-xl font-bold text-gray-900">Rp. {{ $total_edc_bca }}</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">EDC Refunds</div>
                        <div class="text-xl font-bold text-red-500">Rp. {{ number_format($bcaMethods->total_pos_payment_refund) }}</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">Expected EDC Payment</div>
                        <div class="text-xl font-bold text-blue-600">Rp. {{ number_format($bcaMethods->total_pos_payment_expected) }}</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- EDC BRI -->
        @if($briMethods != null)
            @php
                $total_edc_bri = number_format($briMethods->total_pos_payment + $briMethods->total_pos_payment_partials);
            @endphp
            <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl p-6 shadow-md border border-orange-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-credit-card mr-3 text-orange-600"></i>
                    EDC BRI
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">Done Transactions</div>
                        <div class="text-xl font-bold text-gray-900">Rp. {{ $total_edc_bri }}</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">EDC Refunds</div>
                        <div class="text-xl font-bold text-red-500">Rp. {{ number_format($briMethods->total_pos_payment_refund) }}</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">Expected EDC Payment</div>
                        <div class="text-xl font-bold text-blue-600">Rp. {{ number_format($briMethods->total_pos_payment_expected) }}</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- EDC BNI -->
        @if($bniMethods != null)
            @php
                $total_edc_bni = number_format($bniMethods->total_pos_payment + $bniMethods->total_pos_payment_partials);
            @endphp
            <div class="bg-gradient-to-r from-green-50 to-teal-50 rounded-xl p-6 shadow-md border border-green-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-credit-card mr-3 text-green-600"></i>
                    EDC BNI
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">Done Transactions</div>
                        <div class="text-xl font-bold text-gray-900">Rp. {{ $total_edc_bni }}</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">EDC Refunds</div>
                        <div class="text-xl font-bold text-red-500">Rp. {{ number_format($bniMethods->total_pos_payment_refund) }}</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">Expected EDC Payment</div>
                        <div class="text-xl font-bold text-blue-600">Rp. {{ number_format($bniMethods->total_pos_payment_expected) }}</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Transfer BCA -->
        @if($transferBca != null)
            @php
                $total_tf_bca = number_format($transferBca->total_pos_payment + $transferBca->total_pos_payment_partials);
            @endphp
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-xl p-6 shadow-md border border-indigo-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-exchange-alt mr-3 text-indigo-600"></i>
                    Transfer BCA
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">Done Transactions</div>
                        <div class="text-xl font-bold text-gray-900">Rp. {{ $total_tf_bca }}</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">Transfer BCA Refunds</div>
                        <div class="text-xl font-bold text-red-500">Rp. {{ number_format($transferBca->total_pos_payment_refund) }}</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">Expected Transfer Payment</div>
                        <div class="text-xl font-bold text-blue-600">Rp. {{ number_format($transferBca->total_pos_payment_expected) }}</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Transfer BRI -->
        @if($transferBri != null)
            @php
                $total_tf_bri = number_format($transferBri->total_pos_payment + $transferBri->total_pos_payment_partials);
            @endphp
            <div class="bg-gradient-to-r from-pink-50 to-rose-50 rounded-xl p-6 shadow-md border border-pink-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-exchange-alt mr-3 text-pink-600"></i>
                    Transfer BRI
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">Done Transactions</div>
                        <div class="text-xl font-bold text-gray-900">Rp. {{ $total_tf_bri }}</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">Transfer BRI Refunds</div>
                        <div class="text-xl font-bold text-red-500">Rp. {{ number_format($transferBri->total_pos_payment_refund) }}</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">Expected Transfer Payment</div>
                        <div class="text-xl font-bold text-blue-600">Rp. {{ number_format($transferBri->total_pos_payment_expected) }}</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Transfer BNI -->
        @if($transferBni != null)
            <div class="bg-gradient-to-r from-teal-50 to-cyan-50 rounded-xl p-6 shadow-md border border-teal-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-exchange-alt mr-3 text-teal-600"></i>
                    Transfer BNI
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">Done Transactions</div>
                        <div class="text-xl font-bold text-gray-900">Rp. {{ number_format($transferBni->total_pos_payment + $transferBni->total_pos_payment_partials) }}</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">Transfer BNI Refunds</div>
                        <div class="text-xl font-bold text-red-500">Rp. {{ number_format($transferBni->total_pos_payment_refund) }}</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="text-sm text-gray-500 mb-1">Expected Transfer Payment</div>
                        <div class="text-xl font-bold text-blue-600">Rp. {{ number_format($transferBni->total_pos_payment_expected) }}</div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Summary Card -->
    <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-xl p-6 shadow-lg text-white">
        <h3 class="text-xl font-bold mb-6 flex items-center">
            <i class="fas fa-calculator mr-3"></i>
            Summary
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white bg-opacity-10 rounded-lg p-4 backdrop-blur-sm">
                <div class="text-sm text-gray-300 mb-1">Total Expected</div>
                <div class="text-xl font-semibold">Rp. {{ number_format($total_expected_payment) }}</div>
            </div>
            <div class="bg-white bg-opacity-10 rounded-lg p-4 backdrop-blur-sm">
                <div class="text-sm text-gray-300 mb-1">Total Actual</div>
                <div class="text-xl font-semibold text-green-300">Rp. {{ number_format($total_actual_payment + $total_payment_two) }}</div>
            </div>
            <div class="bg-white bg-opacity-10 rounded-lg p-4 backdrop-blur-sm">
                <div class="text-sm text-gray-300 mb-1">Actual Ending Cash</div>
                <div class="text-xl font-semibold text-yellow-300">Rp. {{ number_format($data['laba_shift']) }}</div>
            </div>
            <div class="bg-white bg-opacity-10 rounded-lg p-4 backdrop-blur-sm">
                <div class="text-sm text-gray-300 mb-1">Difference</div>
                <div class="text-xl font-semibold {{ ($total_expected_payment - $total_actual_payment) >= 0 ? 'text-green-300' : 'text-red-300' }}">
                    Rp. {{ number_format($total_expected_payment - $total_actual_payment) }}
                </div>
            </div>
        </div>
    </div>
</div>
