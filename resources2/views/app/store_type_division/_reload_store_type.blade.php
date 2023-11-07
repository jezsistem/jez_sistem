<select class="form-control" id="stt_id" name="stt_id">
    <option value="">- Pilih -</option>
    @foreach ($data['stt_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>