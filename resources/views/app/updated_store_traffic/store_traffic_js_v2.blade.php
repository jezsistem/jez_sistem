@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Pria button click
    $("#btnPria").click(function() {
        updateCount("male", $(this));
    });
    
    // Wanita button click
    $("#btnWanita").click(function() {
        updateCount("female", $(this));
    });
    
    // Anak-anak button click
    $("#btnAnak").click(function() {
        updateCount("child", $(this));
    });
    
    function updateCount(gender, button) {
        // Disable button temporarily
        button.prop('disabled', true);
        var originalHtml = button.html();
        button.html('<i class="fa fa-spinner fa-spin mr-2"></i> Memproses...');
        
        $.ajax({
            type: "POST",
            url: "/update_traffic_customer",
            data: { gender: gender },
            success: function(response) {
                // Update counts without page reload
                if (response.counts) {
                    var male = 0, female = 0, child = 0;
                    response.counts.forEach(function(item) {
                        if (item.type === 'male') male = item.total;
                        if (item.type === 'female') female = item.total;
                        if (item.type === 'child') child = item.total;
                    });
                    
                    $('#countmale').text(male);
                    $('#countfemale').text(female);
                    $('#countchild').text(child);
                }
                
                if (response.countsTotal && response.countsTotal[0]) {
                    $('#totalCount').text(response.countsTotal[0].total);
                }
                
                // Show success feedback
                toastr.success('Data berhasil ditambahkan!', 'Berhasil');
                
                // Re-enable button
                button.html(originalHtml);
                button.prop('disabled', false);
                
                // Visual feedback animation
                button.addClass('ring-4');
                setTimeout(function() {
                    button.removeClass('ring-4');
                }, 300);
            },
            error: function(error) {
                console.error("Error updating count:", error);
                toastr.error('Gagal menyimpan data', 'Error');
                
                // Re-enable button
                button.html(originalHtml);
                button.prop('disabled', false);
            }
        });
    }
});
</script>
@endpush
