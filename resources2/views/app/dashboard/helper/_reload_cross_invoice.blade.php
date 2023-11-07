<select class="form-control" id="cross_invoice" name="cross_invoice">
    <option value="">PILIH INVOICE</option>
    @foreach ($data['invoice'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>

<script>
    $('#cross_invoice').select2({
        width: "100%",
        dropdownParent: $('#TakeCrossOrderModal')
    });
</script>