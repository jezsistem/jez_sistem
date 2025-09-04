<label class="form-label">Payment Method</label>
<select class="form-control border border-secondary" id="payment_method_select">
    <option value="">-- Pilih Payment Method --</option>
    @forelse ($paymentMethods as $paymentMethodId => $paymentMethod)
        <option value="{{ $paymentMethod }}">{{ $paymentMethod }}</option>
    @empty
        <option value="">Pilih Store dulu</option>
    @endforelse
</select>

<script>
</script>
