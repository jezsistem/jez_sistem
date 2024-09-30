{{-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman All Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body> --}}
@extends('app.structure')
@section('content')
    <div class="container mt-5 d-flex justify-content-center align-items-center flex-column">

        
        <div class="subheader py-2 py-lg-8 subheader-solid" id="kt_subheader" style="background: #ffeded">
            <div class="container-fluid justify-content-between">
                <!--begin::Toolbar-->
                <div class="d-flex align-items-center ml-auto">
                    <span id="realtime-date" class="me-4" style="font-weight: 500; color: #000000;"></span>
                    <!-- Realtime date -->
                    <a href="{{ url('dashboards') }}" class="btn btn-inventory me-2" style="margin-left: auto;">Dashboard
                        V2</a>
                    <a href="https://report.jez.co.id" target="_blank" class="btn btn-dark me-2"
                        style="font-weight:600; ">Report Dashboard</a>
                </div>
                <!--end::Toolbar-->
            </div>
        </div>


        <div class="mb-4 text-center">
            <img src="{{ asset('logo/allstock2.gif') }}" alt="All Stock" class="stock-logo">
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-borderless text-center">
                <thead class="table-header">
                    <tr>
                        <th>#</th>
                        <th>Status Tracking</th>
                        <th>Total Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stocks as $stock)
                        <tr>
                            <td>{{ $stock['name'] }}</td>
                            <td>{{ $stock['quantity'] }}</td>
                            <td>{{ $stock['price'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    {{-- </body> --}}

    {{-- </html> --}}

    <style>
        body {
            animation: backgroundChange 30s infinite;
        }

        @keyframes backgroundChange {
            33% {
                background-color: #fc8b8b;
            }

            66% {
                background-color: #ffeded;
            }

            100% {
                background-color: #ffffff;
            }
        }

        /* Button Styles */
        .btn-inventory {
            background-color: #fac6c5;
            color: rgb(0, 0, 0);
            border-radius: 30px;
            padding: 10px 20px;
            font-weight: bold;
        }

        .btn-inventory:hover {
            background-color: #FF5C58;
            color: white;
        }

        .btn-date-info {
            background-color: #FFDB89;
            color: #850000;
            border-radius: 30px;
            padding: 10px 20px;
        }

        .btn-dark {
            background-color: #FF5C58;
            color: #000000;
            border-radius: 30px;
            padding: 10px 20px;
        }

        .stock-logo {
            max-width: 700px;
        }

        .table-responsive {
            margin-top: 5px;
            padding: 20px;
            border-radius: 8px;
            overflow-y: auto;
            max-width: 80%;
            /* Ubah ini agar tabel dapat menyesuaikan lebar layar */
            white-space: nowrap;
            border-radius: 30px;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 12px;
            overflow: hidden;
        }

        .table-header {
            background-color: #FF5C58;
            color: white;
            font-weight: bold;
        }

        .table-header th {
            padding: 15px;
            text-transform: uppercase;
            font-weight: 700;
        }

        /* Warna Baris Tabel */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #ffeded;
        }

        .table-striped tbody tr:nth-of-type(even) {
            background-color: #fac6c5;
        }

        tbody td {
            padding: 15px;
            color: #5a0000;
        }



        /* Responsive Media Queries */
        @media (max-width: 768px) {

            /* Untuk layar mobile */
            .subheader {
                padding: 10px;
            }

            .btn-inventory,
            .btn-date-info,
            .btn-dark {
                padding: 8px 15px;
                font-size: 12px;
            }

            .table-responsive {
                padding: 10px;
                /* Mengurangi padding di layar kecil */
                max-width: 100%;
                /* Membuat tabel menyesuaikan lebar layar */
                overflow-x: auto;
                /* Tambahkan ini untuk menghindari horizontal scroll */
            }

            .table-header th,
            tbody td {
                padding: 8px;
                /* Kurangi padding di layar kecil */
                font-size: 12px;
                /* Kecilkan ukuran font */
            }

            .stock-logo {
                max-width: 100%;
            }
        }


        /* Responsive Media Queries */
        @media (max-width: 360px) {

            /* Untuk layar mobile */
            .subheader {
                padding: 5px;
                /* Mengurangi padding */
                font-size: 14px;
                /* Sesuaikan ukuran font */
            }

            .btn-inventory,
            .btn-date-info,
            .btn-dark {
                padding: 5px 10px;
                /* Kurangi padding di tombol */
                font-size: 10px;
                /* Kecilkan ukuran font */
            }

            .table-responsive {
                padding: 1px;
                /* Mengurangi padding di layar kecil */
                max-width: 100%;
                /* Pastikan tabel menyesuaikan lebar layar */
                overflow-x: auto;
                /* Tambahkan ini untuk menghindari horizontal scroll */
            }

            .table {
                width: 100%;
                /* Buat tabel mengikuti lebar layar */
                font-size: 10px;
                /* Kecilkan font di tabel */
            }

            .table-header th {
                padding: 5px;
                /* Kurangi padding di header tabel */
                font-size: 10px;
                /* Kecilkan ukuran font */
            }

            tbody td {
                padding: 5px;
                /* Kurangi padding di isi tabel */
                font-size: 10px;
                /* Kecilkan ukuran font */
            }

            .stock-logo {
                max-width: 80%;
                /* Kecilkan ukuran logo agar tidak terlalu besar */
                margin-bottom: 10px;
            }

            /* Kurangi margin atau padding yang berlebihan */
            .mb-4.text-center {
                margin-bottom: 3px;
                /* Mengurangi jarak bawah */
            }
        }


        @media (min-width: 769px) and (max-width: 1200px) {

            /* Untuk layar laptop dan tablet */
            .btn-inventory,
            .btn-date-info,
            .btn-dark {
                padding: 10px 18px;
                font-size: 12px;
            }

            .table-responsive {
                padding: 30px;
                margin-top: 5px;
                max-width: 100%;
            }

            .table-header th,
            tbody td {
                padding: 12px;
                font-size: 16px;
            }

            .stock-logo {
                max-width: 80%;
            }
        }

        .mb-4.text-center {
            margin-bottom: 5px;
        }
    </style>



    @include('app.allstock.allstock_modal')
    @include('app._partials.js')
    @include('app.allstock.allstock_js')
@endSection()
