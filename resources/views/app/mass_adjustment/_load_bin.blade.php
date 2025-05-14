<div class="mb-2">
<select class="form-control mt-2 bg-primary text-white" id="bin_filter">
    <option value='all'>- Semua BIN -</option>
    @foreach ($data['pl_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>
<div id="bin_filter_parent"></div>
</div>
@include('app.mass_adjustment.mass_adjustment_js')