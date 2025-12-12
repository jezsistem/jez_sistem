<!-- Modal-->

<style>
    #imageGallery .img-box {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        transition: transform 0.2s ease-in-out;
    }

    #imageGallery img {
        width: 100%;
        height: 120px;
        object-fit: cover;
    }

    #imageGallery .img-box:hover {
        transform: scale(1.05);
    }
</style>

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
                            <label>Download Template
                                <span class="text-danger">*</span></label>
                            <a href="{{ asset('upload/template/artikel_template_import.xlsx') }}"
                                class="btn btn-xs btn-primary">Download</a>
                        </div>
                        <div class="form-group">
                            <label>Pilih template yang sudah di download dan diisi
                                <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="p_template" id="p_template" required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" id="close_import_btn"
                        data-dismiss="modal">Tutup
                    </button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="import_data_btn">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal-->
<div class="modal fade" id="ProductModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
    data-backdrop="static" data-keyboard="false" style="overflow-y: auto;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="f_product">
                @csrf
                <input type="hidden" name="_id" id="_id" value="" />
                <input type="hidden" name="_mode" id="_mode" value="" />
                <input type="hidden" name="_sz_barcode" id="_sz_barcode" value="" />
                <input type="hidden" name="_sz_id" id="_sz_id" value="" />
                <input type="hidden" name="_sz_sell_price" id="_sz_sell_price" value="" />
                <input type="hidden" name="_current_pc_id" id="_current_pc_id" value="" />
                <input type="hidden" name="_current_psc_id" id="_current_psc_id" value="" />
                <input type="hidden" name="_current_pssc_id" id="_current_pssc_id" value="" />
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Artikel </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-4 pt-1">
                                <label>Kode Artikel</label>
                                <input type="text" name="article_id" id="article_id" class="form-control"
                                    placeholder="Kode Artikel" />
                            </div>
                            <div class="col-lg-4 pt-1">
                                <label>Attachment</label><br>
                                <button type="button" class="btn btn-sm btn-info mr-2" id="showImageModalBtn">
                                    <i class="fa fa-image"></i> View Image
                                </button>
                                <button type="button" class="btn btn-sm btn-primary mr-2" id="showLinkModalBtn">
                                    <i class="fa fa-link"></i> Social Media
                                </button>
                            </div>
                            <div class="col-lg-4 pt-1 float-right ml-auto">
                                <div id="product_qr"></div>
                            </div>
                        </div>
                        <div class="form-group row" id="pcpscpssc_edit" style="display:none;">
                            <div class="col-lg-4 pt-1">
                                <label>Kategori <span class="text-danger">*</span></label>
                                <select class="form-control" id="_pc_id" name="_pc_id" required>
                                    <option value="">- Pilih Kategori -</option>
                                    @foreach ($data['pc_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <div id="_pc_id_parent"></div>
                            </div>
                            <div class="col-lg-4 pt-1">
                                <label>Sub Kategori <span class="text-danger">*</span></label>
                                <select class="form-control" id="_psc_id" name="_psc_id" required>
                                    <option value="">- Pilih Sub Kategori -</option>
                                    @foreach ($data['psc_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <div id="_psc_id_parent"></div>
                            </div>
                            <div class="col-lg-4 pt-1">
                                <label>Sub-Sub Kategori <span class="text-danger">*</span></label>
                                <select class="form-control" id="_pssc_id" name="_pssc_id" required>
                                    <option value="">- Pilih Sub-Sub Kategori -</option>
                                    @foreach ($data['pssc_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <div id="_pssc_id_parent"></div>
                            </div>
                        </div>


                        {{-- ini dalam masa perbaikan --}}
                        <div class="form-group row">
                            <div class="col-lg-4 pt-1">
                                <label>Nama Artikel <span class="text-danger">*</span></label>
                                <input type="text" name="p_name" id="p_name" class="form-control"
                                    placeholder="Nama artikel" required />
                            </div>
                            <div class="col-lg-4 pt-1">
                                <label>Brand <span class="text-danger">*</span></label>
                                <select class="form-control" id="br_id" name="br_id" required>
                                    <option value="">- Pilih Brand -</option>
                                    @foreach ($data['br_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <div id="br_id_parent"></div>
                            </div>
                            <div class="col-lg-4 pt-1">
                                <label>Supplier <span class="text-danger">*</span></label>
                                <select class="form-control" id="ps_id" name="ps_id" required>
                                    <option value="">- Pilih Supplier -</option>
                                    @foreach ($data['ps_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <div id="ps_id_parent"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4 pt-1">
                                <label>Satuan Artikel <span class="text-danger">*</span></label>
                                <select class="form-control" id="pu_id" name="pu_id" required>
                                    <option value="">- Pilih Satuan -</option>
                                    @foreach ($data['pu_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <div id="pu_id_parent"></div>
                            </div>
                            <div class="col-lg-4 pt-1">
                                <label>Gender <span class="text-danger">*</span></label>
                                <select class="form-control" id="gn_id" name="gn_id" required>
                                    <option value="">- Pilih Gender -</option>
                                    @foreach ($data['gn_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <div id="gn_id_parent"></div>
                            </div>
                            <div class="col-lg-4 pt-1">
                                <label>Season <span class="text-danger">*</span></label>
                                <select class="form-control" id="ss_id" name="ss_id" required>
                                    <option value="">- Pilih Season -</option>
                                    @foreach ($data['ss_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <div id="ss_id_parent"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4 pt-1">
                                <label>Warna Utama <span class="text-danger">*</span></label>
                                <select class="form-control" id="mc_id" name="mc_id" required>
                                    <option value="">- Pilih Warna -</option>
                                    @foreach ($data['mc_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <div id="mc_id_parent"></div>
                            </div>
                            <div class="col-lg-4 pt-1">
                                <label>Warna Artikel <span class="text-danger">*</span></label>
                                <input type="text" name="p_color" id="p_color" class="form-control"
                                    placeholder="Warna artikel" required />
                            </div>
                            <div class="col-lg-4 pt-1">
                                <label>Aging</label>
                                <input type="month" name="p_aging" id="p_aging" class="form-control"
                                    placeholder="Aging / Usia Artikel" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4 pt-1">
                                <label>Berat Items </label>
                                <input type="number" name="p_weight" id="p_weight" class="form-control"
                                    placeholder="gram" />
                            </div>

                            <div class="col-lg-4 pt-1">
                                <label>Size Schema </label>
                                <select class="form-control" id="sz_schema_modal_id" name="sz_schema_modal_id">
                                    <option value="">- Pilih Size Schema -</option>
                                    @foreach ($data['sz_schema_id'] as $key => $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <div id="sz_schema_modal_id_parent"></div>
                            </div>

                            <div class="col-lg-4 d-flex align-items-center" style="min-height: 38px;">
                                <div class=" my-auto">
                                    <input class="" type="checkbox" id="consignment" name="consignment"
                                        value="1">
                                    <label class="" for="consignment">
                                        Is Consignment
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4 d-flex align-items-center" style="min-height: 38px;">
                                <div class=" my-auto">
                                    <input class="" type="checkbox" id="complement" name="complement"
                                        value="1">
                                    <label class="" for="complement">
                                        Complement
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-4 d-flex align-items-center" style="min-height: 38px;">
                                <div class=" my-auto">
                                    <input class="" type="checkbox" id="mp_best_seller" name="mp_best_seller"
                                        value="1">
                                    <label class="" for="mp_best_seller">
                                        MP Best Seller
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-4 d-flex align-items-center" style="min-height: 38px;">
                                <div class=" my-auto">
                                    <input class="" type="checkbox" id="mp_stock_masking"
                                        name="mp_stock_masking" value="1">
                                    <label class="" for="mp_stock_masking">
                                        Impairment
                                    </label>
                                </div>
                            </div>

                            <!-- resources/views/product_modal.blade.php -->
                            {{-- <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    Select Product Flags
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    @foreach (['MP_best_seller', 'MP_stock_masking', 'Complement', 'Cosignment'] as $flag)
                                        <a class="dropdown-item" href="#"
                                            onclick="toggleFlag('{{ $flag }}', {{ $product->id }})">
                                            {{ ucwords(str_replace('_', ' ', $flag)) }}
                                            <span
                                                id="{{ $flag }}_status">{{ $product->$flag ? '✓' : '✗' }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div> --}}

                            <!-- Dropdown untuk mengelola flag produk -->
                            {{--                            <div class="dropdown"> --}}
                            {{--                                <button class="btn btn-secondary dropdown-toggle" type="button" --}}
                            {{--                                    id="productFlagsDropdown" data-toggle="dropdown" aria-haspopup="true" --}}
                            {{--                                    aria-expanded="false"> --}}
                            {{--                                    Pilih Flag Produk --}}
                            {{--                                </button> --}}
                            {{--                                <div class="dropdown-menu" aria-labelledby="productFlagsDropdown"> --}}
                            {{--                                    @foreach ($data['products'] as $product) --}}
                            {{--                                        <h5>{{ $product->name ?? 'Produk' }} ID: {{ $product->p_name }}</h5> --}}
                            {{--                                        <a class="dropdown-item {{ $product->MP_best_seller ? 'bg-pink' : '' }}" href="#" --}}
                            {{--                                            onclick="toggleFlag({{ $product->p_name }}, 'MP_best_seller')"> --}}
                            {{--                                            MP Best Seller --}}
                            {{--                                        </a> --}}
                            {{--                                        <a class="dropdown-item {{ $product->MP_stock_masking ? 'bg-pink' : '' }}" href="#" --}}
                            {{--                                            onclick="toggleFlag({{ $product->p_name }}, 'MP_stock_masking')"> --}}
                            {{--                                            MP Stock Masking --}}
                            {{--                                        </a> --}}
                            {{--                                        <a class="dropdown-item {{ $product->Complement ? 'bg-pink' : '' }}" href="#" --}}
                            {{--                                            onclick="toggleFlag({{ $product->p_name }}, 'Complement')"> --}}
                            {{--                                            Complement --}}
                            {{--                                        </a> --}}
                            {{--                                        <a class="dropdown-item {{ $product->Consignment ? 'bg-pink' : '' }}" href="#" --}}
                            {{--                                            onclick="toggleFlag({{ $product->p_name }}, 'Consignment')"> --}}
                            {{--                                            Consignment --}}
                            {{--                                        </a> --}}
                            {{--                                        <hr> --}}
                            {{--                                    @endforeach --}}
                            {{--                                </div> --}}
                            {{--                            </div> --}}


                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4 d-flex align-items-center" style="min-height: 38px;">
                                <div class=" my-auto">
                                    <input class="" type="checkbox" id="is_everlast" name="is_everlast"
                                        value="1">
                                    <label class="" for="is_everlast">
                                        Everlast
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-4 d-flex align-items-center" style="min-height: 38px;">
                                <div class=" my-auto">
                                    <input class="" type="checkbox" id="is_supersale" name="is_supersale"
                                        value="1">
                                    <label class="" for="is_supersale">
                                        Super Sale
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-4 d-flex align-items-center" style="min-height: 38px;">
                                <div class=" my-auto">
                                    <input class="" type="checkbox" id="is_reguler" name="is_reguler"
                                        value="1">
                                    <label class="" for="is_reguler">
                                        Reguler
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4 d-flex align-items-center" style="min-height: 38px;">
                                <div class=" my-auto">
                                    <input class="" type="checkbox" id="mark_down" name="mark_down"
                                        value="1">
                                    <label class="" for="mark_down">
                                        Mark Down
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4 pt-1">
                                <label>Sub Category 1 <span class="text-danger"></span></label>
                                <textarea class="form-control" id="subcatone" name="subcatone"></textarea>
                                {{--                                <input type="text" name="subcatone" id="subcatone" class="form-control" placeholder="Sub Category 1" /> --}}
                            </div>
                            <div class="col-lg-4 pt-1">
                                <label>Sub Category 2 <span class="text-danger"></span></label>
                                <textarea class="form-control" id="subcattwo" name="subcattwo"></textarea>
                                {{--                                <input type="text" name="subcattwo" id="subcattwo" class="form-control" placeholder="Sub Category 2" /> --}}
                            </div>
                            <div class="col-lg-4 pt-1">
                                <label>Link Konten <span class="text-danger"></span></label>
                                <textarea class="form-control" id="link_content" name="link_content"></textarea>
                            </div>
                            <div class="col-lg-12 pt-1 mt-2">
                                <label>Turn Over Class </label>
                                <input type="text" name="p_turnoverclass" id="p_turnoverclass"
                                    class="form-control" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4 pt-1">
                                <label>Harga Banderol <span class="text-danger">*</span></label>
                                <input type="text" name="p_price_tag" id="p_price_tag"
                                    class="form-control decimal" placeholder="Rp." required />
                            </div>
                            <div class="col-lg-4 pt-1">
                                <label>Harga Beli <span class="text-danger">*</span></label>
                                <input type="text" name="p_purchase_price" id="p_purchase_price"
                                    class="form-control decimal" placeholder="Rp." required />
                            </div>
                            <div class="col-lg-4 pt-1">
                                <label>Harga Jual <span class="text-danger">*</span></label>
                                <input type="text" name="p_sell_price" id="p_sell_price"
                                    class="form-control decimal" placeholder="Rp." required />
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-12 pt-1">
                                <div class="row">

                                    <!-- Grid 1 -->
                                    <div class="col-md-4">
                                        <label class="label-space">
                                            Size Artikel:
                                            <span id="barcode_running_label" style="display:none;"></span>
                                            <a class="btn btn-sm btn-primary" onclick="return activateColumn()">undisabled</a>
                                        </label>
                                    </div>

                                    <!-- Grid 2 -->
                                    <div class="col-md-4 d-flex align-items-center">
                                        <button type="button" class="btn btn-info btn-sm" id="btnLogHistory">
                                            Log History updated
                                        </button>
                                    </div>

                                    <!-- Grid 3 -->
                                    <div class="col-md-4">
                                        <label>
                                            Schema Display
                                            <button type="button" class="btn btn-primary" onclick="showAllSchema()">All Schema</button>
                                            <button type="button" class="btn btn-secondary" onclick="showStockedSchema()">Stocked Schema</button>
                                        </label>
                                    </div>

                                </div>

                                @if (!Str::contains($data['stt'], ['MARKOM', 'ONLINE']))
                                    <div id="reload_size"></div>
                                @endif
                            </div>
                        </div>


                        <div class="form-group row">
                            <div class="col-lg-12 pt-1">
                                <label for="exampleTextarea">Deskripsi Artikel</label>
                                <textarea class="form-control" name="p_description" id="p_description" rows="3"></textarea>
                            </div>
                        </div>


                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup
                    </button>

                    @if (!Str::contains($data['stt'], ['MARKOM', 'ONLINE']))
                        <button type="button" class="btn btn-danger font-weight-bold" id="delete_product_btn"
                            style="display:none;">Hapus
                        </button>

                        <button type="submit" class="btn btn-dark font-weight-bold" id="save_product_btn">Simpan
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal untuk menampilkan semua gambar -->
<div class="modal fade" id="ProductImageModal" tabindex="-1" role="dialog"
    aria-labelledby="ProductImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title" id="ProductImageModalLabel">Gambar Produk</h5>

                <div>
                    <button id="downloadAllBtn" class="btn btn-primary btn-sm mr-2">
                        <i class="fas fa-download"></i> Download All
                    </button>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
            </div>

            <div class="modal-body">
                <div id="imageGallery" class="row">
                    <div class="col-12 text-center text-muted" id="noImageMessage" style="display:none;">
                        Tidak ada gambar untuk produk ini.
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<!-- Modal untuk menampilkan semua link -->
<div class="modal fade" id="HistoryModal" tabindex="-1" role="dialog" aria-labelledby="HistoryModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title" id="HistoryModalLabel">Log History Changes</h5>

                <div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
            </div>

            <div class="modal-body">

                <div class="tab-content mt-3">
                    <!-- Marketplace -->
                    <div class="tab-pane fade show active">
{{--                        <button class="btn btn-primary mb-3" id="tableHistory">+ Add Marketplace</button>--}}
                        <table id="tableHistory" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>user</th>
                                <th>Activity</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>


<!-- Modal untuk menampilkan semua link -->
<div class="modal fade" id="ProductLinkModal" tabindex="-1" role="dialog" aria-labelledby="ProductLinkModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title" id="ProductLinkModalLabel">Link Produk</h5>

                <div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
            </div>

            <div class="modal-body">

                <!-- Nav Tabs -->
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tabMarketplace">Marketplace</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabSocial">Social Media</a>
                    </li>
                </ul>

                <div class="tab-content mt-3">

                    <!-- Marketplace -->
                    <div class="tab-pane fade show active" id="tabMarketplace">
                        <button class="btn btn-primary mb-3" id="addMarketplaceLinkBtn">+ Add Marketplace</button>
                        <table id="tableMarketplaceLinks" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Platform</th>
                                    <th>URL</th>
                                    <th>Location</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <!-- Social Media -->
                    <div class="tab-pane fade" id="tabSocial">
                        <button class="btn btn-primary mb-3" id="addSocialLinkBtn">+ Add Social Media</button>
                        <table id="tableSocialLinks" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Platform</th>
                                    <th>URL</th>
                                    <th>Location</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- Modal Add/Edit Link -->
<div class="modal fade" id="ProductLinkFormModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="ProductLinkFormTitle"><span>Add</span> Link</h5>
                <button type="button" class="close" data-dismiss="modal"><i class="ki ki-close"></i></button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="pl_id">
                <input type="hidden" id="pl_product_id">
                <input type="hidden" id="pl_type">
                <input type="hidden" id="mode">

                <!-- PLATFORM DROPDOWN -->
                <div class="form-group">
                    <label>Platform</label>
                    <select class="form-control" id="pl_platform">
                        <!-- dinamis -->
                    </select>
                </div>

                <!-- URL -->
                <div class="form-group">
                    <label>URL</label>
                    <input type="text" id="pl_url" class="form-control">
                </div>

                <!-- LOCATION -->
                <div class="form-group">
                    <label>Lokasi</label>
                    <select id="pl_location" class="form-control"></select>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="SaveProductLinkBtn">Save</button>
            </div>

        </div>
    </div>
</div>

<!-- Modal Preview Gambar Besar -->
<div class="modal fade" id="ImagePreviewModal" tabindex="-1" aria-labelledby="ImagePreviewModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-dark text-center border-0">
            <div class="modal-body position-relative p-0">
                <button type="button" class="btn btn-light btn-sm position-absolute" data-dismiss="modal"
                    style="top:10px; right:10px; border-radius:50%; width:35px; height:35px; display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-times"></i>
                </button>
                <img id="previewImageFull" src="" alt="Preview" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>

<!-- Modal-->
<div class="modal fade" id="ProductDetailModal" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Detail Artikel <span
                        id="product_name_label"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="productDetailContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup
                </button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->


<!-- Modal-->
<div class="modal fade" id="ProductBarcodeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Lengkapi Barcode</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <input type="search" class="form-control  col-6" id="p_search" placeholder="Cari artikel" /><br />
                <table class="table table-hover table-checkable" id="Ptb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Brand</th>
                            <th class="text-dark">Artikel</th>
                            <th class="text-dark">Warna</th>
                            <th class="text-dark">Size</th>
                            <th class="text-dark">Barcode</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup
                </button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<div class="modal fade" id="MassUpdateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_mass_update" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Mass Update Products</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Download Template
                                <span class="text-danger">*</span></label>
                            <a href="{{ asset('upload/template/mass_update_product.xlsx') }}"
                                class="btn btn-xs btn-primary">Download</a>
                        </div>
                        <div class="form-group">
                            <label>Pilih template yang sudah di download dan diisi
                                <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="p_mass_import" id="p_mass_import"
                                required />
                        </div>
                        <div class="form-group">
                            <label>Pilih Tipe Update
                                <span class="text-danger">*</span></label>
                            <div>
                                <select class="form-control" name="update_type" id="update_type" required>
                                    <option value="">-- Pilih Tipe Update --</option>
                                    <option value="article">Level Artikel</option>
                                    <option value="sku">Level SKU</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" id="close_import_btn"
                        data-dismiss="modal">Tutup
                    </button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="import_data_btn">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="MassImgModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_mass_img" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Mass Image Import</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="alert alert-info" role="alert">
                            <strong>Correct ZIP Format:</strong><br>
                            • Inside the ZIP should be the product’s <b>Article ID</b>.<br>
                            • Include the product image files (JPG, PNG).<br>
                            • It is recommended that each file size does not exceed 1MB.<br><br>
                            <b>Example ZIP Structure:</b><br>
                            ├── 9405306/<br>
                            │ ├── 9405306 (1).jpg<br>
                            │ ├── 9405306 (2).jpg<br>
                            ├── 9405307/<br>
                            │ ├── 9405307 (1).jpg<br>
                            │ └── 9405307 (2).jpg
                        </div>
                        <div class="form-group">
                            <label>
                                <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="p_mass_import" id="p_mass_import"
                                required />
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" id="close_import_btn"
                        data-dismiss="modal">Close
                    </button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="import_img_btn">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Product Links -->
<div class="modal fade" id="ProductLinkDetailModal" tabindex="-1" role="dialog"
    aria-labelledby="ProductLinkDetailModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="ProductLinkDetailModalLabel">Product Link Detail</h5>
                <button type="button" class="close close_product_link_detail" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <!-- Link Information -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Link Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Link Type:</strong></div>
                            <div class="col-md-8"><span class="badge badge-primary"
                                    id="detail_link_type"></span></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Platform:</strong></div>
                            <div class="col-md-8" id="detail_platform"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>URL:</strong></div>
                            <div class="col-md-8"><a href="#" target="_blank" class="text-primary"
                                    id="detail_url"></a></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Location:</strong></div>
                            <div class="col-md-8" id="detail_location"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Created By:</strong></div>
                            <div class="col-md-8" id="detail_created_by"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Created At:</strong></div>
                            <div class="col-md-8" id="detail_created_at"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Updated By:</strong></div>
                            <div class="col-md-8" id="detail_updated_by"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Updated At:</strong></div>
                            <div class="col-md-8" id="detail_updated_at"></div>
                        </div>
                    </div>
                </div>

                <!-- Article Colors/SKU Section -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Associated Article Colors & SKU</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>SKU</th>
                                        <th>Color</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="productColorSkuBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold close_product_link_detail"
                    data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    .label-space {
        margin-right: 10cm;
        /* atau 60px, tergantung preferensi */

    }

    .bg-pink {
        background-color: pink !important;
        /* Ubah warna sesuai keinginan */
    }

    .option-yes {
        color: green;
    }

    .option-no {
        color: red;
    }

    input[type="checkbox"] {
        transform: scale(1.5);
        margin: 10px;
    }
</style>
