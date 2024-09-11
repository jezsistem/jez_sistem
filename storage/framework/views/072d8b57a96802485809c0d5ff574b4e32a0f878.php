
<!DOCTYPE html>
<html lang="en">
    <?php echo $__env->make('auth._partials.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	<!--begin::Body-->
	<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
		<div class="d-flex flex-column flex-root">
			<!--begin::Login-->
			<div class="login login-1 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid" id="kt_login">
				<!--begin::Content-->
				<div class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden mx-auto">
					<!--begin::Content body-->
					<div class="d-flex flex-center">
						<!--begin::Signin-->
						<div class="login-form login-signin">
							<!--begin::Form-->
							<a href="#" class="d-flex flex-center pt-5 mb-6">
								<img src="<?php echo e(asset('logo')); ?>/LOGOJEZ.png" class="h-36px" alt="" />
							</a>
							<form id="f_login" class="form" novalidate="novalidate" id="kt_login_signin_form" style="padding:20px;">
                                <?php echo csrf_field(); ?>
								<!--begin::Title-->
								<h3 class="font-weight-bolder text-dark text-center font-size-h4 font-size-h2-lg">Silahkan Login terlebih dahulu.</h3>
								<div class="pb-10 pt-lg-0 pt-4">
									<!-- <span class="text-muted font-weight-bold font-size-h4">New Here?
									<a href="javascript:;" id="kt_login_signup" class="text-primary font-weight-bolder">Create an Account</a></span> -->
								</div>
								<!--begin::Title-->
								<!--begin::Form group-->
								<div class="form-group" style="width:420px;">
									<label class="font-size-h6 font-weight-bolder text-dark">Email</label>
									<input class="form-control form-control-solid h-auto py-6 px-6 rounded-lg" type="email" name="u_email" id="u_email" autocomplete="off" required/>
								</div>
								<!--end::Form group-->
								<!--begin::Form group-->
								<div class="form-group" style="width:420px;">
									<div class="d-flex justify-content-between mt-n5">
										<label class="font-size-h6 font-weight-bolder text-dark pt-5">Password</label>
									</div>
									<input class="form-control form-control-solid h-auto py-6 px-6 rounded-lg" type="password" name="password" id="password" autocomplete="off"  required/>
								</div>
								<!--begin::Action-->
								<div class="pb-lg-0 pb-5">
									<button type="submit" id="kt_login_signin_submit" class="float-right btn btn-primary font-weight-bold fs-4 px-8 py-4 my-3 mr-2">Login</button>
								</div>
								<!--end::Action-->
							</form>
							<!--end::Form-->
						</div>
						<!--end::Signin-->
					</div>
					<!--end::Content body-->
        			<?php echo $__env->make('auth.login_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
				</div>
				<!--end::Content-->
			</div>
			<!--end::Login-->
		</div>
		<!--end::Main-->
        <?php echo $__env->make('auth._partials.js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('auth.login_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	</body>
	<!--end::Body-->
</html><?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/auth/login.blade.php ENDPATH**/ ?>