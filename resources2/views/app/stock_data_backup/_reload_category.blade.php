<select class="form-control col-md-12" id="pc_id">
    @foreach ($data['pc_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>