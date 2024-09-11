<!-- Modal-->
<div class="modal fade" id="ActivityModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">User Activity Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <!--begin: Datatable-->
                <input type="search" class="form-control  col-6" id="user_activity_search" placeholder="Cari user / aktifitas"/><br/>
                <table class="table table-hover table-checkable" id="Activitytb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Store</th>
                            <th class="text-dark">Nama User</th>
                            <th class="text-dark">Aktifitas</th>
                            <th class="text-dark">Waktu</th>
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
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Total Asset Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <!--begin: Datatable-->
                <input type="search" class="form-control  col-6" id="brands_search" placeholder="Cari brands"/><br/>
                <table class="table table-hover table-checkable" id="BrandAssetstb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Brand</th>
                            <th class="text-dark">Qty</th>
                            <th class="text-dark">Total</th>
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
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Daftar Hutang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <!--begin: Datatable-->
                <table class="table table-hover table-checkable" id="BrandDebttb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Brand</th>
                            <th class="text-dark">Hutang</th>
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
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Total Asset <span id="brands_name_label"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <!--begin: Datatable-->
                <input type="hidden" id="_br_id" value=""/>
                <table class="table table-hover table-checkable" id="Assetstb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Kategori</th>
                            <th class="text-dark">Qty</th>
                            <th class="text-dark">Total</th>
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
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Total Penjualan Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <!--begin: Datatable-->
                <input type="search" class="form-control  col-6" id="brands_nett_sales_search" placeholder="Cari brands"/><br/>
                <table class="table table-hover table-checkable" id="BrandNettSalestb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Brand</th>
                            <th class="text-dark">Qty</th>
                            <th class="text-dark">Total</th>
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
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Hutang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <!--begin: Datatable-->
                <table class="table table-hover table-checkable" id="DebtByStoretb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Store</th>
                            <th class="text-dark">Hutang</th>
                            <th class="text-dark">Dibayar</th>
                            <th class="text-dark">Sisa</th>
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
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Aset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <!--begin: Datatable-->
                <table class="table table-hover table-checkable" id="AssetByStoretb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Store</th>
                            <th class="text-dark">Total</th>
                            <th class="text-dark">Credit</th>
                            <th class="text-dark">Consignment</th>
                            <th class="text-dark">Cash</th>
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
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Aset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <!--begin: Datatable-->
                <table class="table table-hover table-checkable" id="NettSaleByStoretb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Store</th>
                            <th class="text-dark">Total</th>
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
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Total By Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <!--begin: Datatable-->
                <input type="hidden" id="data_type" value=""/>
                <input type="search" class="form-control  col-6" id="brands_value_search" placeholder="Cari brands"/><br/>
                <table class="table table-hover table-checkable" id="BrandValuetb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Brand</th>
                            <th class="text-dark">Qty</th>
                            <th class="text-dark">Total</th>
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
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Urban Panel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6 col-xxl-4">
                        <!--begin::Stats Widget 11-->
                        <div class="card card-custom card-stretch gutter-b" style="border-radius:.625rem;">
                            <!--begin::Body-->
                            <div class="card-body p-0">
                                <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                    <span class="btn-sm btn-primary font-weight-bold fs-4">
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
                        <div class="card card-custom card-stretch gutter-b" style="border-radius:.625rem;">
                            <!--begin::Body-->
                            <div class="card-body p-0">
                                <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                    <span class="btn-sm btn-primary font-weight-bold fs-4">
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
                        <div class="card card-custom card-stretch gutter-b border" style="border-radius:.625rem;">
                            <!--begin::Body-->
                            <div class="card-body p-0">
                                <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                    <span class="btn-sm btn-primary font-weight-bold fs-4">
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
                        <div class="card card-custom card-stretch gutter-b" style="border-radius:.625rem;">
                            <!--begin::Body-->
                            <div class="card-body p-0">
                                <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                    <span class="btn-sm btn-danger font-weight-bold fs-4" id="urban_cash_credit_asset_label">
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
                        <div class="card card-custom card-stretch gutter-b" style="border-radius:.625rem;">
                            <!--begin::Body-->
                            <div class="card-body p-0">
                                <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                    <span class="btn-sm btn-danger font-weight-bold fs-4" id="urban_consignment_asset_label">
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
                        <div class="card card-custom card-stretch gutter-b" style="border-radius:.625rem;">
                            <!--begin::Body-->  
                            <div class="card-body p-0">
                                <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                    <span class="btn-sm btn-primary font-weight-bold fs-4">
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
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Total By Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <!--begin: Datatable-->
                <table class="table table-hover table-checkable" id="BrandProfittb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">Brand</th>
                            <th class="text-dark">Total (Non Admin)</th>
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

<?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/dashboard/dashboard_modal.blade.php ENDPATH**/ ?>