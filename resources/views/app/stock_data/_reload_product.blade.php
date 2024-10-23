<select class="form-control col-md-12" id="p_name">
    @foreach ($data['p_name'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>