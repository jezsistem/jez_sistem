<!--begin::Row-->
<div class="row">
    <div class="col-lg-7">
        <!--begin::Card-->
        <div class="card card-custom">
            <div class="card-header bg-primary rounded">
                <div class="card-title">
                    <h3 class="card-label text-white">Deskripsi</h3>
                </div>
            </div>
            <div class="card-body">
                @if ($data['product']->p_description != null)
                    {!! $data['product']->p_description !!}
                @else
                    <p>Belum ada deskripsi</p>
                @endif
            </div>
        </div>
        <!--end::Card-->
    </div>
    <div class="col-lg-5">
        <!--begin::Card-->
        <div class="card card-custom  mb-4">
            <div class="card-header bg-primary rounded">
                <div class="card-title">
                    <h3 class="card-label text-white">Informasi</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-4 col-form-label">Harga Banderol</label>
                    <div class="col-8">
                    <input class="form-control" type="text" value="Rp. {{ number_format($data['product']->p_price_tag) }}" readonly/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-4 col-form-label">Harga Beli</label>
                    <div class="col-8">
                    <input class="form-control" type="text" value="Rp. {{ number_format($data['product']->p_purchase_price) }}" readonly/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-4 col-form-label">Harga Jual</label>
                    <div class="col-8">
                    <input class="form-control" type="text" value="Rp. {{ number_format($data['product']->p_sell_price) }}" readonly/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-4 col-form-label">Warna Utama</label>
                    <div class="col-8">
                    <input class="form-control" type="text" value="{{ $data['product']->mc_name }}" readonly/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-4 col-form-label">Warna Artikel</label>
                    <div class="col-8">
                    <input class="form-control" type="text" value="{{ $data['product']->p_color }}" readonly/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-4 col-form-label">Brand</label>
                    <div class="col-8">
                    <input class="form-control" type="text" value="{{ $data['product']->br_name }}" readonly/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-4 col-form-label">Supplier</label>
                    <div class="col-8">
                    <input class="form-control" type="text" value="{{ $data['product']->ps_name }}" readonly/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-4 col-form-label">Kategori</label>
                    <div class="col-8">
                    <input class="form-control" type="text" value="{{ $data['product']->pc_name }} / {{ $data['product']->psc_name }} / {{ $data['product']->pssc_name }}" readonly/>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Card-->
        <!--begin::Card-->
        <div class="card card-custom  mb-4">
            <div class="card-header bg-primary rounded">
                <div class="card-title">
                    <h3 class="card-label text-white">Stok</h3>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-hover" id="ProductStockDetailtb">
                    <thead>
                        <tr>
                            <th style="white-space: nowrap;">Size</th>
                            <th style="white-space: nowrap;">Stok</th>
                            <th style="white-space: nowrap;">Barcode</th>
                            <th style="white-space: nowrap;">Running Code</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <!--end::Card-->
    </div>
</div>
<!--end::Row-->