<select class="form-control col-md-12" id="sz_id">
    @foreach ($data['sz_id'] as $key => $value)
    @if ($value == null)
        @continue;
    @endif
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>