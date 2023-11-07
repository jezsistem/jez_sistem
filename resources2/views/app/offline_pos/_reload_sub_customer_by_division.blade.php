<select class="arabic-select-reload-dropshipper select-down" id="sub_cust_id" name="sub_cust_id">
    <option value="">- Pilih -</option>
    @foreach ($data['cust_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select> <a href="#" class="btn-sm btn-primary" data-id="" id="check_customer">Check</a>
<script>
jQuery(function() {
    jQuery('.arabic-select-reload-dropshipper').multipleSelect({
        filter: true,
        filterAcceptOnEnter: true
    })
});
</script>