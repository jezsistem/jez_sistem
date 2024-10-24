<select class="form-control col-md-12" id="main_color_id">
    @foreach ($data['mc_name'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>