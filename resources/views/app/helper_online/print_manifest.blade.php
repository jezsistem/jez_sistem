<!DOCTYPE html>
<html>

<head>
    <title>Delivery Manifest</title>
</head>

<style>
    * {
        font-family: Arial, sans-serif;
        font-size: 8pt;
    }

    .page-header,
    .page-header-space {
        height: 100px;
    }

    .page-footer,
    .page-footer-space {
        height: 100px;
    }

    .page-footer {
        position: fixed;
        bottom: 0;
        width: 100%;
    }

    .page-header {
        position: fixed;
        top: 0mm;
        width: 100%;
    }

    .page {
        page-break-after: always;
    }

    h2 {
        font-size: 12pt;
    }

    h3 {
        font-size: 10pt;
    }

    @media print {
        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        button {
            display: none;
        }

        body {
            margin: 0;
        }
    }
</style>

<body>

<div class="page-header" style="text-align: center">
    <img src="{{ asset('header_footer/header.png') }}" alt="" style="width: 100%;">
</div>

<div class="page-footer">
    <img src="{{ asset('header_footer/footer.png') }}" alt="" style="width: 100%;">
</div>

<table style="width: 100%">
    <thead>
    <tr>
        <td>
            <div class="page-header-space"></div>
        </td>
    </tr>
    </thead>

    <tbody style="margin-bottom: 4cm">
    <tr>
        <td>
            @php
                $totalItems = count($items);
                $chunks = [];
                $currentIndex = 0;

                if ($totalItems <= 38) {
                    $chunks[] = $items;
                } else {
                    $chunks[] = array_slice($items, 0, 38);
                    $currentIndex = 38;

                    while ($currentIndex < $totalItems) {
                        $remaining = $totalItems - $currentIndex;

                        if ($remaining <= 38) {
                            $chunks[] = array_slice($items, $currentIndex);
                            break;
                        } elseif ($remaining <= 50) {
                            $chunks[] = array_slice($items, $currentIndex, 45);
                            $chunks[] = array_slice($items, $currentIndex + 45);
                            break;
                        } else {
                            $chunks[] = array_slice($items, $currentIndex, 45);
                            $currentIndex += 45;
                        }
                    }
                }
            @endphp

            @foreach ($chunks as $pageIndex => $pageItems)
                <div class="page">
                    @if ($pageIndex === 0)
                        <center>
                            <u><strong>
                                    <h2 style="margin-bottom: 0px;">DELIVERY MANIFEST</h2>
                                </strong></u>
                            <h2 style="font-weight: 100; margin-top:0px">INV/AMP-MLG/JNT/20251029/001</h2>
                        </center>
                        <h3 style="font-weight: 100; margin-top:1cm">
                            Tanggal Manifest: {{ $recap_date }}
                        </h3>
                    @endif

                    @if ($pageIndex === 0)
                        <table style="width: 100%; margin-top: 0.5cm; font-size:8pt">
                            <tr>
                                <td style="width: 50%; vertical-align: top;">
                                    <strong>Informasi Ekspedisi</strong><br>
                                    <table style="width: 100%; border-collapse: collapse; margin-top: 2px;">
                                        <tr>
                                            <td>Nama Ekspedisi</td>
                                            <td>:</td>
                                            <td>{{ $expedition_name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Nama Kurir</td>
                                            <td>:</td>
                                            <td>{{ $courier_name }}</td>
                                        </tr>
                                        <tr>
                                            <td>No. Kontak Kurir</td>
                                            <td>:</td>
                                            <td>{{ $courier_phone }}</td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="width: 50%; vertical-align: top;">
                                    <strong>Informasi Seller</strong><br>
                                    <table style="width: 100%; border-collapse: collapse; margin-top: 2px;">
                                        <tr>
                                            <td>Alamat Pickup</td>
                                            <td>:</td>
                                            <td>{{ $pickup_address }}</td>
                                        </tr>
                                        <tr>
                                            <td>Nama Toko</td>
                                            <td>:</td>
                                            <td>{{ $store_name }}</td>
                                        </tr>
                                        <tr>
                                            <td>PIC Seller</td>
                                            <td>:</td>
                                            <td>{{ $pic_seller }}</td>
                                        </tr>
                                        <tr>
                                            <td>No. HP PIC</td>
                                            <td>:</td>
                                            <td>{{ $pic_phone }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    @endif

                    <table style="width: 100%; border-collapse: collapse; font-size: 8pt; margin-top: 0.5cm;">
                        <thead>
                        <tr style="background-color: #f0f0f0;">
                            <th style="border: 1px solid #000; padding: 6px; width: 5%;">No.</th>
                            <th style="border: 1px solid #000; padding: 6px; width: 15%;">No. Resi / No. Order</th>
                            <th style="border: 1px solid #000; padding: 6px; width: 20%;">Marketplace</th>
                            <th style="border: 1px solid #000; padding: 6px; width: 15%;">Qty</th>
                            <th style="border: 1px solid #000; padding: 6px; width: 25%;">Kota Tujuan</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $startNumber =
                                array_sum(array_map('count', array_slice($chunks, 0, $pageIndex))) + 1;
                        @endphp
                        @foreach ($pageItems as $index => $item)
                            <tr>
                                <td style="border: 1px solid #000; text-align: center;">
                                    {{ $startNumber + $index }}</td>
                                <td style="border: 1px solid #000;">{{ $item['resi'] }}</td>
                                <td style="border: 1px solid #000;">{{ $item['marketplace_name'] }}</td>
                                <td style="border: 1px solid #000; text-align: center;">
                                    {{ $item['item_qty'] }}</td>
                                <td style="border: 1px solid #000;">{{ $item['city_destinations'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                        @if ($pageIndex === count($chunks) - 1)
                            <div style="margin-top: 1cm; font-size: 8pt;">
                                <p><strong>Note Penjual:</strong></p>
                                {!! $note ?? '<em>Tidak ada catatan.</em>' !!}

                            </div>
                        @endif

                    @if ($pageIndex === count($chunks) - 1)
                        <div style="margin-top: 1cm; font-size: 8pt;">
                            <p><strong>Caution:</strong></p>
                            <ol>
                                <li>Pastikan seluruh paket telah sesuai dengan daftar manifest sebelum diserahkan.</li>
                                <li>Nomor manifest ini menjadi bukti sah serah terima antara pihak Seller dan Ekspedisi.</li>
                                <li>Jika terdapat ketidaksesuaian jumlah atau kerusakan barang, mohon dicatat di kolom keterangan.</li>
                            </ol>
                            <table style="width: 100%; font-size: 8pt; margin-top: 1cm;">
                                <tr>
                                    <td style="width: 50%; text-align: center;">
                                        <h3>Diserahkan oleh</h3>
                                        <div>
                                            @if(!empty($signature_pic_url))
                                                <img src="{{ $signature_pic_url }}" alt="TTD PIC" width="120" style="margin-bottom: 5px;">
                                            @else
                                                <i style="color: gainsboro">Tanda Tangan</i><br>
                                            @endif
                                            <h3>{{$pic_seller}}</h3>
                                        </div>
                                    </td>
                                    <td style="width: 50%; text-align: center;">
                                        <h3>Diterima oleh</h3>
                                        <div>
                                            @if(!empty($signature_courier_url))
                                                <img src="{{ $signature_courier_url }}" alt="TTD Kurir" width="120" style="margin-bottom: 5px;">
                                            @else
                                                <i style="color: gainsboro">Tanda Tangan</i><br>
                                            @endif
                                            <h3>{{ $courier_name }} ({{$expedition_name}})</h3>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    @endif
                </div>
            @endforeach
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>
