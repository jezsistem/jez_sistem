@if (!empty($data['order_list']))
    <div class="card-body table-responsive">
        <table class="table table-hover table-checkable">
            <thead class="btn btn-inventory text-light">
                <tr>
                    <th class="text-dark">No</th>
                    <th class="text-dark">Artikel</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 1; @endphp
                @foreach($data['order_list'] as $row)
                @php 
                    $qty_pickup = $row->pos_td_qty-$row->pos_td_qty_pickup;
                    if ($qty_pickup < 0) {
                        $qty_pickup = 0;
                    }
                @endphp 
                <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $row->p_name }} {{ $row->p_color }} [{{ $row->sz_name }}]
                    <br/>   <a class="btn btn-sm btn-primary">Jml : {{ $qty_pickup }}</a>
                            <a class="btn btn-sm btn-primary">
                                {{ $row->pl_code }}
                            </a>
                            @if (($row->pos_td_qty-$row->pos_td_qty_pickup) != 0 AND ($row->pos_td_qty-$row->pos_td_qty_pickup) > 0)
                            <a class="btn btn-inventory" data-pos_td_qty="{{ $row->pos_td_qty }}" data-id="{{ $row->ptd_id }}" data-name="{{ $row->p_name }}" data-pl_id="{{ $row->pl_id }}" data-pst="{{ $row->pst_id }}" id="pl_id">
                                Ambil
                            </a>
                            <a class="btn btn-sm btn-success change_bin" data-pos_td_qty="{{ $row->pos_td_qty }}" data-name="{{ $row->p_name }}" data-pl_id="{{ $row->pl_id }}" data-pst="{{ $row->pst_id }}">
                                Ganti BIN
                            </a>
                            <a class="btn btn-sm btn-danger cancel_order_list" data-pos_td_qty="{{ $row->pos_td_qty }}" data-id="{{ $row->ptd_id }}" data-name="{{ $row->p_name }}" data-pl_id="{{ $row->pl_id }}" data-pst="{{ $row->pst_id }}" id="_id">
                                Batal
                            </a>
                            @else 
                                <a class="btn btn-sm btn-success">Ok</a>
                            @endif
                    </td>
                </tr>
                @php $i ++; @endphp
                @endforeach
            </tbody>
        </table>
    </div>
@else 
    <center><h4>Invoice tidak ditemukan</h4></center>
@endif