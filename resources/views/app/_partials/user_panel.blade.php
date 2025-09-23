<!-- begin::User Panel-->
<div id="kt_quick_user" class="offcanvas offcanvas-right p-10">
    <!--begin::Header-->
    <div class="offcanvas-header d-flex align-items-center justify-content-between pb-5">
        <h3 class="font-weight-bold m-0 text-black">{{ $data['user']->u_name }}</h3>
        <a href="#" id="kt_quick_user_close">
            <i class="ki-outline ki-cross-square text-black fs-2x"></i>
        </a>
    </div>
    <!--end::Header-->
    <!--begin::Content-->
    <div class="offcanvas-content pr-5 mr-n5">
        <!--begin::Header-->
        <div class="d-flex align-items-center mt-5">
            <div class="symbol symbol-100 mr-5">
                @if($data['user']->u_photo == '' || $data['user']->u_photo == null)
                    <div class="symbol-label"
                         style="background-image:url('{{ asset('app') }}/assets/media/users/default.jpg')"></div>
                @else
                    <div class="symbol-label"
                         style="background-image:url('{{ asset($data['user']->u_photo) }}');"></div>
                @endif
                <i class="symbol-badge bg-success"></i>

                <button class="btn btn-sm btn-primary position-absolute" id="btnChangePhoto"
                        style="bottom: -10px; left: 50%; transform: translateX(-50%); opacity: 0.7; font-size: 12px; padding: 2px 8px;">
                    Change
                </button>
            </div>
            <div class="d-flex flex-column">
                <div class="font-weight-semibold fs-5 text-black mt-1">{{ $data['user']->u_name }}</div>
                <div class="navi mt-1">
                    <a href="#" class="navi-item">
                        <span class="navi-link p-0 pb-2">
                            <span class="navi-icon mr-1">
                                <span class="svg-icon svg-icon-lg svg-icon-primary">
                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Mail-notification.svg-->
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                         width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path d="M21,12.0829584 C20.6747915,12.0283988 20.3407122,12 20,12 C16.6862915,12 14,14.6862915 14,18 C14,18.3407122 14.0283988,18.6747915 14.0829584,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,12.0829584 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z"
                                                  fill="#000000"/>
                                            <circle fill="#000000" opacity="0.3" cx="19.5" cy="17.5" r="2.5"/>
                                        </g>
                                    </svg>
                                    <!--end::Svg Icon-->
                                </span>
                            </span>
                            <span class="navi-text text-gray-400 text-hover-primary">{{ $data['user']->u_email }}</span>
                        </span>
                    </a>
                    <a href="{{ url('logout') }}" class="btn btn-sm btn-dark font-weight-bolder py-2 px-5">Logout</a>
                </div>
            </div>
        </div>
        <!--end::Header-->
        <!--begin::Separator-->
        <div class="separator separator-dashed mt-8 mb-5"></div>


        <div id="area-uploads" style="display: none;">
            <form action="{{ route('upload.photo') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="container">
                    <div class="row">
                        <div class="form-group">
                            <label class="control-label">Upload File</label>
                            <img id="preview-image" src="" alt="Image Preview" style="display: none; width: 200px;">

                            <div class="dropzone-wrapper" ondragover="handleDragOver(event)" ondrop="handleDrop(event)">

                                <div class="dropzone-desc">
                                    <i class="glyphicon glyphicon-download-alt"></i>

                                    <p>Drag and drop an image file here, or click to select one.</p>
                                </div>
                                <input type="file" name="img_logo" class="dropzone" onchange="previewFile(event)">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <button type="submit" class="btn btn-primary pull-right">change</button>
                    </div>
                </div>
            </form>
            <!-- Image Preview -->
            <div class="separator separator-dashed mt-8 mb-5"></div>

        </div>

        <script>
            document.getElementById('btnChangePhoto').addEventListener('click', function () {
                const areaUploads = document.getElementById('area-uploads');
                const btnChangePhoto = document.getElementById('btnChangePhoto');

                // Toggle the display property between 'none' and 'block'
                if (areaUploads.style.display === 'none' || areaUploads.style.display === '') {
                    areaUploads.style.display = 'block';  // Show the div
                    btnChangePhoto.textContent = 'Cancel'; // Change button text to 'Cancel'
                    console.log('Button clicked! Displaying upload area.');
                } else {
                    areaUploads.style.display = 'none';  // Hide the div
                    btnChangePhoto.textContent = 'Change'; // Change button text to 'Change Photo'
                    console.log('Button clicked! Hiding upload area.');
                }
            });

            function handleDragOver(event) {
                event.preventDefault();
                const dropzone = document.querySelector('.dropzone-wrapper');
                dropzone.classList.add('dragover');
            }

            function handleDrop(event) {
                event.preventDefault();
                const dropzone = document.querySelector('.dropzone-wrapper');
                dropzone.classList.remove('dragover');
                const fileInput = document.querySelector('.dropzone');
                fileInput.files = event.dataTransfer.files;
                previewFile({target: fileInput});
            }

            function previewFile(event) {
                const file = event.target.files[0];
                const previewImage = document.getElementById('preview-image'); // Make sure this element exists
                const previewZone = document.querySelector('.preview-zone');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewImage.src = e.target.result; // Set the image preview source
                        previewImage.style.display = 'block'; // Show the preview image
                        previewZone.classList.remove('hidden'); // Make the preview zone visible
                    };
                    reader.readAsDataURL(file);
                }
            }
        </script>

        <!-- Show Toastr Notification -->
        @if (session('success'))
            <script>
                toastr.success('{{ session('success') }}');
            </script>
        @endif

        @if (session('error'))
            <script>
                toastr.error('{{ session('error') }}', 'Nonaktif');
            </script>
        @endif

        <style>
            .dropzone-wrapper {
                border: 2px dashed #007bff;
                border-radius: 5px;
                padding: 20px;
                text-align: center;
                position: relative;
                cursor: pointer;
                background: #f8f9fa;
                transition: background 0.3s ease-in-out;
            }

            .dropzone-wrapper.dragover {
                background: #e9ecef;
            }

            .dropzone {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                opacity: 0;
                cursor: pointer;
            }

            .dropzone-desc {
                font-size: 16px;
                color: #6c757d;
            }

            .preview-zone {
                margin-top: 15px;
            }

            .hidden {
                display: none;
            }
        </style>


        <!--end::Separator-->
        <!--begin::Nav-->
        <div class="navi navi-spacer-x-0 p-0">
            <!--begin::Item-->
            <a href="#" data-toggle="modal" data-target="#staffModal" class="navi-item">
                <div class="navi-link">
                    <i class="ki-outline ki-document bg-icon-sm fs-2 mr-2"></i>
                    <div class="navi-text">
                        <div class="font-weight-bold">Data Pribadi</div>
                    </div>
                </div>
            </a>

            <a href="#" data-toggle="modal" data-target="#ChangePasswordModal" class="navi-item">
                <div class="navi-link">
                    <i class="ki-outline ki-key bg-icon-sm fs-2 mr-2"></i>
                    <div class="navi-text">
                        <div class="font-weight-bold">Ganti Password</div>
                    </div>
                </div>
            </a>
            <!--end:Item-->
        </div>
        <!--end::Nav-->
        <!--begin::Separator-->
        <div class="separator separator-dashed my-7"></div>
        <!--end::Separator-->
    </div>
    <!--end::Content-->
</div>
<!-- end::User Panel-->


<!-- Modal-->
<div class="modal fade" id="ChangePasswordModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_password">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Ganti Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Password Lama
                                <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="old_password" id="old_password" required/>
                        </div>
                        <div class="form-group">
                            <label>Password Baru
                                <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" id="password" required/>
                        </div>
                        <div class="form-group">
                            <label>Konfirmasi Password Baru
                                <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password"
                                   required/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup
                    </button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="change_password_btn">Ganti</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal-->
<div class="modal fade" id="BalanceNotifModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-dark" id="exampleModalLabel">[Reminder] Besok system akan shutdown otomatis
                    karena saldo dibawah $5.92, segera Topup</h5>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal -->
<div class="modal fade" id="staffModal" tabindex="-1" role="dialog" aria-labelledby="staffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form action="#" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="staffModalLabel">Tambah Data Staf</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">

                            <!-- Kiri -->
                            <div class="col-md-6">
                                <div class="card shadow-sm mb-3">
                                    <div class="card-header bg-light">
                                        <strong>Identitas</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>NIK KTP</label>
                                            <input type="text" name="nik_ktp" class="form-control" required>
                                            <input type="file" name="foto_ktp" class="form-control mt-2" accept="image/*">
                                        </div>
                                        <div class="form-group">
                                            <label>NPWP</label>
                                            <input type="text" name="npwp" class="form-control">
                                            <input type="file" name="foto_npwp" class="form-control mt-2" accept="image/*">
                                        </div>
                                        <div class="form-group">
                                            <label>Tanggal Lahir</label>
                                            <input type="date" name="tanggal_lahir" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Alamat Domisili</label>
                                            <textarea name="alamat_domisili" class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kanan -->
                            <div class="col-md-6">
                                <div class="card shadow-sm mb-3">
                                    <div class="card-header bg-light">
                                        <strong>Data Tambahan</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Foto Formal Staf</label>
                                            <input type="file" name="foto_staf" class="form-control" accept="image/*">
                                        </div>
                                        <div class="form-group">
                                            <label>No. BPJS Kesehatan</label>
                                            <input type="text" name="bpjs_kesehatan" class="form-control">
                                            <input type="file" name="foto_bpjs_kesehatan" class="form-control mt-2" accept="image/*">
                                        </div>
                                        <div class="form-group">
                                            <label>No. BPJS Ketenagakerjaan</label>
                                            <input type="text" name="bpjs_ketenagakerjaan" class="form-control">
                                            <input type="file" name="foto_bpjs_ketenagakerjaan" class="form-control mt-2" accept="image/*">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Full Width -->
                            <div class="col-12">
                                <div class="card shadow-sm mb-3">
                                    <div class="card-header bg-light">
                                        <strong>Kontak & Rekening</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label>Nama Bank</label>
                                                <input type="text" name="bank_name" class="form-control">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>No. Rekening</label>
                                                <input type="text" name="bank_no" class="form-control">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>a.n Rekening</label>
                                                <input type="text" name="bank_owner" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Email</label>
                                                <input type="email" name="email" class="form-control">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>No. HP / WA</label>
                                                <input type="text" name="no_hp" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div><!-- row -->
                    </div><!-- container -->
                </div><!-- modal-body -->

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
