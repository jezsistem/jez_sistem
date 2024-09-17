<script>
    $('#f_login').on('submit', function(e) {
        e.preventDefault();
		var data = $(this).serialize();
        var email = $('#u_email').val();
        var password = $('#password').val();
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

        if ($.trim(email)=='') {
            swal("Email","Silahkan input email", "warning");
            return false;
        } else if (!emailReg.test(email)) {
            swal("Email","Silahkan input email sesuai format", "warning");
            return false;
        } else if ($.trim(password)=='') {
            swal("Password","Silahkan input password", "warning");
            return false;
		} else {
		    $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
			$.ajax({
				type: "POST",
				data: data,
				dataType: 'json',
				url: "<?php echo e(url('user_login')); ?>",
				success: function(r) {
					if (r.status=='200') {
						swal('Berhasil','Login berhasil','success');
						setTimeout(() => {
							window.location.href = "redirect";
						}, 600);
					} else if (r.status=='400') {
						swal('Gagal','Email atau password salah','error');
					} else {
						swal('Nonaktif','Status akun anda tidak aktif / dihapus','error');
					}
				}
			});
			return false;
		}
    });
</script><?php /**PATH C:\laragon\www\JEZ_S7\JEZ sistem\jez_sistem\resources\views/auth/login_js.blade.php ENDPATH**/ ?>