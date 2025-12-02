<script>
    $('#purchase_label_reload').text(addCommas({{ $data['total'] }}));
    pcChart_render.updateSeries([
    @if (!empty($data['item']))
    @foreach ($data['item'] as $row)
    {
        name: '{{ $row['st_name'] }}',
        data: [{{ $row['total'] }}],
        color: '{{ $row['color'] }}'
    },
    @endforeach
    @endif
    ]);
</script>
