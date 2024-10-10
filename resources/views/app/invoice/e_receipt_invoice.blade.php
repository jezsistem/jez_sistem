<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<style type="text/css">
    @import url(https://fonts.googleapis.com/css?family=Open+Sans:400,700);

    body {
        margin: 0;
        padding: 0;
        background: #e1e1e1;
    }

    div, p, a, li, td {
        -webkit-text-size-adjust: none;
    }

    .ReadMsgBody {
        width: 100%;
        background-color: #ffffff;
    }

    .ExternalClass {
        width: 100%;
        background-color: #ffffff;
    }

    body {
        width: 100%;
        height: 100%;
        background-color: #e1e1e1;
        margin: 0;
        padding: 0;
        -webkit-font-smoothing: antialiased;
    }

    html {
        width: 100%;
    }

    p {
        padding: 0 !important;
        margin-top: 0 !important;
        margin-right: 0 !important;
        margin-bottom: 0 !important;
        margin-left: 0 !important;
    }

    .visibleMobile {
        display: none;
    }

    .hiddenMobile {
        display: block;
    }

    @media only screen and (max-width: 600px) {
        body {
            width: auto !important;
        }

        table[class=fullTable] {
            width: 96% !important;
            clear: both;
        }

        table[class=fullPadding] {
            width: 85% !important;
            clear: both;
        }

        table[class=col] {
            width: 45% !important;
        }

        .erase {
            display: none;
        }
    }

    @media only screen and (max-width: 420px) {
        table[class=fullTable] {
            width: 100% !important;
            clear: both;
        }

        table[class=fullPadding] {
            width: 85% !important;
            clear: both;
        }

        table[class=col] {
            width: 100% !important;
            clear: both;
        }

        table[class=col] td {
            text-align: left !important;
        }

        .erase {
            display: none;
            font-size: 0;
            max-height: 0;
            line-height: 0;
            padding: 0;
        }

        .visibleMobile {
            display: block !important;
        }

        .hiddenMobile {
            display: none !important;
        }
    }
</style>


@if (!empty($data['invoice_data']))
    @foreach ($data['invoice_data'] as $row)
        <!-- Header -->
        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable"
               bgcolor="#e1e1e1">
            <tr>
                <td height="20"></td>
            </tr>
            <tr>
                <td>
                    <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable"
                           bgcolor="#ffffff" style="border-radius: 10px 10px 0 0;">
                        <tr class="hiddenMobile">
                            <td height="40"></td>
                        </tr>
                        <tr class="visibleMobile">
                            <td height="30"></td>
                        </tr>

                        <tr>
                            <td>
                                <table width="480" border="0" cellpadding="0" cellspacing="0" align="center"
                                       class="fullPadding">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <table width="220" border="0" cellpadding="0" cellspacing="0" align="left"
                                                   class="col">
                                                <tbody>
                                                <tr>
                                                    <td align="left"><img src="{{ asset('logo/LOGOJEZ.png') }}"
                                                                          width="70" height="24" alt="logo"
                                                                          border="0"/></td>
                                                </tr>

                                                <tr class="hiddenMobile">
                                                    <td height="40"></td>
                                                </tr>
                                                <tr class="visibleMobile">
                                                    <td height="20"></td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size: 12px; color: #5b5b5b; font-family: 'Open Sans', sans-serif; line-height: 18px; vertical-align: top; text-align: left;">
                                                        Hello, {{ $row->cust_name }}.
                                                        <br> Thank you for your order.
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <table width="220" border="0" cellpadding="0" cellspacing="0" align="right"
                                                   class="col">
                                                <tbody>
                                                <tr class="visibleMobile">
                                                    <td height="20"></td>
                                                </tr>
                                                <tr>
                                                    <td height="5"></td>
                                                </tr>
                                                <tr>
                                                <tr class="hiddenMobile">
                                                    <td height="50"></td>
                                                </tr>
                                                <tr class="visibleMobile">
                                                    <td height="20"></td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size: 12px; font-weight: bold; color: #5b5b5b; font-family: 'Open Sans', sans-serif; line-height: 18px; vertical-align: top; text-align: right;">
                                                        <small>ORDER INVOICE</small> #{{ $data['invoice'] }}<br/>
                                                        <small>Date: {{ \Carbon\Carbon::parse($row->pos_created)->translatedFormat('d F Y') }}
                                                            <br/></small>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!-- /Header -->
        <!-- Order Details -->
        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable"
               bgcolor="#e1e1e1">
            <tbody>
            <tr>
                <td>
                    <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable"
                           bgcolor="#ffffff">
                        <tbody>
                        <tr>
                        <tr class="hiddenMobile">
                            <td height="60"></td>
                        </tr>
                        <tr class="visibleMobile">
                            <td height="40"></td>
                        </tr>
                        <tr>
                            <td>
                                <table width="480" border="0" cellpadding="0" cellspacing="0" align="center"
                                       class="fullPadding">
                                    <thead>
                                    <tr>
                                        <th style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #1e2b33; font-weight: bold; line-height: 1; vertical-align: top; padding: 0 10px 7px 0;"
                                            width="52%" align="left">
                                            Item
                                        </th>
                                        <th style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #1e2b33; font-weight: bold; line-height: 1; vertical-align: top; padding: 0 0 7px;"
                                            align="left">
                                            SKU
                                        </th>
                                        <th style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #1e2b33; font-weight: bold; line-height: 1; vertical-align: top; padding: 0 0 7px;"
                                            align="center">
                                            Quantity
                                        </th>
                                        <th style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #1e2b33; font-weight: bold; line-height: 1; vertical-align: top; padding: 0 0 7px;"
                                            align="right">
                                            Subtotal
                                        </th>
                                    </tr>
                                    <tr>
                                        <td height="1" colspan="4" style="border-bottom:1px solid #e4e4e4"></td>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @php
                                        $groups = array();
                                        $total_item = 0;
                                        $total_price = 0;
                                        $nameset = 0;
                                        $total_discount = 0;
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
                                            $total_item += $srow->pos_td_qty;
                                            $total_price += $srow->pos_td_discount_price;
                                            $nameset += $srow->pos_td_nameset_price;
                                            $total_discount += $srow->pos_td_discount_number;
                                        @endphp
                                        <tr>
                                            <td style="font-size: 12px; font-weight: bold; font-family: 'Open Sans', sans-serif; color: #ff0000;  line-height: 18px;  vertical-align: top; padding:10px 0; max-width: 250px; word-wrap: break-word; white-space: normal;" class="article">
                                            {{ $key }}
                                            </td>
                                            <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #646a6e;  line-height: 18px;  vertical-align: top; padding:10px 0;">
                                                <small>{{ $srow->ps_barcode }}</small></td>
                                            <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #646a6e;  line-height: 18px;  vertical-align: top; padding:10px 0;"
                                                align="center">{{ $srow->pos_td_qty }}
                                            </td>
                                            <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #1e2b33;  line-height: 18px;  vertical-align: top; padding:10px 0;"
                                                align="right">
                                                Rp. {{ \App\Libraries\CurrencyFormatter::formatToIDR($srow->pos_td_sell_price) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="1" colspan="4" style="border-bottom:1px solid #e4e4e4"></td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td height="1" colspan="4" style="border-bottom:1px solid #e4e4e4"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td height="20"></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
        <!-- /Order Details -->
        <!-- Total -->
        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable"
               bgcolor="#e1e1e1">
            <tbody>
            <tr>
                <td>
                    <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable"
                           bgcolor="#ffffff">
                        <tbody>
                        <tr>
                            <td>

                                <!-- Table Total -->
                                <table width="480" border="0" cellpadding="0" cellspacing="0" align="center"
                                       class="fullPadding">
                                    <tbody>
                                    <tr>
                                        <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #646a6e; line-height: 22px; vertical-align: top; text-align:right; ">
                                            Subtotal
                                        </td>
                                        <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #646a6e; line-height: 22px; vertical-align: top; text-align:right; white-space:nowrap;"
                                            width="80">
                                            Rp. {{ \App\Libraries\CurrencyFormatter::formatToIDR($total_price) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #646a6e; line-height: 22px; vertical-align: top; text-align:right; ">
                                            Total Discount
                                        </td>
                                        <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #646a6e; line-height: 22px; vertical-align: top; text-align:right; ">
                                            @if (!empty($total_discount))
                                                Rp. {{ number_format($total_discount) }}
                                            @else
                                                Rp. 0
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                            <strong>Grand Total (Incl.Tax)</strong>
                                        </td>
                                        <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                            <strong>
                                                @if (!empty($total_discount))
                                                    Rp. {{ \App\Libraries\CurrencyFormatter::formatToIDR((($total_price+$nameset) - ($total_discount)) - $total_voucher) }}
                                                @else
                                                    Rp. {{ \App\Libraries\CurrencyFormatter::formatToIDR(($total_price+$nameset+($total_price+$nameset)/100*$row->pos_cc_charge+$row->pos_another_cost) - $total_voucher) }}
                                                @endif
                                            </strong>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <!-- /Table Total -->

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
        <!-- /Total -->
        <!-- Information -->
        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable"
               bgcolor="#e1e1e1">
            <tbody>
            <tr>
                <td>
                    <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable"
                           bgcolor="#ffffff">
                        <tbody>
                        <tr>
                        <tr class="hiddenMobile">
                            <td height="60"></td>
                        </tr>
                        <tr class="visibleMobile">
                            <td height="40"></td>
                        </tr>
                        <tr>
                            <td>
                                <table width="480" border="0" cellpadding="0" cellspacing="0" align="center"
                                       class="fullPadding">
                                    <tbody>
                                    <tr>
                                        <td style="text-align: center; padding: 0;">
                                            <!-- Center align the image and remove padding -->
                                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2386.6536865716757!2d112.61772398730268!3d-7.94519045989204!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e788275925cfd71%3A0xd3d14abb78f91b40!2sJerseyzone%20ID%20-%20Toko%20Jersey%20Olahraga%20Terlengkap%20-%20Bola%2C%20Futsal%2C%20Club%20%26%20Negara%20-%20Ortuseight%20Mills%20Specs!5e0!3m2!1sid!2sid!4v1728457417705!5m2!1sid!2sid"
                                                    width="450" height="300" style="border:0;"
                                                    referrerpolicy="no-referrer-when-downgrade"></iframe>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="480" border="0" cellpadding="0" cellspacing="0" align="center"
                                       class="fullPadding">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <table width="220" border="0" cellpadding="0" cellspacing="0" align="left"
                                                   class="col">
                                                <tbody>
                                                <tr class="hiddenMobile">
                                                    <td height="35"></td>
                                                </tr>
                                                <tr class="visibleMobile">
                                                    <td height="20"></td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size: 11px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 1; vertical-align: top;">
                                                        <strong>PAYMENT INFORMATION</strong>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="100%" height="10"></td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 20px; vertical-align: top;">
                                                        Payment Method: Bank Transfer<br>
                                                        Transaction ID: 123456789<br>
                                                        Date: October 8, 2024
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>


                                            <table width="220" border="0" cellpadding="0" cellspacing="0" align="right"
                                                   class="col">
                                                <tbody>
                                                <tr class="hiddenMobile">
                                                    <td height="35"></td>
                                                </tr>
                                                <tr class="visibleMobile">
                                                    <td height="20"></td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size: 11px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 1; vertical-align: top;">
                                                        <strong>CONTACT US</strong>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="100%" height="10"></td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 20px; vertical-align: top;">
                                                        Phone: 0813-3345-8788<br>
                                                        Instagram: @jerseyzoneid <br>
                                                        Jl. Soekarno Hatta No.30 Kav. 10 Malang
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr class="hiddenMobile">
                            <td height="60"></td>
                        </tr>
                        <tr class="visibleMobile">
                            <td height="30"></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
        <!-- /Information -->
        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable"
               bgcolor="#e1e1e1">

            <tr>
                <td>
                    <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable"
                           bgcolor="#ffffff" style="border-radius: 0 0 10px 10px;">
                        <tr>
                            <td>
                                <table width="480" border="0" cellpadding="0" cellspacing="0" align="center"
                                       class="fullPadding">
                                    <tbody>
                                    <tr>
                                        <td style="font-size: 12px; font-weight: bold; color: #5b5b5b; font-family: 'Open Sans', sans-serif; line-height: 18px; vertical-align: top; text-align: left;">
                                            Terimakasih telah belanja di store kami! <br> Refund WAJIB dengan perjanjian
                                            di
                                            awal, berlaku 1x24 Jam
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr class="spacer">
                            <td height="50"></td>
                        </tr>

                    </table>
                </td>
            </tr>
            <tr>
                <td height="20"></td>
            </tr>
        </table>
    @endforeach
@endif
</body>
</html>