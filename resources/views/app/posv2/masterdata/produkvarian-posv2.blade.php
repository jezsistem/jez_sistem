@extends('app.layout')
@section('css')
<link href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="/assets/extensions/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" />
<link href="/assets/extensions/select2/dist/css/select2.min.css" rel="stylesheet" />
<link href="/assets/extensions/select2-bootstrap-5-theme-1.3.0/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<link href="/assets/extensions/select2-bootstrap-5-theme-1.3.0/select2-bootstrap-5-theme.rtl.min.css" rel="stylesheet" />
<style>
    .invsel {
        color: red;
        margin-top: 2px;
        font-size: small;
    }
</style>
@endsection
@section('content')

<div class="page-heading">
    <div class="row">
        <div class="col-6">
            <h3>Data Varian <span style="text-decoration: underline;"><?php echo $produk->nama_barang; ?></span></h3>
        </div>
        <div class="col-6 text-end">
            <button class="btn btn-primary" onclick="tambah()"><i class="fa fa-plus"></i>&nbsp; Tambah Varian</button>
        </div>
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
                                        <th>Satuan</th>
                                        <th>Nama Varian</th>
                                        <th>Harga Jual</th>
                                        <th>Harga Modal</th>
                                        <th>Kelola Stok</th>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form" autocomplete="off">
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="id_barang" id="id_barang" value="<?php echo $produk->id; ?>">
                    <div class="mb-3">
                        <label for="satuan" class="form-label">Satuan</label>
                        <select class="form-control" id="satuan" name="satuan">
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Varian</label>
                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama varian">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga Jual</label>
                        <input type="text" class="form-control harga" id="harga" name="harga" placeholder="Masukkan harga jual varian">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="modal" class="form-label">Harga Modal</label>
                        <input type="text" class="form-control harga" id="harga_modal" name="modal" placeholder="Masukkan harga modal varian">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" placeholder="Masukkan keterangan varian"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <div class="form-check">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="form-check-input form-check-primary" checked="" name="stokcheck" id="stokcheck">
                                <label class="form-check-label" for="stokcheck">Kelola Stok</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 stok">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="text" class="form-control nomor" id="stok" name="stok" placeholder="Masukkan stok saat ini">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3 stok">
                        <label for="stokmin" class="form-label">Stok Minimum</label>
                        <input type="text" class="form-control nomor" id="stokmin" name="stokmin" placeholder="Masukkan stok minimum">
                        <div class="invalid-feedback"></div>
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
                <h5 class="modal-title" id="modaldLabel">Hapus data varian "<span><strong id="varian"></strong></span>"</h5>
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

<!-- Modal stok -->
<div class="modal fade" id="modals" tabindex="-1" aria-labelledby="modaldLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaldLabel">Kelola stok varian "<span><strong class="varian"></strong></span>"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0"><strong>Stok sekarang : <span id="stok-sekarang"></span></strong></p>
                <small class="text-muted">Stok min : <span id="stok-min"></span></small>
                <div id="list-stok">
                </div>
                <div id="aturstok" class="collapse">
                    <hr>
                    <div class="row">
                        <div class="col-8">
                            <select class="form-select" id="tipe" name="tipe">
                                <option value="1">Penambahan Stok</option>
                                <option value="2">Pengurangan Stok</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-4">
                            <input class="form-control nomor" type="number" id="jumlah" name="jumlah" placeholder="Qty">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <button class="btn btn-success mt-3 w-100" id="btnkonfirmasi">Konfirmasi</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary collapsed" data-bs-toggle="collapse" data-bs-target="#aturstok" aria-expanded="false" aria-controls="collapseExample"><i class="fas fa-sliders-h"></i>&nbsp; Atur Stok</button>
            </div>
        </div>
    </div>
</div>


@endSection
@section('js')

<script src="/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/extensions/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="/assets/extensions/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
<script src="/assets/extensions/select2/dist/js/select2.min.js"></script>
<script src="/assets/extensions/select2/dist/js/select2.full.min.js"></script>

<script>
    var table;
    var modal = $('#modal');
    var modald = $('#modald');
    var modals = $('#modals');

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
                url: '/produk/datatableVarian',
                method: 'POST',
                data: function(d) {
                    d.id_barang = <?php echo $produk->id; ?>;
                }
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    width: 10
                },
                {
                    data: 'nama_satuan',
                    orderable: false,
                    width: 100
                },
                {
                    data: 'nama_varian',
                    orderable: false,
                    width: 100
                },
                {
                    data: 'harga_jual',
                    orderable: false,
                    className: 'text-md-end',
                    width: 100
                },
                {
                    data: 'harga_modal',
                    orderable: false,
                    className: 'text-md-end',
                    width: 100
                },
                {
                    data: 'stok',
                    orderable: false,
                    className: 'text-md-center',
                    width: 50
                },
                {
                    data: 'is_active',
                    orderable: false,
                    className: 'text-md-center',
                    width: 100
                },
                {
                    data: 'action',
                    orderable: false,
                    className: 'text-md-center',
                    width: 100
                },
            ],
            language: {
                url: '/assets/extensions/bahasa/id.json',
            },

        });
    });

    $(document).ready(function() {
        $(".harga").keyup(function(e) {
            $(this).val(formatRupiah($(this).val(), "Rp. "));
        });
    });

    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, "").toString(),
            split = number_string.split(","),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? "." : "";
            rupiah += separator + ribuan.join(".");
        }

        rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
        return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
    }

    function changeStatus(id) {
        var isChecked = $('#set_active' + id);
        $.ajax({
            type: "POST",
            url: "/produk/setStatusVarian",
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

    function changeStok(id) {
        var isChecked = $('#set_active2' + id);
        $.ajax({
            type: "POST",
            url: "/produk/setStok",
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
                    if (response.data.kelola_stok == 0) {
                        $('#btnkel' + response.id).addClass('disabled');
                    } else {
                        $('#btnkel' + response.id).removeClass('disabled');
                    }
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

    function stok(id) {
        $.ajax({
            type: "POST",
            url: "/produk/getStok",
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
                $('.varian').text(response.data.nama_varian);
                $('#stok-sekarang').text(response.data.stok);
                $('#stok-min').text(response.data.stok_min);

                $('#list-stok').html(response.html);

                $('#btnkonfirmasi').attr('onclick', 'updateStok(' + response.data.id + ')');
                $('#jumlah').val('');
                $('.collapse').removeClass('show');
                $('#tipe, #jumlah').removeClass('is-invalid is-valid');
                modals.modal('show');
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

    function updateStok(id_varian) {
        $.ajax({
            type: "POST",
            url: "/produk/updateStok",
            data: {
                id_varian: id_varian,
                tipe: $('#tipe').val(),
                jumlah: $('#jumlah').val()
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
                    if (response.data.tipe == 1) {
                        var html = `<div class="card mb-2 mt-2" style="background-color: #f2f7ff;">
                            <div class="card-body">
                                Penambahan Stok &nbsp;<span class="text-success">+` + response.data.jumlah + `</span>
                                <br>
                                <small style="font-size: x-small;">` + response.date + `</small>
                            </div>
                         </div>`;
                    } else {
                        var html = `<div class="card mb-2 mt-2" style="background-color: #f2f7ff;">
                            <div class="card-body">
                                Pengurangan Stok &nbsp;<span class="text-danger">-` + response.data.jumlah + `</span>
                                <br>
                                <small style="font-size: x-small;">` + response.date + `</small>
                            </div>
                         </div>`;
                    }
                    toastr.success('Berhasil update stok');
                    $('#stok-sekarang').text(response.stok);
                    $('#nostok').hide();
                    $('#list-stok').append(html);
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
    }

    function tambah() {
        $('#id').val('');
        $('.stok').show();

        $('#form')[0].reset();
        var form = $('#form input, #form select');
        form.removeClass('is-invalid is-valid');
        $('#satuan').val('').trigger('change');

        $('#title').text('Tambah Varian');
        modal.modal('show');
    }

    function edit(id) {
        $.ajax({
            type: "POST",
            url: "/produk/getdataVarian",
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
                    var form = $('#form input, #form select');
                    form.removeClass('is-invalid is-valid');

                    var newSatuan = new Option(response.data.nama_satuan, response.data.id_satuan, true, true);
                    $('#satuan').append(newSatuan).trigger('change');

                    if (response.data.kelola_stok == 0) {
                        $('#stokcheck').removeAttr('checked', 'checked');
                        $('.stok').hide();
                    } else {
                        $('#stokcheck').attr('checked', 'checked');
                        $('.stok').show();
                    }

                    $('#nama').val(response.data.nama_varian);
                    $('#stok').val(response.data.stok);
                    $('#stokmin').val(response.data.stok_min);

                    $('#id').val(response.data.id);
                    $('#harga').val(response.harga);
                    $('#harga_modal').val(response.modal);
                    $('#keterangan').val(response.data.keterangan);
                    $('#stok').val(response.data.stok);
                    $('#title').text('Edit Varian');
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

    function hapus(id, kode) {
        $('#varian').text(kode);
        $('#btn-delete').attr('onclick', 'remove(' + id + ')');
        modald.modal('show');
    }

    function remove(id) {
        $.ajax({
            url: "/produk/hapusVarian",
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
            success: function(response) {
                if (response.status) {
                    toastr.success('Data Berhasil dihapus');
                    modald.modal('hide');
                    table.ajax.reload();
                } else {
                    toastr.warning('Maaf, anda tidak dapat menghapus data tersebut karna telah berelasi dengan data lain.');
                    modald.modal('hide');
                }
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

    $('.nomor').keypress(function(e) {
        var charCode = (e.which) ? e.which : event.keyCode
        if (String.fromCharCode(charCode).match(/[^0-9]/g))
            return false;
    });

    $('#stokcheck').on('click', function() {
        var isChecked = $("#stokcheck").prop('checked');
        if (isChecked) {
            $('.stok').show();
        } else {
            $('.stok').hide();
            $('#stok').val('').removeClass('is-valid is-invalid');
            $('#stokmin').val('').removeClass('is-valid is-invalid');
        }
    });

    $('#satuan').select2({
        placeholder: 'Pilih Satuan',
        allowClear: false,
        theme: 'bootstrap-5',
        dropdownParent: modal,
        width: '100%',
        language: {
            noResults: function() {
                return "Data tidak ditemukan";
            }
        },
        ajax: {
            type: "POST",
            url: "/produk/getSatuan",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                }
            },
            processResults: function(data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });

    $('#form').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/produk/simpanVarian",
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
                        if (key == 'satuan') {
                            $('[name="' + key + '"]').next().next().text(value);
                        } else {
                            $('[name="' + key + '"]').next().text(value);
                        }
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


@endSection