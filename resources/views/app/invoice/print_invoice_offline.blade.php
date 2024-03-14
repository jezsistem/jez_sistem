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
            width: 80mm;
            margin-left: 1mm;
            font-size: 13px;
        }

        .content .title {
            text-align: center;
            width: 93%;
        }

        .title-left {
            text-align: left;
        }

        .content .head-desc {
            margin-top: 10px;
            display: table;
            width: 93%;
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
            width: 93%;
            margin-top: 10px;
            margin-bottom: 15px;
            border-top: 1px dashed #000;
        }

        .content .transaction-table {
            width: 93%;
            font-size: 13px;
        }

        .content .transaction-table .name {
            width: 100px;
        }

        .content .transaction-table .qty {
            padding-left: 10px;
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
            margin-top: 5px;
            text-align: center;
            font-size: 13px;
        }

        @media print {
            @page {
                width: 80mm;
                margin: 2mm;
            }
        }

    </style>
</head>
<body>
@if (!empty($data['invoice_data']))
    @foreach ($data['invoice_data'] as $row)
        <center class="content">
            <center>
                @if($data['store_code'] == 'JZ')
                    <img class="rounded reload" data-pt_id="{{ $row->pt_id }}"
                         src="{{ asset('logo/invoice-logo.png') }}"
                         style="width:40%; border-radius:100px; padding:10px; background-color:#000;"/>
                @elseif($data['store_code'] == 'SZ')
                    <img class="rounded reload" data-pt_id="{{ $row->pt_id }}"
                         src="{{ asset('logo/invoice-logo-sz.png') }}"
                         style="width:30%; border-radius:100px; padding:10px; background-color:#000;"/>
                @endif
                <div class="separate"></div>
                <div class="title">
                    <strong>{{ $row->st_name }}</strong><br/>
                    {{ $row->st_address }}<br/>
                    {{ $row->st_phone }}<br/><br/>
                    Jersey Zone<br/>
                    www.jez.co.id
                </div>

                <div class="head-desc">
                    <div class="date">
                        {{ date('d-m-Y H:i:s', strtotime($row->pos_created)) }}<br/>
                        Pembayaran: {{ $row->pm_name }} @if (!empty($row->pm_name_partial))
                            & {{ $row->pm_name_partial }}
                        @endif
                    </div>
                    <div class="user">
                        {{ $row->u_name }}</strong>
                    </div>
                </div>

                <div class="nota">
                    {{ $data['invoice'] }}
                </div>
                <div class="separate"></div>

                <div class="transaction">
                    <table class="transaction-table" cellspacing="0" cellpadding="0">
                        @php
                            $groups = array();
                            $total_item = 0;
                            $total_price = 0;
                            $nameset = 0;
                            $total_discount = $data['invoice_data'][0]['pos_total_discount'];
                            $total_voucher = $data['invoice_data'][0]['pos_total_vouchers'];
                            foreach ($row->subitem as $srow) {
                                $key = '['.$srow->br_name.'] '.$srow->p_name.' '.$srow->p_color.' '.$srow->sz_name;
                                if (!array_key_exists($key, $groups)) {
                                    $groups[$key] = array(
                                        'article' => '['.$srow->br_name.'] '.$srow->p_name.' '.$srow->p_color.' '.$srow->sz_name,
                                        'total_item' => $srow->pos_td_qty,
                                        'sell_price' => $srow->pos_td_sell_price,
                                        'total_sell_price' => $srow->pos_td_discount_price,
                                        'nameset' => $srow->pos_td_nameset_price,
                                    );
                                } else {
                                    $groups[$key]['total_item'] = $groups[$key]['total_item'] + $srow->pos_td_qty;
                                    $groups[$key]['sell_price'] = $groups[$key]['sell_price'];
                                    $groups[$key]['total_sell_price'] = $groups[$key]['total_sell_price'];
                                    $groups[$key]['nameset'] = $groups[$key]['nameset'] + $srow->pos_td_nameset_price;
                                }
                            }

                        @endphp

                        @foreach ($row->subitem as $srow)
                            @php
                                $key = '['.$srow->br_name.'] '.$srow->p_name.' '.$srow->p_color.' '.$srow->sz_name;
                                $total_item += $srow->pos_td_qty;
                                $total_price += $srow->pos_td_discount_price;
                                $nameset += $srow->pos_td_nameset_price;
                                $total_discount += $srow->pos_td_discount_number;
                            @endphp
                            <tr style="margin-bottom:5px;">
                                <td class="name">{{ $key }}</td>
                                @if (!empty($srow->pos_td_description) AND $srow->pos_td_qty > 1)
                                    <td class="qty" style="width: 1px;">{{ $srow->pos_td_description }}</td>
                                @else
                                    <td class="qty">{{ $srow->pos_td_qty }}x</td>
                                    <td class="sell-price">
                                        <s>{{ \App\Libraries\CurrencyFormatter::formatToIDR($srow->productStock->ps_price_tag) }}</s><br>
                                        {{--                                        @php--}}
                                        {{--                                            $number = $srow->pos_td_sell_price;--}}
                                        {{--                                            $formatted_number = number_format($number, 0, ',', '.');--}}
                                        {{--                                            $newFormatter = 'Rp ' . $formatted_number;--}}
                                        {{--                                            \App\Libraries\CurrencyFormatter::--}}
                                        {{--                                        @endphp--}}
                                        {{ \App\Libraries\CurrencyFormatter::formatToIDR($srow->pos_td_sell_price) }}


                                        @if (!empty($srow->pos_td_discount))
                                            <br/>{{ $srow->pos_td_discount }}%
                                        @endif

                                    </td>

                                @endif
                                <td class="final-price">
                                    @if(!empty($srow->pos_td_discount_number))
                                        {{ \App\Libraries\CurrencyFormatter::formatToIDR($srow->pos_td_discount_price - $srow->pos_td_discount_number) }}
                                    @else
                                        {{ \App\Libraries\CurrencyFormatter::formatToIDR($srow->pos_td_discount_price) }}
                                    @endif
                                    @if(!empty($srow->pos_td_discount_number))
                                        <br/> <span class="text-red">(-{{ $srow->pos_td_discount_number }})</span>
                                    @endif
                                </td>


                            </tr>
                        @endforeach
                        <!-- Discount -->
                        <tr class="discount-tr">
                            <td colspan="4">
                                <div class="separate-line"></div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="final-price">
                                <span style="float:left;">TOTAL ITEM</span>
                            </td>
                            <td class="final-price">
                                        <span style="float:right;">
                                        {{ $total_item }}
                                        </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="final-price">
                                <span style="float:left;">Voucher</span>
                            </td>
                            <td class="final-price">
                                        <span style="float:right;">
                                        {{ $total_voucher }}
                                        </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="final-price">
                                <span style="float:left;">DISKON</span>
                            </td>
                            <td class="final-price">
                                        <span style="float:right;">
                                        (
                                        @if (!empty($total_discount))
                                                {{ number_format($total_discount) }}
                                            @else
                                                0
                                            @endif
                                            )
                                        </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="final-price">
                                <span style="float:left;">SUBTOTAL</span>
                            </td>
                            <td class="final-price">
                                        <span style="float:right;">
                                        {{ \App\Libraries\CurrencyFormatter::formatToIDR($total_price) }}
                                        </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="final-price">
                                <span style="float:left;">NAMESET</span>
                            </td>
                            <td class="final-price">
                                        <span style="float:right;">
                                            {{ \App\Libraries\CurrencyFormatter::formatToIDR($nameset) }}
                                        </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="final-price">
                                <span style="float:left;">CHARGE (CC)</span>
                            </td>
                            <td class="final-price">
                                        <span style="float:right; white-space: nowrap;">
                                        {{ $row->pos_cc_charge }} % (+ {{ \App\Libraries\CurrencyFormatter::formatToIDR(($total_price+$nameset)/100*$row->pos_cc_charge) }})
                                        </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="final-price">
                                <span style="float:left;">BIAYA LAIN</span>
                            </td>
                            <td class="final-price">
                                <span style="float:right;">{{ \App\Libraries\CurrencyFormatter::formatToIDR($row->pos_another_cost) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="final-price">
                                <span style="float:left; font-weight:bold;">TOTAL AKHIR</span>
                            </td>
                            <td class="final-price">
                                        <span style="float:right;">
                                            @if (!empty($total_discount))
                                                {{--                                                {{ number_format($total_discount+$row->pos_another_cost) }}--}}
                                                {{ \App\Libraries\CurrencyFormatter::formatToIDR((($total_price+$nameset) - ($total_discount)) - $total_voucher) }}
                                            @else
                                                {{ \App\Libraries\CurrencyFormatter::formatToIDR(($total_price+$nameset+($total_price+$nameset)/100*$row->pos_cc_charge+$row->pos_another_cost) - $total_voucher) }}
                                            @endif
                                        </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="final-price">
                                <span style="float:left;">BAYAR</span>
                            </td>
                            <td class="final-price">
                                        <span style="float:right;">
                                        @if (!empty($row->pos_payment))
                                                {{ \App\Libraries\CurrencyFormatter::formatToIDR($row->pos_payment + $row->pos_payment_partial) }}
                                            @else
                                                {{ \App\Libraries\CurrencyFormatter::formatToIDR($total_price+$nameset+($total_price+$nameset)/100*$row->pos_cc_charge) }}
                                            @endif
                                        </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="final-price">
                                <span style="float:left;">KEMBALI</span>
                            </td>
                            <td class="final-price">
                                        <span style="float:right;">
                                        @if (!empty($row->pos_payment))
                                                {{--                                            {{ number_format(($row->pos_payment + $row->pos_payment_partial) - ($total_price+$nameset+($total_price+$nameset)/100*$row->pos_cc_charge) - $row->pos_another_cost) }}--}}

                                                {{ \App\Libraries\CurrencyFormatter::formatToIDR(($row->pos_payment + $row->pos_payment_partial + $total_voucher) - (($total_price+$nameset) - ($total_discount))) }}
                                            @else
                                                0
                                            @endif
                                        </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="final-price">
                                <span style="float:left; margin-top:10px;"><i>* Barang Kena Pajak (BKP) : Harga sudah termasuk PPN</i></span>
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
                <br/>
                <div class="title">
                    <strong><i>Ketentuan penukaran barang :</i></strong>
                    <br/>
                    <p style="text-align:left; font-size:11px;">
                        1. Batas waktu penukaran barang maksimal 3 hari dari saat transaksi (Barang yang dapat ditukar
                        hanya produk sepatu)<br/>
                        2. Penukaran barang tidak berlaku untuk produk jersey, t-shirt, asesoris maupun equipment<br/>
                        3. Penukaran barang tidak berlaku untuk barang yang diskon 25% keatas<br/>
                        4. Produk sepatu yang dapat ditukar yaitu belum pernah digunakan untuk beraktifitas dan wajib
                        memiliki dus yang sesuai dengan barang<br/>
                        5. Wajib menyertakan struk pembelanjaan saat proses penukaran barang baik pembelian offline &
                        online<br/>
                    </p>
                </div>
            </center>
        </center>
    @endforeach
@endif
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        window.print();
    });

    $(document).delegate('.reload', 'click', function (e) {
        e.preventDefault();
        var pt_id = $(this).attr('data-pt_id');
        $.ajax({
            type: "GET",
            data: {id: pt_id},
            dataType: 'json',
            url: "{{ url('check_sync') }}",
            success: function (r) {
                if (r.status == '200') {
                    window.location.reload();
                }
            }
        });
        return false;
    });
</script>
</body>
</html>