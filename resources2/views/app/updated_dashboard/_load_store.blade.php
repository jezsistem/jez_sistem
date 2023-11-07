<script>
@if ($data['label'] == 'sales')
brChart_render.updateSeries([{
            name: 'Total Omset (Non Admin)',
            data: [
            @foreach ($data['item'] as $row)
            {
                x: '{{ $row['st_name'] }}',
                y: [{{ $row['sales'] }}],
            },
            @endforeach
            ]
    }]);
@endif

@if ($data['label'] == 'profits')
brChart_render.updateSeries([{
            name: 'Total Profit (Non Admin)',
            data: [
            @foreach ($data['item'] as $row)
            {
                x: '{{ $row['st_name'] }}',
                y: [{{ $row['profits'] }}],
            },
            @endforeach
            ]
    }]);
@endif

@if ($data['label'] == 'cross_sales')
brChart_render.updateSeries([{
            name: 'Total Omset (Non Admin)',
            data: [
            @foreach ($data['item'] as $row)
            {
                x: '{{ $row['st_name'] }}',
                y: [{{ $row['csales'] }}],
            },
            @endforeach
            ]
    }]);
@endif

@if ($data['label'] == 'cross_profits')
brChart_render.updateSeries([{
            name: 'Total Profit (Non Admin)',
            data: [
            @foreach ($data['item'] as $row)
            {
                x: '{{ $row['st_name'] }}',
                y: [{{ $row['profits'] }}],
            },
            @endforeach
            ]
    }]);
@endif

@if ($data['label'] == 'purchases')
brChart_render.updateSeries([{
            name: 'Total Pembelian',
            data: [
            @foreach ($data['item'] as $row)
            {
                x: '{{ $row['st_name'] }}',
                y: [{{ $row['purchases'] }}],
            },
            @endforeach
            ]
    }]);
@endif

@if ($data['label'] == 'cc_assets')
brChart_render.updateSeries([{
            name: 'Total Cash/Credit',
            data: [
            @foreach ($data['item'] as $row)
            {
                x: '{{ $row['st_name'] }}',
                y: [{{ $row['cc_assets'] }}],
            },
            @endforeach
            ]
    }]);
@endif

@if ($data['label'] == 'c_assets')
brChart_render.updateSeries([{
            name: 'Total Consignment',
            data: [
            @foreach ($data['item'] as $row)
            {
                x: '{{ $row['st_name'] }}',
                y: [{{ $row['c_assets'] }}],
            },
            @endforeach
            ]
    }]);
@endif

@if ($data['label'] == 'cc_exc_assets')
brChart_render.updateSeries([{
            name: 'Total Cash/Credit',
            data: [
            @foreach ($data['item'] as $row)
            {
                x: '{{ $row['st_name'] }}',
                y: [{{ $row['cc_assets'] }}],
            },
            @endforeach
            ]
    }]);
@endif

@if ($data['label'] == 'c_exc_assets')
brChart_render.updateSeries([{
            name: 'Total Consignment',
            data: [
            @foreach ($data['item'] as $row)
            {
                x: '{{ $row['st_name'] }}',
                y: [{{ $row['c_assets'] }}],
            },
            @endforeach
            ]
    }]);
@endif

@if ($data['label'] == 'debts')
brChart_render.updateSeries([{
            name: 'Total Hutang',
            data: [
            @foreach ($data['item'] as $row)
            {
                x: '{{ $row['st_name'] }}',
                y: [{{ $row['debts'] }}],
            },
            @endforeach
            ]
    }]);
@endif
</script>
