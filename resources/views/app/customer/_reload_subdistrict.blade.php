<select class="form-control" id="cust_subdistrict" name="cust_subdistrict" required>
    <option value="">- Pilih -</option>
    @foreach ($data['cust_subdistrict'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>
<div id="cust_subdistrict_parent"></div>