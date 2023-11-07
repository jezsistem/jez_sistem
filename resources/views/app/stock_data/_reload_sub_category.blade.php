<select class="form-control col-md-12" id="psc_id">
    @foreach ($data['psc_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>