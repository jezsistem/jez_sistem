<label for="exampleTextarea">Posisi*</label>
<select class="form-control" id="position_id" name="position_id">
    <option value="">- Pilih -</option>
    @foreach ($position as $position)
        <option value="{{ $position->id }}">{{ $position->up_code }}</option>
    @endforeach
</select>