<select class="form-control col-md-12" id="br_id">
    @foreach ($data['br_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>