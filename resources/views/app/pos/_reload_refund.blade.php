<select class="arabic-select-reload-refund select-down" id="refund_invoice" name="refund_invoice">
    <option value="">- Pilih -</option>
    @foreach ($data['invoice'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>
<script>
jQuery(function() {
    jQuery('.arabic-select-reload-refund').multipleSelect({
        filter: true,
        filterAcceptOnEnter: true
    })
});
</script>