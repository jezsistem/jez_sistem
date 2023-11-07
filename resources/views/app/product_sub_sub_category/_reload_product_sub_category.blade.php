<select class="form-control" id="psc_id" name="psc_id" required>
    <option value="">- Pilih Sub Kategori -</option>
    @foreach ($data['psc_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>