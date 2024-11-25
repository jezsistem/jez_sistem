<select class="form-control mt-2 bg-primary text-white" id="bin_filter">
    <option value='all'>- Semua BIN -</option>
    @foreach ($data['pl_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>
