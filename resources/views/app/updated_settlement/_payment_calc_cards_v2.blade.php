<div class="flex flex-wrap gap-4">
    @forelse ($merged as $payment)
    <div class="flex-1 min-w-[200px] max-w-[200px]">
        <div class="bg-gray-100 rounded-lg p-3">
            <h6 class="text-sm text-gray-500 mb-1">{{$payment->pm_name}}</h6>
            <h4 class="text-base font-bold text-gray-900 mb-1">Rp {{ number_format((int)$payment->total_payment, 0, ',', '.') }}</h4>
            <div class="flex justify-between mt-3 text-xs text-gray-500">
                <span>Settled:</span>
                <span class="text-green-600">Rp {{ number_format((int)$payment->settled_payment, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-xs text-gray-500">
                <span>Unsettle:</span>
                <span class="text-yellow-600">Rp {{ number_format((int)$payment->unsettled_payment, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
    @empty
    @endforelse
</div>
