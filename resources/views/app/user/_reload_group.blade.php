<select class="form-control" id="gr_id" name="gr_id">
    <option value="">- Pilih -</option>
    @foreach ($data['gr_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>