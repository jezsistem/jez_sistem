<html moznomarginboxes mozdisallowselectionprint>
<head>
    <title>Jersey Zone - Invoice Print</title>
    <style type="text/css">
        html { font-family: "Verdana"; }
        .content { text-align: center; width: 80mm; margin-left: 1mm; font-size: 15px; }
        .title-left { text-align: left; }
        .head-desc { margin-top: 10px; display: table; width: 93%; }
        .head-desc > div { display: table-cell; }
        .head-desc .date { text-align: left; }
        .head-desc .user { text-align: right; }
        .nota { text-align: center; margin-top: 5px; margin-bottom: 5px; }
        .separate { width: 93%; margin-top: 10px; margin-bottom: 15px; border-top: 1px dashed #000; }
        .transaction-table { width: 93%; font-size: 12px; }
        .transaction-table .name { width: 100px; height: 85px; }
        .transaction-table .qty { padding-left: 10px; text-align: center; }
        .transaction-table .sell-price, .transaction-table .final-price { text-align: right; width: 65px; }
        .transaction-table tr td { vertical-align: top; }
        .transaction-table .price-tr td, .transaction-table .discount-tr td { padding-top: 7px; padding-bottom: 7px; }
        .thanks, .azost { margin-top: 15px; text-align: center; font-size: 15px; }
        @media print { @page { width: 80mm; margin: 2mm; } }
        .page-end { page-break-after: always; }
    </style>
</head>
<body>
@if (!empty($data['invoice_data']))
    @foreach ($data['invoice_data'] as $row)
        <center class="content">
            <img class="rounded reload" data-pt_id="{{ $row->pt_id }}" src="{{ asset('logo/logo_jez_sport.png') }}" style="width:43%; padding:10px; background-color:#000;" />
            <div class="title" style="margin-top: 20px;">
                <strong>{{ $row->st_name }}</strong><br/>
                {{ $row->st_address }}<br/>
                {{ $row->st_phone }}<br/><br/>
                Jersey Zone<br/>
                www.jez.co.id
            </div>
            <div class="separate"></div>
            <div class="nota" style="margin-top: 10px; margin-bottom: 10px;">{{ $data['invoice'] }}</div>
            <div class="head-desc">
                <div class="date">{{ \Carbon\Carbon::parse($row->pos_created)->translatedFormat('d F Y') }}<br/>Kasir<br/>Customer<br/>Pembayaran</div>
                <div class="user">{{ \Carbon\Carbon::parse($row->pos_created)->translatedFormat('H:i') }}<br/>{{ ucwords(strtolower($row->u_name)) }}<br>{{ $row->cust_name }}<br>{{ $row->pm_name }}@if (!empty($row->pm_name_partial)) & {{ $row->pm_name_partial }} @endif</div>
            </div>
            <div class="separate"></div>
            <div class="transaction">
                <table class="transaction-table" cellspacing="0" cellpadding="0">
                    @php
                        $total_item = 0;
                        $total_price = 0;
                        $total_discount = 0;
                        $nameset = 0;
                        $total_voucher = $row->pos_total_vouchers;
                    @endphp

                    @foreach ($row->subitem as $srow)
                        @php
                            $total_item += $srow->pos_td_qty;
                            $total_price += $srow->pos_td_discount_price;
                            $nameset += $srow->pos_td_nameset_price;
                            $total_discount += $srow->pos_td_discount_number;
                        @endphp
                        <tr>
                            <td class="name">{{ $srow->p_name }}<br></td>
                            <td class="qty">{{ $srow->pos_td_qty }}x</td>
                            <td class="sell-price">{{ !empty($srow->pos_td_discount_number) ? '<s>' . \App\Libraries\CurrencyFormatter::formatToIDR($srow->productStock->ps_price_tag) . '</s><br>' : '' }}@if(!empty($srow->pos_td_discount_number))<span>(-{{ \App\Libraries\CurrencyFormatter::formatToIDR($srow->pos_td_discount_number) }})</span>@endif@if (!empty($srow->pos_td_discount))<br/>{{ $srow->pos_td_discount }}%@endif</td>
                            <td class="final-price">{{ \App\Libraries\CurrencyFormatter::formatToIDR($srow->pos_td_sell_price) }}</td>
                        </tr>
                    @endforeach

                    <tr class="discount-tr">
                        <td colspan="3"><div class="separate-line"></div></td>
                    </tr>
                    <tr>
                        <td colspan="2">TOTAL ITEM</td>
                        <td class="final-price">{{ $total_item }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">Voucher</td>
                        <td class="final-price">{{ $total_voucher }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">DISKON</td>
                        <td class="final-price">({{ $total_discount }})</td>
                    </tr>
                    <tr>
                        <td colspan="2">SUBTOTAL</td>
                        <td class="final-price">{{ \App\Libraries\CurrencyFormatter::formatToIDR($total_price) }}</td>
                    </tr>
