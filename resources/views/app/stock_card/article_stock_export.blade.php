<table>
    <thead>
    <tr>
        <th>No</th>
        <th>Article</th>
        <th>Item Name</th>
        <th>Barcode</th>
        <th>Size</th>
        <th>Brand</th>
        <th>Beginning Stock</th>
        <th>Purchase</th>
        <th>Transfer In</th>
        <th>Transfer Out</th>
        <th>Sales</th>
        <th>Adjustment +</th>
        <th>Adjustment -</th>
        <th>Adjustment Diff</th>
        <th>Ending Stock</th>
        <th>Today Stock</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $i => $row)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $row->article_id }}</td>
            <td>{{ $row->item_name }}</td>
            <td>{{ $row->ps_barcode }}</td>
            <td>{{ $row->size }}</td>
            <td>{{ $row->brand }}</td>
            <td>{{ $row->begin_stocks }}</td>
            <td>{{ $row->purchase }}</td>
            <td>{{ $row->tf_in }}</td>
            <td>{{ $row->tf_out }}</td>
            <td>{{ $row->sales }}</td>
            <td>{{ $row->SO_adjustment_plus }}</td>
            <td>{{ $row->SO_adjustment_minus }}</td>
            <td>{{ $row->SO_adjustment_diff }}</td>
            <td>{{ $row->ending_stocks }}</td>
            <td>{{ $row->today_stocks }}</td>
        </tr>
    @endforeach
    </tbody>
</table>