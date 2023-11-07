<!-- Modal-->
<div class="modal fade" id="ActivityModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">User Activity Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <!--begin: Datatable-->
                <input type="search" class="form-control form-control-sm col-6" id="user_activity_search" placeholder="Cari user / aktifitas"/><br/>
                <table class="table table-hover table-checkable" id="Activitytb">
                    <thead class="bg-primary text-light">
                        <tr>
                            <th class="text-light">No</th>
                            <th class="text-light">Store</th>
                            <th class="text-light">Nama User</th>
                            <th class="text-light">Aktifitas</th>
                            <th class="text-light">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <!--end: Datatable-->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="BrandAssetsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Total Asset Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <!--begin: Datatable-->
                <input type="search" class="form-control form-control-sm col-6" id="brands_search" placeholder="Cari brands"/><br/>
                <table class="table table-hover table-checkable" id="BrandAssetstb">
                    <thead class="bg-primary text-light">
                        <tr>
                            <th class="text-light">No</th>
                            <th class="text-light">Brand</th>
                            <th class="text-light">Qty</th>
                            <th class="text-light">Total</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <!--end: Datatable-->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="DebtModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Daftar Hutang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <!--begin: Datatable-->
                <table class="table table-hover table-checkable" id="BrandDebttb">
                    <thead class="bg-primary text-light">
                        <tr>
                            <th class="text-light">No</th>
                            <th class="text-light">Brand</th>
                            <th class="text-light">Hutang</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <!--end: Datatable-->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="AssetsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Total Asset <span id="brands_name_label"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <!--begin: Datatable-->
                <input type="hidden" id="_br_id" value=""/>
                <table class="table table-hover table-checkable" id="Assetstb">
                    <thead class="bg-primary text-light">
                        <tr>
                            <th class="text-light">No</th>
                            <th class="text-light">Kategori</th>
                            <th class="text-light">Qty</th>
                            <th class="text-light">Total</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <!--end: Datatable-->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="BrandNettSalesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Total Penjualan Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <!--begin: Datatable-->
                <input type="search" class="form-control form-control-sm col-6" id="brands_nett_sales_search" placeholder="Cari brands"/><br/>
                <table class="table table-hover table-checkable" id="BrandNettSalestb">
                    <thead class="bg-primary text-light">
                        <tr>
                            <th class="text-light">No</th>
                            <th class="text-light">Brand</th>
                            <th class="text-light">Qty</th>
                            <th class="text-light">Total</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <!--end: Datatable-->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->


<!-- ======================================================== -->
<!-- Modal-->
<div class="modal fade" id="DebtByStoreModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Hutang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <!--begin: Datatable-->
                <table class="table table-hover table-checkable" id="DebtByStoretb">
                    <thead class="bg-primary text-light">
                        <tr>
                            <th class="text-light">No</th>
                            <th class="text-light">Store</th>
                            <th class="text-light">Hutang</th>
                            <th class="text-light">Dibayar</th>
                            <th class="text-light">Sisa</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <!--end: Datatable-->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="AssetByStoreModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Aset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <!--begin: Datatable-->
                <table class="table table-hover table-checkable" id="AssetByStoretb">
                    <thead class="bg-primary text-light">
                        <tr>
                            <th class="text-light">No</th>
                            <th class="text-light">Store</th>
                            <th class="text-light">Total</th>
                            <th class="text-light">Credit</th>
                            <th class="text-light">Consignment</th>
                            <th class="text-light">Cash</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <!--end: Datatable-->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="NettSaleByStoreModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Aset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <!--begin: Datatable-->
                <table class="table table-hover table-checkable" id="NettSaleByStoretb">
                    <thead class="bg-primary text-light">
                        <tr>
                            <th class="text-light">No</th>
                            <th class="text-light">Store</th>
                            <th class="text-light">Total</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <!--end: Datatable-->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->



<!-- Modal-->
<div class="modal fade" id="BrandValueModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Total By Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <!--begin: Datatable-->
                <input type="hidden" id="data_type" value=""/>
                <input type="search" class="form-control form-control-sm col-6" id="brands_value_search" placeholder="Cari brands"/><br/>
                <table class="table table-hover table-checkable" id="BrandValuetb">
                    <thead class="bg-primary text-light">
                        <tr>
                            <th class="text-light">No</th>
                            <th class="text-light">Brand</th>
                            <th class="text-light">Qty</th>
                            <th class="text-light">Total</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <!--end: Datatable-->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="UrbanPanelModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <input type="hidden" id="urban_first_load" value=""/>
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Urban Panel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6 col-xxl-4">
                        <!--begin::Stats Widget 11-->
                        <div class="card card-custom card-stretch gutter-b" style="border-radius:15px;">
                            <!--begin::Body-->
                            <div class="card-body p-0">
                                <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                    <span class="btn-sm btn-primary font-weight-bolder font-size-h6">
                                        Jual Bersih
                                    </span>
                                    <div class="d-flex flex-column text-right">
                                        <span class="text-white font-weight-bolder font-size-h1 btn-sm btn-primary" id="nett_sales_label"><span id="urban_nett_sales_label_reload">0</span></span>
                                    </div>
                                </div>
                                <div class="d-none" id="nettsaleChart"></div>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Stats Widget 11-->
                    </div>
                    <div class="col-lg-6 col-xxl-4">
                        <!--begin::Stats Widget 12-->
                        <div class="card card-custom card-stretch gutter-b" style="border-radius:15px;">
                            <!--begin::Body-->
                            <div class="card-body p-0">
                                <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                    <span class="btn-sm btn-primary font-weight-bolder font-size-h6">
                                        Profit
                                    </span>
                                    <div class="d-flex flex-column text-right">
                                        <span class="text-white font-weight-bolder font-size-h1 btn-sm btn-primary" id="profit_label"><span id="urban_profit_label_reload">0</span></span>
                                    </div>
                                </div>
                                <div class="d-none" id="profitChart"></div>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Stats Widget 12-->
                    </div>
                    
                    <div class="col-lg-6 col-xxl-4">
                        <!--begin::Stats Widget 11-->
                        <div class="card card-custom card-stretch gutter-b border" style="border-radius:15px;">
                            <!--begin::Body-->
                            <div class="card-body p-0">
                                <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                    <span class="btn-sm btn-primary font-weight-bolder font-size-h6">
                                        Pembelian
                                    </span>
                                    <div class="d-flex flex-column text-right">
                                        <span class="text-white font-weight-bolder font-size-h1 btn-sm btn-primary" id="purchase_label"><span id="urban_purchase_label_reload">0</span></span>
                                    </div>
                                </div>
                                <div class="d-none" id="purchaseChart"></div>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Stats Widget 11-->
                    </div>
                    <div class="col-lg-6 col-xxl-4">
                        <!--begin::Stats Widget 12-->
                        <div class="card card-custom card-stretch gutter-b" style="border-radius:15px;">
                            <!--begin::Body-->
                            <div class="card-body p-0">
                                <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                    <span class="btn-sm btn-danger font-weight-bolder font-size-h6" id="urban_cash_credit_asset_label">
                                        Aset C/C
                                    </span>
                                    <div class="d-flex flex-column text-right">
                                        <span class="text-white font-weight-bolder font-size-h1 btn-sm btn-primary" id="urban_assets_label">0</span>
                                    </div>
                                </div>
                                <div class="d-none" id="assetChart"></div>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Stats Widget 12-->
                    </div>
                    <div class="col-lg-6 col-xxl-4">
                        <!--begin::Stats Widget 12-->
                        <div class="card card-custom card-stretch gutter-b" style="border-radius:15px;">
                            <!--begin::Body-->
                            <div class="card-body p-0">
                                <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                    <span class="btn-sm btn-danger font-weight-bolder font-size-h6" id="urban_consignment_asset_label">
                                        Aset Con
                                    </span>
                                    <div class="d-flex flex-column text-right">
                                        <span class="text-white font-weight-bolder font-size-h1 btn-sm btn-primary" id="urban_consign_assets_label">0</span>
                                    </div>
                                </div>
                                <div class="d-none" id="consignAssetChart"></div>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Stats Widget 12-->
                    </div>
                    <div class="col-lg-6 col-xxl-4">
                        <!--begin::Stats Widget 12-->
                        <div class="card card-custom card-stretch gutter-b" style="border-radius:15px;">
                            <!--begin::Body-->  
                            <div class="card-body p-0">
                                <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                    <span class="btn-sm btn-primary font-weight-bolder font-size-h6">
                                        Hutang
                                    </span>
                                    <div class="d-flex flex-column text-right">
                                        <span class="text-white font-weight-bolder font-size-h1 btn-sm btn-primary" id="urban_debt_label">0</span>
                                    </div>
                                </div>
                                <div class="d-none" id="debtChart"></div>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Stats Widget 12-->
                    </div>
                    <!-- INCOMING STOCK -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->
<!-- ======================================================== -->


<!-- Modal-->
<div class="modal fade" id="BrandProfitModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Total By Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <!--begin: Datatable-->
                <table class="table table-hover table-checkable" id="BrandProfittb">
                    <thead class="bg-primary text-light">
                        <tr>
                            <th class="text-light">Brand</th>
                            <th class="text-light">Total (Non Admin)</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <!--end: Datatable-->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

