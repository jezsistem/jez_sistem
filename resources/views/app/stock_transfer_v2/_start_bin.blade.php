<select class="form-control" id="pl_id_start" name="pl_id_start" required>
    <option value="">- BIN Awal -</option>
    @foreach ($data['pl_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>
<div id="pl_id_start_parent"></div>