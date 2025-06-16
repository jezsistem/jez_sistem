<script>
    var pr_id = '';

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var data_promo_recommendation_tb = '';
        data_promo_recommendation_tb = $('#PromoRecommendationtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs'
            }],
            ajax: {
                url: "{{ url('rekomendasi_promo_datatables') }}",
                data: function(d) {
                    d.search = $('#rekomendasi_promo_search').val();
                    d.channel = $('#channel_rekomendasi_promo').val();
                    d.date_start = $('#rekomendasi_promo_date_start').val();
                    d.date_end = $('#rekomendasi_promo_date_end').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'pr_id',
                    searchable: false
                },
                {
                    data: 'pr_code',
                    name: 'pr_code'
                },
                {
                    data: 'channel',
                    name: 'channel'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function(data, type, row) {
                        if (data) {
                            var date = new Date(data);
                            var days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat',
                                'Sabtu'
                            ];
                            var months = ['Januari', 'Februari', 'Maret', 'April', 'Mei',
                                'Juni', 'Juli', 'Agustus', 'September', 'Oktober',
                                'November', 'Desember'
                            ];

                            var dayName = days[date.getDay()];
                            var day = date.getDate();
                            var month = months[date.getMonth()];
                            var year = date.getFullYear();
                            var hours = date.getHours().toString().padStart(2, '0');
                            var minutes = date.getMinutes().toString().padStart(2, '0');
                            var seconds = date.getSeconds().toString().padStart(2, '0');

                            return dayName + ', ' + day + ' ' + month + ' ' + year + ' ' +
                                hours + ':' + minutes + ':' + seconds;
                        }
                        return '';
                    }
                }
            ],
            columnDefs: [{
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
            order: [
                [0, 'desc']
            ],
        });

        var data_promo_recommendation_detail_tb = '';
        data_promo_recommendation_detail_tb = $('#PromoRecommendationDetailtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            searching: true,
            ajax: {
                url: "{{ url('rekomendasi_promo_detail_datatables') }}",
                data: function(d) {
                    d.pr_id = pr_id;
                    d.search = d.search.value;
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'prd_id',
                    searchable: false
                },
                {
                    data: 'article_id',
                    name: 'article_id',
                    searchable: true
                },
                {
                    data: 'p_name',
                    name: 'p_name',
                    searchable: true
                },
                {
                    data: 'promo_disc',
                    name: 'promo_disc',
                    searchable: false
                },
                {
                    data: 'p_price_tag',
                    name: 'p_price_tag',
                    searchable: false
                },
                {
                    data: 'price_discount',
                    name: 'price_discount',
                    searchable: false
                },
                {
                    data: 'notes',
                    name: 'notes',
                    searchable: false
                },
            ],
            columnDefs: [{
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
            order: [
                [0, 'desc']
            ],
        });

        data_promo_recommendation_tb.buttons().container().appendTo($('#rekomendasi_promo_excel_btn'));
        $('#rekomendasi_promo_search').on('keyup', function() {
            data_promo_recommendation_tb.draw();
        });


        $('#PromoRecommendationtb tbody').on('click', 'tr td:not(:nth-child(12))', function() {
            var row = data_promo_recommendation_tb.row(this).data();
            pr_id = row.pr_id;
            var channel = row.channel;
            jQuery.noConflict();
            $('#PromoRecommendationDetailModal').modal('show');
            $('.pr_code').text('(' + row.pr_code + ')');
            data_promo_recommendation_detail_tb.draw();
        });

        $('#add_rekomendasi_promo_btn').on('click', function() {
            jQuery.noConflict();
            $('#PromoRecommendationModal').modal('show');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#PromoRecommendationModal form')[0].reset();
            $('#delete_rekomendasi_promo_btn').hide();
        });

        $('#PromoRecommendationModal form').on('submit', function(e) {
            e.preventDefault();
            $("#save_rekomendasi_promo_btn").html('Proses ..');
            $("#save_rekomendasi_promo_btn").attr("disabled", true);
            var formData = new FormData(this);
            var isEdit = $('#_mode').val() === 'edit'; // Check if it's edit mode
            var currentId = $('#_id').val(); // Get the current ID

            $.ajax({
                type: 'POST',
                url: "{{ url('rekomendasi_promo_save') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_rekomendasi_promo_btn").html('Simpan');
                    $("#save_rekomendasi_promo_btn").attr("disabled", false);

                    if (data.status == '200') {
                        $("#PromoRecommendationModal").modal('hide');
                        swal("Success", "Data saved successfully Jez", "success");

                        if (isEdit) {
                            // Update the row in the DataTable
                            data_promo_recommendation_tb.ajax.reload(null, false);
                        } else {
                            // Add new data: reload the DataTable
                            data_promo_recommendation_tb.ajax.reload(null, false);
                        }
                    } else if (data.status == '400') {
                        swal("Failed", "Data not saved Jez", "warning");
                    } else {
                        swal("Error", data.message, "error");
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    $("#save_rekomendasi_promo_btn").html('Simpan').attr("disabled", false);
                    swal("Error", "Terjadi kesalahan saat memproses", "error");
                }
            });
        });

        $('#rekomendasi_promo_search').on('keyup', function() {
            data_promo_recommendation_tb.draw();
        });

        $('#channel_rekomendasi_promo').on('change', function() {
            data_promo_recommendation_tb.draw();
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            $('#import_data_btn').html('Proses...');
            $('#import_data_btn').attr('disabled', true);
            var formData = new FormData(this);

            $.ajax({
                type: 'POST',
                url: "{{ url('rekomendasi_promo_import') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#import_data_btn").html('Import');
                    $("#import_data_btn").attr("disabled", false);
                    jQuery.noConflict();

                    if (data.status == '200') {
                        $("#ImportModal").modal('hide');
                        toastr.success('Data berhasil diimport', 'Berhasil');
                        $('#f_import')[0].reset();

                        // start_bin_table.draw();
                        data_promo_recommendation_tb.ajax.reload();
                        excelImportData = data.data['processedData'];

                        checkMissingBarcode(data.data['missingBarcode']);

                    } else if (data.status == '400') {
                        $("#ImportModal").modal('hide');
                        toastr.warning(
                            'File yang anda import kosong atau format tidak tepat',
                            'File');
                    } else {
                        $("#ImportModal").modal('hide');
                        toastr.error('Terjadi kesalahan saat memproses file', 'Error');
                    }
                },
                error: function(data) {
                    toastr.error('An error occurred while processing your request',
                        'Error');
                }
            });
        });

        $('#ExportArticleData').on('click', function(e) {
            e.preventDefault();

            swal({
                title: "Ekspor Data",
                text: "Apakah Anda ingin mengekspor data?",
                icon: "info",
                buttons: [
                    'Batal',
                    'Ekspor'
                ],
            }).then(function(isConfirm) {
                if (isConfirm) {
                    var url = "{{ url('export_rekomendasi_promo_detail') }}" + "?pr_id=" +
                        pr_id;
                    window.location.href = url;
                }
            });
        });

        $('#export_btn').on('click', function(e) {
            e.preventDefault();

            var search = $('#rekomendasi_promo_search').val();
            var channel = $('#channel_rekomendasi_promo').val();
            var date_start = $('#rekomendasi_promo_date_start').val();
            var date_end = $('#rekomendasi_promo_date_end').val();

            swal({
                title: "Ekspor Data",
                text: "Apakah Anda ingin mengekspor data?",
                icon: "info",
                buttons: [
                    'Batal',
                    'Ekspor'
                ],
            }).then(function(isConfirm) {
                if (isConfirm) {
                    var url = "{{ url('export_rekomendasi_promo') }}" + "?search=" + encodeURIComponent(search) +
                        "&channel=" + encodeURIComponent(channel) +
                        "&date_start=" + encodeURIComponent(date_start) +
                        "&date_end=" + encodeURIComponent(date_end);
                    window.location.href = url;
                }
            });
        });

        $('#delete_promo_recommendation').on('click', function() {
            swal({
                title: "Hapus..?",
                text: "Yakin hapus data ini ?",
                icon: "warning",
                buttons: [
                    'Batalkan',
                    'Hapus'
                ],
                dangerMode: true,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {
                            _id: pr_id
                        },
                        dataType: 'json',
                        url: "{{ url('rekomendasi_promo_delete') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                toastr.success("Data successfully deleted Jez",
                                    "Success");
                                $('#PromoRecommendationDetailModal').modal('hide');
                                data_promo_recommendation_tb.ajax.reload();
                            } else {
                                toastr.error("Failed to delete data Jez", "Failed");
                            }
                        },
                        error: function() {
                            toastr.error(
                                "Terjadi kesalahan saat memproses permintaan",
                                "Error");
                        }
                    });
                    return false;
                }
            });
        });

        $(document).ready(function() {
            // Open the second modal when the button is clicked
            $("#ImportModalBtn").click(function() {
                $("#ImportModal").modal("show");
            });
        });

        jQuery.noConflict();
        var picker = $('#kt_dashboard_daterangepicker');
        if ($('#kt_dashboard_daterangepicker').length == 0) {
            return;
        }
        var start = moment().subtract(null, null); // Default start date
        var end = moment(); // Default end date

        function cb(start, end, label) {
            var title = '';
            var range = '';
            var hidden_range = '';

            if (label == 'All Days' || !label) {
                title = '';
                range = 'All Days';
                hidden_range = '';
            } else if ((end - start) < 100 || label == 'Today') {
                title = 'Today:';
                range = start.format('MMM D');
                hidden_range = start.format('YYYY-MM-DD');
            } else if (label == 'Yesterday') {
                title = 'Yesterday:';
                range = start.format('MMM D');
                hidden_range = start.format('YYYY-MM-DD');
            } else {
                range = start.format('MMM D') + ' - ' + end.format('MMM D');
                hidden_range = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
            }

            $('#rekomendasi_promo_date_start').val(hidden_range);
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);

            // Trigger DataTable reload with date range filter
            data_promo_recommendation_tb.ajax.reload();
        }

        picker.daterangepicker({
            direction: KTUtil.isRTL(),
            startDate: start,
            endDate: end,
            opens: 'left',
            applyClass: 'btn-primary',
            cancelClass: 'btn-light-primary',
            ranges: {
                'All Days': [null, null],
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                    'month').endOf('month')]
            }
        }, cb);

        // cb(start, end, 'All Days'); // Set default to "All Days"

        // Update DataTable AJAX request to include date range filter
        data_promo_recommendation_tb.on('preXhr.dt', function(e, settings, data) {
            var dateRange = $('#rekomendasi_promo_date_start').val();
            if (dateRange) {
                var dates = dateRange.split('|');
                data.date_start = dates[0];
                data.date_end = dates[1];
            }
        });

    });
</script>
