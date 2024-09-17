<div class="modal fade text-left" id="choosecustomer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel13" style="display: none;" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
		<div class="modal-content">
		  <div class="modal-header bg-light">
			<h4 class="modal-title text-white" id="myModalLabel13">Customer</h4>
			<button type="button" class="close rounded-pill btn btn-sm btn-icon btn-light btn-hover-primary m-0" data-dismiss="modal" aria-label="Close">
			  <svg width="20px" height="20px" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
				  <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
			  </svg>
			</button>
		  </div>
		  <div class="modal-body bg-white">
			<form id="f_customer">
				<input type="hidden" id="_mode" name="_mode"/>
				<input type="hidden" id="_id" name="_id"/>
				<div class="form-group row">
					<div class="col-md-12">
						<label  class="text-body">Tipe Customer</label>
						<fieldset class="form-group mb-3">
							<select class="js-states form-control bg-transparent p-0 border-0" id="ct_id" name="ct_id" required>
								<option value="">- Pilih -</option>
								<?php $__currentLoopData = $data['ct_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</select>
						</fieldset>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-12">
						<label  class="text-body">Nama Customer *</label>
						<fieldset class="form-group mb-3">
							<input type="text" id="cust_name" name="cust_name"  class="form-control"  placeholder="Nama" required>
						</fieldset>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-12">
						<label  class="text-body">Toko</label>
						<fieldset class="form-group mb-3">
							<input type="text" id="cust_store" name="cust_store"  class="form-control"  placeholder="Isi jika dropshipper">
						</fieldset>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-12">
						<label  class="text-body">No Telp </label>
						<fieldset class="form-group mb-3">
							<input type="number" id="cust_phone" name="cust_phone"  class="form-control"  placeholder="No HP">
						</fieldset>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-12">
						<label  class="text-body">Email</label>
						<fieldset class="form-group mb-3">
							<input type="text" id="cust_email" name="cust_email"  class="form-control"  placeholder="Email">
						</fieldset>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-12">
						<label  class="text-body">Provinsi *</label>
						<fieldset class="form-group mb-3">
							<select class="form-control" id="cust_province" name="cust_province">
								<option value="">- Pilih -</option>
								<?php $__currentLoopData = $data['cust_province']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</select>
						</fieldset>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-12">
						<label  class="text-body">Kota *</label>
						<fieldset class="form-group mb-3">
							<select class="form-control" id="cust_city" name="cust_city">
								<option value="">- Pilih -</option>
							</select>
						</fieldset>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-12">
						<label  class="text-body">Kecamatan *</label>
						<fieldset class="form-group mb-3">
							<select class="form-control" id="cust_subdistrict" name="cust_subdistrict">
								<option value="">- Pilih -</option>
							</select>
						</fieldset>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-12">
						<label  class="text-body">Alamat</label>
						<fieldset class="form-group mb-3">
							<input type="text" id="cust_address" name="cust_address" class="form-control"  placeholder="Alamat">
						</fieldset>
					</div>
				</div>
				<div class="form-group row justify-content-end mb-0">
					<div class="col-md-6  text-right">
						<button type="submit" class="btn btn-primary" id="save_customer_btn">Simpan</button>
					</div>
				</div>
			</form>
		  </div>
		</div>
	</div>
</div>

<div class="modal fade text-left" id="shippingcost" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1444" style="display: none;" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
		  <div class="modal-header bg-light">
			<h3 class="modal-title text-white" id="myModalLabel1444">Tambah Ongkir</h3>
			<button type="button" class="close rounded-pill btn btn-sm btn-icon btn-light btn-hover-primary m-0" data-dismiss="modal" aria-label="Close">
			  <svg width="20px" height="20px" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
				  <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
			  </svg>
			</button>
		  </div>
		  <div class="modal-body">
			<form id="f_ongkir">
				<div class="form-group row">
					<div class="col-md-6">
						<label  class="text-body">Kurir</label>
						<fieldset class="form-group mb-3">
							<select name="courier" id="courier"  class="form-control">
								<option value="">- Pilih -</option>
								<?php $__currentLoopData = $data['courier']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($key); ?>" ><?php echo e($value); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</select>
						</fieldset>
					</div>
					<div class="col-md-6">
						<label  class="text-body">Total Ongkir</label>
						<fieldset class="form-group mb-3">
							<input type="number" name="shipping_cost" id="shipping_cost"  class="form-control"  placeholder="Total ongkir " value="">
						</fieldset>
					</div>
				</div>
				<div class="form-group row justify-content-end mb-0">
					<div class="col-md-6  text-right">
						<button type="submit" class="btn btn-primary">Tambah</button>
					</div>
				</div>
			</form>
		  </div>
		</div>
	</div>
</div>

<div class="modal fade text-left" id="payment-online-popup" role="dialog" aria-labelledby="myModalLabel11" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered modal-md" role="document">
		<div class="modal-content">
		  <div class="modal-header bg-light">
			<h3 class="modal-title text-white" id="myModalLabel11">Pembayaran</h3>
			<button type="button" class="close rounded-pill btn btn-sm btn-icon btn-light btn-hover-primary m-0" data-dismiss="modal" aria-label="Close">
			  <svg width="20px" height="20px" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
				  <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
			  </svg>
			</button>
		  </div>
		  <div class="modal-body bg-white">
			<table class="table right-table">
				<tbody>
					<tr class="d-flex align-items-center justify-content-between">
						<th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
								No. Pesanan 
						</th>
						<td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
							<input type="text" placeholder="INVxxxxx" id="order_code" class="bg-light-primary"/>
						</td>
					</tr>
					<tr class="d-flex align-items-center justify-content-between">
						<th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
								Total 
						</th>
						<td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
								<span id="payment_total"></span>
						</td>
					</tr>
					<tr class="d-flex align-items-center justify-content-between" id="payment_type_content">
						<th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
								Tipe Pembayaran 
						</th>
						<td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
							<select id="pm_id">
									<?php $__currentLoopData = $data['payment_method']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<?php if(strtolower($value) == 'cash'): ?>
										<option value="<?php echo e($key); ?>" selected><?php echo e($value); ?></option>
										<?php else: ?>
										<option value="<?php echo e($key); ?>" ><?php echo e($value); ?></option>
										<?php endif; ?>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</select>
							<div id="pm_id_parent"></div>
						</td>
					</tr>
					<tr class="d-flex align-items-center justify-content-between" id="card_provider_content">
						<th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
								Rek Tujuan (Jika WA/Web)
						</th>
						<td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
							<select id="cp_id">
									<option value="" >- Pilih -</option>
									<?php $__currentLoopData = $data['cp_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($key); ?>" ><?php echo e($value); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</select>
						</td>
					</tr>
					<tr class="d-flex align-items-center justify-content-between" id="ref_number_label">
						<th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
							<span class="btn-sm btn-info">Kode Referensi</span>
						</th>
						<td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
							<input class="bg-light-primary" type="text" placeholder="" id="ref_number"/>
						</td>
					</tr>
					<tr class="d-flex align-items-center justify-content-between" id="marketplace_total_tr">
						<th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
								Total Harga Marketplace 
						</th>
						<td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
							<input type="text" placeholder="" id="marketplace_side" readonly/>
						</td>
					</tr>
					<tr class="d-flex align-items-center justify-content-between" id="marketplace_selisih_tr">
						<th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
								Selisih (Harga Jual dan Marketplace) 
						</th>
						<td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
							<input type="text" placeholder="" id="marketplace_sell_price" readonly/>
						</td>
					</tr>
					<tr class="d-flex align-items-center justify-content-between">
						<th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
								Kode Unik 
						</th>
						<td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
							<input type="text" placeholder="" id="unique_code" class="bg-light-primary"/>
						</td>
					</tr>
					<tr class="d-flex align-items-center justify-content-between">
						<th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
								Total Bayar (Total + Kode Unik) 
						</th>
						<td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
							<input type="text" placeholder="" id="final_total_unique_code" readonly/>
						</td>
					</tr>
					<tr class="d-flex align-items-center justify-content-between">
						<th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
							Diskon Penjual (-)
						</th>
						<td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
							<input type="number" placeholder="" id="discount_seller" class="bg-light-primary"/>
						</td>
					</tr>
					<tr class="d-flex align-items-center justify-content-between">
						<th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
								Biaya Admin (-) 
						</th>
						<td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
							<input type="text" placeholder="" id="admin_cost" class="bg-light-primary"/>
						</td>
					</tr>
					<tr class="d-flex align-items-center justify-content-between">
						<th class="border-0 px-0 font-size-lg mb-0 font-size-bold text-primary">
								Biaya Lain-Lain (+) 
						</th>
						<td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
							<input type="text" placeholder="" id="another_cost" class="bg-light-primary"/>
						</td>
					</tr>
					<tr class="d-flex align-items-center justify-content-between">
						<th class="border-0 px-0 font-size-lg mb-0 font-size-bold pr-2 pl-2 btn-primary rounded">
								Harga Total 
						</th>
						<td class="border-0 justify-content-end d-flex text-primary font-size-lg font-size-bold px-0 font-size-lg mb-0 font-size-bold text-primary">
							<input type="text" placeholder="" id="real_price" readonly/>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="form-group row">
				<div class="col-md-12">
					<label  class="text-body">Catatan (Jika ada)</label>
					<fieldset class="form-label-group ">
						<textarea class="form-control fixed-size" id="note" rows="5" placeholder="Enter Note"></textarea>
					</fieldset>
				</div>
			</div>
			<div class="form-group row justify-content-end mb-0">
				<div class="col-md-12  text-right">
					<a href="#" class="btn btn-primary" id="save_transaction">Checkout</a>
				</div>
			</div>
		  </div>
		</div>
	</div>
</div>


<!-- Modal-->
<div class="modal fade" id="RefundExchangeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Refund / Penukaran [<span id="refund_retur_invoice_label">INV0000000</span>]</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body table-responsive">
				<input id="refund_retur_pt_id" type="hidden"/>
				<table class="table table-hover table-checkable" id="RefundReturtb">
					<thead class="bg-light text-dark">
						<tr>
							<th class="text-dark">No</th>
							<th class="text-dark">Artikel</th>
							<th class="text-dark">Tanggal Trx</th>
							<th class="text-dark">Qty</th>
							<th class="text-dark">Price</th>
							<th class="text-dark">Action</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark font-weight-bold" data-dismiss="modal">Selesai</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal -->
<div class="modal fade text-left" id="voucherModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1444" style="display: none;" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-light">
				<h3 class="modal-title text-primary" id="myModalLabel1444">Tambah Voucher</h3>
				<button type="button" class="close rounded-pill btn btn-sm btn-icon btn-light btn-hover-primary m-0" data-dismiss="modal" aria-label="Close">
					<svg width="20px" height="20px" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
					</svg>
				</button>
			</div>
			<div class="modal-body">
				<form id="f_add_voucher">
					<div class="form-group row">
						<div class="col-md-6">
							<label class="text-body">Voucher</label>
							<fieldset class="form-group mb-3">
								<div id="voucher-container">
									<div class="input-group mb-3">
										<input type="text" name="voucher-list[]" class="form-control" placeholder="Kode Voucher" value="">
										<div class="input-group-append ml-3">
											<button class="btn btn-xs btn-outline-secondary add-voucher" type="button">+</button>
										</div>
									</div>
								</div>
							</fieldset>
						</div>
					</div>

					<div class="form-group row justify-content-end mb-0">
						<div class="col-md-6  text-right">
							<button type="submit" class="btn btn-primary">Tambah</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- /Modal -->

<!-- Modal -->
<div class="modal fade text-left" id="totalDiscountModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1444" style="display: none;" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-light">
				<h3 class="modal-title text-primary" id="myModalLabel1444">Tambah Diskon</h3>
				<button type="button" class="close rounded-pill btn btn-sm btn-icon btn-light btn-hover-primary m-0" data-dismiss="modal" aria-label="Close">
					<svg width="20px" height="20px" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
					</svg>
				</button>
			</div>
			<div class="modal-body">
				<form id="f_add_total_discount">
							<div class="form-group col-md-6">
								<label  class="text-body">Tipe Diskon</label>
								<fieldset class="form-group mb-3">
									<select class="form-control" name="discount-type-list">
										<option value="nominal">Nominal</option>
										<option value="percentage">Percentage</option>
									</select>
								</fieldset>
							</div>
							<div class="col-md-6">
								<label class="text-body">Diskon</label>
								<fieldset class="form-group mb-3">
									<div id="total-discount-container">
										<div class="input-group mb-3">
											<input type="text" name="total-discount-list[]" class="form-control" placeholder="Diskon" value="">
											<div class="input-group-append ml-3">
												<button class="btn btn-xs btn-outline-secondary add-total-discount" type="button">+</button>
											</div>
										</div>
									</div>
								</fieldset>
							</div>
					<div class="form-group row justify-content-end mb-0">
						<div class="col-md-6 text-right">
							<button type="button" class="btn btn-warning" id="total_discount_reset">Reset</button>
							<button type="submit" class="btn btn-primary">Tambah</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- /Modal -->

<!-- Modal -->
<div class="modal fade text-left" id="shiftEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel13" style="display: none;" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header bg-light">
				<h4 class="modal-title text-dark" id="myModalLabel13">Shift Employee</h4>
				<button type="button" class="close rounded-pill btn btn-sm btn-icon btn-light btn-hover-primary m-0" data-dismiss="modal" aria-label="Close">
					<svg width="20px" height="20px" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
					</svg>
				</button>
			</div>
			<div class="modal-body">
				<form id="f_shift_customer">
					<div class="btn-group" role="group">
						<button type="button" class="btn btn-primary" id="startShiftButton">Start Shift</button>
						<button type="button" class="btn btn-danger" id="stopShiftButton" style="display: none;">Stop Shift</button>
					</div>
					<div id="shiftStatus">Shift not started</div>
					<div class="clock"></div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- /Modal --><?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/pos/pos_modal.blade.php ENDPATH**/ ?>