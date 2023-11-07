<select class="form-control" id="pl_id_end" name="pl_id_end" required>
    <option value="">- BIN Tujuan -</option>
    @foreach ($data['pl_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>
<div id="pl_id_end_parent"></div>