<div class="col-xl-3 col-lg-4 col-md-4 sidebarPOS" style="display:none;">
    <div class="card card-custom gutter-b bg-white border-0">
        <div class="card-body" >
            <div class="shop-profile">
                <div class="media">
                    <div class="bg-primary w-100px h-100px d-flex justify-content-center align-items-center">
                        <h2 class="mb-0 white">TOPS</h2>
                    </div>
                    <div class="media-body ml-3">
                        <h3 class="title font-weight-bold">{{ $data['store']->st_name }}</h3>
                        <p class="phoonenumber">
                            {{ $data['store']->st_phone }}
                        </p>
                        <p class="adddress">
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
                    <tr class="d-flex align-items-center justify-content-between bg-primary rounded">
                        <th class="border-0 font-size-h5 mb-0 font-size-bold text-white pl-2">
                                Voucher
                        </th>
                        <td class="border-0 justify-content-end d-flex text-dark font-size-base pl-4">
                            <form id="f_voucher">
                                <input class="col-10" id="voucher_code" placeholder="Enter stl input" type="text"/>
                                <a href="#" id="cancel_voucher" class="btn-sm btn-danger ml-2">X</a>
                            </form>
                        </td>
                    </tr>
                    <tr class="d-flex align-items-center justify-content-between item-price pl-3 pr-3" style="background:#fef6df;">
                        <th class="border-0 font-size-h5 mb-0 font-size-bold text-primary">
                                TOTAL
                        </th>
                        <td class="border-0 justify-content-end d-flex text-primary font-size-base"><span id="total_final_price_side">0</span></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div>
                
            </div>
            <div class="d-flex justify-content-end align-items-center flex-column buttons-cash">
                <div>
                    <a href="#" class="btn btn-primary white mb-2" id="payment_btn">
                        <i class="fas fa-money-bill-wave mr-2"></i>
                        Bayar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>