@if (!empty($data['product']))
<div class="table-responsive">
<table class="table table-hover table-checkable" id="PurchaseOrdertb">
    <thead class="bg-light text-dark">
        <tr>
            <th class="text-dark">No</th>
            <th class="text-dark">Artikel</th>
            <th class="text-dark">Diskon</th>
            <th class="text-dark">Detail</th>
            <th class="text-dark">Total</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; $final_price = 0; $a = 0; @endphp
        @foreach($data['product'] as $row)
        <tr>
            <td>
                {{ $no }}
            </td>
            <td style="white-space: nowrap;">
            [{{ $row->br_name }}] {{ $row->p_name }}<br/>{{ $row->p_color }} <img onclick="return deletePoa( {{ $row->poa_id }}, {{ $row->po_id }} )" src="{{ asset('cdn/details_close.png') }}"/><br/>
                <input style="width:155px;" type="month" name="poa_reminder" id="poa_reminder{{ $row->poa_id }}" class="form-control" placeholder="Reminder" value="{{ $row->poa_reminder }}" onchange="return reminder( {{ $row->poa_id }} )"/>
            </td>
            <td style="white-space: nowrap;">
                <input type="text" style="width:65px;" value="Disc" readonly/><input type="text" name="poa_discount" id="poa_discount{{ $row->poa_id }}" style="width:33px;" value="{{ $row->poa_discount }}" onchange="return discount( {{ $row->poa_id }} )"/><br/>
                <input type="text" style="width:65px;" value="Ex. Disc" readonly/><input type="text" name="poa_extra_discount" id="poa_extra_discount{{ $row->poa_id }}" style="width:33px;" value="{{ $row->poa_extra_discount }}" onchange="return extraDiscount( {{ $row->poa_id }} )"/><br/>
                <input type="text" disabled style="width:65px;" value="Sub. Disc" readonly/><input disabled type="text" name="poa_sub_discount" id="poa_sub_discount{{ $row->poa_id }}" style="width:33px;" value="{{ $row->poa_sub_discount }}" onchange="return subDiscount( {{ $row->poa_id }} )"/>
            </td>
            <td style="white-space: nowrap;">
                    <input type="text" style="width:60px;" value="Sz" readonly/>
                    <input type="text" class="bg-primary text-white" style="width:50px;" value="Order" readonly/>
                    <input type="text" style="width:100px;" value="Harga Band" readonly/>
                    <input type="text" style="width:100px;" value="Harga Beli" readonly/>
                    <input type="text" style="width:100px;" value="Total" readonly/><br/>
                    @if (!empty($row->subitem))
                        @php $i = 0; $total_poad_price = 0; @endphp
                        @foreach ($row->subitem as $srow)
                        <span data-poa-{{ $row->poa_id }}>
                        <input type="text" style="width:60px;" value="{{ $srow->sz_name }}" readonly/>
                        <input type="text" id="poad_qty_{{ $row->poa_id }}_{{ $i }}" style="width:50px;" value="{{ $srow->poad_qty }}" class="order_po_qty order_po_qty_row{{ $a }}" onchange="return orderQty( {{ $row->poa_id }}, {{ $i }}, {{ $srow->poad_id }} )" required/>

                        @if ($srow->ps_price_tag == null || $srow->ps_price_tag == 0)
                        <input type="text" style="width:100px;" id="price_tag_{{ $row->poa_id }}_{{ $i }}" value="{{ number_format($row->p_price_tag) }}" readonly/>
                            @if ($srow->poad_purchase_price == null || $srow->poad_purchase_price == 0)
                            <input type="text" style="width:100px;" data-poad-id="{{ $srow->poad_id }}" onchange="return poPurchasePrice( {{ $row->poa_id }}, {{ $i }}, {{ $srow->poad_id }}, {{ $row->po_id }} )" id="poad_purchase_price_{{ $row->poa_id }}_{{ $i }}" value=""/>
                            @else
                            <input type="text" style="width:100px;" data-poad-id="{{ $srow->poad_id }}" onchange="return poPurchasePrice( {{ $row->poa_id }}, {{ $i }}, {{ $srow->poad_id }}, {{ $row->po_id }} )" id="poad_purchase_price_{{ $row->poa_id }}_{{ $i }}" value=""/>
                            @endif
                        @else
                        <input type="text" style="width:100px;" id="price_tag_{{ $row->poa_id }}_{{ $i }}" value="{{ number_format($srow->ps_price_tag) }}" readonly/>
                            @if ($srow->poad_purchase_price == null || $srow->poad_purchase_price == 0)
                            <input type="text" style="width:100px;" data-poad-id="{{ $srow->poad_id }}" onchange="return poPurchasePrice( {{ $row->poa_id }}, {{ $i }}, {{ $srow->poad_id }}, {{ $row->po_id }} )" id="poad_purchase_price_{{ $row->poa_id }}_{{ $i }}" value=""/>
                            @else
                            <input type="text" style="width:100px;" data-poad-id="{{ $srow->poad_id }}" onchange="return poPurchasePrice( {{ $row->poa_id }}, {{ $i }}, {{ $srow->poad_id }}, {{ $row->po_id }} )" id="poad_purchase_price_{{ $row->poa_id }}_{{ $i }}" value=""/>
                            @endif
                        @endif
                        <input type="text" style="width:100px;" id="total_purchase_price_{{ $row->poa_id }}_{{ $i }}" value="{{ number_format($srow->poad_total_price) }}" readonly/> <img onclick="return deletePoad( {{ $srow->poad_id }} )" src="{{ asset('cdn/details_close.png') }}"/><br/>
                        @php $i ++; $total_poad_price += $srow->poad_total_price; $a++; @endphp
                        @endforeach
                    @endif
            </td>
            <td><span id="poad_total_price_{{ $row->poa_id }}">{{ number_format($total_poad_price) }}</span></td>
        </tr>
        @php $no++; $final_price += $total_poad_price; @endphp
        @endforeach
        <tr>
            <!-- <td>
                <a class="btn btn-sm btn-primary" id="save_all_po_btn" onclick="return saveAllPo()">Simpan</a>
            </td> -->
            <td colspan="11">
                    <span class="float-right">
                        <a class="btn-sm btn-primary form-control"><center>Rp. <span id="poad_total_price">{{ number_format($final_price) }}</span></center></a>
                    </span>
            </td>
        </tr>
    </tbody>
</table>
</div>
@endif
