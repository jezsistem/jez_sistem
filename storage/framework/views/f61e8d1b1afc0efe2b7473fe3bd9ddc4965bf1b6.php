<script>
    $(document).ready(function() {
        $('#ur_description').val('');
        $('.rating-css div').css('color', '#fff');
        var imageUrl1 = "<?php echo e(asset('upload/background/bg-blue.png')); ?>";
        var imageUrl2 = "<?php echo e(asset('upload/background/bg-customer.png')); ?>";
        if ($(window).width() <= 768) {
            $('#device_background').css('background', 'url(' + imageUrl2 + ')');
            $('#device_background').css('background-repeat', 'no-repeat');
            $('#device_background').css('background-attachment', 'fixed');
            $('#device_background').css('background-position', 'center');
        } else {
            $('#device_background').css('background-image', 'url(' + imageUrl1 + ')');
            $('#device_background').css('background-repeat', 'no-repeat');
            $('#device_background').css('background-attachment', 'fixed');
            $('#device_background').css('background-position', 'center');
        }
        <?php if(request()->segment(1) != 'rating_app'): ?>
        setInterval(() => {
            var rating_mode = $('#rating_mode').val();
            jQuery.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                url:"<?php echo e(url('check_waiting_for_review')); ?>",
                method:"POST",
                dataType:"JSON",
                success:function(r){
                    if (r.status == '200') {
                        if (rating_mode == '') {
                            if ($('#device_background_panel').hasClass('d-none')) {
                                $('#device_background_panel').removeClass('d-none');
                            }
                            $('#rating_mode').val('1');
                            $('#ur_id').val(r.ur_id);
                        }
                    } else {
                        $('#rating_mode').val('');
                        setTimeout(() => {
                            $('#f_rating')[0].reset();
                            $('.rating-css div').css('color', '#fff');
                            $('#rating_label').text('Pilih Bintang');
                            if (!$('#device_background_panel').hasClass('d-none')) {
                                $('#device_background_panel').addClass('d-none');
                            }
                        }, 3000);   
                    }
                }
            });
        }, 3000);
        <?php endif; ?>
        $("#cust_subdistrict_label").focus(function() {
            $('html, body').animate({
                scrollTop: $(this).offset().top + 200
            }, 500);
        });

        $(document).delegate('body', 'click', function(e) {
            $('#subdistrictList').fadeOut();
        });

        $(document).delegate('#rating1', 'click', function() {
            $('.rating-css div').css('color', '#ffe400');
            $('#rating_label').text('Sangat Buruk');
            $('#rating_value').val('1');
        });
        $(document).delegate('#rating2', 'click', function() {
            $('.rating-css div').css('color', '#ffe400');
            $('#rating_label').text('Buruk');
            $('#rating_value').val('2');
        });
        $(document).delegate('#rating3', 'click', function() {
            $('.rating-css div').css('color', '#ffe400');
            $('#rating_label').text('Lumayan');
            $('#rating_value').val('3');
        });
        $(document).delegate('#rating4', 'click', function() {
            $('.rating-css div').css('color', '#ffe400');
            $('#rating_label').text('Bagus');
            $('#rating_value').val('4');
        });
        $(document).delegate('#rating5', 'click', function() {
            $('.rating-css div').css('color', '#ffe400');
            $('#rating_label').text('Sangat Bagus');
            $('#rating_value').val('5');
        });

        $(document).delegate('#sikap_choice', 'click', function() {
            var ur_description = $('#ur_description').val();
            if ($.trim(ur_description) == '') {
                $('#ur_description').val('sikap');
            } else {
                $('#ur_description').val(ur_description+' sikap');
            }
        });

        $(document).delegate('#pelayanan_choice', 'click', function() {
            var ur_description = $('#ur_description').val();
            if ($.trim(ur_description) == '') {
                $('#ur_description').val('pelayanan');
            } else {
                $('#ur_description').val(ur_description+' pelayanan');
            }
        });

        $(document).delegate('#komunikasi_choice', 'click', function() {
            var ur_description = $('#ur_description').val();
            if ($.trim(ur_description) == '') {
                $('#ur_description').val('komunikasi');
            } else {
                $('#ur_description').val(ur_description+' komunikasi');
            }
            
        });

        $(document).delegate('#penampilan_choice', 'click', function() {
            var ur_description = $('#ur_description').val();
            if ($.trim(ur_description) == '') {
                $('#ur_description').val('penampilan');
            } else {
                $('#ur_description').val(ur_description+' penampilan');
            }
        });

        $(document).delegate('#produk_choice', 'click', function() {
            var ur_description = $('#ur_description').val();
            if ($.trim(ur_description) == '') {
                $('#ur_description').val('pengetahuan produk');
            } else {
                $('#ur_description').val(ur_description+' pengetahuan produk');
            }
        });
        
        $(document).delegate('#cust_name', 'focus', function() {
            if ($('#rating_value').val() == '') {
                $(this).val('');
                swal('Pilih Bintang', 'Silahkan pilih bintang terlebih dahulu', 'warning');
            }
        });

        $(document).delegate('#cust_phone', 'focus', function() {
            if ($('#rating_value').val() == '') {
                $(this).val('');
                swal('Pilih Bintang', 'Silahkan pilih bintang terlebih dahulu', 'warning');
            }
        });

        $(document).delegate('#cust_subdistrict_label', 'focus', function() {
            if ($('#rating_value').val() == '') {
                $(this).val('');
                swal('Pilih Bintang', 'Silahkan pilih bintang terlebih dahulu', 'warning');
            }
        });

        $(document).delegate('#ur_description', 'focus', function() {
            if ($('#rating_value').val() == '') {
                $(this).val('');
                swal('Pilih Bintang', 'Silahkan pilih bintang terlebih dahulu', 'warning');
            }
        });

        $('#f_rating').on('submit', function(e) {
            $("#kt_login_signin_submit").html('Proses ..');
            $("#kt_login_signin_submit").attr("disabled", true);
            e.preventDefault();
            jQuery.noConflict();
            var rating = $('#rating_value').val();
            // var cust_id = $('#cust_id').val();
            // var cust_name = $('#cust_name').val();
            // var cust_phone = $('#cust_phone').val();
            var ur_description = $('#ur_description').val();
            var ur_id = $('#ur_id').val();

            // console.log(rating);
            // console.log(ur_description)
            if (rating == '') {
                swal({
                    title: "Pilih Bintang",
                    text: "Silahkan pilih bintang / rating terlebih dahulu",
                    type: "info",
                    timer: 3000
                });
                return false;
            }

            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_ur_id:ur_id, _ur_description:ur_description, _rating:rating},
                dataType: 'json',
                url: "<?php echo e(url('save_rating')); ?>",
                success:function(r){
                    if (r.status == '200') {
                        $('#f_rating')[0].reset();
                        $('.rating-css div').css('color', '#fff');
                        $('#rating_label').text('Pilih Bintang');
                        $('#rating_mode').val('');
                        $('#ThankyouModal').on('show.bs.modal', function() {
                            setTimeout(() => {
                                $('#ThankyouModal').modal('hide');
                                if (!$('#device_background_panel').hasClass('d-none')) {
                                    $('#device_background_panel').addClass('d-none');
                                }
                            }, 5000);
                        }).modal('show');
                    } else {
                        window.location.reload();
                    }
                }
            });
            
        });

        $('#cust_phone').on('change', function() {
            var cust_phone = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_cust_phone:cust_phone},
                dataType: 'json',
                url: "<?php echo e(url('check_customer_rating_phone')); ?>",
                success: function(r) {
                    if (r.status == '200') {
                        $('#cust_id').val(r.cust_id);
                        $('#cust_name').val(r.cust_name);
                        $('#cust_subdistrict_label').val(r.cust_subdistrict_label);
                        $('#cust_subdistrict').val(r.cust_subdistrict);
                    } else {
                        $('#cust_id').val('');
                    }
                }
            });
        });

        $('#ur_description').on('keyup', function(){ 
            var query = $(this).val();
            if (query.indexOf('    ') >= 0) {
                $(this).val($.trim(query));
            }
        });

        $('#cust_subdistrict_label').on('keyup', function(){ 
            var query = $(this).val();
            var key = event.keyCode || event.charCode;
            var cust_id = $('#cust_id').val();
            if (cust_id != '') {
                return false;
            }
            if( key == 8 || key == 46 ) {
                if (query.length > 11) {
                    $(this).val('');
                    $('#cust_subdistrict').val('');
                }
            }
            if($.trim(query) != '' || $.trim(query) != null) {
                if ($.trim(query).length > 2) {
                    jQuery.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    jQuery.ajax({
                        url:"<?php echo e(url('autocomplete_subdistrict')); ?>",
                        method:"POST",
                        data:{query:query},
                        success:function(data){
                            $('#subdistrictList').fadeIn();
                            $('#subdistrictList').html(data);
                        }
                    });
                } else {
                    $('#subdistrictList').fadeOut();
                }
            } else {
                $('#subdistrictList').fadeOut();
            }
        });

        $(document).delegate('#add_to_subdistrict_list', 'click', function(e) {
            var kode = $(this).attr('data-kode');
            var nama = $(this).attr('data-nama');
            $('#cust_subdistrict_label').val(nama);
            $('#cust_subdistrict').val(kode);
            $('#subdistrictList').fadeOut();
        });
    });
</script><?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/rating_by_customer/rating_by_customer_js.blade.php ENDPATH**/ ?>