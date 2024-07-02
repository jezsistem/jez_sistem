<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
@include('app.pos.pos_head')
<!--end::Head-->
<!--begin::Body-->
<body id="tc_body" class="header-fixed header-mobile-fixed subheader-enabled aside-enabled aside-fixed" style="background-color:#e2e2e2;">
   	<!-- Paste this code after body tag -->
    <div class="se-pre-con">
        <div class="pre-loader">
        <img class="img-fluid" src="{{ asset('pos/images') }}/loadergif.gif" alt="loading">
        </div>
    </div>
	@include('app.pos.pos_header')
	<div class="contentPOS">
		<div class="container-fluid">
			<div class="row">
				<div class="col-xl-10 col-lg-9 col-md-9">
					<div class="">
						<div class="card card-custom gutter-b bg-white border-0 table-contentpos" style="border-radius:10px;">
							<div class="card-body">
								<div class="d-flex justify-content-between colorfull-select">
									<input type="hidden" id="_pt_id" value=""/>
									<input type="hidden" id="_pt_id_complaint" value=""/>
									<input type="hidden" id="_exchange" value=""/>
									<input type="hidden" id="cross_order" value=""/>
									<div class="selectmain">
										<label class="text-dark d-flex font-weight-bold" >Divisi </label>
										<select class="arabic-select select-down " id="std_id" name="std_id">
											<option value="">- Pilih -</option>
											@foreach ($data['std_id'] as $key => $value)
												<option value="{{ $key }}">{{ $value }}</option>
											@endforeach
										</select>
									</div>
									<div class="selectmain bg-primary" style="padding:5px; border-radius:10px;">
										<label class="text-white d-flex font-weight-bold" >Customer
											<span class="badge badge-success white rounded-circle" id="add_customer_btn" data-toggle="modal" data-target="#choosecustomer">
											<svg xmlns="http://www.w3.org/2000/svg" class="svg-sm" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_122" x="0px" y="0px" width="512px" height="512px" viewBox="0 0 512 512" enable-background="new 0 0 512 512" xml:space="preserve">
												<g>
												<rect x="234.362" y="128" width="43.263" height="256"></rect>
												<rect x="128" y="234.375" width="256" height="43.25"></rect>
												</g>
												</svg>
											</span>
										</label>
										<input type="hidden" id="cust_id" value=""/>
										<input type="hidden" id="sub_cust_id" value=""/>
										<input type="search" id="cust_id_label" placeholder="Ketik minimal 4 huruf customer" autocomplete="off"/> <a href="#" class="btn btn-inventory" data-id="" id="check_customer">Check</a>
										<div id="itemListCust"></div><br/>
										<label class="text-white d-flex font-weight-bold" >Sub Customer (isi jika dropshipper)</label>
										<input type="search" id="sub_cust_id_label" placeholder="Ketik minimal 4 huruf sub customer" autocomplete="off"/> <a href="#" class="btn btn-inventory" data-id="" id="check_sub_customer">Check</a>
										<div id="itemListSubCust"></div>
									</div>
									<div class="selectmain">
										<label class="btn-sm btn-primary col-12 rounded text-white d-flex font-weight-bold">Refund / Penukaran</label>
										<input type="search" id="refund_invoice_label" placeholder="Ketik minimal 6 huruf invoice"/>
										<div id="itemListRefund"></div><br/>
										<span class="bg-danger d-none font-weight-bold text-white" id="complaint_info">Anda memiliki transaksi refund yang belum diselesaikan dengan invoice berikut &nbsp;<br/><span id="complaint_invoice"></span>&nbsp;<br/>silahkan pilih kembali invoice pada list kemudian uncheck apabila tidak jadi</span>
									</div>
									<div class="selectmain">
										<label class="btn btn-inventory col-12 rounded text-white d-flex font-weight-bold">Store</label>
										<select class="arabic-select-store select-down " id="st_id" name="st_id">
											<option value="">- Store -</option>
											@foreach ($data['st_id'] as $key => $value)
												<option value="{{ $key }}">{{ $value }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="card card-custom gutter-b bg-white border-0 table-contentpos" id="posContent" style="display:none;">
							<div class="card-body bg-primary rounded">
								<div class="form-group row mb-0">
									<div class="col-md-12">
										<label class="text-white">Produk</label>
										<fieldset class="form-group mb-0 d-flex barcodeselection">
											<!-- <input type="text" class="form-control border-dark col-4 mr-2" id="product_barcode_input" placeholder="scan barcode"> -->
											<input style="background:#fef6df; color:black;" type="text" class="form-control border-dark col-8 mr-2" id="product_name_input" placeholder="Ketik minimal 3 huruf pertama nama artikel" autocomplete="off">
											<input type="text" class="form-control border-dark col-4" id="invoice_input" placeholder="Invoice (ketik 5 angka invoice)">
										</fieldset>
										<div id="itemList"></div>
									</div>
								</div>
							</div>
							<br/>
							<div class="table-datapos">
								<div class="table-responsive" id="printableTable">
									<input type="hidden" id="total_row" value="0"/>
									<table id="orderTable" class="display table table-hover" style="width:100%">
										<thead class="bg-primary">
											<tr>
												<th class="text-white">Produk</th>
												<th class="text-white">BIN</th>
												<th class="text-white">Disc(%)</th>
												<th class="text-white">Disc(Rp)</th>
												<th class="text-white">Jml Beli</th>
												<th class="text-white">Nameset</th>
												<th class="text-white">Marketplace</th>
												<th class="text-white">Harga</th>
												<th class="text-white">Subtotal</th>
												<th class="text-right no-sort"></th>
											</tr>
										</thead>
										<tbody>
	
										</tbody>
									</table>
								</div>
								<div id="voucher_information" class='d-none'>
								<table id="orderTable" class="display table table-hover" style="width:100%">
									<input type="hidden" id="_voc_pst_id"/>
									<input type="hidden" id="_voc_value"/>
									<input type="hidden" id="_voc_id"/>
									<thead class="bg-primary">
									<tr class="d-flex align-items-center justify-content-between pl-3 pr-3" style="background:#fef6df;">
										<th class="col-6">
												Produk
										</th>
										<th class="col-6">
												<span id="_voc_article"></span>
										</th>
									</tr>
									<tr class="d-flex align-items-center justify-content-between pl-3 pr-3" style="background:#fef6df;">
										<th class="col-6">
												Bandrol
										</th>
										<th class="col-6">
											<span id="_voc_bandrol"></span>
										</th>
									</tr>
									<tr class="d-flex align-items-center justify-content-between pl-3 pr-3" style="background:#fef6df;">
										<th class="col-6">
												Disc
										</th>
										<th class="col-6">
											<span id="_voc_disc"></span> <span id="_voc_disc_type"></span> <span id="_voc_disc_value"></span>
										</th>
									</tr>
									<tr class="d-flex align-items-center justify-content-between pl-3 pr-3" style="background:#fef6df;">
										<th class="col-6">
												Harga Baru
										</th>
										<th class="col-6">
											<span id="_voc_value_show"></span>
										</th>
									</tr>
									</thead>
								</table>
								</div>
							</div>
							<div class="card-body" >

							</div>
						</div>
					</div>
				</div>
				@include('app.pos.pos_sidebar')
			</div>
		</div>
	</div>
@include('app.pos.pos_modal')
@include('app.pos.pos_js')
<script>
	jQuery(function() {
		jQuery('.arabic-select').multipleSelect({
			filter: true,
			filterAcceptOnEnter: false
		})
	});
	jQuery(function() {
		jQuery('.arabic-select-store').multipleSelect({
			filter: true,
			filterAcceptOnEnter: false
		})
	});
	jQuery(function() {
		jQuery('.js-example-basic-single').multipleSelect({
			filter: true,
			filterAcceptOnEnter: false
		})
	});
</script>
</body>
<!--end::Body-->
</html>