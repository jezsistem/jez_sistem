@if (!empty($data['pos_data']))
    @php
        $i = 0;
        $total = 0;
    @endphp
    @foreach($data['pos_data'] as $row)
    @php
        if (!empty($row->ps_sell_price)) {
            $sell_price = $row->ps_sell_price;
        } else {
            $sell_price = $row->p_sell_price;
        }
        
        if (date('Y-m-d') <= $row->pd_date) {
            if (empty($row->st_id)) {
                if ($row->pd_type == 'percent') {
                    $sell_price = $sell_price - ($sell_price/100 * $row->pd_value);
                } elseif ($row->pd_type == 'amount') {
                    $sell_price = $sell_price - $row->pd_value;
                } else {
                    $sell_price = $sell_price;
                }
            } else {
                if (Auth::user()->st_id == $row->st_id) {
                    if ($row->pd_type == 'percent') {
                        $sell_price = $sell_price - ($sell_price/100 * $row->pd_value);
                    } elseif ($row->pd_type == 'amount') {
                        $sell_price = $sell_price - $row->pd_value;
                    } else {
                        $sell_price = $sell_price;
                    }
                }
            }
            
        }

        if ($row->pl_code == 'TOKO' || $row->pl_code == 'TOKA') {
            $status = '';
            $pls_qty = $row->pls_qty+1;
        } else {
            $status = 'readonly';
            $pls_qty = $row->pls_qty;
        }
    @endphp
    <tr data-list-item class='pos_item_list"{{ $row->pst_id }}" mb-2 bg-light-primary' id='orderList{{ $i+1 }}'> <td style='white-space: nowrap; font-size:14px;' id='item_name{{ $i+1 }}'>[{{ $row->br_name }}] {{ $row->p_name }} {{ $row->p_color }} {{ $row->sz_name }}</td> <td>{{ $pls_qty }}</td> <td><input type='number' class='form-control border-dark col-4 basicInput2{{ $row->pst_id }}' id='item_qty{{ $i+1 }}' value='1' onchange='return changeQty({{ ($i+1) }}, {{ $row->pst_id }}, {{ $pls_qty }})' {{ $status }}></td> </td> <td><input type='number' class='col-8 nameset_price' id='nameset_price{{ $i+1 }}' onchange='return namesetPrice({{ $i+1 }})'/></td> <td><span id='sell_price_item{{ $i+1 }}'>{{ number_format($sell_price) }}</span></td> <td><span class='subtotal_item' id='subtotal_item{{ $i+1 }}'>{{ number_format($sell_price) }}</span></td> <td><div class='card-toolbar text-right'><a href='#' class='saveItem' id='saveItem{{ $i+1 }}' onclick='return saveItem({{ $i+1 }}, {{ $row->pst_id }}, {{ $row->p_sell_price }}, {{ $row->plst_id }}, {{ $row->pl_id }})'><i class='fa fa-eye' style='display:none;'></i></a> <a href='#' class='confirm-delete' title='Delete' onclick='return deleteItem({{ $row->pst_id }}, {{ $sell_price }}, {{ $i+1 }}, {{ $row->pl_id }}, {{ $row->plst_id }})'><i class='fas fa-trash-alt'></i></a></div></td></tr>
    @php $i ++; $total += $sell_price;@endphp
    @endforeach
@endif

<script>
    jQuery('#total_item_side').text('{{ $i }}');
    jQuery('#total_price_side').text('{{ number_format($total) }}');
    jQuery('#total_final_price_side').text('{{ number_format($total) }}');
</script>
