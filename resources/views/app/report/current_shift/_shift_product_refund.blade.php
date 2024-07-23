<table class="table">
    <thead>
    <tr>
        <th scope="col">#</th>
        <th scope="col">Product Name</th>
        <th scope="col">Size</th>
        <th scope="col">Qty</th>
    </tr>
    </thead>
    <tbody>

    @if($items != null)
        @foreach($items as $item)
            <tr>
                <th scope="row">{{ $loop->iteration }}</th>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->size_name }}</td>
                <td>{{ $item->qty }}</td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="3" class="text-center">No Refund Items</td>
        </tr>
    @endif
    </tbody>
</table>