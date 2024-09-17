<script>
    var date = '';
    $(document).ready(function() {
        $('#f_filter').on('submit', function(e) {
            e.preventDefault();
            var data = $('#data_filter').val();
            var article = $('#article_filter').val();
            var formData = new FormData(this);

            if (data == 'article' && article == '') {
                swal('Data Artikel', 'Silahkan tentukan data artikel, hanya detail ke warna atau size', 'warning');
                return false;
            }

            formData.append('date', date);

            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('ad_load_data')); ?>",
                data: formData,
				dataType: 'html',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $('#table').html(data);
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
            return false;
        });

        $('#export_btn').on('click', function(e) {
            e.preventDefault();
            var st_id = $('#st_id').val();
            var data = $('#data_filter').val();
            var article = $('#article_filter').val();

            if (st_id == '') {
                swal('Store', 'Tentukan store terlebih dahulu', 'warning');
                return false;
            }

            if (data == '') {
                swal('Tipe Data', 'Tentukan tipe data terlebih dahulu', 'warning');
                return false;
            }

            if (data == 'article' && article == '') {
                swal('Data Artikel', 'Silahkan tentukan data artikel, hanya detail ke warna atau size', 'warning');
                return false;
            }

            $('#export_btn').text('Mohon Menunggu ...');
            // data: {st_id:st_id, data:data, article:article, date:date},
            window.location.href = "<?php echo e(url('ad_export')); ?>";
            $('#export_btn').text('Export');
        });

        jQuery.noConflict();
        var picker = $('#kt_dashboard_daterangepicker');
        if ($('#kt_dashboard_daterangepicker').length == 0) {
            return;
        }
        var start = moment();
        var end = moment();

        function cb(start, end, label) {
            var title = '';
            var range = '';

            if ((end - start) < 100 || label == 'Hari Ini') {
                title = 'Hari Ini:';
                range = start.format('DD MMM YYYY');
                date = start.format('YYYY-MM-DD');
            } else if (label == 'Kemarin') {
                title = 'Kemarin:';
                range = start.format('DD MMM YYYY');
                date = start.format('YYYY-MM-DD');
            } else {
                range = start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY');
                date = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
            }
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);
        }

        picker.daterangepicker({
            direction: KTUtil.isRTL(),
            startDate: start,
            endDate: end,
            opens: 'center',
            applyClass: 'btn-primary',
            cancelClass: 'btn-light-primary',
            ranges: {
                'Hari Ini': [moment(), moment()],
                'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                'Bulan Kemarin': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);
        cb(start, end, '');

    });
</script>
<?php /**PATH C:\laragon\www\jez\jez_sistem\resources\views/app/asset_detail/asset_detail_js.blade.php ENDPATH**/ ?>