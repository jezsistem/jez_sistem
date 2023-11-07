<select class="form-control" id="ct_id" name="ct_id">
    <option value="">- Pilih -</option>
    @foreach ($data['ct_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>