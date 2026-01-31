@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Wait for jQuery to be available before loading daterangepicker
    (function() {
        function waitForJQuery(callback) {
            if (window.jQuery) {
                callback();
            } else {
                setTimeout(function() {
                    waitForJQuery(callback);
                }, 100);
            }
        }
        
        waitForJQuery(function() {
            // Load moment.js if not already loaded
            if (typeof moment === 'undefined') {
                var momentScript = document.createElement('script');
                momentScript.src = 'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js';
                momentScript.onload = function() {
                    loadDaterangepicker();
                };
                document.head.appendChild(momentScript);
            } else {
                loadDaterangepicker();
            }
        });
        
        function loadDaterangepicker() {
            // Check if daterangepicker is already loaded
            if (typeof jQuery.fn.daterangepicker !== 'undefined') {
                initApp();
                return;
            }
            
            // Load daterangepicker CSS
            var daterangepickerCSS = document.createElement('link');
            daterangepickerCSS.rel = 'stylesheet';
            daterangepickerCSS.href = 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css';
            document.head.appendChild(daterangepickerCSS);
            
            // Load daterangepicker JS
            var daterangepickerScript = document.createElement('script');
            daterangepickerScript.src = 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js';
            daterangepickerScript.onload = function() {
                initApp();
            };
            daterangepickerScript.onerror = function() {
                console.error('Failed to load daterangepicker');
            };
            document.head.appendChild(daterangepickerScript);
        }
        
        function initApp() {
            var date = '';
            var currentDataTable = null;
            
            $(document).ready(function() {
                // Initialize date range picker
                initDateRangePicker();
                
                // Form submit handler
                $('#f_filter').on('submit', function(e) {
                    e.preventDefault();
                    var data = $('#data_filter').val();
                    var article = $('#article_filter').val();

                    if (data == 'article' && article == '') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Data Artikel',
                            text: 'Silahkan tentukan data artikel, hanya detail ke warna atau size',
                            confirmButtonColor: '#ef4444',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }

                    var formData = new FormData(this);
                    formData.append('date', date);

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    
                    $.ajax({
                        type: 'POST',
                        url: "{{ url('ad_load_data') }}",
                        data: formData,
                        dataType: 'html',
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(data) {
                            $('#table').html(data);
                            // Reinitialize date range picker after content load
                            setTimeout(function() {
                                initDateRangePicker();
                            }, 100);
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan saat memuat data',
                                confirmButtonColor: '#ef4444',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                    return false;
                });

                // Export button handler
                $('#export_btn').on('click', function(e) {
                    e.preventDefault();
                    var st_id = $('#st_id').val();
                    var data = $('#data_filter').val();
                    var article = $('#article_filter').val();

                    if (st_id == '') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Store',
                            text: 'Tentukan store terlebih dahulu',
                            confirmButtonColor: '#ef4444',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }

                    if (data == '') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tipe Data',
                            text: 'Tentukan tipe data terlebih dahulu',
                            confirmButtonColor: '#ef4444',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }

                    if (data == 'article' && article == '') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Data Artikel',
                            text: 'Silahkan tentukan data artikel, hanya detail ke warna atau size',
                            confirmButtonColor: '#ef4444',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }

                    $('#export_btn').text('Mohon Menunggu ...');
                    
                    // Build export URL with parameters
                    var exportUrl = "{{ url('ad_export') }}?st_id=" + st_id + "&data_filter=" + data + "&article_filter=" + article + "&date=" + date;
                    window.location.href = exportUrl;
                    
                    setTimeout(function() {
                        $('#export_btn').text('Export');
                    }, 2000);
                });
            });

            function initDateRangePicker() {
                var picker = $('#kt_dashboard_daterangepicker');
                if (picker.length == 0) {
                    return;
                }
                
                // Destroy existing picker if any
                if (picker.data('daterangepicker')) {
                    picker.data('daterangepicker').remove();
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
                    direction: false,
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
            }
        }
    })();
</script>
@endpush
