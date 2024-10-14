{{--@if (!empty($data['pos_complaint']))--}}
{{--    @php $i = 0; $total = 0; $pt_id = ''; @endphp --}}
{{--    @foreach($data['pos_complaint'] as $row)--}}
{{--    @php--}}
{{--        $pt_id = $row->pt_id;--}}
{{--        if (!empty($row->ps_sell_price)) {--}}
{{--            $sell_price = $row->ps_sell_price;--}}
{{--        } else {--}}
{{--            $sell_price = $row->p_sell_price;--}}
{{--        }--}}

{{--        if (date('Y-m-d') <= $row->pd_date) {--}}
{{--            if (empty($row->st_id)) {--}}
{{--                if ($row->pd_type == 'percent') {--}}
{{--                    $sell_price = $sell_price - ($sell_price/100 * $row->pd_value);--}}
{{--                } elseif ($row->pd_type == 'amount') {--}}
{{--                    $sell_price = $sell_price - $row->pd_value;--}}
{{--                } else {--}}
{{--                    $sell_price = $sell_price;--}}
{{--                }--}}
{{--            } else {--}}
{{--                if (Auth::user()->st_id == $row->st_id) {--}}
{{--                    if ($row->pd_type == 'percent') {--}}
{{--                        $sell_price = $sell_price - ($sell_price/100 * $row->pd_value);--}}
{{--                    } elseif ($row->pd_type == 'amount') {--}}
{{--                        $sell_price = $sell_price - $row->pd_value;--}}
{{--                    } else {--}}
{{--                        $sell_price = $sell_price;--}}
{{--                    }--}}
{{--                }--}}
{{--            }--}}
{{--            --}}
{{--            $subtotal_price = $sell_price * $row->plst_qty;--}}
{{--        }--}}
{{--       --}}
{{--    @endphp--}}
{{--    <tr data-list-item class="bg-danger text-white pos_item_list{{ $row->pst_id }} mb-2" id="data_plst{{ $row->plst_id }}"> <td style="white-space: nowrap; font-size:14px;">[{{ $row->br_name }}] {{ $row->p_name }} {{ $row->p_color }} {{ $row->sz_name }}</td> <td>{{ $row->plst_qty }}</td> <td><input type="number" class="form-control border-dark col-4 basicInput2{{ $row->pst_id }}" id='item_qty{{ ($i+1) }}' value="-{{ $row->plst_qty }}" onchange="return changeReturQty({{ ($i+1) }}, {{ $row->pst_id }}, {{ $row->plst_qty }})"></td> <td><input type="number" class="col-8 nameset_price" value="-{{ $row->pos_td_nameset_price }}" id="nameset_price{{ $i+1 }}" onchange="return namesetPrice{{'$i+1'}}" readonly/></td> <td><span id="sell_price_item{{ $i+1 }}">-{{ number_format($sell_price) }}</span></td> <td><span class='subtotal_item' id="subtotal_item{{ $i+1 }}">-{{ number_format($subtotal_price) }}</span></td> <td><div class="card-toolbar text-right"><a href="#" class='saveItem' id="saveItem{{ $i+1 }}" onclick="return saveItem({{ $i+1 }}, {{ $row->pst_id }}, {{ $sell_price }}, {{ $row->plst_id }}, {{ $row->pl_id }})"><i class="fa fa-eye" style="display:none;"></i></a></div></td></tr>--}}
{{--    @php  $i ++; $total += $subtotal_price; @endphp--}}
{{--    @endforeach--}}

{{--<script>--}}
{{--    @if (!empty($pt_id))--}}
{{--    jQuery('#_pt_id_complaint').val('{{ $pt_id }}');--}}
{{--    jQuery('#total_item_side').text('-{{ $i }}');--}}
{{--    jQuery('#total_price_side').text('-{{ number_format($total) }}');--}}
{{--    jQuery('#total_final_price_side').text('-{{ number_format($total) }}');--}}
{{--    @endif--}}
{{--</script>--}}
{{--@endif--}}

@if (!empty($data['pos_complaint']))
    @php $i = 0; $total = 0; $pt_id = ''; @endphp
    @foreach($data['pos_complaint'] as $row)
        @php $pt_id = $row->pt_id @endphp
        @if (empty($row->ps_sell_price))
            <tr data-list-item class="bg-danger text-white pos_item_list{{ $row->pst_id }} mb-2"
                id="data_plst{{ $row->plst_id }}">
                <td style="white-space: nowrap;">[{{ $row->br_name }}
                    ] {{ $row->p_name }} {{ $row->p_color }} {{ $row->sz_name }}</td>
                <td>-</td>
                <td>-</td>
                <td><input type="text" class="form-control border-dark col-6 basicInput2{{ $row->pst_id }}"
                           id="basicInput2" value="-{{ $row->plst_qty }}" readonly></td>
                <td><input type="number" class="col-8" value="-{{ $row->pos_td_nameset_price }}"
                           id="nameset_price{{ $i+1 }}" onchange="return namesetPrice{{'$i+1'}}" readonly/></td>
                <td><span id="sell_price_item{{ $i+1 }}">-{{ number_format($row->p_sell_price) }}</span></td>
                <td><span id="subtotal_item{{ $i+1 }}">-{{ number_format($row->p_sell_price*$row->plst_qty) }}</span>
                </td>
                <td>
                    <div class="card-toolbar text-right"><a href="#" class='saveItem' id="saveItem{{ $i+1 }}"
                                                            onclick="return saveItem({{ $i+1 }}, {{ $row->pst_id }}, {{ $row->p_sell_price }}, {{ $row->plst_id }}, {{ $row->pl_id }})"><i
                                    class="fa fa-eye" style="display:none;"></i></a></div>
                </td>
            </tr>
            @php $i ++; $total += $row->p_sell_price @endphp
        @else
            <tr data-list-item class="bg-danger text-white pos_item_list{{ $row->pst_id }} mb-2"
                id="data_plst{{ $row->plst_id }}">
                <td style="white-space: nowrap;">[{{ $row->br_name }}
                    ] {{ $row->p_name }} {{ $row->p_color }} {{ $row->sz_name }}</td>
                <td>-</td>
                <td>-</td>
                <td><input type="text" class="form-control border-dark col-6 basicInput2{{ $row->pst_id }}"
                           id="basicInput2" value="-{{ $row->plst_qty }}" readonly></td>
                <td><input type="number" class="col-8" value="-{{ $row->pos_td_nameset_price }}"
                           id="nameset_price{{ $i+1 }}" onchange="return namesetPrice{{'$i+1'}}" readonly/></td>
                <td><span id="sell_price_item{{ $i+1 }}">-{{ number_format($row->ps_sell_price) }}</span></td>
                <td><span id="subtotal_item{{ $i+1 }}">-{{ number_format($row->ps_sell_price*$row->plst_qty) }}</span>
                </td>
                <td>
                    <div class="card-toolbar text-right"><a href="#" class='saveItem' id="saveItem{{ $i+1 }}"
                                                            onclick="return saveItem({{ $i+1 }}, {{ $row->pst_id }}, {{ $row->ps_sell_price }}, {{ $row->plst_id }}, {{ $row->pl_id }})"><i
                                    class="fa fa-eye" style="display:none;"></i></a></div>
                </td>
            </tr>
            @php $i ++; $total += $row->ps_sell_price @endphp
        @endif
    @endforeach

    <script>
        @if (!empty($pt_id))
        jQuery('#_pt_id_complaint').val('{{ $pt_id }}');
        jQuery('#total_item_side').text('-{{ $i }}');
        jQuery('#total_price_side').text('-{{ number_format($total) }}');
        jQuery('#total_final_price_side').text('-{{ number_format($total) }}');
        @endif
    </script>
@endif