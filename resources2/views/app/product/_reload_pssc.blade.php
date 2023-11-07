<select class="form-control" id="_pssc_id" name="_pssc_id" disabled required>
    <option value="">- Pilih Sub-Sub Kategori -</option>
    @foreach ($data['pssc_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>