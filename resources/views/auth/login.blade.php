<!DOCTYPE html>
<html lang="en">
@include('auth._partials.head')
<style>
    #kt_login {
        background: url('{{ url('/') === 'https://jezpro.id' ? asset('app/assets/media/misc/bg-login.jpg') : asset('app/assets/media/misc/login_jezpro.jpg') }}');
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
    }
    
    /* Google Login Styling */
    .divider {
        position: relative;
        text-align: center;
        margin: 20px 0;
    }
    
    .divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: #e1e5e9;
    }
    
    .divider-text {
        background: white;
        padding: 0 15px;
        color: #6c757d;
        font-size: 14px;
        font-weight: 500;
    }
    
    .btn-google {
        transition: all 0.3s ease;
        border: 2px solid #dc3545;
        color: #dc3545;
        background: white;
    }
    
    .btn-google:hover {
        background: #dc3545;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }
    
    .btn-google i {
        font-size: 18px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .btn-google {
            width: 100% !important;
            max-width: 420px;
        }
    }
</style>
<!--begin::Body-->

<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<body id="kt_body"
    class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
    <div class="d-flex flex-column flex-root">
        <!--begin::Login-->
        <div class="login login-1 login-signin-on d-flex flex-md-column flex-lg-row flex-column-fluid" id="kt_login">
            <!--begin::Content-->
            <div
                class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden mx-md-auto mx-6">
                <!--begin::Content body-->
                <div class="d-flex flex-center">
                    <!--begin::Signin-->
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-12 col-sm-10 col-md-8 col-lg-5 login-form login-signin">
                                <!--begin::Logo-->
                                <a href="#" class="d-flex flex-center pt-5 mb-6">
                                    <img src="{{ asset('logo') }}/LOGOJEZ.png" class="h-36px" alt="Logo" />
                                </a>
                                <!--end::Logo-->

                                <!--begin::Form-->
                                <form id="f_login" class="form mx-auto w-100 px-3 px-md-5" style="padding: 20px;" novalidate>
                                    @csrf

                                    <!--begin::Title-->
                                    <h3 class="font-weight-bolder text-dark text-center font-size-h4 font-size-h2-lg">
                                        Silahkan Login terlebih dahulu.
                                    </h3>
                                    <div class="pb-10 pt-lg-0 pt-4"></div>

                                    <!--begin::Form group-->
                                    <div class="form-group">
                                        <label class="font-size-h6 font-weight-bolder text-dark">Email</label>
                                        <input class="form-control form-control-solid h-auto py-6 px-6 rounded-lg"
                                            type="email" name="u_email" id="u_email" autocomplete="off" required />
                                    </div>

                                    <div class="form-group">
                                        <div class="d-flex justify-content-between mt-n5">
                                            <label class="font-size-h6 font-weight-bolder text-dark pt-5">Password</label>
                                        </div>
                                        <input class="form-control form-control-solid h-auto py-6 px-6 rounded-lg"
                                            type="password" name="password" id="password" autocomplete="off" required />
                                    </div>

                                    <!--begin::Action-->
                                    <div class="pb-lg-0 pb-5 mt-5 mb-5">
                                        <button type="submit" id="kt_login_signin_submit"
                                            class="btn btn-primary btn-lg font-weight-bold px-8 py-4 w-100">Login</button>
                                    </div>
                                    <!--end::Action-->
                                </form>
                                <!--end::Form-->
                            </div>
                        </div>
                    </div>
                    <!--end::Signin-->
                </div>
                <!--end::Content body-->
                @include('auth.login_modal')
            </div>
            <!--end::Content-->
        </div>
        <!--end::Login-->
    </div>
    <!--end::Main-->
    @include('auth._partials.js') <!-- Include your other JS files here -->
    @include('auth.login_js') <!-- Include your login JS file here -->
</body>
<!--end::Body-->
</html>
