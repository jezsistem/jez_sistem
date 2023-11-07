<!DOCTYPE html>
<head>
    <meta charset="utf-8" />
    <title>{{ $data['title'] }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Topsystem" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Global Theme Styles(used by all pages)-->
    <link href="{{ asset('app') }}/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Global Theme Styles-->
    <link rel="shortcut icon" href="{{ asset('logo') }}/fav.png" />
</head>
<body>
@if (!empty($data['invoice_data']))
@foreach ($data['invoice_data'] as $row)
<!--end::Head-->
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="card card-custom">
                <div class="card-body p-0" id="printElement">
                    <!--begin::Invoice-->
                    <!--begin::Invoice header-->
                    <div class="container">
                        <div class="card card-custom card-shadowless">
                            <div class="card-body p-0">
                                <div class="row justify-content-center py-8 px-8 py-md-27 px-md-0">
                                    <div class="col-md-9">
                                        <div class="d-flex justify-content-between align-items-center flex-column flex-md-row">
                                            <div class="d-flex flex-column px-0 order-2 order-md-1 align-items-center align-items-md-start mr-2">
                                                <!--begin::Logo-->
                                                <a href="#" class="mb-5 max-w-160px">
                                                    <img style="width:100%;" alt="Logo" src="{{ asset('upload') }}/logo/logo-black.png" />
                                                </a>
                                                <!--end::Logo-->
                                                <span class="d-flex flex-column font-size-h5 font-weight-bold text-muted align-items-center align-items-md-start">
                                                    <span>{{ $row->st_phone }}<br/>{{ $row->st_address }}</span>
                                                </span>
                                            </div>
                                            <h3 class="display-4 font-weight-boldest order-1 order-md-2 mb-md-0 ml-2">{{ $row->pos_invoice }}<br/><small style="font-size:15px; float:right;">{{ date('d-m-Y H:i:s', strtotime($row->pos_created)) }}</small>
                                            <br/><small style="font-size:15px; float:right;">Tipe <strong>{{ $row->dv_name }}</strong></small></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Invoice header-->
                    <!--begin::Invoice Footer-->
                    <div class="container pb-30">
                        <div class="row justify-content-center">
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="d-flex flex-column flex-md-row">
                                            <div class="d-flex flex-column">
                                                <div class="font-weight-bold font-size-h6 mb-3">METODE <span class="float-right"><strong>{{ $row->pm_name }}</strong></span></div>
                                                @if ($row->pm_name == 'Debit')
                                                <div class="d-flex justify-content-between font-size-lg mb-3">
                                                    <td><span class="font-weight-bold mr-15">No. Kartu</span></td>
                                                    <td><span class="text-right">{{ $row->pos_card_number }}</span></td>
                                                </div>
                                                @endif
                                                <div class="d-flex justify-content-between font-size-lg">
                                                    <td><span class="font-weight-bold mr-15">No. Ref</span></td>
                                                    <td><span class="text-right">{{ $row->pos_ref_number }}</span></td>
                                                </div>
                                                <div class="d-flex justify-content-between font-size-lg">
                                                    <td><span class="font-weight-bold mr-15">Kasir</span></td>
                                                    <td><span class="text-right">{{ $row->u_name }}</span></td>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-7 mt-md-0">
                                        <!--begin::Invoice To-->
                                        <div class="text-dark-50 font-size-lg font-weight-bold mb-3 text-right">CUSTOMER</div>
                                        <div class="font-size-lg font-weight-bold mb-10 text-right">{{ $data['customer']->cust_name }}
                                        <br />{{ $data['customer']->cust_phone }}<br/>{{ $data['customer']->cust_address }}, {{ $data['cust_subdistrict'] }}, {{ $data['cust_city'] }}, {{ $data['cust_province'] }}
                                        <br/><br/> <div id="qrcodeprint"><div style="float:right;" id="qrcode"></div></div></div>
                                        <!--end::Invoice To-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Invoice Footer-->
                    <!--begin::Invoice Body-->
                    <div class="position-relative">
                        <!--begin::Background Rows-->
                        <div class="bgi-size-cover bgi-position-center bgi-no-repeat h-65px" style="background: rgb(0,0,0);
                        background: linear-gradient(172deg, rgba(0,0,0,1) 33%, rgba(148,0,0,1) 75%, rgba(147,0,0,1) 100%);"></div>
                        <!--end::Background Rows-->
                        <!--begin:Table-->
                        <div class="container position-absolute top-0 left-0 right-0">
                            <div class="row justify-content-center">
                                <div class="col-md-9">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr class="font-weight-boldest text-white h-65px">
                                                    <td class="align-middle font-size-h4 pl-0 border-0">PRODUK</td>
                                                    <td class="align-middle font-size-h4 text-right border-0">QTY</td>
                                                    <td class="align-middle font-size-h4 text-right border-0">PRICE</td>
                                                    <td class="align-middle font-size-h4 text-right border-0">DISC</td>
                                                    <td class="align-middle font-size-h4 text-right pr-0 border-0">TOTAL</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $total_price = 0; $nameset = 0; $subtotal = 0; $total_discount = 0; $total_marketplace = 0; @endphp
                                                @foreach ($row->subitem as $crow)
                                                <tr class="font-size-lg font-weight-bolder h-65px">
                                                    <td class="align-middle pl-0 border-0">{{ strtoupper($crow->p_name) }} {{ strtoupper($crow->p_color) }} ({{ $crow->sz_name }})</td>
                                                    <td class="align-middle text-right border-0">{{ $crow->pos_td_qty }}</td>
                                                    @if ($row->dv_name != 'DROPSHIPPER' AND $row->dv_name != 'RESELLER' AND $row->dv_name != 'WHATSAPP' AND $row->dv_name != 'WEBSITE')
                                                    <td class="align-middle text-right border-0">{{ number_format(round($crow->pos_td_marketplace_price/$crow->pos_td_qty)) }}</td>
                                                    <td class="align-middle text-right border-0">{{ number_format($crow->pos_td_discount) }}%</td>
                                                    <td class="align-middle text-right text-danger font-weight-boldest font-size-h5 pr-0 border-0">{{ number_format($crow->pos_td_marketplace_price) }}</td>
                                                    @else 
                                                    @if ($row->is_website == '1')
                                                    <td class="align-middle text-right border-0">{{ number_format($crow->pos_td_discount_price) }}</td>
                                                    <td class="align-middle text-right border-0">-</td>
                                                    <td class="align-middle text-right text-danger font-weight-boldest font-size-h5 pr-0 border-0">{{ number_format($crow->pos_td_qty * $crow->pos_td_discount_price) }}</td>
                                                    @else
                                                    <td class="align-middle text-right border-0">{{ number_format($crow->pos_td_sell_price) }}</td>
                                                    <td class="align-middle text-right border-0">{{ number_format($crow->pos_td_discount) }}%</td>
                                                    <td class="align-middle text-right text-danger font-weight-boldest font-size-h5 pr-0 border-0">{{ number_format($crow->pos_td_qty * ($crow->pos_td_sell_price - ($crow->pos_td_sell_price/100 * $crow->pos_td_discount))) }}</td>
                                                    @endif
                                                    @endif
                                                </tr>
                                                @php 
                                                if ($row->is_website == '1') {
                                                    $subtotal += ($crow->pos_td_qty * $crow->pos_td_discount_price); 
                                                    $total_price += $crow->pos_td_total_price; 
                                                } else {
                                                    $nameset += $crow->pos_td_nameset_price; 
                                                    $subtotal += ($crow->pos_td_qty * $crow->pos_td_sell_price); 
                                                    $total_price += $crow->pos_td_total_price; 
                                                    $total_discount += $crow->pos_td_qty * ($crow->pos_td_sell_price/100 * $crow->pos_td_discount); 
                                                    $total_marketplace += $crow->pos_td_marketplace_price; 
                                                }
                                                @endphp
                                                @endforeach
                                                <tr class="font-size-lg font-weight-bolder h-65px">
                                                    @if ($row->is_website == '1')
                                                    <td class="align-middle pl-0 border-0" colspan="4">ONGKOS KIRIM | {{ $row->pos_courier }}</td>
                                                    @else
                                                    <td class="align-middle pl-0 border-0" colspan="4">ONGKOS KIRIM ({{ $row->cr_name }})</td>
                                                    @endif
                                                    <td class="align-middle text-right font-weight-boldest font-size-h5 pr-0 border-0">{{ number_format($row->pos_shipping) }}</td>
                                                </tr>
                                                @if ($row->is_website == '1')
                                                <tr class="font-size-lg font-weight-bolder h-65px">
                                                    <td class="align-middle pl-0 border-0" colspan="4">Kode Unik</td>
                                                    <td class="align-middle text-right font-weight-boldest font-size-h5 pr-0 border-0">{{ number_format($row->pos_unique_code) }}</td>
                                                </tr>
                                                @else 
                                                <tr class="font-size-lg font-weight-bolder h-65px">
                                                    <td class="align-middle pl-0 border-0" colspan="4">DISKON</td>
                                                    @if ($row->dv_name != 'DROPSHIPPER' AND $row->dv_name != 'RESELLER' AND $row->dv_name != 'WHATSAPP' AND $row->dv_name != 'WEBSITE')
                                                    <td class="align-middle text-right font-weight-boldest font-size-h5 pr-0 border-0">0</td>
                                                    @else 
                                                    <td class="align-middle text-right font-weight-boldest font-size-h5 pr-0 border-0">(-{{ (number_format($total_discount)) }})</td>
                                                    @endif 
                                                </tr>
                                                @endif
                                                <tr class="font-size-lg font-weight-bolder h-65px">
                                                    <td class="align-middle pl-0 border-0" colspan="4">NAMESET</td>
                                                    <td class="align-middle text-right font-weight-boldest font-size-h5 pr-0 border-0">{{ number_format($nameset) }}</td>
                                                </tr>
                                                <tr class="font-size-lg font-weight-bolder h-65px">
                                                    <td class="align-middle pl-0 border-0" colspan="4">BIAYA LAIN</td>
                                                    <td class="align-middle text-right font-weight-boldest font-size-h5 pr-0 border-0">{{ number_format($row->pos_another_cost) }}</td>
                                                </tr>
                                                <tr class="font-size-lg font-weight-bolder h-65px">
                                                    <td class="align-middle pl-0 border-0" colspan="4">TOTAL</td>
                                                    <td class="align-middle text-right font-weight-boldest font-size-h5 pr-0 border-0">
                                                    @if ($row->dv_name != 'DROPSHIPPER' AND $row->dv_name != 'RESELLER' AND $row->dv_name != 'WHATSAPP' AND $row->dv_name != 'WEBSITE')
                                                    <span class="font-weight-boldest font-size-h3 line-height-sm">Rp. {{ number_format($total_marketplace+$nameset+$row->pos_another_cost) }}</span>
                                                    @else 
                                                    @if ($row->is_website == '1')
                                                    <span class="font-weight-boldest font-size-h3 line-height-sm">Rp. {{ number_format($subtotal-$total_discount+$row->pos_shipping+$nameset+$row->pos_unique_code) }}</span>
                                                    @else
                                                    <span class="font-weight-boldest font-size-h3 line-height-sm">Rp. {{ number_format($subtotal-$total_discount+$row->pos_shipping+$nameset+$row->pos_another_cost) }}</span>
                                                    @endif
                                                    @endif</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end:Table-->
                    </div>
                    <!--end::Invoice Body-->
                    <!--end::Invoice-->
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->
@endforeach
@endif
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js" ></script>
<script>
    function generateQR()
    {
        var value = "{{ $data['invoice'] }}";
        $('#qrcode').empty();
        $("#barcodeTarget").hide();
        $('#qrcode').css({
            'width' : 128,
            'height' : 128
        })
        $('#qrcode').qrcode({width: 128,height: 128,text: value});
    }

    function printInv()
    {
        var mywindow = window.open('', 'PRINT', 'height=400,width=600');
        mywindow.document.write('<html><head><title>' + document.title  + '</title>');
        mywindow.document.write('</head><body >');
        mywindow.document.write(document.getElementById('qrcodeprint').innerHTML);
        mywindow.document.write('</body></html>');
        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/
        mywindow.print();
        mywindow.close();

        return true;
    }

    $(document).ready(function() {
        generateQR();
    });
</script>
</body>
	<!--end::Body-->
</html>