<script src="{{ asset('barcode/html5-qrcode.min.js') }}"></script>
<script>
    var sa_code = '{{ $data['sa_code'] }}';
    var st_id = '{{ $data['st_id'] }}';
    var pl_id = '';
    var sa_custom = '{{ $data['sa_custom'] }}';
    var manual = 0;
    var not_found = 0;
    var submit = 0;
    var scan_mode = 0;
    var search_article = 0;
    var qty_edit = 0;

    function loadBin(query)
    {
        if($.trim(query) != '' || $.trim(query) != null) {
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:"{{  url('fetch_start_scan_adjustment_bin') }}",
                method:"POST",
                data:{query:query, st_id:st_id},
                success:function(data){
                    $('#itemList').fadeIn();
                    $('#itemList').html(data);
                }
            });
        } else {
            $('#itemList').fadeOut();
        }
    }

    function scanned(barcode)
    {
        var data = new FormData();
        data.append('barcode', barcode);
        data.append('st_id', st_id);
        data.append('pl_id', pl_id);
        data.append('sa_code', sa_code);
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:"{{  url('scan_adjustment_barcode') }}",
            method:"POST",
            data:data,
            dataType: 'json',
            cache:false,
            contentType: false,
            processData: false,
            success:function(r){
                if (r.status == '200') {
                    loadQty();
                    toast('Scanned', 'Berhasil discan', 'success');
                } else {
                    $('#not_found_barcode').val(r.barcode);
                    jQuery.noConflict();
                    $('#NotFoundBarcode').modal('show');
                }
                document.getElementById('barcode').focus();
            }
        });
    }

    function sync()
    {
        var data = new FormData();
        data.append('pl_id', pl_id);
        data.append('sa_code', sa_code);
        data.append('sa_custom', sa_custom);
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:"{{  url('scan_adjustment_sync') }}",
            method:"POST",
            data:data,
            dataType: 'json',
            cache:false,
            contentType: false,
            processData: false,
            success:function(r){
                if (r.status == '200') {
                    toast('Berhasil', 'Berhasil synchronize', 'success');
                } else {
                    toast('Gagal', 'synchronize', 'warning');
                }
                document.getElementById('barcode').focus();
            }
        });
    }

    function loadQty()
    {
        var data = new FormData();
        data.append('pl_id', pl_id);
        data.append('sa_code', sa_code);
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:"{{  url('scan_adjustment_qty') }}",
            method:"POST",
            data:data,
            dataType: 'json',
            cache:false,
            contentType: false,
            processData: false,
            success:function(r){
                if (r.status == '200') {
                    $('#current_qty').text(r.current_qty);
                    $('#scanned_qty').text(r.scanned_qty);
                    toast('Scanned', 'Berhasil discan', 'success');
                }
            }
        });
    }

    function playSound()
    {
        var audioElement = document.createElement('audio');
        audioElement.setAttribute('src', "{{ asset('scanner/audio/beep.mp3') }}");
        audioElement.play();
    }

    $(document).ready(function() {
        document.getElementById('bin_search').focus();
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var scan_adjustment_table = $('#SAtb').DataTable({
            destroy: true,
            processing: false,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"p>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('start_scan_adjustment_datatables') }}",
                data : function (d) {
                    d.st_id = st_id;
                    d.pl_id = pl_id;
                    d.sa_code = sa_code;
                    d.scan_filter = $('#scan_filter').val();
                    d.sa_custom = sa_custom;
                    d.search = $('#search_article').val();
                }
            },
            columns: [
            { data: 'article', name: 'p_name' },
            { data: 'barcode', name: 'barcode', orderable:false },
            { data: 'action', name: 'action', orderable:false },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-left",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        $('#search_article').on('click', function(e) {
            e.preventDefault();
            manual = 0;
            search_article = 1;
        });

        $('#search_article').on('keyup', function(e) {
            e.preventDefault();
            scan_adjustment_table.draw();
        });

        $(document).delegate('.edit_qty_so', 'click', function(e) {
            e.preventDefault();
            manual = 0;
            qty_edit = 1;
        });

        $(document).delegate('.edit_qty_so', 'change', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            var qty = $(this).val();
            var data = new FormData();
            data.append('id', id);
            data.append('qty', qty);
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:"{{  url('scan_adjustment_qty_update') }}",
                method:"POST",
                data:data,
                dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success:function(r){
                    if (r.status == '200') {
                        scan_adjustment_table.draw();
                        toast('Updated', 'Berhasil diupdate', 'success');
                    } else {
                        toast('Gagal', 'Gagal diupdate', 'error');
                    }
                }
            });
        });

        function scanner() {
            function docReady(fn) {
                // see if DOM is already available
                if (document.readyState === "complete" || document.readyState === "interactive") {
                    // call on next available tick
                    setTimeout(fn, 1);
                } else {
                    document.addEventListener("DOMContentLoaded", fn);
                }
            }

            docReady(function() {
                var lastResult, countResults = 0;

                var html5QrcodeScanner = new Html5QrcodeScanner(
                    "qr-reader", { fps: 10, qrbox: 250 });

                function onScanSuccess(decodedText, decodedResult) {
                    playSound();
                    if (decodedText !== lastResult) {
                        ++countResults;
                        lastResult = decodedText;
                        scanned(decodedText);
                        html5QrcodeScanner.clear();
                        setTimeout(() => {
                            scan_adjustment_table.draw();
                            scanner();
                        }, 1000);
                    }
                }
                html5QrcodeScanner.render(onScanSuccess);
            });
        }

        $('#bin_search').on('click', function(e) {
            e.preventDefault();
            $(this).val();
        });

        $('#bin_search').on('keyup', function(e) {
            e.preventDefault();
            var query = $(this).val();
            loadBin(query);
        });

        $(document).on('click', '#add_to_item_list', function(e) {
            var id = $(this).attr('data-id');
            var pl_code = $(this).attr('data-pl_code');
            pl_id = id;
            $('#bin_search').val(pl_code);
            $('#itemList').fadeOut();
            $('#bin_search').prop('readonly', true);
            $('#bin_search').addClass('bg-primary');
            $('#bin_search').addClass('text-white');
            $('#locked_btn').removeClass('d-none');
            scan_adjustment_table.draw();
            document.getElementById('barcode').focus();
            loadQty();
            $('#scan_filter').removeClass('d-none');
        });

        $(document).on('click', '.bin_custom', function(e) {
            var id = $(this).attr('data-id');
            var pl_code = $(this).text();
            pl_id = id;
            $('#bin_search').val(pl_code);
            $('#itemList').fadeOut();
            $('#bin_search').prop('readonly', true);
            $('#bin_search').addClass('bg-primary');
            $('#bin_search').addClass('text-white');
            $('#locked_btn').removeClass('d-none');
            scan_adjustment_table.draw();
            document.getElementById('barcode').focus();
            loadQty();
            $('#scan_filter').removeClass('d-none');
            jQuery.noConflict();
            $('.bin_custom').removeClass('btn-success');
            $('.bin_custom').addClass('btn-primary');
            $(this).removeClass('btn-primary');
            $(this).addClass('btn-success');
            $('#BINCustom').modal('hide');
        });

        $(document).on('click', '#locked_btn', function(e) {
            pl_id = '';
            $('#bin_search').val('');
            $('#bin_search').prop('readonly', false);
            $('#bin_search').removeClass('bg-primary');
            $('#bin_search').removeClass('text-white');
            $('#locked_btn').addClass('d-none');
            $('.bin_custom').removeClass('btn-success');
            $('.bin_custom').addClass('btn-primary');
            scan_adjustment_table.draw();
            document.getElementById('bin_search').focus();
            loadQty();
            $('#scan_filter').addClass('d-none');
        });

        $(document).on('change', '#scan_filter', function(e) {
            scan_adjustment_table.draw();
        });

        $(document).on('click', 'body', function(e) {
            e.preventDefault();
            $('#manualList').fadeOut();
            $('#notFoundList').fadeOut();
            $('#itemList').fadeOut();
            if (pl_id == '') {
                document.getElementById('bin_search').focus();
            } else {
                if (manual == '1') {
                    document.getElementById('manual').focus();
                } else if (not_found == '1') {
                    document.getElementById('not_found_article').focus();
                } else {
                    if (submit != '1' && scan_mode != '1' && search_article != '1' && qty_edit != '1') {
                        document.getElementById('barcode').focus();
                    }
                }
            }
        });

        $(document).on('change', '#barcode', function(e) {
            e.preventDefault();
            var barcode = $(this).val();
            scanned(barcode);
            $(this).val('');
            scan_adjustment_table.draw();
        });

        $(document).on('click', '#reset_btn', function(e) {
            e.preventDefault();
            if (pl_id == '') {
                swal('BIN', 'Belum ada BIN yang aktif', 'warning');
                return false;
            }
            swal({
                title: "Reset..?",
                text: "Yakin reset dari 0 scan adjustment untuk bin ini ?",
                icon: "warning",
                buttons: [
                    'Batalkan',
                    'Yakin'
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
                        data: {sa_code:sa_code, pl_id:pl_id},
                        dataType: 'json',
                        url: "{{ url('scan_adjustment_reset')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil direset", "success");
                                scan_adjustment_table.draw();
                                loadQty();
                            } else {
                                swal('Gagal', 'Gagal reset data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).on('click', '#sync_btn', function(e) {
            e.preventDefault();
            if (pl_id == '') {
                swal('BIN', 'Belum ada BIN yang aktif', 'warning');
                return false;
            }
            swal({
                title: "Synchronize..?",
                text: "Diusahakan klik tombol ini terakhir, setelah anda selesai scan semua artikel pada BIN",
                icon: "warning",
                buttons: [
                    'Batalkan',
                    'Yakin'
                ],
                dangerMode: true,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    sync();
                    scan_adjustment_table.draw();
                    setTimeout(() => {
                        scan_adjustment_table.draw();
                        scanner();
                    }, 1500);
                }
            })
        });

        $(document).on('click', '#back_btn', function(e) {
            e.preventDefault();
            window.location.href = "{{ url('scan_adjustment') }}";
        });

        $(document).on('click', '#custom_btn', function(e) {
            e.preventDefault();
            jQuery.noConflict();
            $('#BINCustom').modal('show');
        });

        $(document).on('click', '#manual', function(e) {
            e.preventDefault();
            manual = 1;
        });

        $(document).on('click', '#barcode', function(e) {
            e.preventDefault();
            manual = 0;
            scan_mode = 0;
            search_article = 0;
            qty_edit = 0;
        });

        function loadArticle(query)
        {
            if($.trim(query) != '' || $.trim(query) != null) {
                $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url:"{{  url('fetch_start_scan_adjustment_article') }}",
                    method:"POST",
                    data:{query:query},
                    success:function(data){
                        $('#manualList').fadeIn();
                        $('#manualList').html(data);
                    }
                });
            } else {
                $('#manualList').fadeOut();
            }
        }

        $(document).delegate('#add_manual_article', 'click', function(e) {
            e.preventDefault();
            var pst_id = $(this).attr('data-id');
            var data = new FormData();
            data.append('pl_id', pl_id);
            data.append('sa_code', sa_code);
            data.append('pst_id', pst_id);
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:"{{  url('scan_adjustment_manual') }}",
                method:"POST",
                data:data,
                dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success:function(r){
                    if (r.status == '200') {
                        loadQty();
                        scan_adjustment_table.draw();
                        toast('Ditambah', 'Berhasil ditambah', 'success');
                    } else {
                        toast('Gagal', 'Gagal ditambah', 'warning');
                    }
                    $('#manualList').fadeOut();
                    $('#manual').val('');
                    document.getElementById('barcode').focus();
                }
            });
        });

        function minPlus(type, qty, sad_id)
        {
            var data = new FormData();
            data.append('type', type);
            data.append('qty', qty);
            data.append('sad_id', sad_id);
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:"{{  url('min_plus_start_scan_adjustment') }}",
                method:"POST",
                data:data,
                dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success:function(r){
                    if (r.status == '200') {
                        loadQty();
                        scan_adjustment_table.draw();
                        toast('Diubah', 'Berhasil diubah', 'success');
                    } else {
                        toast('Gagal', 'Gagal diubah', 'warning');
                    }
                    document.getElementById('barcode').focus();
                }
            });
        }

        $(document).delegate('#manual', 'keyup', function(e) {
            e.preventDefault();
            var query = $(this).val();
            loadArticle(query);
        });

        $(document).delegate('#min_btn', 'click', function(e) {
            e.preventDefault();
            var type = '-';
            var qty = $(this).attr('data-qty_so');
            var sad_id = $(this).attr('data-id');
            minPlus(type, qty, sad_id);
        });

        $(document).delegate('#plus_btn', 'click', function(e) {
            e.preventDefault();
            var type = '+';
            var qty = $(this).attr('data-qty_so');
            var sad_id = $(this).attr('data-id');
            minPlus(type, qty, sad_id);
        });

        function loadNotFoundArticle(query)
        {
            if($.trim(query) != '' || $.trim(query) != null) {
                $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url:"{{  url('fetch_start_scan_adjustment_article_barcode') }}",
                    method:"POST",
                    data:{query:query},
                    success:function(data){
                        $('#notFoundList').fadeIn();
                        $('#notFoundList').html(data);
                    }
                });
            } else {
                $('#notFoundList').fadeOut();
            }
        }

        $(document).on('click', '#not_found_article', function(e) {
            e.preventDefault();
            not_found = 1;
        });

        $(document).delegate('#not_found_article', 'keyup', function(e) {
            e.preventDefault();
            var query = $(this).val();
            loadNotFoundArticle(query);
        });

        $(document).delegate('#add_article_barcode', 'click', function(e) {
            e.preventDefault();
            not_found = 0;
            submit = 1;
            var pst_id = $(this).attr('data-id');
            var article = $(this).text();
            $('#not_found_pst_id').val(pst_id);
            $('#not_found_article').val(article);
            $('#notFoundList').fadeOut();
        });

        $(document).delegate('#save_barcode', 'click', function(e) {
            e.preventDefault();
            if ($('#not_found_pst_id').val() == '') {
                swal('Tentukan Artikel', 'Silahkan tentukan artikel terkait barcode tersebut', 'warning');
                return false;
            }
            var id = $('#not_found_pst_id').val();
            var barcode = $('#not_found_barcode').val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {id:id, barcode:barcode},
                dataType: 'json',
                url: "{{ url('scan_adjustment_barcode_update')}}",
                success: function(r) {
                    if (r.status == '200'){
                        $('#f_not_found')[0].reset();
                        $('#not_found_pst_id').val('');
                        jQuery.noConflict();
                        $('#NotFoundBarcode').modal('hide');
                        submit = 0;
                        swal("Berhasil", "Data berhasil diupdate, silahkan scan ulang", "success");
                    } else {
                        swal('Gagal', 'Gagal update data', 'error');
                    }
                }
            });
            return false;
        });

        $(document).on('click', '#scanner_btn', function(e) {
            e.preventDefault();
            scan_mode = 1;
            scanner();
        });

        $(document).on('click', '#scanner_btn', function(e) {
            e.preventDefault();
            scan_mode = 1;
            scanner();
        });

    });
</script>
