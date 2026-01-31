<label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
<select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="payment_method_select">
    <option value="0">-- Pilih Payment Method --</option>
    @forelse ($paymentMethods as $paymentMethodId => $paymentMethod)
        <option value="{{ $paymentMethod }}">{{ $paymentMethod }}</option>
    @empty
        <option value="">Pilih Store dulu</option>
    @endforelse
</select>

<script>
$(document).ready(function() {
    // Initialize select2 only if element exists
    if ($('#payment_method_select').length) {
        $('#payment_method_select').select2({
            placeholder: "-- Pilih Payment Method --",
            width: '100%'
        });
    }
});
</script>
