<!DOCTYPE html>
<html>

<head>
</head>
<style>
    * {
        font-family: Arial, sans-serif;
        font-size: 8pt;
    }

    /* Styles go here */

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
        /* for demo */
        background: yellow;
        /* for demo */
    }

    .page-header {
        position: fixed;
        top: 0mm;
        width: 100%;
        /* for demo */
        background: yellow;
        /* for demo */
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
                    <!--place holder for the fixed-position header-->
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

                        if ($totalItems <= 15) {
                            // All items fit on one page
                            $chunks[] = $items;
                        } else {
                            // First page: 15 items
                            $chunks[] = array_slice($items, 0, 15);
                            $currentIndex = 15;

                            // Middle pages: 24 items each
                            while ($currentIndex < $totalItems) {
                                $remaining = $totalItems - $currentIndex;

                                if ($remaining <= 15) {
                                    // Last page
                                    $chunks[] = array_slice($items, $currentIndex);
                                    break;
                                } elseif ($remaining <= 39) {
                                    // If remaining is 16-39, split into two pages (24 + rest)
                                    $chunks[] = array_slice($items, $currentIndex, 24);
                                    $chunks[] = array_slice($items, $currentIndex + 24);
                                    break;
                                } else {
                                    // Add full page of 24
                                    $chunks[] = array_slice($items, $currentIndex, 24);
                                    $currentIndex += 24;
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
                                    <h2 style="font-weight: 100; margin-top:0px">{{ $manifest_number }}
                                        <h2>
                                </center>
                                <h3 style="font-weight: 100; margin-top:1cm">Tanggal Manifest: {{ $manifest_date }}
                                </h3>
                            @endif

                            @if ($pageIndex === 0)
                                <table style="width: 100%; margin-top: 0.5cm; font-size:8pt">
                                    <tr>
                                        <td style="width: 50%; vertical-align: top;">
                                            <strong>Informasi Ekspedisi</strong><br>
                                            <table style="width: 100%; border-collapse: collapse; margin-top: 2px;">
                                                <tr>
                                                    <td style="padding: 1px 0; max-width: 2cm;">Nama Ekspedisi</td>
                                                    <td style="padding: 1px 10px;">:</td>
                                                    <td style="padding: 1px 0;">{{ $expedition_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 1px 0;max-width: 2cm">Nama Kurir / Petugas
                                                        Pickup</td>
                                                    <td style="padding: 1px 10px;">:</td>
                                                    <td style="padding: 1px 0;">{{ $courier_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 1px 0;max-width: 2cm">Nomor Kontak Kurir</td>
                                                    <td style="padding: 1px 10px;">:</td>
                                                    <td style="padding: 1px 0;">{{ $courier_phone }}</td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td style="width: 50%; vertical-align: top;">
                                            <strong>Informasi Seller</strong><br>
                                            <table style="width: 100%; border-collapse: collapse; margin-top: 2px;">
                                                <tr>
                                                    <td style="padding: 1px 0;">Alamat Pickup</td>
                                                    <td style="padding: 1px 10px;">:</td>
                                                    <td style="padding: 1px 0;">{{ $pickup_address }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 1px 0;">Nama Toko / Seller</td>
                                                    <td style="padding: 1px 10px;">:</td>
                                                    <td style="padding: 1px 0;">{{ $store_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 1px 0;">Nama PIC Seller</td>
                                                    <td style="padding: 1px 10px;">:</td>
                                                    <td style="padding: 1px 0;">{{ $pic_seller }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 1px 0;">Nomor HP PIC</td>
                                                    <td style="padding: 1px 10px;">:</td>
                                                    <td style="padding: 1px 0;">{{ $pic_phone }}</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <table style="width: 100%; border-collapse: collapse; font-size: 8pt; margin-top: 0.5cm;">
                                <thead>
                                    <tr style="background-color: #f0f0f0;">
                                        <th
                                            style="border: 1px solid #000; padding: 6px; text-align: center; width: 5%;">
                                            No.</th>
                                        <th
                                            style="border: 1px solid #000; padding: 6px; text-align: center; width: 15%;">
                                            No. Resi</th>
                                        <th
                                            style="border: 1px solid #000; padding: 6px; text-align: center; width: 20%;">
                                            Marketplace</th>
                                        <th
                                            style="border: 1px solid #000; padding: 6px; text-align: center; width: 15%;">
                                            Jumlah Item (Qty)</th>
                                        <th
                                            style="border: 1px solid #000; padding: 6px; text-align: center; width: 25%;">
                                            Kota Tujuan</th>
                                        <th
                                            style="border: 1px solid #000; padding: 6px; text-align: center; width: 20%;">
                                            Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $startNumber =
                                            array_sum(array_map('count', array_slice($chunks, 0, $pageIndex))) + 1;
                                    @endphp
                                    @foreach ($pageItems as $index => $item)
                                        <tr>
                                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                                                {{ $startNumber + $index }}</td>
                                            <td style="border: 1px solid #000; padding: 6px;">{{ $item['resi'] }}</td>
                                            <td style="border: 1px solid #000; padding: 6px;">
                                                {{ $item['marketplace'] }}</td>
                                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                                                {{ $item['qty'] }}</td>
                                            <td style="border: 1px solid #000; padding: 6px;">{{ $item['city'] }}</td>
                                            <td style="border: 1px solid #000; padding: 6px;"></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            @if ($pageIndex === count($chunks) - 1)
                                <div style="margin-top: 1cm; font-size: 8pt;">
                                    <p>Note:</p>
                                    <ol>
                                        <li>Pastikan seluruh paket telah sesuai dengan daftar manifest di atas sebelum diserahkan.</li>
                                        <li>Nomor manifest ini menjadi bukti sah serah terima antara pihak Seller dan Ekspedisi</li>
                                        <li>Apabila terdapat ketidaksesuaian jumlah atau kerusakan barang, harap dicatat dalam kolom keterangan dan dikonfirmasi segera.</li>
                                    </ol>
                                    <table style="width: 100%; font-size: 8pt; margin-top: 1cm;">
                                        <tr>
                                            <td style="width: 50%; text-align: center; vertical-align: bottom;">
                                                <div>
                                                   <h3>Diserahkan oleh</h3>
                                                </div>
                                                <div
                                                    style="margin-top: 2cm; display: inline-block; padding-top: 5px; min-width: 150px;">
                                                    <i style="color: gainsboro">Tanda Tangan</i><br>
                                                   <h3>Seller / PIC Toko</h3>
                                                </div>
                                            </td>
                                            <td style="width: 50%; text-align: center; vertical-align: bottom;">
                                                <div>
                                                   <h3>Diterima oleh</h3>
                                                </div>
                                                <div
                                                    style="margin-top: 2cm; display: inline-block; padding-top: 5px; min-width: 150px;">
                                                    <i style="color: gainsboro">Tanda Tangan</i><br>
                                                   <h3>Kurir Pickup / Ekspedisi</h3>
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
        {{-- <tfoot>
            <tr>
                <td>
                    <!--place holder for the fixed-position footer-->
                    <div class="page-footer-space"></div>
                </td>
            </tr>
        </tfoot> --}}

    </table>

</body>

</html>
