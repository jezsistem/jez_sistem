<html moznomarginboxes mozdisallowselectionprint>
    <head>
        <title>
            Jersey Zone - Invoice Print
        </title>
        <style type="text/css">
            html {
                font-family: "Verdana";
            }
            .content {
                text-align: center;
                width: 82mm;
                margin-left:1mm;
                font-size: 10px;
            }
            .content .title {
                text-align: center;
                width: 97%;
            }
            .title-left {
                text-align: left;
            }
            .content .head-desc {
                margin-top: 10px;
                display: table;
                width: 95%;
            }
            .content .head-desc > div {
                display: table-cell;
            }
            .content .head-desc .date {
                text-align: left;
            }
            .content .head-desc .user {
                text-align: right;
            }
            .content .nota {
                text-align: center;
                margin-top: 5px;
                margin-bottom: 5px;
            }
            .content .separate {
                width: 95%;
                margin-top: 10px;
                margin-bottom: 15px;
                border-top: 1px dashed #000;
            }
            .content .transaction-table {
                width: 95%;
                font-size: 10px;
            }
            .content .transaction-table .name {
                width: 100px;
            }
            .content .transaction-table .qty {
                padding-left:10px;
                text-align: center;
            }
            .content .transaction-table .sell-price, .content .transaction-table .final-price {
                text-align: right;
                width: 65px;
            }
            .content .transaction-table tr td {
                vertical-align: top;
            }
            .content .transaction-table .price-tr td {
                padding-top: 7px;
                padding-bottom: 7px;
            }
            .content .transaction-table .discount-tr td {
                padding-top: 7px;
                padding-bottom: 7px;
            }
            .content .transaction-table .separate-line {
                height: 1px;
                border-top: 1px dashed #000;
            }
            .content .thanks {
                margin-top: 15px;
                text-align: center;
            }
            .content .azost {
                margin-top:5px;
                text-align: center;
                font-size:10px;
            }

            .text-red {
                color: red;
            }
            @media print {
                @page  { 
                    width: 82mm;
                    margin: 2mm;
                }
            }

        </style>
    </head>
    <body>
        @if (!empty($data['transaction']))
        <div class="content">
            <center>
            <div class="user">{{ $data['invoice'] }}</div><br/>
            <div id="product_qr"></div>
            </center><br/>
            <div class="separate"></div>
            <div class="title">
                <strong>{{ $data['transaction']->st_name }}</strong><br/>
                {{ $data['transaction']->st_address }}<br/>
                {{ $data['transaction']->st_phone }}<br/><br/>
                Jersey Zone<br/>
                www.jez.co.id
            </div>

            <div class="head-desc">
                <div class="date">
                    {{ date('d-m-Y H:i:s', strtotime($data['transaction']->pos_created)) }}<br/>
                    Pembayaran: {{ $data['transaction']->pm_name }}
                </div>
                <div class="user">
                    {{ $data['transaction']->u_name }}<br/>
                    Tipe: <strong>{{ $data['transaction']->dv_name }}</strong>
                </div>
            </div>

            <div class="nota">
                {{ $data['invoice'] }}
            </div>
            <div class="separate"></div>

            <div class="transaction">
                <table class="transaction-table" cellspacing="0" cellpadding="0">
                    @php $discount = 0; $nameset = 0; $subtotal = 0; $total_price = 0;
                        $total_discount = $data['transaction']->pos_total_discount;
                        $total_discount_show= $data['transaction']->pos_total_discount ;
                        $total_marketplace = 0; @endphp
                    @if (!empty($data['transaction_detail']))
                        @foreach ($data['transaction_detail'] as $srow)
                        <tr style="margin-bottom:5px;">
                            <td class="name">[{{ $srow->br_name }}] {{ $srow->p_name }} {{ $srow->p_color }} ({{ $srow->sz_name }})</td>
                            <td class="qty">{{ $srow->pos_td_qty }}x</td>
                            @if ($data['transaction']->dv_name != 'DROPSHIPPER' AND $data['transaction']->dv_name != 'RESELLER' AND $data['transaction']->dv_name != 'WHATSAPP' AND $data['transaction']->dv_name != 'TIKTOK' AND $data['transaction']->dv_name != 'WEBSITE')
                                <td class="sell-price">{{ round($srow->pos_td_marketplace_price/$srow->pos_td_qty) }}
                                    @if (!empty($srow->pos_td_discount))
                                    <br/>{{ $srow->pos_td_discount }}%
                                    @endif
                                </td>
                                <td class="final-price">{{ number_format($srow->pos_td_marketplace_price) }}
                                </td>
                            @else 
                                @if ($data['transaction']->is_website == '1')
                                <td class="sell-price">{{ number_format($srow->pos_td_sell_price) }}
                                </td>
                                <td class="final-price">{{ number_format($srow->pos_td_qty * $srow->pos_td_sell_price) }}</td>
                                @else 
                                <td class="sell-price">
                                    <s>{{ number_format($srow->productStock->ps_price_tag) }}</s>
                                    <br/>
                                    {{ number_format($srow->pos_td_sell_price) }}
                                    @if (!empty($srow->pos_td_discount))
                                    <br/>{{ $srow->pos_td_discount }}%
                                    @endif
                                    @if(!empty($srow->pos_td_discount_number))
                                        <br/> <span class="text-red">({{ $srow->pos_td_discount_number }})</span>
                                    @endif
                                </td>
                                <td class="final-price">
                                    @if(!empty($srow->pos_td_discount_number))
                                        <span>{{ number_format(($srow->pos_td_qty * $srow->pos_td_sell_price) - $srow->pos_td_discount_number) }}</span>
                                    @else
                                        {{ number_format($srow->pos_td_qty * $srow->pos_td_sell_price) }}
                                    @endif
                                    @if (!empty($srow->pos_td_discount))
                                    <br/>(-{{ number_format($srow->pos_td_qty * ($srow->pos_td_sell_price/100 * $srow->pos_td_discount)) }})
                                    @endif
                                </td>
                                @endif
                            @endif
                        </tr>
                        @php 
                        if ($data['transaction']->is_website == '1') {
                            $subtotal += ($srow->pos_td_qty * $srow->pos_td_sell_price); 
                            $total_price += $srow->pos_td_total_price;
                        } else {
                            $nameset += $srow->pos_td_nameset_price; 
                            $subtotal += ($srow->pos_td_qty * $srow->pos_td_sell_price); 
                            $total_price += $srow->pos_td_total_price; 
//                            $total_discount += $srow->pos_td_qty * ($srow->pos_td_sell_price/100 * $srow->pos_td_discount);
                            $total_discount += $srow->pos_td_discount_number;
                            $total_discount_show += $srow->pos_td_discount_number;
                            $total_discount_show += $srow->pos_td_price_item_discount;
//                            $total_marketplace +=     $srow->pos_td_marketplace_price;
                            $total_marketplace+= ($srow->pos_td_qty * $srow->pos_td_sell_price);;
                        }
                        @endphp
                        @endforeach
                    @endif
                    <!-- Discount -->
                    <tr class="discount-tr">
                        <td colspan="4">
                            <div class="separate-line"></div>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="3" class="final-price">
                            <span style="float:left;">SUBTOTAL</span>
                        </td>
                        <td class="final-price">
                            <span style="float:right;">
                            @if ($data['transaction']->dv_name != 'DROPSHIPPER' AND $data['transaction']->dv_name != 'RESELLER' AND $data['transaction']->dv_name != 'WHATSAPP' AND $data['transaction']->dv_name != 'TIKTOK' AND $data['transaction']->dv_name != 'WEBSITE')
                                {{ number_format($total_marketplace) }}
                            @else 
                                {{ number_format($subtotal) }}
                            @endif
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="final-price">
                            <span style="float:left;">DISKON</span>
                        </td>
                        <td class="final-price">
                            <span style="float:right;">
                            @if ($data['transaction']->dv_name != 'DROPSHIPPER' AND $data['transaction']->dv_name != 'RESELLER' AND $data['transaction']->dv_name != 'WHATSAPP' AND $data['transaction']->dv_name != 'TIKTOK' AND $data['transaction']->dv_name != 'WEBSITE')
                                0
                            @else
                            <span class="text-red">
                            (
                            @if (!empty($total_discount_show))
                            {{ ($total_discount_show) }}
                            @else 
                            0
                            @endif
                            )
                                </span>
                            @endif</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="final-price">
                            <span style="float:left;">ONGKIR</span>
                        </td>
                        <td class="final-price">
                            <span style="float:right;">{{ (number_format($data['transaction']->pos_shipping)) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="final-price">
                            <span style="float:left;">NAMESET</span>
                        </td>
                        <td class="final-price">
                            <span style="float:right;">{{ (number_format($nameset)) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="final-price">
                            <span style="float:left;">BIAYA LAIN</span>
                        </td>
                        <td class="final-price">
                            <span style="float:right;">{{ (number_format($data['transaction']->pos_another_cost)) }}</span>
                        </td>
                    </tr>
                    @if ($data['transaction']->is_website == '1')
                    <tr>
                        <td colspan="3" class="final-price">
                            <span style="float:left;">KODE UNIK</span>
                        </td>
                        <td class="final-price">
                            <span style="float:right;">{{ (number_format($data['transaction']->pos_unique_code)) }}</span>
                        </td>
                    </tr>     
                    @endif
                    @if (!empty($data['transaction']->pos_discount))
                    <tr>
                        <td colspan="3" class="final-price">
                            <span style="float:left;">ADDITIONAL DISCOUNT</span>
                        </td>
                        <td class="final-price">
                            <span style="float:right;">
                            {{ $data['transaction']->pos_discount }} %
                            </span>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="final-price">
                            <span style="float:left; font-weight:bold;">TOTAL AKHIR</span>
                        </td>
                        <td class="final-price">
                            <span style="float:right;">
                            @if ($data['transaction']->dv_name != 'DROPSHIPPER' AND $data['transaction']->dv_name != 'RESELLER' AND $data['transaction']->dv_name != 'WHATSAPP' AND $data['transaction']->dv_name != 'WEBSITE')
                                @php 
//                                $totals = $total_marketplace+$nameset+$data['transaction']->pos_another_cost;
                                $totals = $total_marketplace+$nameset+$data['transaction']->pos_another_cost;
                                if (!empty($data['transaction']->pos_discount)) {
                                    $totals = $totals - ($totals/100 * $data['transaction']->pos_discount_number);
                                }
//                                if(!empty($data['transaction']->pos_td_discount_number)) {
//                                    $totals -= $total_discount;
//                                }

                                if ($total_discount > 0) {
                                    $totals -= $total_discount;
                                }

                                @endphp
                                {{ number_format($totals) }}
                            @else
                                @php 
                                if (!empty($total_discount)) {
                                    $totals = $subtotal-$total_discount+$data['transaction']->pos_shipping+$nameset+$data['transaction']->pos_another_cost;
                                } else {
                                    if ($data['transaction']->is_website == '1') {
                                        $totals = $subtotal+$data['transaction']->pos_shipping+$nameset+$data['transaction']->pos_unique_code;
                                    } else {
                                        $totals = $subtotal+$data['transaction']->pos_shipping+$nameset+$data['transaction']->pos_another_cost;
                                    }
                                }
                                if (!empty($data['transaction']->pos_discount)) {
                                    $totals = $totals - ($totals/100 * $data['transaction']->pos_discount);
                                }

                                if(!empty($data['transaction']->pos_discount_number)) {
                                    $totals = $totals - $data['transaction']->pos_discount_number;
                                }
                                @endphp 
                                {{ number_format($totals) }}
                            @endif
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="final-price">
                            <span style="float:left; margin-top:10px;"><i>* Harga sudah termasuk PPN</i></span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="thanks">
                ~~~ Terimakasih ~~~
            </div>
            <div class="azost">
                www.jez.co.id
            </div>

            <div class="separate"></div>

            <div class="nota">
                {{ $data['invoice'] }}<br/>
                @if ($data['transaction']->is_website == '1')
                <strong>{{ $data['transaction']->pos_courier }}</strong>
                @else
                <strong>{{ $data['transaction']->cr_name }}</strong>
                @endif
            </div>
            <div class="title-left">
                <strong>PENERIMA</strong><br/>
                {{ $data['customer']->cust_name }}<br/>
                {{ $data['customer']->cust_address }}, {{ $data['cust_subdistrict'] }}, {{ $data['cust_city'] }}, {{ $data['cust_province'] }}<br/>
                {{ $data['customer']->cust_phone }}
            </div><br/>
            @if (!empty($data['dropshipper']))
            <div class="title-left">
                <strong>PENGIRIM</strong><br/>
                {{ $data['dropshipper']->cust_store }}<br/>
                {{ $data['dropshipper']->cust_phone }}
            </div>
            @else 
            <div class="title-left">
                <strong>PENGIRIM</strong><br/>
                {{ $data['transaction']->st_name }}<br/>
                {{ $data['transaction']->st_phone }}
            </div>
            @endif
            <br/>
            <div class="title">
                <strong><i>KOMPLAIN TIDAK KAMI TERIMA TANPA VIDEO<br/>UNBOXING</i></strong>
            </div>
        </div>
        @endif
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js" ></script>
        <script>
            function generateQR()
            {
                var value="{{ $data['invoice'] }}";
                $('#product_qr').empty();
                $('#product_qr').css({
                    'width' : 100,
                    'height' : 100
                })
                $('#product_qr').qrcode({width: 100,height: 100,text: value});
            }

            $(document).ready(function() {
                generateQR();
                window.print();
            });
        </script>
    </body>
</html>