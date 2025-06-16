<script>
    $('#assets_label').text(addCommas({{ $data['total'] }}));
    asChart_render.updateSeries([
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
