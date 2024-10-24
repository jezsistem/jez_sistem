<select class="form-control" id="pssc_id" name="pssc_id" required>
    <option value="">- Pilih Sub-Sub Kategori -</option>
    @foreach ($data['pssc_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>