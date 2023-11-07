<script>
@if ($data['label'] == 'offline')
xchart_render.updateSeries([{
        name: 'Total Qty Waiting Offline',
        data: [
        @foreach ($data['offline_item'] as $row)
        {
            x: '{{ $row->br_name }}',
            y: [{{ $row->total }}],
        },
        @endforeach
        ]
}]);
@endif

@if ($data['label'] == 'online')
xchart_render.updateSeries([{
        name: 'Total Qty Waiting Online',
        data: [
        @foreach ($data['online_item'] as $row)
        {
            x: '{{ $row->br_name }}',
            y: [{{ $row->total }}],
        },
        @endforeach
        ]
}]);
@endif

@if ($data['label'] == 'instock')
xchart_render.updateSeries([{
        name: 'Total Qty Instock',
        data: [
        @foreach ($data['instock_item'] as $row)
        {
            x: '{{ $row->br_name }}',
            y: [{{ $row->total }}],
        },
        @endforeach
        ]
}]);
@endif

@if ($data['label'] == 'sales_instock')
xchart_render.updateSeries([{
        name: 'Total Qty Refund/Exchange',
        data: [
        @foreach ($data['sales_instock_item'] as $row)
        {
            x: '{{ $row->br_name }}',
            y: [{{ $row->total }}],
        },
        @endforeach
        ]
}]);
@endif

@if ($data['label'] == 'sales')
xchart_render.updateSeries([{
        name: 'Total Qty Terjual',
        data: [
        @foreach ($data['sales_item'] as $row)
        {
            x: '{{ $row->br_name }}',
            y: [{{ $row->total }}],
        },
        @endforeach
        ]
}]);
@endif
</script>
