<select class="form-control" id="history_pl_id_end" name="history_pl_id_end" required>
    <option value="">- BIN Tujuan -</option>
    @foreach ($data['pl_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>
<div id="history_pl_id_end_parent"></div>
<script>
    $('#history_pl_id_end').select2({
        width: "100%",
        dropdownParent: $('#history_pl_id_end_parent')
    });
    $('#history_pl_id_end').on('select2:open', function (e) {
        const evt = "scroll.select2";
        $(e.target).parents().off(evt);
        $(window).off(evt);
    });
    
    $(document).delegate('#history_pl_id_end', 'change', function() {
        bin_history_table.draw();
    });
</script>