<select class="form-control col-md-12" id="gender_id">
    @foreach ($data['gn_name'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>