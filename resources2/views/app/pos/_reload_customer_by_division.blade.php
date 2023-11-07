@if ($data['type'] == 'DROPSHIPPER')
<select class="arabic-select-reload select-down mb-2" id="cust_id" name="cust_id">
    <option value="">- Pilih Dropshipper -</option>
    @foreach ($data['cust_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select> <a href="#" class="btn-sm btn-primary" data-id="" id="check_dropshipper">Check</a><br/>
<select class="arabic-select-reload select-down" id="sub_cust_id" name="sub_cust_id">
    <option value="">- Pilih Customer -</option>
    @foreach ($data['sub_cust_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select> <a href="#" class="btn-sm btn-primary" data-id="" id="check_customer">Check</a>
@else
<select class="arabic-select-reload select-down" id="cust_id" name="cust_id">
    <option value="">- Pilih -</option>
    @foreach ($data['cust_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select> <a href="#" class="btn-sm btn-primary" data-id="" id="check_customer">Check</a>
@endif
<script>
jQuery(function() {
    jQuery('.arabic-select-reload').multipleSelect({
        filter: true,
        filterAcceptOnEnter: false
    })
});
</script>