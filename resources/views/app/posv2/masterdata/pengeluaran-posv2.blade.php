@extends('app.layout')
@section('css')
<link href="/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="/assets/extensions/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" />
<style>
    .img-preview {
        max-width: 100%;
        max-height: 100px;
    }
</style>

@endsection
@section('content')


<div class="page-heading">
    <div class="row">
        <div class="col-6">
            <h3>Daftar Pengeluaran</h3>
        </div>
        <div class="col-6 text-end">
            <button class="btn btn-primary" onclick="tambah()"><i class="fa fa-plus"></i>&nbsp; Tambah Pengeluaran</button>
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
                                        <th>Kategori</th>
                                        <th>Pelanggan</th>
                                        <th>Jumlah</th>
                                        <th>Catatan</th>
                                        <th>Tanggal</th>
                                        <th>Foto</th>
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
            <div class="modal-body">
                <form id="form" autocomplete="off">
                    <input type="hidden" name="id" id="id">
                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <select class="form-select" name="kategori" id="kategori">
                            <option disabled selected>Pilih kategori pengeluaran</option>
                            <?php foreach ($kategori as $key) : ?>
                                <option value="<?php echo $key->id; ?>"><?php echo $key->nama_kategori; ?></option>
                            <?php endforeach ?>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Total Pengeluaran</label>
                        <input type="text" class="form-control harga" id="jumlah" name="jumlah" placeholder="Masukkan jumlah pengeluaran">
                        <div class="invalid-feedback"></div>
                    </div>
                    <hr>
                    <p class="text-center">Informasi Opsional</p>
                    <div class="mb-3">
                        <label for="foto" class="form-label">Upload Foto</label>
                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                        <div class="invalid-feedback"></div>
                        <p class="text-muted mt-1" style="font-size: x-small;">Silakan upload foto yang berkaitan dengan transaksi ini.</p>
                        <div id="preview"></div>
                    </div>
                    <div class="mb-3">
                        <label for="foto" class="form-label">Catatan</label>
                        <textarea class="form-control" name="catatan" id="catatan" placeholder="Masukkan catatan transaksi"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="pelanggan" class="form-label">Pelanggan</label>
                        <select class="form-select" name="pelanggan" id="pelanggan">
                            <option disabled selected>Pilih pelanggan</option>
                            <?php foreach ($pelanggan as $key) : ?>
                                <option value="<?php echo $key->id; ?>"><?php echo $key->nama; ?></option>
                            <?php endforeach ?>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="tgl" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tgl" name="tgl">
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
                <h5 class="modal-title" id="modaldLabel">Hapus data pengeluaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Yakin ingin menghapus data tersebut?. Data yang sudah dihapus tidak dapat kembalikan lagi.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btn-delete">Ya, hapus</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')

<script src="/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/extensions/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="/assets/extensions/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>

<script src="/assets/extensions/fancybox/fancybox.js"></script>

<script>
    var table;
    var modal = $('#modal');
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
                url: '/pengeluaran/datatable',
                method: 'POST',
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    width: 10
                },
                {
                    data: 'kategori',
                    orderable: false,
                    width: 200
                },
                {
                    data: 'pelanggan',
                    orderable: false,
                    width: 200
                },
                {
                    data: 'jumlah',
                    orderable: false,
                    className: 'text-end',
                    width: 200
                },
                {
                    data: 'catatan',
                    orderable: false,
                    width: 400
                },
                {
                    data: 'tgl',
                    orderable: false,
                },
                {
                    data: 'foto',
                    orderable: false,
                    width: 100
                },
                {
                    data: 'action',
                    orderable: false,
                    className: 'text-center',
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

    function tambah() {
        $('#id').val('');
        $('#preview').empty();

        var today = new Date().toISOString().slice(0, 10);
        $('#tgl').attr("value", today);

        $('#form')[0].reset();
        var form = $('#form input, #form select');
        form.removeClass('is-invalid is-valid');

        $('#title').text('Tambah data pengeluaran');
        modal.modal('show');
    }

    function edit(id) {
        $.ajax({
            type: "POST",
            url: "/pengeluaran/getdata",
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

                    $('#foto').val(null);
                    $('#preview').html('<img src="/assets/img/pengeluaran/' + response.data.foto + '" alt="Preview Gambar" class="img-preview rounded">');

                    $('#id').val(response.data.id);
                    $('#kategori').val(response.data.id_kategori_pengeluaran).attr('selected', 'selected');
                    $('#pelanggan').val(response.data.id_pelanggan).attr('selected', 'selected');
                    $('#jumlah').val('Rp. ' + response.data.jumlah);
                    $('#catatan').val(response.data.catatan);
                    $('#tgl').val(response.data.tgl);
                    $('#title').text('Edit data pengeluaran');
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

    function hapus(id, foto) {
        $('#btn-delete').attr('onclick', 'remove(' + id + ', \'' + foto + '\')');
        modald.modal('show');
    }

    function remove(id, foto) {
        $.ajax({
            url: "/pengeluaran/hapus",
            type: "POST",
            dataType: "JSON",
            data: {
                id: id,
                foto: foto
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

    $('#foto').on('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(event) {
                var imgSrc = event.target.result;
                $('#preview').html('<img src="' + imgSrc + '" alt="Preview Gambar" class="img-preview rounded">');
            };
            reader.readAsDataURL(file);
        }
    });

    $('#form').submit(function(e) {
        e.preventDefault();
        var form = $('#form')[0];
        var formData = new FormData(form);
        $.ajax({
            type: "POST",
            url: "/pengeluaran/simpan",
            data: formData,
            processData: false,
            contentType: false,
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
</script>

@endsection