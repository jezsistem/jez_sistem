<select class="form-control" id="cust_city" name="cust_city" required>
    <option value="">- Pilih -</option>
    @foreach ($data['cust_city'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>
<div id="cust_city_parent"></div>