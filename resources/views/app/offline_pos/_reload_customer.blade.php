<select class="arabic-select-reload select-down" id="cust_id" name="cust_id">
    <option value="">- Pilih -</option>
    @foreach ($data['cust_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>
<script>
jQuery(function() {
    jQuery('.arabic-select-reload').multipleSelect({
        filter: true,
        filterAcceptOnEnter: true
    })
});
</script>