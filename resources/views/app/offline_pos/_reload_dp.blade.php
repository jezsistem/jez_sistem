<select class="arabic-select-reload-dp select-down" id="dp_invoice" name="dp_invoice">
    <option value="">- Pilih -</option>
    @foreach ($data['invoice'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>
<script>
    jQuery(function() {
        jQuery('.arabic-select-reload-dp').multipleSelect({
            filter: true,
            filterAcceptOnEnter: true
        })
    });
</script>