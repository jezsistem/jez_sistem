<label class="form-label">Payment Method</label>
<select class="form-control" id="payment_method_select">
    <option value="0">-- Pilih Payment Method --</option>
    @forelse ($paymentMethods as $paymentMethodId => $paymentMethod)
        <option value="{{ $paymentMethod }}">{{ $paymentMethod }}</option>
    @empty
        <option value="">Pilih Store dulu</option>
    @endforelse
</select>

<script>
$(document).ready(function() {
    $('#payment_method_select').select2({
        placeholder: "-- Pilih Payment Method --"
    });
});
</script>
