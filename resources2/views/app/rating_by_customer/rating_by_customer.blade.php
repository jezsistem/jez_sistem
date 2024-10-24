<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
@include('app._partials.head')
<!--end::Head-->
@if (request()->segment(1) == 'rating_app')
<body id="device_background_panel">
@else 
<body class="d-none" id="device_background_panel">
@endif
    <div class="d-flex flex-column-fluid flex-center" id="device_background">
        <form id="f_rating" class="form" novalidate="novalidate">
            @csrf
            <input type="hidden" value="" id="rating_mode"/>
            <input type="hidden" value="" id="ur_id"/>
            <input type="hidden" value="" id="cust_id"/>
            <center>
            <div style="width:100%;">
                <h3 class="font-weight-bolder text-white font-size-h1-lg" style="font-size:22px;">Terimakasih telah berbelanja</h3>
                <p class="font-weight-bolder text-white">Silahkan beri penilaian Anda tentang produk dan pelayanan kami.</p>

                <h3 class="font-weight-bolder text-white font-size-h1-lg">Rating</h3>
                <p class="font-weight-bolder text-white">Kasir tidak mengetahui rating yang Anda berikan. </br>Silahkan beri rating <span style="color:#F2C94C;">paling jujur</span>, ya!</p>
                <div class="rating-css">
                    <div class="star-icon">
                    <input type="radio" name="rating1" id="rating1">
                    <label for="rating1" class="fa fa-star"></label>
                    <input type="radio" name="rating1" id="rating2">
                    <label for="rating2" class="fa fa-star"></label>
                    <input type="radio" name="rating1" id="rating3">
                    <label for="rating3" class="fa fa-star"></label>
                    <input type="radio" name="rating1" id="rating4">
                    <label for="rating4" class="fa fa-star"></label>
                    <input type="radio" name="rating1" id="rating5">
                    <label for="rating5" class="fa fa-star"></label>
                    <input type="hidden" id="rating_value" value="" />
                    </div>
                </div>
                <h3 class="font-weight-bolder text-white font-size-h1-lg">" <span id="rating_label">Pilih Bintang</span> "</h3>
            </div>
            <div style="width:80%;">
                <div class="form-group">
                    <label class="font-size-h6 font-weight-bolder text-white float-left">Nama Lengkap</label>
                    <input class="form-control form-control-solid h-auto py-5 px-5 rounded-lg" type="text" name="cust_name" id="cust_name" autocomplete="off" required/>
                </div>
                <div class="form-group">
                    <label class="font-size-h6 font-weight-bolder text-white float-left">No. HP / Whatsapp</label>
                    <input class="form-control form-control-solid h-auto py-5 px-5 rounded-lg" type="number" name="cust_phone" id="cust_phone" autocomplete="off" placeholder="08xxx" required/>
                </div>
                <div class="form-group">
                    <label class="font-size-h6 font-weight-bolder text-white float-left">Kecamatan</label>
                    <input class="form-control form-control-solid h-auto py-5 px-5 rounded-lg" type="search" name="cust_subdistrict_label" id="cust_subdistrict_label" placeholder="Pilih Kecamatan" autocomplete="off" required/>
                    <input type="hidden" name="cust_subdistrict" id="cust_subdistrict"/>
                    <div id="subdistrictList"></div>
                </div>
                <div class="form-group">
                    <label class="font-size-h6 font-weight-bolder text-white float-left">Alasan Rating</label>
                    <textarea class="form-control form-control-solid h-auto py-5 px-5 rounded-lg mb-2" name="ur_description" id="ur_description" autocomplete="off" placeholder="Tulis alasan"></textarea>
                    <span class="btn btn-sm btn-white" style="color:#000; margin-top:5px;" id="sikap_choice">Sikap</span>
                    <span class="btn btn-sm btn-white" style="color:#000; margin-top:5px;" id="pelayanan_choice">Pelayanan</span>
                    <span class="btn btn-sm btn-white" style="color:#000; margin-top:5px;" id="komunikasi_choice">Komunikasi</span>
                    <span class="btn btn-sm btn-white" style="color:#000; margin-top:5px;" id="penampilan_choice">Penampilan</span>
                    <span class="btn btn-sm btn-white" style="color:#000; margin-top:5px;" id="produk_choice">Pengetahuan Produk</span>
                </div>
                <button type="submit" id="kt_login_signin_submit" class="btn btn-warning font-weight-bolder font-size-h3 px-8 py-4 my-3 mr-2 col-12" style="background-color:#F2C94C;">Kirim</button>
            </div>
            </center>
        </form>
    </div>
    @include('app.rating_by_customer.rating_by_customer_modal')
    @include('app._partials.js')
    @include('app.rating_by_customer.rating_by_customer_js')
</body>
</html>