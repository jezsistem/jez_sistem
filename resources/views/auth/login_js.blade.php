<script>
    // Konfigurasi Toastr
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    // Kode login
    $('#f_login').on('submit', function(e) {
        e.preventDefault(); // Mencegah submit default
        var data = $(this).serialize();
        var email = $('#u_email').val();
        var password = $('#password').val();
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

        // Validasi input
        if ($.trim(email) == '') {
            toastr.warning("Silahkan input email", "Email");
            return false;
        } else if (!emailReg.test(email)) {
            toastr.warning("Silahkan input email sesuai format", "Email");
            return false;
        } else if ($.trim(password) == '') {
            toastr.warning("Silahkan input password", "Password");
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
                url: "{{ url('user_login') }}",
                success: function(r) {
                    if (r.status == '200') {
                        toastr.success('Login berhasil', 'Berhasil');
                        setTimeout(() => {
                            window.location.href = "redirect"; 
                        }, 600);
                    } else if (r.status == '400') {
                        toastr.error('Email atau password salah', 'Gagal');
                    } else {
                        toastr.error('Status akun anda tidak aktif / dihapus', 'Nonaktif');
                    }
                }
            });
            return false;
        }
    });
</script>
