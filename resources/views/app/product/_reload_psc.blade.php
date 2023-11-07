<select class="form-control" id="_psc_id" name="_psc_id" disabled required>
    <option value="">- Pilih Sub Kategori -</option>
    @foreach ($data['psc_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>