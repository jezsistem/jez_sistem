
<!DOCTYPE html>
<html lang="en">
    @include('auth._partials.head')
	<!--begin::Body-->
	<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
		<div class="d-flex flex-column flex-root">
			@include('auth._partials.aside')
			<!--begin::Login-->
			<div class="login login-1 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid" id="kt_login">
				<!--begin::Content-->
				<div class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto">
					<!--begin::Content body-->
					<div class="d-flex flex-column-fluid flex-center" style="background:#d2cbcb; border-radius:20px;">
						<!--begin::Signin-->
						<div class="login-form login-signin">
							<!--begin::Form-->
							<form id="f_login" class="form" novalidate="novalidate" id="kt_login_signin_form" style="padding:20px;">
                                @csrf
								<!--begin::Title-->
								<div class="pb-13 pt-lg-0 pt-5">
									<h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg">User Login</h3>
									<!-- <span class="text-muted font-weight-bold font-size-h4">New Here?
									<a href="javascript:;" id="kt_login_signup" class="text-primary font-weight-bolder">Create an Account</a></span> -->
								</div>
								<!--begin::Title-->
								<!--begin::Form group-->
								<div class="form-group" style="width:300px;">
									<label class="font-size-h6 font-weight-bolder text-dark">Email</label>
									<input class="form-control form-control-solid h-auto py-6 px-6 rounded-lg" type="email" name="u_email" id="u_email" autocomplete="off" required/>
								</div>
								<!--end::Form group-->
								<!--begin::Form group-->
								<div class="form-group" style="width:300px;">
									<div class="d-flex justify-content-between mt-n5">
										<label class="font-size-h6 font-weight-bolder text-dark pt-5">Password</label>
									</div>
									<input class="form-control form-control-solid h-auto py-6 px-6 rounded-lg" type="password" name="password" id="password" autocomplete="off"  required/>
								</div>
								<!--begin::Action-->
								<div class="pb-lg-0 pb-5">
									<button type="submit" id="kt_login_signin_submit" class="float-right btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-2">Login</button>
								</div>
								<!--end::Action-->
							</form>
							<!--end::Form-->
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
        @include('auth._partials.js')
        @include('auth.login_js')
	</body>
	<!--end::Body-->
</html>