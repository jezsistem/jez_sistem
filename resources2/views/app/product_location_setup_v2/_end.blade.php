<select class="form-control" id="pl_id_end" name="pl_id_end" required>
    <option value="">- BIN Tujuan -</option>
    @foreach ($data['pl_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>
<div id="pl_id_end_parent"></div>
<script>
    $('#pl_id_end').select2({
        width: "100%",
        dropdownParent: $('#pl_id_end_parent')
    });
    $('#pl_id_end').on('select2:open', function (e) {
        const evt = "scroll.select2";
        $(e.target).parents().off(evt);
        $(window).off(evt);
    });
    
    $(document).delegate('#pl_id_end', 'change', function() {
        end_bin_table.draw();
    });
</script>