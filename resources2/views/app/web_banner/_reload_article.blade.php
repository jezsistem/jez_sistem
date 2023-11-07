<div class="form-group mb-1 pb-1">
    <label for="exampleTextarea">Article</label>
    <select class="form-control" id="pssc_id" name="pssc_id" required>
        <option value="">- Sub Sub Kategori -</option>
        @foreach ($data['pssc_id'] as $key => $value)
            <option value="{{ $key }}">{{ $value }}</option>
        @endforeach
    </select>
</div>
<div id="pssc_id_parent"></div>

<script>
    $('#pssc_id').select2({
        width: "100%",
        dropdownParent: $('#pssc_id_parent')
    });
    $('#pssc_id').on('select2:open', function (e) {
        const evt = "scroll.select2";
        $(e.target).parents().off(evt);
        $(window).off(evt);
    });
</script>