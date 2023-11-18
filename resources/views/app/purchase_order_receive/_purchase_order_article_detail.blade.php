@if (!empty($data['product']))
<div class="table-responsive">
<table class="table table-hover table-checkable" id="PurchaseOrdertb">
    <thead class="bg-light text-dark">
        <tr>
            <th class="text-dark">No</th>
            <th style="white-space: nowrap;" class="text-light">Artikel</th>
            <th class="text-dark">Diskon</th>
            <th class="text-dark">Detail</th>
            <th style="white-space: nowrap;" class="text-light">Value PO</th>
            <th style="white-space: nowrap;" class="text-light">Value Terima</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; $final_price = 0; $final_price_poads = 0; $a = 0; @endphp
        @foreach($data['product'] as $row)
        <tr>
            <td>
                {{ $no }}
            </td>
            <td style="white-space: nowrap;">[{{ $row->br_name }}]<br/>{{ $row->p_name }}<br/>{{ $row->p_color }}<br/>
                @if ($row->poa_reminder != '' || $row->poa_reminder != 0000-00-00)
                {{ date('M Y', strtotime($row->poa_reminder)) }}
                @endif
            </td>
            <td style="white-space: nowrap;">
                <input type="text" style="width:65px;" value="Disc" readonly/><input type="text" name="poa_discount" id="poa_discount{{ $row->poa_id }}" style="width:33px;" value="{{ $row->poa_discount }}" onchange="return discount( {{ $row->poa_id }} )"/><br/>
                <input type="text" style="width:65px;" value="Ex. Disc" readonly/><input type="text" name="poa_extra_discount" id="poa_extra_discount{{ $row->poa_id }}" style="width:33px;" value="{{ $row->poa_extra_discount }}" onchange="return extraDiscount( {{ $row->poa_id }} )"/><br/>
                <input type="text" disabled style="width:65px;" value="Sub. Disc" readonly/><input disabled type="text" name="poa_sub_discount" id="poa_sub_discount{{ $row->poa_id }}" style="width:33px;" value="{{ $row->poa_sub_discount }}" onchange="return subDiscount( {{ $row->poa_id }} )"/>
            </td>
            <td style="white-space: nowrap;">

                    <input type="text" style="width:60px;" value="Sz" readonly/>
                    <input type="text" style="width:60px;" value="Ord" readonly/>
                    <input type="text" style="width:60px;" value="Krg" readonly/>
                    <input type="text" class="bg-primary text-white" style="width:52px;" value="Terima" readonly/>
                    <input type="text" style="width:80px;" value="Harga Bnd" readonly/>
                    <input type="text" style="width:80px;" value="Harga Beli" readonly/>
                    <input type="text" style="width:80px;" value="Total" readonly/>
                    <input type="text" style="width:90px;" value="Total Terima" readonly/>
                    <br/>
                    @if (!empty($row->subitem))
                        @php $i = 0; $total_poad_price = 0; $total_poads_price = 0; @endphp
                        @foreach ($row->subitem as $srow)
                        <span data-poa-{{ $row->poa_id }}>
                        <input type="text" style="width:60px;" value="{{ $srow->sz_name }}" readonly/>
                        <input type="number" style="width:60px;" value="{{ $srow->poad_qty }}" readonly/>
                        <input type="number" id="poads_qty_remain_{{ $row->poa_id }}_{{ $i }}" style="width:60px;" value="{{ ($srow->poad_qty-$srow->poads_qty) }}" readonly/>
                        @if ($srow->poad_qty-$srow->poads_qty == 0)
                            <input type="text" style="width:52px;" value="Full" disabled/>
                        @elseif($srow->qty_import != null)
                            <input type="text" id="poads_qty_{{ $row->poa_id }}_{{ $i }}" style="width:52px;" value="{{ $srow->qty_import }}" onchange="return receiveQtyImport({{ $row->poa_id }}, {{ $i }}, {{ $srow->qty_import }} )" required/>
                        @else
                            <input type="text" id="poads_qty_{{ $row->poa_id }}_{{ $i }}" style="width:52px;" value="" onchange="return receiveQty({{ $row->poa_id }}, {{ $i }} )" required/>
                        @endif
                        @if ($srow->ps_price_tag == null || $srow->ps_price_tag == 0)
                        <input type="text" style="width:80px;" id="price_tag_{{ $row->poa_id }}_{{ $i }}" value="{{ number_format($row->p_price_tag) }}" readonly/>
                        @else
                        <input type="text" style="width:80px;" id="price_tag_{{ $row->poa_id }}_{{ $i }}" value="{{ number_format($srow->ps_price_tag) }}" readonly/>
                        @endif
                        @if($srow->qty_import != null || $srow->qty_import != 0)
                            @if ($srow->ps_price_tag == null || $srow->ps_price_tag == 0)
                                <input type="text" style="width:80px;" data-poad-id="{{ $srow->poad_id }}" id="poad_purchase_price_{{ $row->poa_id }}_{{ $i }}" onchange="return receivePurchasePrice( {{ $row->poa_id }}, {{ $i }} )" value="{{ number_format($srow->qty_import * $row->p_price_tag) }}"/>
                            @else
                                <input type="text" style="width:80px;" data-poad-id="{{ $srow->poad_id }}" id="poad_purchase_price_{{ $row->poa_id }}_{{ $i }}" onchange="return receivePurchasePrice( {{ $row->poa_id }}, {{ $i }} )" value="{{ number_format($srow->qty_import * $srow->ps_price_tag) }}"/>
                            @endif
                        @else
                        <input type="text" style="width:80px;" data-poad-id="{{ $srow->poad_id }}" id="poad_purchase_price_{{ $row->poa_id }}_{{ $i }}" onchange="return receivePurchasePrice( {{ $row->poa_id }}, {{ $i }} )" value=""/>
                        @endif
                        <input type="text" style="width:80px;" id="total_purchase_price_{{ $row->poa_id }}_{{ $i }}" value="{{ number_format($srow->poad_total_price) }}" readonly/>
                        @if($srow->qty_import != null)
                            <input type="text" style="width:90px;" id="total_purchase_price_receive{{ $row->poa_id }}_{{ $i }}" value="{{ number_format($srow->qty_import *  $srow->poad_total_price) }}" readonly/> <img data-img-poads id="savePoads{{ $a }}" onclick="return savePoads( {{ $srow->poad_id }}, {{ $row->poa_id }}, {{ $i }}, {{ $srow->pst_id }} )" src="{{ asset('cdn/details_open.png') }}" ondblclick="return bulkSavePoads( {{ $srow->poad_id }}, {{ $row->poa_id }}, {{ $i }}, {{ $srow->pst_id }} )" src="{{ asset('cdn/details_open.png') }}"/>
                        @else
                        <input type="text" style="width:90px;" id="total_purchase_price_receive{{ $row->poa_id }}_{{ $i }}" value="" readonly/> <img data-img-poads id="savePoads{{ $a }}" onclick="return savePoads( {{ $srow->poad_id }}, {{ $row->poa_id }}, {{ $i }}, {{ $srow->pst_id }} )" src="{{ asset('cdn/details_open.png') }}" ondblclick="return bulkSavePoads( {{ $srow->poad_id }}, {{ $row->poa_id }}, {{ $i }}, {{ $srow->pst_id }} )" src="{{ asset('cdn/details_open.png') }}"/>
                        @endif
                        <i class="fa fa-eye" data-p_name="[{{ $row->br_name }}] {{ $row->p_name }} {{ $row->p_color }} [{{ $srow->sz_name }}]" data-poad_id="{{ $srow->poad_id }}" id="receive_history"> </i>
                        <br/>
                        @php $i ++; $a++; $total_poad_price += $srow->poad_total_price; $total_poads_price += $srow->poads_total_price; @endphp
                        @endforeach
                    @endif
            </td>
            <td><span id="poad_total_price_{{ $row->poa_id }}">{{ number_format($total_poad_price) }}</span></td>
            <td><span id="poads_total_price_{{ $row->poa_id }}">{{ number_format($total_poads_price) }}</span></td>
        </tr>
        @php $no++; $final_price += $total_poad_price; $final_price_poads += $total_poads_price; @endphp
        @endforeach
        <tr>
            <td>
            </td>
            <td>
            </td>
            <td>
            </td>
            <td>
                <a class="btn btn-sm btn-success float-right" id="save_all_ro_btn" onclick="return saveAllPoads()">Terima</a>
            </td>
            <td>
                <span class="float-right">
                    <input type="text" style="width:90px;" id="poads_total_price_receive" value="{{ number_format($final_price) }}" readonly/>
                </span>
            </td>
            <td>
                <span class="float-right">
                    <input type="text" style="width:90px;" id="poads_total_price_receive" value="{{ number_format($final_price_poads) }}" readonly/>
                </span>
            </td>
        </tr>
    </tbody>
</table>
</div>
@endif
