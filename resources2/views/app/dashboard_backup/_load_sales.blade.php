<script>
    $('#nett_sales_label_reload').text(addCommas({{ $data['total'] }}));
    nsChart_render.updateSeries([
    @if (!empty($data['item']))
    @foreach ($data['item'] as $row)
    {
        name: '{{ $row['st_name'] }}',
        data: [{{ $row['total'] }}],
        color: primary
    },
    @endforeach
    @endif
    ]);
</script>
