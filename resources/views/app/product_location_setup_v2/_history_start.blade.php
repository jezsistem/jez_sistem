<!-- <input type="hidden" id="_pc_id" value="" /> -->
<select class="form-control" id="history_pl_id_start" name="history_pl_id_start" required>
    <option value="">- BIN Awal -</option>
    @foreach ($data['pl_id'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>
<div id="history_pl_id_start_parent"></div>

<script>
    $('#history_pl_id_start').select2({
        width: "100%",
        dropdownParent: $('#history_pl_id_start_parent')
    });
    $('#history_pl_id_start').on('select2:open', function (e) {
        const evt = "scroll.select2";
        $(e.target).parents().off(evt);
        $(window).off(evt);
    });

    $(document).delegate('#history_pl_id_start', 'change', function() {
        bin_history_table.draw();
    });
</script>