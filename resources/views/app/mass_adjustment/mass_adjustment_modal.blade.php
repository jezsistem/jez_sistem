<!-- Modal-->
<div class="modal fade" id="ImportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_import" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Import Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Pilih template yang sudah diisi dari hasil export template
                                <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="template" id="template" required />
                        </div>

                        <div class="form-group">
                            <label>Tipe Adjustment</label>
                            <select class="form-control" name="tipe_adjustment" id="tipe_adjustment">
                                <option value="">-- Pilih Tipe Adjustment --</option>
                                <option value="KERUGIAN">KERUGIAN</option>
                                <option value="BELUM TERBAYAR">BELUM TERBAYAR</option>
                                <option value="TERBAYAR">TERBAYAR</option>
                                <option value="BIAYA PROMOSI">BIAYA PROMOSI</option>
                                <option value="BIAYA OPERASIONAL">BIAYA OPERASIONAL</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label><span class="text-danger">*</span>Note Adjustment</label>
                            <textarea class="form-control" id="note_adjustment" name="note_adjustment" style="border: 2px solid #000;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup
                    </button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="import_data_btn">Import</button>
                </div>
            </form>
        </div>
    </div>
</div><!-- Modal-->

<div class="modal fade" id="ImportScanModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_import" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Import Data Scan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <!--begin::List Widget 9-->
                            <!--begin::Header-->
                            <h3 class="card-title align-items-start flex-column">
                                <span class="font-weight-bolder btn-sm btn-primary text-white">Filter Template</span>
                            </h3>
                            <select class="form-control bg-primary text-white" id="st_filter">
                                <option value='all'>- Semua Store -</option>
                                @foreach ($data['st_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select class="form-control mt-2 bg-primary text-white" id="br_filter">
                                <option value='all'>- Semua Brand -</option>
                                @foreach ($data['br_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select class="form-control mt-2 bg-primary text-white" id="psc_filter">
                                <option value='all'>- Semua Sub Kategori -</option>
                                @foreach ($data['psc_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select class="form-control mt-2 bg-primary text-white" id="qty_filter">
                                <option value='1'>- Hanya yang Ada Stok -</option>
                                <option value='0'>- Termasuk yang Sudah Habis -</option>
                            </select>
                            <div id="bin_panel"></div>
                            <br />
                            <div class="row" id="bin_filter_panel">

                            </div>
                            <!--end::Timeline-->
                            <!--end: Card Body-->

                            <!--end: List Widget 9-->
                            <label>Masukkan file csv data scan
                                <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="template" id="template" required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup
                    </button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="import_data_btn">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal Filter tanggal-->
<div class="modal fade" id="TanggalModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_import" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Filter Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Pilih range tanggal
                                <span class="text-danger">*</span></label>
                            <input value="" name="tanggal" id="tanggalrange" type="text"
                                class="form-control" placeholder="Periode Tanggal">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup
                    </button>
                    <button type="submit" class="btn btn-dark font-weight-bold"
                        id="import_data_btn">Tampilkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Export-->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_export" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">
                        <Export></Export>
                        Data
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Pilih Range Tanggal
                                <span class="text-danger">*</span></label>
                            {{--                            <input type="file" class="form-control" name="template" id="template" required/> --}}
                            {{--                            <input class="form-control form-control-solid" placeholder="Pick date rage" id="kt_daterangepicker_4"/> --}}
                            <input value="" name="tanggal" id="tanggalrange" type="text"
                                class="form-control" placeholder="Periode Tanggal">

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup
                    </button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="import_data_btn">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal-->
<div class="modal fade" id="MassAdjustmentExportModal" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true" style="overflow-y: auto;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Export SO Adjustment</h5>
            </div>

{{--            <style>--}}
{{--                #MassAdjustmentDetailtb {--}}
{{--                    /*table-layout: fixed;*/--}}
{{--                    width: 100%;--}}
{{--                }--}}

{{--                #MassAdjustmentDetailtb td:nth-child(2),--}}
{{--                #MassAdjustmentDetailtb th:nth-child(2) {--}}
{{--                    width: 550px;--}}
{{--                }--}}

{{--                #MassAdjustmentDetailtb td:nth-child(2),--}}
{{--                #MassAdjustmentDetailtb th:nth-child(2) {--}}
{{--                    width: 150px !important;--}}
{{--                }--}}
{{--            </style>--}}

            <div class="modal-body table-responsive">
                <a class="btn-sm btn-primary float-left" id="excel_report">Excel</a><br />
                <table class="table table-hover" id="MassAdjustmentDetailtb" style="width: 100%;">
                    <thead class="text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark" style="min-width: 200px; max-width: 250px;">Tgl Adjustment</th>
                            <th class="text-dark">Kode</th>
                            <th class="text-dark">Store</th>
                            <th class="text-dark">BIN</th>
                            <th class="text-dark">BRAND</th>
                            <th class="text-dark">SKU</th>
                            <th class="text-dark">ARTIKEL</th>
                            <th class="text-dark">WARNA</th>
                            <th class="text-dark">SIZE</th>
                            <th class="text-dark">Sub Kategori</th>
                            <th class="text-dark">HB</th>
                            <th class="text-dark">HJ</th>
                            <th class="text-dark">Qty System</th>
                            <th class="text-dark">Qty SO</th>
                            <th class="text-dark">Type</th>
                            <th class="text-dark">Diff</th>
                            <th class="text-dark">Notes</th>
                            <th class="text-dark">Tipe Adjustment</th>
                            <th class="text-dark">Tgl Approve</th>
                            <th class="text-dark">Tgl Executor</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold"
                    data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->
