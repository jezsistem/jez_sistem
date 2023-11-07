<script>
    var loadBanner = function(event) {
        var output = document.getElementById('bannerPreview');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
        URL.revokeObjectURL(output.src) // free memory
        }
    };

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#bannerPreview').on('click', function() {
            $(this).attr('src', '')
            $("#psc_banner").val('');
        });

        var wsc_table = $('#Wsctb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('wsc_datatables') }}",
                data : function (d) {
                    d.search = $('#wsc_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'psc_name', name: 'psc_name' },
            { data: 'psc_slug', name: 'psc_slug' },
            { data: 'psc_banner_show', name: 'psc_banner' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        wsc_table.buttons().container().appendTo($('#wsc_excel_btn' ));
        $('#wsc_search').on('keyup', function() {
            wsc_table.draw();
        });

        $('#Wsctb tbody').on('click', 'tr', function () {
            $('#bannerPreview').attr('src', '');
            var id = wsc_table.row(this).data().id;
            var psc_name = wsc_table.row(this).data().psc_name;
            var psc_slug = wsc_table.row(this).data().psc_slug;
            var psc_banner = wsc_table.row(this).data().psc_banner;
            jQuery.noConflict();
            $('#WebSubCategoryModal').modal('show');
            $('#psc_name').val(psc_name);
            $('#psc_slug').val(psc_slug);
            if (psc_banner != null) {
                $('#bannerPreview').attr('src', "{{ asset('api/sub_category/banner') }}/"+psc_banner);
            }
            $('#_id').val(id);
            $('#_mode').val('edit');
            $('#_banner').val(psc_banner);
            @if ( $data['user']->delete_access == '1' )
            $('#delete_wsc_btn').show();
            @endif
        });

        $('#f_wsc').on('submit', function(e) {
            e.preventDefault();
            $("#save_wsc_btn").html('Proses ..');
            $("#save_wsc_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "{{ url('wsc_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_wsc_btn").html('Simpan');
                    $("#save_wsc_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $('#bannerPreview').trigger('click');
                        $("#WebSubCategoryModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        wsc_table.draw();
                    } else if (data.status == '400') {
                        $("#WebSubCategoryModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

    });
</script>