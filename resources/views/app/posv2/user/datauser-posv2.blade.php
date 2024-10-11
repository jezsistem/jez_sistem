@extends('app.layout')
@section('css')
<link href="{{ asset('assetsnew/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assetsnew/assets/extensions/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
<div class="page-heading">
    <div class="row">
        <div class="col-6">
            <h3>Daftar User</h3>
        </div>
        <div class="col-6 text-end">
            <button class="btn btn-primary" onclick="tambah()"><i class="fa fa-plus"></i>&nbsp; Tambah User</button>
        </div>
    </div>
</div>

<div class="card-body">
    <div class="d-flex justify-content-between colorfull-select">
        <input type="hidden" id="_pt_id" value=""/>
        <input type="hidden" id="_pt_id_complaint" value=""/>
        <input type="hidden" id="_exchange" value=""/>
        <input type="hidden" id="cross_order" value="0"/>
        <div class="selectmain bg-primary" style="padding:5px; border-radius:10px;">
            <label class="text-white d-flex font-weight-bold"> Customer
            </label>
            <input type="hidden" id="cust_id" value="1"/>
            <input type="hidden" id="cust_id" value="{{ Auth::user()->st_id }}"/>
            <input type="search" id="cust_id_label"
                   placeholder="Ketik minimal 4 huruf customer" autocomplete="off" style="background: #F7DAD9"/> 
                   <a href="#" 
                   class="btn-inventory" 
                   data-id="" 
                   id="check_customer" 
                   style="background:#FFDB89 ; font-weight: bold; color:black; font-size: 11px; text-decoration: none; padding: 4px 8px; border: none; border-radius: 4px; display: inline-block;"
                   onmouseover="this.style.background='#e18080'; this.style.color='white'; this.style.fontWeight='bold';" 
                   onmouseout="this.style.background='#FFDB89'; this.style.color='black'; this.style.fontWeight='bold';">
                   Check
                </a>
            <div id="itemListCust"></div>
            <br/>
            <select class="form-control border-dark col-12 mr-1 bg-info text-white"
                    id="std_id">
                <option value="17" selected>OFFLINE</option>
                <option value="14">ONLINE / WHATSAPP</option>
                <option value="18">TIKTOK</option>
                <option value="19">Shopee</option>
            </select>
        </div>
        <input type="hidden" id="free_sock_customer_mode" value=""/>
        <div class="btn btn-info d-none" id="waiting_customer_label">
            menunggu customer isi rating ..
            <a class="btn-sm btn-danger" id="cancel_rating_btn">X</a>
        </div>
        <div class="btn btn-info d-none" id="free_sock_customer_panel">
            <label class="text-white d-flex">[RATING CUSTOMER]</label>
            <input type="text" id="free_sock_customer_label" value=""/>
            <input type="hidden" id="free_sock_customer_id" value=""/>
            <input type="hidden" id="free_sock_customer_ur_id" value=""/>
        </div>
        <div class="selectmain">
            <label class="btn-sm btn-primary col-12 rounded text-white d-flex"
                   id="reload_refund_list">Refund / Penukaran</label>
            <div id="refund_reload"></div>
            <a class="btn-sm btn-primary" id="product_barcode_btn"
               style="cursor:pointer;">Lengkapi Barcode</a>
        </div>
        {{--                                <div class="selectmain">--}}
        {{--                                    <label--}}
        {{--                                            class="btn btn-inventory col-12 rounded text-white d-flex font-weight-bold">Store</label>--}}
        {{--                                    <select class="arabic-select-store select-down " id="st_id"--}}
        {{--                                            name="st_id">--}}
        {{--                                        <option value="">- Store -</option>--}}
        {{--                                        @foreach ($data['st_id'] as $key => $value)--}}
        {{--                                            <option value="{{ $key }}">{{ $value }}</option>--}}
        {{--                                        @endforeach--}}
        {{--                                    </select>--}}
        {{--                                </div>--}}
    </div>
</div>

<section class="section">
    <div class="card">
        <div class="card-header">
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <div id="table1_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                    <div class="row dt-row">
                        <div class="col-sm-12">
                            <table class="table dataTable no-footer" id="table" aria-describedby="table1_info">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>E-mail</th>
                                        <th>Nomor HP</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form" autocomplete="off">
                    <input type="hidden" name="id" id="id">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama pelanggan">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan e-mail user">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="nohp" class="form-label">Nomor HP</label>
                        <input type="number" class="form-control" id="nohp" name="nohp" placeholder="Masukkan nomor hp user">
                        <div class="invalid-feedback"></div>
                    </div>
                    <label for="pw" class="form-label">Password <small class="text-muted" id="alertpw">*Isi jika ingin merubah password</small></label>
                    <div class="input-group mb-3">
                        <input type="password" id="pw" name="pw" class="form-control" placeholder="Masukkan password">
                        <span class="input-group-text" style="cursor: pointer;" id="pwtoggle"><i class="fas fa-eye-slash"></i></span>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Akses Menu -->
<div class="modal modal-lg fade" id="modala" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Akses menu user</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formakses" autocomplete="off">
                    <input type="hidden" name="id_akses" id="id_akses">
                    <div class="alert alert-warning d-none" role="alert" id="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> Pilih setidaknya 1 akses menu untuk user
                    </div>
                   
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal delete -->
<div class="modal fade" id="modald" tabindex="-1" aria-labelledby="modaldLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaldLabel">Hapus data user "<span><strong id="pelanggan"></strong></span>"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Yakin ingin menghapus data tersebut?. Data yang telah dihapus tidak dapat dikembalikan lagi.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btn-delete">Ya, hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js') ?>
<script src="{{ asset('assets/extensions/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/extensions/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/extensions/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>

<script>
    var table;
    var modal = $('#modal');
    var modala = $('#modala');
    var modald = $('#modald');

    document.addEventListener("DOMContentLoaded", function() {
        table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            info: true,
            paging: true,
            searching: true,
            stateSave: true,
            bDestroy: true,
            order: [],
            ajax: {
                url: '/user/datatable',
                method: 'POST',
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    width: 10
                },
                {
                    data: 'nama',
                    orderable: false,
                    width: 100
                },
                {
                    data: 'email',
                    orderable: false,
                    width: 100
                },
                {
                    data: 'nohp',
                    orderable: false,
                    width: 100
                },
                {
                    data: 'is_active',
                    orderable: false,
                    width: 100
                },
                {
                    data: 'action',
                    orderable: false,
                    width: 50
                },
            ],
            language: {
                url: '/assets/extensions/bahasa/id.json',
            },

        });
    });

    $(document).ready(function() {
        $(".menu").change(function() {
            var isChecked = $(this).prop("checked");

            var parentID = $(this).attr("id");
            $(".child[data-parent='" + parentID + "']").prop("checked", isChecked);
        });

        $(".child").change(function() {
            var parentID = $(this).attr("data-parent");

            var isAnyChildChecked = $(".child[data-parent='" + parentID + "']:checked").length > 0;
            $("#" + parentID).prop("checked", isAnyChildChecked);
        });
    });

    function changeStatus(id) {
        var isChecked = $('#set_active' + id);
        $.ajax({
            type: "POST",
            url: "/user/setStatus",
            data: {
                id: id
            },
            dataType: "JSON",
            beforeSend: function() {
                showblockUI();
            },
            complete: function() {
                hideblockUI();
            },
            success: function(response) {
                if (response.status) {
                    isChecked.next().text($(isChecked).is(':checked') ? 'Aktif' : 'Nonaktif');
                    toastr.success('Data Berhasil Diperbaharui');
                } else {
                    isChecked.prop('checked', isChecked.is(':checked') ? null : 'checked');
                    toastr.error('Data gagal Diperbaharui');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                isChecked.prop('checked', isChecked.is(':checked') ? null : 'checked');

            },
        });
    }

    function tambah() {
        $('#id').val('');

        $('#form')[0].reset();
        $('#alert').addClass('d-none');
        var form = $('#form input, #form select');
        form.removeClass('is-invalid is-valid');

        $('#alertpw').addClass('d-none');

        $('#title').text('Tambah data user');
        modal.modal('show');
    }

    function edit(id) {
        $.ajax({
            type: "POST",
            url: "/user/getdata",
            data: {
                id: id
            },
            dataType: "JSON",
            beforeSend: function() {
                showblockUI();
            },
            complete: function() {
                hideblockUI();
            },
            success: function(response) {
                if (response.status) {
                    $.each(response.child, function(index, childID) {
                        $("#child" + childID).prop("checked", true);
                    });

                    $.each(response.menu, function(index, menuID) {
                        $("#menu" + menuID).prop("checked", true);
                    });

                    var form = $('#form input, #form select');
                    form.removeClass('is-invalid is-valid');

                    $('#alertpw').removeClass('d-none');
                    $('#id').val(response.data.id);
                    $('#id_akses').val(response.akses);
                    $('#nama').val(response.data.nama);
                    $('#email').val(response.data.email);
                    $('#nohp').val(response.data.nohp);
                    $('#title').text('Edit data user');
                    modal.modal('show');
                }
            },
            error: function(jqXHR, textStatus, errorThrown, exception) {
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
            }
        });
    }

    function aksesMenu(id) {
        $.ajax({
            type: "POST",
            url: "/user/getAksesMenu",
            data: {
                id: id
            },
            dataType: "JSON",
            beforeSend: function() {
                showblockUI();
            },
            complete: function() {
                hideblockUI();
            },
            success: function(response) {
                if (response.status) {
                    $('#formakses')[0].reset();
                    $('#alert').addClass('d-none');
                    $.each(response.child, function(index, childID) {
                        $("#child" + childID).prop("checked", true);
                    });
                    $.each(response.menu, function(index, menuID) {
                        $("#menu" + menuID).prop("checked", true);
                    });
                    $('#id_akses').val(response.akses);
                    modala.modal('show');
                } else {
                    toastr.warning('Maaf, tidak menemukan data');
                }
            },
            error: function(jqXHR, textStatus, errorThrown, exception) {
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
            }
        });
    }

    function hapus(id, nama) {
        $('#pelanggan').text(nama);
        $('#btn-delete').attr('onclick', 'remove(' + id + ')');
        modald.modal('show');
    }

    function remove(id) {
        $.ajax({
            url: "/user/hapus",
            type: "POST",
            dataType: "JSON",
            data: {
                id: id
            },
            beforeSend: function() {
                showblockUI();
            },
            complete: function() {
                hideblockUI();
            },
            success: function(data) {
                toastr.success('Data Berhasil dihapus');
                modald.modal('hide');
                table.ajax.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
            }
        });
    }

    $("#pwtoggle").click(function() {
        if ($("#pw").attr('type') == 'password') {
            $("#pw").attr('type', 'text');
            $(this).html('<i class="fas fa-eye"></i>');
        } else {
            $("#pw").attr('type', 'password');
            $(this).html('<i class="fas fa-eye-slash"></i>');
        }
    });

    $('#form').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/user/simpan",
            data: $(this).serialize(),
            dataType: "JSON",
            beforeSend: function() {
                showblockUI();
            },
            complete: function() {
                hideblockUI();
            },
            success: function(response) {
                if (response.status) {
                    $('#form')[0].reset();
                    table.ajax.reload();
                    toastr.success(response.notif);
                    modal.modal('hide');
                } else {
                    $.each(response.errors, function(key, value) {
                        $('[name="' + key + '"]').addClass('is-invalid');
                        $('[name="' + key + '"]').next().text(value);
                        if (value == "") {
                            $('[name="' + key + '"]').removeClass('is-invalid');
                            $('[name="' + key + '"]').addClass('is-valid');
                        }
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown, exception) {
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
            }
        });
    });

    $('#formakses').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/user/simpanAkses",
            data: $(this).serialize(),
            dataType: "JSON",
            beforeSend: function() {
                showblockUI();
            },
            complete: function() {
                hideblockUI();
            },
            success: function(response) {
                if (response.status) {
                    table.ajax.reload();
                    toastr.success("Akses user berhasil diperbaharui");
                    modala.modal('hide');
                } else {
                    $.each(response.errors, function(key, value) {
                        $('[name="' + key + '"]').addClass('is-invalid');
                        $('[name="' + key + '"]').next().text(value);
                        if (value == "") {
                            $('[name="' + key + '"]').removeClass('is-invalid');
                            $('[name="' + key + '"]').addClass('is-valid');
                        }
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown, exception) {
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
            }
        });
    });
</script>
@endSection()