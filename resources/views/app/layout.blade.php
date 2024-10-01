
<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>POS By sikoding</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('assetsnew/assets/img/logo.svg')}}" type="image/x-icon" rel="shortcut icon"/>
    <link href="{{ asset('assetsnew/assets/compiled/css/app.css') }}" rel="stylesheet" />
    <link href="{{ asset('assetsnew/assets/compiled/css/app-dark.css') }}" rel="stylesheet" />
    <link href="{{ asset('assetsnew/assets/extensions/toastr/toastr.min.css') }}" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <style>
        .container-foto {
            max-width: 960px;
            margin: 30px auto;
            padding: 20px;
        }

        .avatar-upload {
            position: relative;
            max-width: 205px;
            margin: 50px auto;
        }

        .avatar-upload .avatar-edit {
            position: absolute;
            right: 12px;
            z-index: 1;
            top: 10px;
        }

        .avatar-upload .avatar-edit input {
            display: none;
        }

        .avatar-upload .avatar-edit input+label {
            display: inline-block;
            width: 34px;
            height: 34px;
            margin-bottom: 0;
            border-radius: 100%;
            background: #FFFFFF;
            border: 1px solid transparent;
            box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
            cursor: pointer;
            font-weight: normal;
            transition: all 0.2s ease-in-out;
        }

        .avatar-upload .avatar-edit input+label:hover {
            background: #f1f1f1;
            border-color: #d6d6d6;
        }

        .avatar-upload .avatar-edit input+label:after {
            content: "\f044";
            font-family: 'Font Awesome\ 5 Free';
            color: #757575;
            position: absolute;
            top: 10px;
            left: 0;
            right: 0;
            text-align: center;
            margin: auto;
        }

        .avatar-upload .avatar-preview {
            width: 192px;
            height: 192px;
            position: relative;
            border-radius: 100%;
            border: 6px solid #F8F8F8;
            box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
        }

        .avatar-upload .avatar-preview>div {
            width: 100%;
            height: 100%;
            border-radius: 100%;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }
    </style>


</head>
<body>
    <script src="{{asset('assetsnew/assets/static/js/initTheme.js') }}"></script>
   
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="logo">
                            <a href="/dashboard"><img src="{{ asset('assetsnew/assets/img/logo.svg') }}" alt="Logo" style="height: 40px;" /></a>
                        </div>
                        <div class="theme-toggle d-flex gap-2 align-items-center mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--system-uicons" width="20" height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21">
                                <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2" opacity=".3"></path>
                                    <g transform="translate(-210 -1)">
                                        <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                                        <circle cx="220.5" cy="11.5" r="4"></circle>
                                        <path d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2"></path>
                                    </g>
                                </g>
                            </svg>
                            <div class="form-check form-switch fs-6">
                                <input class="form-check-input me-0" type="checkbox" id="toggle-dark" style="cursor: pointer" />
                                <label class="form-check-label"></label>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--mdi" width="20" height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                <path fill="currentColor" d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z"></path>
                            </svg>
                        </div>
                        <div class="sidebar-toggler x">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>
                            <li class="sidebar-item active">
                                                    <a href="/pos_v2" class="sidebar-link">
                                                        <i class="bi bi-grid-fill"></i>
                                                        <span>Dashboard</span>
                                                    </a>
                                                </li>
                                                                                                                                <li class="sidebar-item has-sub ">
                                                    <a href="/master" class="sidebar-link">
                                                        <i class="bi bi-database-fill"></i>
                                                        <span>Master Data</span>
                                                    </a>
                                                    <ul class="submenu submenu-closed" style="--submenu-height: 172px;">
                                                                                                    <li class="submenu-item ">
                                                                <a href="/kategori" class="submenu-link">Kategori Produk</a>
                                                            </li>
                                                                                                    <li class="submenu-item ">
                                                                <a href="/satuan" class="submenu-link">Satuan</a>
                                                            </li>
                                                                                                    <li class="submenu-item ">
                                                                <a href="/bahan-baku" class="submenu-link">Bahan Baku</a>
                                                            </li>
                                                                                                    <li class="submenu-item ">
                                                                <a href="/produk" class="submenu-link">Produk</a>
                                                            </li>
                                                                                            </ul>
                                                </li>
                                                                                                                                <li class="sidebar-item has-sub ">
                                                    <a href="/user" class="sidebar-link">
                                                        <i class="bi bi-people-fill"></i>
                                                        <span>User</span>
                                                    </a>
                                                    <ul class="submenu submenu-closed" style="--submenu-height: 86px;">
                                                                                                    <li class="submenu-item ">
                                                                <a href="/user" class="submenu-link">Data User</a>
                                                            </li>
                                                                                                    <li class="submenu-item ">
                                                                <a href="/pelanggan" class="submenu-link">Pelanggan</a>
                                                            </li>
                                                                                            </ul>
                                                </li>
                                                                                                                                <li class="sidebar-item has-sub ">
                                                    <a href="/pencatatan" class="sidebar-link">
                                                        <i class="fas fa-address-book"></i>
                                                        <span>Pencatatan</span>
                                                    </a>
                                                    <ul class="submenu submenu-closed" style="--submenu-height: 172px;">
                                                                                                    <li class="submenu-item ">
                                                                <a href="/pengeluaran" class="submenu-link">Pengeluaran</a>
                                                            </li>
                                                                                                    <li class="submenu-item ">
                                                                <a href="/pemasukan" class="submenu-link">Pemasukan</a>
                                                            </li>
                                                                                                    <li class="submenu-item ">
                                                                <a href="/utang" class="submenu-link">Utang</a>
                                                            </li>
                                                                                                    <li class="submenu-item ">
                                                                <a href="/piutang" class="submenu-link">Piutang</a>
                                                            </li>
                                                                                            </ul>
                                                </li>
                                                                                                                                <li class="sidebar-item has-sub ">
                                                    <a href="/report" class="sidebar-link">
                                                        <i class="bi bi-journal-text"></i>
                                                        <span>Laporan</span>
                                                    </a>
                                                    <ul class="submenu submenu-closed" style="--submenu-height: 129px;">
                                                                                                    <li class="submenu-item ">
                                                                <a href="/penjualan" class="submenu-link">Penjualan Kasir</a>
                                                            </li>
                                                                                                    <li class="submenu-item ">
                                                                <a href="/bestseller" class="submenu-link">Produk Best Seller</a>
                                                            </li>
                                                                                                    <li class="submenu-item ">
                                                                <a href="/summary" class="submenu-link">Laporan Penjualan</a>
                                                            </li>
                                                                                            </ul>
                                                </li>
                                                                                                                                <li class="sidebar-item has-sub ">
                                                    <a href="/biaya_layanan" class="sidebar-link">
                                                        <i class="bi bi-coin"></i>
                                                        <span>Biaya Layanan</span>
                                                    </a>
                                                    <ul class="submenu submenu-closed" style="--submenu-height: 86px;">
                                                                                                    <li class="submenu-item ">
                                                                <a href="/biaya" class="submenu-link">Biaya Lainnya</a>
                                                            </li>
                                                                                                    <li class="submenu-item ">
                                                                <a href="/discount" class="submenu-link">Diskon</a>
                                                            </li>
                                                                                            </ul>
                                                </li>
                                                                                                                                <li class="sidebar-item has-sub ">
                                                    <a href="/config" class="sidebar-link">
                                                        <i class="bi bi-gear-fill"></i>
                                                        <span>Konfigurasi</span>
                                                    </a>
                                                    <ul class="submenu submenu-closed" style="--submenu-height: 129px;">
                                                                                                    <li class="submenu-item ">
                                                                <a href="/reward" class="submenu-link">Reward Transaksi</a>
                                                            </li>
                                                                                                    <li class="submenu-item ">
                                                                <a href="/tipebayar" class="submenu-link">Tipe Pembayaran</a>
                                                            </li>
                                                                                                    <li class="submenu-item ">
                                                                <a href="/setting" class="submenu-link">Setting Toko</a>
                                                            </li>
                                                                                            </ul>
                                                </li>
                                                                                                                                <li class="sidebar-item ">
                                                    <a href="/kasir" class="sidebar-link">
                                                        <i class="bi bi-grid-1x2-fill"></i>
                                                        <span>Kasir</span>
                                                    </a>
                                                </li>
                                                                                        </ul>
                                </div>
                            <div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; height: 598px; right: 0px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 585px;"></div></div></div>
                        </div>
               

{{--  --}}
                    </ul>
                </div>
            </div>
        </div>
        <div id="main" class="layout-navbar navbar-fixed">
            <header>
                <nav class="navbar navbar-expand navbar-light navbar-top">
                    <div class="container-fluid">
                        <a href="javascript:void(0)" class="burger-btn d-block">
                            <i class="bi bi-justify fs-3"></i>
                        </a>

                        <a href="javascript:void(0)" class="burger-btn d-block ms-3" id="fullScreenBtn">
                            <i class="bi bi-display fs-3"></i>
                        </a>

                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ms-auto mb-lg-0">
                            </ul>
                            <div class="dropdown">
                                <a href="#" data-bs-toggle="dropdown" aria-expanded="false" class="">
                                    <div class="user-menu d-flex">
                                        <div class="user-name text-end me-3">
                                            <h6 class="mb-0 text-gray-600">< ></h6>
                                            <p class="mb-0 text-sm text-gray-600">Administrator</p>
                                        </div>
                                        <div class="user-img d-flex align-items-center">
                                            <div class="avatar avatar-md">
                                                <img src="assetsnew/assets/compiled/jpg/1.jpg">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton" style="min-width: 11rem">
                                    <li>
                                        <a class="dropdown-item" href="/login/logout"><i class="icon-mid bi bi-box-arrow-left me-2"></i>
                                            Logout</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </header>
            <div id="main-content">
                @yield('content')

            </div>
        </div>
    </div>
    <!-- Modal Profile-->
    <div class="modal fade" id="modalp" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ubah Profile Toko</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="javascript:void(0)" id="formp" autocomplete="off">
                        <input type="hidden" name="id" id="id">
                        <div class="container-profile mb-3">
                            <div class="avatar-upload">
                                <div class="avatar-edit">
                                    <input type='file' name="logo" id="imageUpload" accept=".png, .jpg, .jpeg" />
                                    <label for="imageUpload"></label>
                                </div>
                                <div class="avatar-preview">
                                    @if (session('logo'))
                                        <div id="imagePreview" style="background-image: url('{{ asset('/assetsnew/assets/img/logo/' . session('logo')) }}');">
                                        </div>
                                    @else
                                        <div id="imagePreview" style="background-image: url('{{ asset('/assetsnew/assets/compiled/jpg/1.jpg') }}');">
                                        </div>
                                    @endif
                                </div>                                
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="namatoko" class="form-label">Nama Toko</label>
                            <input type="text" class="form-control" name="namatoko" placeholder="Masukkan nama toko" value="< '); ?>">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="nohptoko" class="form-label">Nomor HP</label>
                            <input type="number" class="form-control" name="nohptoko" placeholder="Masukkan nomor hp toko" value="< '); ?>">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="emailtoko" class="form-label">E-mail</label>
                            <input type="text" class="form-control" name="emailtoko" placeholder="Masukkan e-mail toko" value="< o'); ?>">
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

    <script src="{{ asset('assetsnew/assets/static/js/components/dark.js') }}"></script>
    <script src="{{ asset('assetsnew/extensions/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assetsnew/assets/compiled/js/app.js') }}"></script>
    <script src="{{ asset('assetsnew/assets/extensions/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assetsnew/assets/extensions/blockui/jquery.blockui.min.js') }}"></script>
    <script src="{{ asset('assetsnew/assets/extensions/toastr/toastr.min.js') }}"></script>

    <script>
        var modalp = $('#modalp');

        $(document).ready(function() {
            $('#fullScreenBtn').on('click', function() {
                $(this).attr("id", "exitFullScreenBtn");
                const element = document.documentElement;

                if (element.requestFullscreen) {
                    element.requestFullscreen();
                } else if (element.mozRequestFullScreen) {
                    element.mozRequestFullScreen();
                } else if (element.webkitRequestFullscreen) {
                    element.webkitRequestFullscreen();
                } else if (element.msRequestFullscreen) {
                    element.msRequestFullscreen();
                }

                $('#exitFullScreenBtn').on('click', function() {
                    $(this).attr("id", "fullScreenBtn");
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    } else if (document.mozCancelFullScreen) {
                        document.mozCancelFullScreen();
                    } else if (document.webkitExitFullscreen) {
                        document.webkitExitFullscreen();
                    } else if (document.msExitFullscreen) {
                        document.msExitFullscreen();
                    }
                });
            });

            $('#exitFullScreenBtn').on('click', function() {
                $(this).attr("id", "fullScreenBtn");
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            });
        });

        function showblockUI() {
            jQuery.blockUI({
                message: 'Sedang Proses...',
                baseZ: 2000,
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .5,
                    color: '#fff'
                }
            });
        }

        function hideblockUI() {
            $.unblockUI();
        }

        function change_profile() {
            modalp.modal('show');
        }

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
                    $('#imagePreview').hide();
                    $('#imagePreview').fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#imageUpload").change(function() {
            readURL(this);
        });

        $('#formp').submit(function(e) {
            e.preventDefault();
            var form = $('#formp')[0];
            var formData = new FormData(form);
            $.ajax({
                type: "POST",
                url: "/dashboard/change_profile",
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
                        toastr.success('Profile berhasil diperbaharui');
                        modalp.modal('hide');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        $.each(response.errors, function(key, value) {
                            $('[name="' + key + '"]').addClass('is-invalid');
                            if (key != 'logo') {
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

@yield('js')
</body>

</html>