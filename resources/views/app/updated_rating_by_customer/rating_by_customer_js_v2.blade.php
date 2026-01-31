<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $('#ur_description').val('');
    
    // Rating labels mapping
    var ratingLabels = {
        '1': 'Sangat Buruk',
        '2': 'Buruk',
        '3': 'Lumayan',
        '4': 'Bagus',
        '5': 'Sangat Bagus'
    };
    
    // Star rating click handler
    $('input[name="rating"]').on('change', function() {
        var value = $(this).val();
        $('#rating_value').val(value);
        $('#rating_label').text(ratingLabels[value] || 'Pilih Bintang');
    });
    
    // Check for waiting review (polling)
    @if (request()->segment(1) != 'rating_app')
    setInterval(function() {
        var rating_mode = $('#rating_mode').val();
        
        $.ajax({
            url: "{{ url('check_waiting_for_review') }}",
            method: "POST",
            dataType: "JSON",
            success: function(r) {
                if (r.status == '200') {
                    if (rating_mode == '') {
                        if ($('#device_background_panel').hasClass('hidden')) {
                            $('#device_background_panel').removeClass('hidden');
                        }
                        $('#rating_mode').val('1');
                        $('#ur_id').val(r.ur_id);
                    }
                } else {
                    $('#rating_mode').val('');
                    setTimeout(function() {
                        resetForm();
                        if (!$('#device_background_panel').hasClass('hidden')) {
                            $('#device_background_panel').addClass('hidden');
                        }
                    }, 3000);
                }
            }
        });
    }, 3000);
    @endif
    
    // Focus validation
    $('#ur_description').on('focus', function() {
        if ($('#rating_value').val() == '') {
            $(this).blur();
            Swal.fire({
                title: 'Pilih Bintang',
                text: 'Silahkan pilih bintang terlebih dahulu',
                icon: 'warning',
                confirmButtonColor: '#fbbf24'
            });
        }
    });
    
    // Form submission
    $('#f_rating').on('submit', function(e) {
        e.preventDefault();
        
        var rating = $('#rating_value').val();
        var ur_description = $('#ur_description').val();
        var ur_id = $('#ur_id').val();
        
        if (rating == '') {
            Swal.fire({
                title: 'Pilih Bintang',
                text: 'Silahkan pilih bintang / rating terlebih dahulu',
                icon: 'info',
                confirmButtonColor: '#fbbf24',
                timer: 3000
            });
            return false;
        }
        
        $('#kt_login_signin_submit').html('<i class="fa fa-spinner fa-spin mr-2"></i> Proses...');
        $('#kt_login_signin_submit').prop('disabled', true);
        
        $.ajax({
            type: "POST",
            data: {
                _ur_id: ur_id,
                _ur_description: ur_description,
                _rating: rating
            },
            dataType: 'json',
            url: "{{ url('save_rating') }}",
            success: function(r) {
                $('#kt_login_signin_submit').html('<i class="fa fa-paper-plane mr-2"></i> Kirim Penilaian');
                $('#kt_login_signin_submit').prop('disabled', false);
                
                if (r.status == '200') {
                    resetForm();
                    $('#rating_mode').val('');
                    
                    // Show thank you modal
                    $('#ThankyouModal').removeClass('hidden');
                    
                    setTimeout(function() {
                        $('#ThankyouModal').addClass('hidden');
                        if (!$('#device_background_panel').hasClass('hidden')) {
                            $('#device_background_panel').addClass('hidden');
                        }
                    }, 5000);
                } else {
                    window.location.reload();
                }
            },
            error: function() {
                $('#kt_login_signin_submit').html('<i class="fa fa-paper-plane mr-2"></i> Kirim Penilaian');
                $('#kt_login_signin_submit').prop('disabled', false);
                
                Swal.fire({
                    title: 'Error',
                    text: 'Terjadi kesalahan, silahkan coba lagi',
                    icon: 'error',
                    confirmButtonColor: '#fbbf24'
                });
            }
        });
    });
    
    // Reset form function
    function resetForm() {
        $('#f_rating')[0].reset();
        $('#rating_value').val('');
        $('#rating_label').text('Pilih Bintang');
        $('input[name="rating"]').prop('checked', false);
    }
    
    // Close modal
    $(document).on('click', '.close-modal', function() {
        $(this).closest('.modal-overlay').addClass('hidden');
    });
    
    $(document).on('click', '.modal-overlay', function(e) {
        if (e.target === this) {
            $(this).addClass('hidden');
        }
    });
});
</script>
