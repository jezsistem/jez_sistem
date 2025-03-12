<select class="form-control col-md-12" id="pssc_id">
    @foreach ($data['pssc_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>