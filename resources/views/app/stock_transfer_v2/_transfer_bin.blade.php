<select class="form-control" id="pl_id" name="pl_id" required>
    <option value="">- BIN Untuk Ditransfer -</option>
    @foreach ($data['pl_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>
<div id="pl_id_parent"></div>