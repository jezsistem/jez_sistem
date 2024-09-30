@extends('app.layout')
@section('public3.assets.css')
@endsection
@section('content');

<div class="page-heading">
    <h3>Dashboard</h3>
</div>

<div class="page-content">
    <div class="row">
        <div class="col-md-3 mb-4">
            <label for="filter" class="form-label fw-bold"><i class="bi bi-funnel me-2"></i>Filter Ringkasan</label>
            <select id="filter" class="form-control">
                <option value="0">Semua periode</option>
                <option value="1">Hari ini</option>
                <option value="2">Minggu ini</option>
                <option value="3">Bulan ini</option>
                <option value="4">Tahun ini</option>
            </select>
        </div>
    </div>
    <section class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon blue mb-2">
                                        <i class="bi bi-currency-dollar" style="display: flex; align-items: center; justify-content: center;"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">
                                        Total Omset
                                    </h6>
                                    <h6 class="font-extrabold mb-0" id="omset">Rp. #></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon green mb-2">
                                        <i class="bi bi-cash-coin" style="display: flex; align-items: center; justify-content: center;"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Laba</h6>
                                    <h6 class="font-extrabold mb-0" id="laba">Rp. #></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon purple mb-2">
                                        <i class="bi bi-bag-check" style="display: flex; align-items: center; justify-content: center;"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Transaksi</h6>
                                    <h6 class="font-extrabold mb-0" id="trx">#</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon black mb-2">
                                        <i class="bi bi-percent" style="display: flex; align-items: center; justify-content: center;"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Diskon</h6>
                                    <h6 class="font-extrabold mb-0" id="discount">Rp. #?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon blue mb-2">
                                        <i class="bi bi-cash-stack" style="display: flex; align-items: center; justify-content: center;"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Transaksi Cash</h6>
                                    <h6 class="font-extrabold mb-0" id="cash"><#></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon green mb-2">
                                        <i class="bi bi-wallet2" style="display: flex; align-items: center; justify-content: center;"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Transaksi Non Tunai</h6>
                                    <h6 class="font-extrabold mb-0" id="noncash"><#noncash; ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon purple mb-2">
                                        <i class="bi bi-cash-coin" style="display: flex; align-items: center; justify-content: center;"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Pemasukan</h6>
                                    <h6 class="font-extrabold mb-0" id="pemasukan">Rp. <#pemasukan; ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon black mb-2">
                                        <i class="bi bi-graph-up-arrow" style="display: flex; align-items: center; justify-content: center;"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Pengeluaran</h6>
                                    <h6 class="font-extrabold mb-0" id="pengeluaran">Rp. <#pengeluaran; ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Grafik Omset</h4>
                </div>
                <div class="card-body">
                    <div id="chart-profile-visit"></div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Need: Apexcharts -->
<script src="public3/assets/extensions/apexcharts/apexcharts.min.js"></script>

<script>
    $(document).ready(function() {
        var grafik = <#grafik; ?>;
        var dates = <#dates; ?>;

        console.log(grafik);
        var optionsProfileVisit = {
            annotations: {
                position: "back",
            },
            dataLabels: {
                enabled: false,
            },
            chart: {
                type: "bar",
                height: 300,
            },
            fill: {
                opacity: 1,
            },
            plotOptions: {},
            series: [{
                name: "Omset",
                data: grafik,
            }, ],
            colors: "#435ebe",
            xaxis: {
                categories: dates
            },
        }
        var chartProfileVisit = new ApexCharts(
            document.querySelector("#chart-profile-visit"),
            optionsProfileVisit
        )
        chartProfileVisit.render()

    });

    $('#filter').change(function() {
        $.ajax({
            url: '/dashboard/getRingkasan',
            type: 'POST',
            dataType: 'JSON',
            data: {
                filter: $(this).val()
            },
            beforeSend: function() {
                showblockUI();
            },
            complete: function() {
                hideblockUI();
            },
            success: function(response) {
                $('#omset').text(response.omset);
                $('#laba').text(response.laba);
                $('#trx').text(response.trx);
                $('#discount').text(response.discount);
                $('#cash').text(response.cash);
                $('#noncash').text(response.noncash);
                $('#pemasukan').text(response.pemasukan);
                $('#pengeluaran').text(response.pengeluaran);
            },
            error: function(jqXHR, textStatus, errorThrown, exception) {
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
            }
        });
    });
</script>
