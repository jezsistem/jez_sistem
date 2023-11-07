@foreach ( $data['size'] as $key => $row)
<div class="checkbox-inline col-lg-12">
    <label class="checkbox checkbox-outline col-md-1">
    <input type="checkbox" data-key="{{ $key+1 }}" data-id="{{ $row->id }}" id="product_size{{ $key+1 }}" onclick="return getProductSize('{{ $key+1 }}')"/>
    <span></span>
    {{ $row->sz_name }}
    </label>
    <input type="text" class="form-control col-2 col-md-2 psb_hidden ml-auto" id="product_size_barcode{{ $key+1 }}" data-barcode="{{ $row->id }}" data-id="{{ $row->id }}" onchange="return getProductSizeBarcode('{{ $key+1 }}')" placeholder="Barcode" style="margin-bottom:10px; display:none;"/>
    <input type="number" class="form-control col-2 col-md-2 psb_hidden ml-auto" id="product_size_running_code{{ $key+1 }}" data-running-code="{{ $row->id }}" data-id="{{ $row->id }}" style="display:none;"/> 
    <input type="number" class="form-control col-2 col-md-2 psb_hidden ml-auto" id="product_size_price_tag{{ $key+1 }}" data-price-tag="{{ $row->id }}" onchange="return getProductPriceTag('{{ $key+1 }}')" placeholder="Harga Banderol" style="margin-bottom:10px; margin-right:7px; display:none;"/>
    <input type="number" class="form-control col-2 col-md-2 psb_hidden ml-auto" id="product_size_sell_price{{ $key+1 }}" data-sell-price="{{ $row->id }}" onchange="return getProductSellPrice('{{ $key+1 }}')" placeholder="Harga Jual" style="margin-bottom:10px; margin-right:7px; display:none;"/>
    <input type="number" class="form-control col-2 col-md-2 psb_hidden ml-auto" id="product_size_purchase_price{{ $key+1 }}" data-purchase-price="{{ $row->id }}" onchange="return getProductPurchasePrice('{{ $key+1 }}')" placeholder="Harga Beli" style="margin-bottom:10px; margin-right:7px; display:none;"/>
</div>
@endforeach