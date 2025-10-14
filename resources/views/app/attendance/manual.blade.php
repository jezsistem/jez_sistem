@extends('app.structure')

@section('content')
    <style>
        #map {
            width: 100%;
            height: 300px;
            border-radius: 10px;
            margin-top: 10px;
        }

        .webcam-wrapper {
            width: 100%;
            max-width: 640px;
            height: auto;
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            background: #000;
        }

        /.webcam-capture {
             position: relative;
             width: 100%;
             max-width: 100%;
             overflow: hidden;
             border-radius: 10px;
             background: #000;
         }

        .webcam-capture video {
            width: 100% !important;
            height: auto !important;
            object-fit: contain !important; /* Biar tidak crop */
            transform: scaleX(-1); /* mirror kamera depan */
            display: block;
            margin: 0 auto;
        }

        .webcam-capture.video-back video {
            transform: none !important;
        }

        #address {
            z-index: 9;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
            word-break: break-word;
        }

        /* Untuk layar kecil (HP) */
        @media (max-width: 768px) {
            .webcam-wrapper {
                max-width: 100%;
                height: auto;
            }

            #address {
                font-size: 12px;
                padding: 6px;
            }

            #switchCamera {
                font-size: 12px;
                padding: 4px 8px;
            }
        }
    </style>

    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
        <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap mr-2">
                    <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5 fs-3">{{ $data['subtitle'] ?? 'Presensi Webcam' }}</h5>
                </div>
            </div>
        </div>
        <!--end::Subheader-->

        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <style>
                            .webcam-wrapper {
                                width: 100%;
                                max-width: 640px;
                                height: auto;
                            }

                            .webcam-capture {
                                width: 100%;
                                height: auto;
                            }

                            #switchCamera {
                                opacity: 0.9;
                                backdrop-filter: blur(4px);
                            }

                            #map {
                                width: 100%;
                                height: 300px;
                                border-radius: 10px;
                                margin-top: 10px;
                            }
                        </style>

                        <div class="row">
                            <div class="col-md-12 text-center">
                                <input type="hidden" id="lokasi">
                                <input type="hidden" id="alamat">
                                <div class="webcam-wrapper position-relative mx-auto">
                                    <div class="webcam-capture"></div>

                                    <!-- Teks alamat di dalam bingkai kamera -->
                                    <div id="address"
                                         class="position-absolute bottom-0 w-100 text-center text-light p-2"
                                         style="background: rgba(0,0,0,0.5); font-size: 14px; line-height: 1.2;">
                                        Mendeteksi alamat...
                                    </div>

                                    <button id="switchCamera"
                                            class="btn btn-sm btn-light position-absolute d-flex align-items-center gap-1"
                                            style="top: 10px; right: 10px; z-index: 10;">
                                        <ion-icon name="camera-reverse-outline"></ion-icon>
                                        Ganti
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button id="absen"
                                    class="btn btn-lg w-100 {{ $data['button_status']['class'] }}"
                                    {{ $data['button_status']['disabled'] ? 'disabled' : '' }}>
                                <ion-icon name="camera-outline"></ion-icon>
                                {{ $data['button_status']['text'] }}
                            </button>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div id="map"></div>
{{--                                <div id="address" class="mt-3 text-center fw-bold text-primary">--}}
{{--                                    Mendeteksi alamat...--}}
{{--                                </div>--}}
                            </div>
                        </div>

                        <!-- Loading overlay -->
                        <div id="loadingOverlay"
                             style="display: none;
                        position: fixed;
                        top: 0; left: 0;
                        width: 100vw;
                        height: 100vh;
                        background: rgba(0, 0, 0, 0.4);
                        z-index: 9999;
                        display: flex;
                        align-items: center;
                        justify-content: center;">
                            <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Entry-->
    </div>
    <!--end::Content-->

    {{-- JS Section --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-sA+4jvibvVVjwlTlU2d+xvAqaaKYF7dLndpk+z4gD3I=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        let useFrontCamera = true;
        let map, marker, withinRadius = false;
        const lokasi = document.getElementById('lokasi');
        const loadingOverlay = document.getElementById('loadingOverlay');

        const centerLat = -7.945221934890168;
        const centerLng = 112.61913974639859;
        const maxDistanceMeters = 100;

        // Hitung ukuran kamera dinamis berdasarkan device
        function getCameraSize() {
            const isMobile = window.innerWidth <= 768;
            const isTablet = window.innerWidth > 768 && window.innerWidth <= 1024;

            let width, height;

            if (isMobile) {
                width = window.innerWidth;
                height = window.innerHeight * 0.6; // 60% dari tinggi layar
            } else if (isTablet) {
                width = 800;
                height = 500;
            } else {
                // laptop / desktop
                width = 640;
                height = 480;
            }

            return { width, height };
        }

        function startCamera() {
            // Tampilkan overlay loading
            if (loadingOverlay) loadingOverlay.style.display = 'flex';

            Webcam.reset();

            const webcamContainer = document.querySelector('.webcam-capture');
            const { width, height } = getCameraSize();

            if (!useFrontCamera) {
                webcamContainer.classList.add('video-back');
            } else {
                webcamContainer.classList.remove('video-back');
            }

            Webcam.set({
                width: width,
                height: height,
                image_format: 'jpeg',
                jpeg_quality: 90,
                constraints: {
                    facingMode: useFrontCamera ? "user" : "environment",
                    aspectRatio: width / height
                }
            });

            Webcam.attach('.webcam-capture');

            // Tunggu hingga kamera siap benar-benar tampil
            const checkVideoReady = setInterval(() => {
                const video = document.querySelector('.webcam-capture video');
                if (video && video.readyState === 4) {
                    clearInterval(checkVideoReady);

                    // Atur style supaya proporsional dan center
                    video.style.objectFit = 'contain';
                    video.style.width = '100%';
                    video.style.height = 'auto';
                    video.style.display = 'block';
                    video.style.margin = '0 auto';

                    // Jika kamera depan, mirror (biar natural selfie)
                    if (useFrontCamera) {
                        video.style.transform = 'scaleX(-1)';
                    } else {
                        video.style.transform = 'scaleX(1)';
                    }

                    if (loadingOverlay) loadingOverlay.style.display = 'none';
                }
            }, 300);

            // Safety timeout (jika kamera gagal load)
            setTimeout(() => {
                if (loadingOverlay) loadingOverlay.style.display = 'none';
            }, 6000);
        }

        // Tombol untuk ganti kamera
        document.getElementById('switchCamera').addEventListener('click', function () {
            useFrontCamera = !useFrontCamera;
            startCamera();
        });

        // Jalankan kamera pertama kali
        startCamera();

        // Responsif jika layar berubah (misal rotasi HP)
        window.addEventListener('resize', () => {
            startCamera();
        });

        // --- Fungsi Error Lokasi ---
        function errorCallback(error) {
            const messages = {
                1: "⚠️ Akses lokasi ditolak.",
                2: "⚠️ Lokasi tidak tersedia.",
                3: "⚠️ Permintaan lokasi melebihi batas waktu."
            };
            alert(messages[error.code] || "⚠️ Terjadi kesalahan saat mengambil lokasi.");
        }

        // --- Fungsi Konversi dan Perhitungan Jarak ---
        function deg2rad(deg) {
            return deg * (Math.PI / 180);
        }

        function getDistanceFromLatLonInMeters(lat1, lon1, lat2, lon2) {
            const R = 6371000;
            const dLat = deg2rad(lat2 - lat1);
            const dLon = deg2rad(lon2 - lon1);
            const a =
                Math.sin(dLat / 2) ** 2 +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                Math.sin(dLon / 2) ** 2;
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        // --- Ambil Alamat dari Koordinat (Nominatim) ---
        function getAddressFromCoords(lat, lon) {
            const url = `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const address = data.display_name || "Alamat tidak ditemukan";
                    document.getElementById("address").innerText = address;
                    document.getElementById("alamat").value = address;
                    console.log("Alamat:", address);
                })
                .catch(err => {
                    console.error("Gagal mengambil alamat:", err);
                    document.getElementById("address").innerText = "Gagal memuat alamat";
                });
        }

        // --- Ketika Lokasi Diperoleh ---
        function handleLocation(coordinate) {
            const lat = coordinate.coords.latitude;
            const lng = coordinate.coords.longitude;
            const distance = getDistanceFromLatLonInMeters(lat, lng, centerLat, centerLng);
            withinRadius = distance <= maxDistanceMeters;

            lokasi.value = lat + "," + lng;
            getAddressFromCoords(lat, lng);

            if (!map) {
                map = L.map('map').setView([lat, lng], 18);
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);

                L.circle([centerLat, centerLng], {
                    color: 'red',
                    fillColor: 'rgba(11,253,0,0.13)',
                    fillOpacity: 0.5,
                    radius: maxDistanceMeters
                }).addTo(map);

                marker = L.marker([lat, lng]).addTo(map);
            } else {
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng]);
            }
        }

        // --- Aktifkan Geolocation ---
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(handleLocation, errorCallback);
            navigator.geolocation.watchPosition(handleLocation, errorCallback);
        } else {
            alert("Browser Anda tidak mendukung lokasi.");
        }

        // --- Tombol Ganti Kamera ---
        $('#switchCamera').on('click', function () {
            useFrontCamera = !useFrontCamera;
            startCamera();
        });

        // --- Jalankan Kamera Saat Load ---
        startCamera();

        // --- Tombol Absen ---
        {{--$('#absen').click(function () {--}}
        {{--    if (!cameraReady) {--}}
        {{--        Swal.fire('Kamera belum siap', 'Mohon tunggu beberapa detik...', 'info');--}}
        {{--        return;--}}
        {{--    }--}}

        {{--    // if (!withinRadius) {--}}
        {{--    //     Swal.fire('Lokasi Tidak Valid🥺', '⚠️ Kamu Jauh Dari Kantor Nih Jez!', 'warning');--}}
        {{--    //     return;--}}
        {{--    // }--}}

        {{--    $('#loadingOverlay').show();--}}

        {{--    Webcam.snap(function (uri) {--}}
        {{--        const lokasiVal = $('#lokasi').val();--}}
        {{--        const alamatVal = $('#alamat').val();--}}

        {{--        $.ajax({--}}
        {{--            type: 'POST',--}}
        {{--            url: '/presensi/store',--}}
        {{--            data: {--}}
        {{--                _token: "{{ csrf_token() }}",--}}
        {{--                image: uri,--}}
        {{--                lokasi: lokasiVal,--}}
        {{--                alamat: alamatVal--}}
        {{--            },--}}
        {{--            cache: false,--}}
        {{--            success: function (respon) {--}}
        {{--                $('#loadingOverlay').hide();--}}
        {{--                const status = respon.split('|');--}}
        {{--                if (status[0] === 'success') {--}}
        {{--                    Swal.fire('Berhasil!', status[1], 'success');--}}
        {{--                    setTimeout(() => {--}}
        {{--                        location.href = '/dashboard';--}}
        {{--                    }, 3000);--}}
        {{--                } else {--}}
        {{--                    Swal.fire('Error!', 'Silahkan hubungi IT', 'error');--}}
        {{--                }--}}
        {{--            },--}}
        {{--            error: function (xhr) {--}}
        {{--                $('#loadingOverlay').hide();--}}
        {{--                const msg = xhr.responseJSON?.error || 'Terjadi kesalahan sistem.';--}}
        {{--                Swal.fire('Gagal!', msg, 'error');--}}
        {{--            }--}}
        {{--        });--}}
        {{--    });--}}
        {{--});--}}

        $('#absen').click(function () {
            if (!cameraReady) {
                Swal.fire('Kamera belum siap', 'Mohon tunggu beberapa detik...', 'info');
                return;
            }

            $('#loadingOverlay').show();

            Webcam.snap(function (uri) {
                const lokasiVal = $('#lokasi').val();
                const alamatVal = $('#alamat').val();

                $.ajax({
                    type: 'POST',
                    url: '/attendance/manualStore',
                    data: {
                        _token: "{{ csrf_token() }}",
                        photo: uri,
                        lokasi: lokasiVal,
                        alamat: alamatVal
                    },
                    success: function (res) {
                        $('#loadingOverlay').hide();
                        if (res.status === 'success') {
                            Swal.fire('Berhasil', res.message, 'success');
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },
                    error: function (xhr) {
                        $('#loadingOverlay').hide();
                        const msg = xhr.responseJSON?.message || 'Terjadi kesalahan sistem.';
                        Swal.fire('Error!', msg, 'error');
                    }
                });
            });
        });
    </script>

    @include('app._partials.js')
{{--    @include('app.brand.brand_js')--}}
@endsection
