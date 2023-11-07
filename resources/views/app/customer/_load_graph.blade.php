<script>
provinceChart_render.updateSeries([{
        name: 'Total Customer',
        data: [
        @foreach ($data['province_item'] as $row)
        {
            x: '{{ $row->nama }}',
            y: [{{ $row->total }}],
        },
        @endforeach
        ]
}]);

dateChart_render.updateSeries([{
        name: 'Total Customer',
        data: [
        @foreach ($data['date_item'] as $row)
        {
            x: '{{ $row->date }}',
            y: [{{ $row->total }}],
        },
        @endforeach
        ]
}]);
</script>
