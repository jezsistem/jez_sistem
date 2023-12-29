<div class="col-xl-2 col-lg-3 col-md-3 sidebarPOS" style="display:none;">
    <div class="card card-custom gutter-b bg-white border-0">
        <div class="card-body" >
            <div class="shop-profile bg-primary">
                <div class="media">
                    <div class="media-body ml-3 mt-3">
                        <h3 class="title font-weight-bold text-white">{{ $data['store']->st_name }}</h3>
                        <p class="phoonenumber text-white">
                            {{ $data['store']->st_phone }}
                        </p>
                        <p class="adddress text-white">
                            {{ $data['store']->st_address }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="resulttable-pos">
                <table class="table right-table">
                    <tbody>
                    <tr class="d-flex align-items-center justify-content-between">
                        <th class="border-0 font-size-h5 mb-0 font-size-bold text-dark">
                                Total Item
                        </th>
                        <td class="border-0 justify-content-end d-flex text-dark font-size-base"><span id="total_item_side">0</span></td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between">
                        <th class="border-0 font-size-h5 mb-0 font-size-bold text-dark">
                                Subtotal
                        </th>
                        <td class="border-0 justify-content-end d-flex text-dark font-size-base"><span id="total_price_side">0</span></td>
                    </tr>
					<tr class="d-flex align-items-center justify-content-between">
                        <th class="border-0 font-size-h5 mb-0 font-size-bold text-dark">
                               Nameset
                        </th>
                        <td class="border-0 justify-content-end d-flex text-dark font-size-base"><span id="total_nameset_side">0</span></td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between">
                        <th class="border-0">
                            <div class="d-flex align-items-center font-size-h5 mb-0 font-size-bold text-dark">
                            Ongkir &nbsp;<span id="shipping_courier_side"></span>
                                <input type="hidden" id="shipping_courier_value"/>
                                <span class="badge badge-primary white rounded-circle ml-2"  data-toggle="modal" data-target="#shippingcost">
                                <svg xmlns="http://www.w3.org/2000/svg" class="svg-sm" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_11" x="0px" y="0px" width="512px" height="512px" viewBox="0 0 512 512" enable-background="new 0 0 512 512" xml:space="preserve">
                                    <g>
                                    <rect x="234.362" y="128" width="43.263" height="256"></rect>
                                    <rect x="128" y="234.375" width="256" height="43.25"></rect>
                                    </g>
                                    </svg>
                                </span>
                            </div>
                        </th>
                        <td class="border-0 justify-content-end d-flex text-dark font-size-base"><span id="shipping_cost_side">0</span></td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between">
                        <th class="border-0">
                            <div class="d-flex align-items-center font-size-h5 mb-0 font-size-bold text-dark">
                                Voucer new &nbsp;<span id="shipping_courier_side"></span>
                                <input type="hidden" id="shipping_courier_value"/>
                                <span class="badge badge-primary white rounded-circle ml-2"  data-toggle="modal" data-target="#voucherModal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="svg-sm" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_11" x="0px" y="0px" width="512px" height="512px" viewBox="0 0 512 512" enable-background="new 0 0 512 512" xml:space="preserve">
                                    <g>
                                    <rect x="234.362" y="128" width="43.263" height="256"></rect>
                                    <rect x="128" y="234.375" width="256" height="43.25"></rect>
                                    </g>
                                    </svg>
                                </span>
                            </div>
                        </th>
                        <td class="border-0 justify-content-end d-flex text-dark font-size-base"><span id="shipping_cost_side">0</span></td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between bg-primary rounded">
                        <th class="border-0 font-size-h5 mb-0 font-size-bold text-white pl-1">
                                Voc
                        </th>
                        <td class="border-0 justify-content-end d-flex text-dark font-size-base pl-1">
                            <form id="f_voucher">
                                <input class="col-10" id="voucher_code" placeholder="Enter stl input" type="text"/>
                                <a href="#" id="cancel_voucher" class="btn-sm btn-danger">X</a>
                            </form>
                        </td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between item-price">
                        <th class="border-0 font-size-h5 mb-0 font-size-bold text-primary">
                                <span class="btn-sm btn-primary">TOTAL</span>
                        </th>
                        <td class="border-0 justify-content-end d-flex text-primary font-size-base"><span id="total_final_price_side">0</span></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end align-items-center flex-column buttons-cash">
                <div> 
                    <a href="#" class="btn btn-primary white mb-2" id="payment_btn" data-toggle="modal" data-target="#payment-online-popup">
                        <i class="fas fa-money-bill-wave mr-2"></i>
                        Bayar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>