<!DOCTYPE html>
<html>
<head>
    <title>Jersey Zone - Invoice Print</title>
    <style type="text/css">
        html {
            font-family: "Verdana";
        }

        .content {
            text-align: center;
            width: 80mm;
            margin-left: 1mm;
            font-size: 15px;
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
            font-size: 12px;
        }

        .content .transaction-table .name {
            width: 100px;
            height: 85px;
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
            font-size: 15px;
        }

        @media print {
            @page {
                width: 80mm;
                margin: 2mm;
            }
        }

        .page-end {
            page-break-after: always;
        }
    </style>
</head>
<body>
@if (!empty($data['invoice_data']))
    @foreach ($data['invoice_data'] as $row)
<center class="content">
    <center>
        <img class="rounded reload" data-pt_id="{{ $row->pt_id }}"
             src="{{ asset('logo/logo_jez_sport.png') }}"
             style="width:43%; padding:10px; background-color:#000;"/>
        <div class="title" style="margin-top: 20px;">
            <strong>Sneakerzone.id</strong><br/>
            Malang<br/>
            <br/><br/>
            www.jez.co.id
        </div>
        <div class="separate"></div>

        <div class="nota" style="margin-top: 10px; margin-bottom: 10px;">
            {{ $data['invoice'] }}
        </div>
        <div class="head-desc">
            <div class="date">
                {{ now()->format('d F Y') }}<br/>
                Kasir<br/>
                Customer<br/>
                Pembayaran
            </div>
            <div class="user">
                {{ now()->format('H:i') }}<br/>
                @php
                    $kasir = \Illuminate\Support\Facades\Auth::user()->u_name;
                    $kasirLimited = substr($kasir, 0, 15);

                    echo $kasirLimited;
                @endphp<br>
                Jez Customer<br>
                {{ $row->payment_method }}
            </div>
        </div>
        <div class="separate"></div>
        <div class="transaction">
            <table class="transaction-table" cellspacing="0" cellpadding="0">
                @php
                    $groups = array();
                    $total_item = 0;
                    $total_price = 0;
                    $nameset = 0;
                    $total_potongan = $data['invoice_data'][0]['$total_discount'];
                    $total_voucher = $data['invoice_data'][0]['pos_total_vouchers'];
                    foreach ($row->subitem as $srow) {
                        $key = ' '.$srow->p_name.' '.$srow->p_color.' '.$srow->sz_name;
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
                        $key = ' '.$srow->p_name.' '.$srow->p_color.'  @'.$srow->sz_name;
                        $total_item += $srow->qty;
                        $total_price += $srow->original_price - $srow->discount_seller;
                        $nameset += $srow->pos_td_nameset_price;
                        $total_potongan += $srow->total_discount;
                    @endphp

                    <tr style="margin-bottom:15px;">
                        <td class="name">{{ $key }}<br></td>
                        @if (!empty($srow->pos_td_description) AND $srow->qty > 1)
                            <td class="qty" style="width: 1px;">{{ $srow->qty }}</td>
                        @else
                            <td class="qty">{{ $srow->qty }}x</td>

                            <td class="sell-price">
                                @if(!empty($srow->total_discount) || $srow->discount_seller != 0)
                                    <s>{{ \App\Libraries\CurrencyFormatter::formatToIDR($srow->ps_price_tag) }}</s>
                                    <br>
                                @endif

                                @if(!empty($srow->total_discount))
                                    <span>(-{{ \App\Libraries\CurrencyFormatter::formatToIDR($srow->total_discount) }})</span>
                                @endif

{{--                                @if (!empty($srow->total_discount))--}}
{{--                                    <br/>{{ $srow->total_discount }}--}}
{{--                                @endif--}}
                            </td>

                        @endif
                        <td class="final-price">
                            {{ \App\Libraries\CurrencyFormatter::formatToIDR($srow->price_after_discount) }}
                        </td>


                    </tr>
                @endforeach
                <!-- Repeat rows as needed -->
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
                        <span style="float:right;">{{ $total_item }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="final-price">
                        <span style="float:left;">DISKON</span>
                    </td>
                    <td class="final-price">
                        <span style="float:right;">
                             @if (!empty($total_potongan))
                                {{ number_format($total_potongan) }}
                            @else
                                0
                            @endif
                        </span>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="final-price">
                        <span style="float:left; font-weight:bold;">TOTAL AKHIR</span>
                    </td>
                    <td class="final-price">
                        <span style="float:right;">{{ \App\Libraries\CurrencyFormatter::formatToIDR($total_price) }}</span>
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
            <strong><i>Cust Experience :</i></strong>
            <br/>
            <img class="" data-pt_id="{{ $row->pt_id }}"
                 src="{{ asset('logo/qr-prd.png') }}"
                 style="width:43%; background-color:#000;"/>
        </div>
        <br>
        <div class="title">
            <strong><i>Ketentuan penukaran barang :</i></strong>
            <br/>
            <p style="text-align:left; font-size:11px;">
                1. Batas waktu penukaran barang maksimal 1x24 jam dari barang diterima dan penukaran wajib datang ke Offline Store (Barang yang dapat ditukar hanya produk sepatu)<br/>
                2. Penukaran barang tidak berlaku untuk produk jersey, t-shirt, assesoris maupun equipment<br/>
                3. Produk sepatu yang dapat ditukar yaitu belum pernah digunakan untuk beraktifitas dan wajib memiliki dus yang sesuai dengan barang dan tag wajib masih terpasang, jika tidak maka penukaran tidak akan dilayani<br/>
                4. Barang hanya boleh ditukar dengan jenis, warna, dan tipe yang sama dengan ketentuan pengembalian harus datang ke store dengan membawa barang lengkap<br/>
                5. Wajib menyertakan struk pembelanjaan saat proses penukaran barang baik pembelian offline & online <br/>
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
