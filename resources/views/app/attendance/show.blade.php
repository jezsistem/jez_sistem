@extends('app.structure')

@section('content')
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
        <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <!--begin::Info-->
                <div class="d-flex align-items-center flex-wrap mr-2">
                    <!--begin::Page Title-->
                    <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5 fs-3">{{ $data['subtitle'] }}</h5>
                    <!--end::Page Title-->
                </div>
                <!--end::Info-->
                <!--begin::Toolbar-->
                <div class="d-flex align-items-center">
                    <a href="{{ route('attendance.index') }}" class="btn btn-secondary font-weight-bolder">
                        <i class="ki-outline ki-arrow-left"></i> Back
                    </a>
                </div>
                <!--end::Toolbar-->
            </div>
        </div>
        <!--end::Subheader-->
        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container-fluid">
                <div class="card card-custom">
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;
                                </button>
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;
                                </button>
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="150"><strong>Nama Staff</strong></td>
                                        <td>: {{ $attendance->u_name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>NIP</strong></td>
                                        <td>: {{ $attendance->u_nip ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal</strong></td>
                                        <td>: {{ \Carbon\Carbon::parse($attendance->at_date)->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jam Masuk</strong></td>
                                        <td>
                                            : {{ $attendance->at_time_in ? \Carbon\Carbon::parse($attendance->at_time_in)->format('H:i') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jam Keluar</strong></td>
                                        <td>
                                            : {{ $attendance->at_time_out ? \Carbon\Carbon::parse($attendance->at_time_out)->format('H:i') : '-' }}</td>
                                    </tr>

                                    <tr>
                                        <td><strong>Foto Masuk</strong></td>
                                        <td>:
                                            @if(!empty($attendance->at_photos_in))
                                                <button class="btn btn-sm btn-outline-primary view-photo"
                                                        data-img="{{ asset('storage/' . $attendance->at_photos_in) }}"
                                                        data-location="{{ $attendance->at_location_in }}"
                                                        data-label="Lokasi Masuk">
                                                    <ion-icon name="eye-outline"></ion-icon>
                                                    Lihat
                                                </button>
                                            @else
                                                <span class="text-muted">Belum ada foto</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><strong>Foto Keluar</strong></td>
                                        <td>:
                                            @if(!empty($attendance->at_photos_out))
                                                <button class="btn btn-sm btn-outline-primary view-photo"
                                                        data-img="{{ asset('storage/' . $attendance->at_photos_out) }}"
                                                        data-location="{{ $attendance->at_location_out }}"
                                                        data-label="Lokasi Keluar">
                                                    <ion-icon name="eye-outline"></ion-icon>
                                                    Lihat
                                                </button>
                                            @else
                                                <span class="text-muted">Belum ada foto</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Lokasi Masuk</strong></td>
                                        <td>:
                                            @if(!empty($attendance->at_location_in))
                                                <a href="javascript:void(0)" class="text-primary view-photo"
                                                   data-img="{{ asset('storage/' . $attendance->at_photos_in) }}"
                                                   data-location="{{ $attendance->at_location_in }}"
                                                   data-label="Lokasi Masuk">
                                                    📍 {{ $attendance->at_location_in }}
                                                </a>
                                            @else
                                                <span class="text-muted">Belum ada data</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><strong>Lokasi Keluar</strong></td>
                                        <td>:
                                            @if(!empty($attendance->at_location_out))
                                                <a href="javascript:void(0)" class="text-primary view-photo"
                                                   data-img="{{ asset('storage/' . $attendance->at_photos_out) }}"
                                                   data-location="{{ $attendance->at_location_out }}"
                                                   data-label="Lokasi Keluar">
                                                    📍 {{ $attendance->at_location_out }}
                                                </a>
                                            @else
                                                <span class="text-muted">Belum ada data</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><strong>Alamat Absen</strong></td>
                                        <td>:
                                            {{ $attendance->at_address_attendance ?? '-' }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="150"><strong>Status</strong></td>
                                        <td>:
                                            @switch($attendance->at_status)
                                                @case('present')
                                                    <span class="badge badge-success">Hadir</span>
                                                    @break
                                                @case('late')
                                                    <span class="badge badge-warning">Terlambat</span>
                                                    @break
                                                @case('absent')
                                                    <span class="badge badge-danger">Tidak Hadir</span>
                                                    @break
                                                @case('early_leave')
                                                    <span class="badge badge-info">Pulang Awal</span>
                                                    @break
                                                @case('scan_once')
                                                    <span class="badge badge-secondary">Scan 1 Kali</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">{{ ucfirst($attendance->at_status) }}</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Catatan</strong></td>
                                        <td>: {{ $attendance->at_notes ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Sumber</strong></td>
                                        <td>:
                                            @switch($attendance->at_source)
                                                @case('fingerprint')
                                                    <span class="badge badge-primary">Fingerprint</span>
                                                    @break
                                                @case('manual')
                                                    <span class="badge badge-info">Manual</span>
                                                    @break
                                                @case('system')
                                                    <span class="badge badge-secondary">Sistem</span>
                                                    @break
                                                @default
                                                    {{ ucfirst($attendance->at_source) }}
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Divisi</strong></td>
                                        <td>: {{ $attendance->ud_name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Shift</strong></td>
                                        <td>:
                                            @if($attendance->sc_code)
                                                {{ $attendance->sc_code }} - {{ $attendance->sc_shift_name }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Dibuat Oleh</strong></td>
                                        <td>: {{ $attendance->created_by ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Dibuat Pada</strong></td>
                                        <td>
                                            : {{ \Carbon\Carbon::parse($attendance->created_at)->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    @if($attendance->updated_by)
                                        <tr>
                                            <td><strong>Diperbarui Oleh</strong></td>
                                            <td>: {{ $attendance->updated_by }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Diperbarui Pada</strong></td>
                                            <td>
                                                : {{ \Carbon\Carbon::parse($attendance->updated_at)->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        <!-- Daily Schedule Information -->
                        @if($dailySchedule)
                            <div class="card card-custom mt-4">
                                <div class="card-header">
                                    <h3 class="card-title">Informasi Jadwal Harian</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td width="150"><strong>Tanggal Jadwal</strong></td>
                                                    <td>
                                                        : {{ \Carbon\Carbon::parse($dailySchedule->ds_date)->format('d/m/Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Divisi</strong></td>
                                                    <td>: {{ $dailySchedule->ud_name ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Shift</strong></td>
                                                    <td>:
                                                        @if($dailySchedule->sc_code)
                                                            {{ $dailySchedule->sc_code }}
                                                            - {{ $dailySchedule->sc_shift_name }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Jam Mulai</strong></td>
                                                    <td>
                                                        : {{ $dailySchedule->sc_start_time ? \Carbon\Carbon::parse($dailySchedule->sc_start_time)->format('H:i') : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Jam Selesai</strong></td>
                                                    <td>
                                                        : {{ $dailySchedule->sc_end_time ? \Carbon\Carbon::parse($dailySchedule->sc_end_time)->format('H:i') : '-' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td width="150"><strong>Status Jadwal</strong></td>
                                                    <td>:
                                                        @switch($dailySchedule->ds_status)
                                                            @case('present')
                                                                <span class="badge badge-success">Hadir</span>
                                                                @break
                                                            @case('late')
                                                                <span class="badge badge-warning">Terlambat</span>
                                                                @break
                                                            @case('absent')
                                                                <span class="badge badge-danger">Tidak Hadir</span>
                                                                @break
                                                            @case('early_leave')
                                                                <span class="badge badge-info">Pulang Awal</span>
                                                                @break
                                                            @case('scan_once')
                                                                <span class="badge badge-secondary">Scan 1 Kali</span>
                                                                @break
                                                            @default
                                                                <span class="badge badge-secondary">{{ ucfirst($dailySchedule->ds_status ?? 'Belum Diproses') }}</span>
                                                        @endswitch
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Catatan Jadwal</strong></td>
                                                    <td>: {{ $dailySchedule->ds_notes ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Dibuat Oleh</strong></td>
                                                    <td>: {{ $dailySchedule->created_by ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Dibuat Pada</strong></td>
                                                    <td>
                                                        : {{ \Carbon\Carbon::parse($dailySchedule->created_at)->format('d/m/Y H:i') }}</td>
                                                </tr>
                                                @if($dailySchedule->updated_by)
                                                    <tr>
                                                        <td><strong>Diperbarui Oleh</strong></td>
                                                        <td>: {{ $dailySchedule->updated_by }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Diperbarui Pada</strong></td>
                                                        <td>
                                                            : {{ \Carbon\Carbon::parse($dailySchedule->updated_at)->format('d/m/Y H:i') }}</td>
                                                    </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="card card-custom mt-4">
                                <div class="card-header">
                                    <h3 class="card-title">Informasi Jadwal Harian</h3>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Tidak ada jadwal harian</strong> untuk karyawan ini pada
                                        tanggal {{ \Carbon\Carbon::parse($attendance->at_date)->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!--end::Container-->
        </div>
        <!--end::Entry-->
    </div>
    <!--end::Content-->

    <style>
        /* Fix untuk sidebar scroll */
        #kt_aside {
            overflow-y: auto !important;
            height: 100vh !important;
        }

        #kt_aside_menu {
            overflow-y: auto !important;
            max-height: calc(100vh - 100px) !important;
        }

        .aside-menu-wrapper {
            overflow-y: auto !important;
            height: calc(100vh - 100px) !important;
        }

        .aside {
            overflow-y: auto !important;
        }

        .aside-menu {
            overflow-y: auto !important;
        }

        @media (max-width: 768px) {
            #photoPreview {
                height: auto !important;
            }
            #mapModal {
                height: 250px !important;
            }
            .modal-body .bg-light {
                height: 250px !important;
            }
        }
    </style>

    <!-- Modal Foto + Lokasi -->
    <div class="modal fade" id="photoLocationModal" tabindex="-1" role="dialog"
         aria-labelledby="photoLocationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title" id="photoLocationModalLabel">Detail Absen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <!-- FOTO ABSEN -->
                    <div class="mb-4">
                        <h6 class="text-center text-muted mb-2">📸 Foto Absen</h6>
                        <div class="d-flex justify-content-center bg-light rounded shadow-sm p-2"
                             style="height: 350px;">
                            <img id="photoPreview" src="" alt="Foto Absen"
                                 class="img-fluid rounded"
                                 style="max-height: 100%; max-width: 100%; object-fit: contain;">
                        </div>
                    </div>

                    <!-- LOKASI ABSEN -->
                    <div>
                        <h6 class="text-center text-muted mb-2">📍 Lokasi Absen</h6>
                        <div id="mapModal"
                             style="width: 100%; height: 350px; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Leaflet JS & CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-sA+4jvibvVVjwlTlU2d+xvAqaaKYF7dLndpk+z4gD3I=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     $('.view-photo').on('click', function() {
        //         const imgUrl = $(this).data('img');
        //         $('#photoPreview').attr('src', imgUrl);
        //         $('#photoModal').modal('show');
        //     });
        // });

        $(document).ready(function () {
            let mapInstance = null;
            let marker = null;

            $('.view-photo').on('click', function () {
                const imgUrl = $(this).data('img');
                const coords = $(this).data('location'); // format: "lat,lng"
                const label = $(this).data('label') || 'Lokasi Absen';

                // Set Foto
                $('#photoPreview').attr('src', imgUrl);

                // Tampilkan Modal
                $('#photoLocationModal').modal('show');

                // Tunggu modal muncul sebelum inisialisasi peta
                $('#photoLocationModal').on('shown.bs.modal', function () {
                    if (!coords) {
                        $('#mapModal').html('<div class="text-center text-muted mt-5">Tidak ada data lokasi.</div>');
                        return;
                    }

                    const [lat, lng] = coords.split(',').map(Number);

                    if (!mapInstance) {
                        mapInstance = L.map('mapModal').setView([lat, lng], 17);
                        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; OpenStreetMap'
                        }).addTo(mapInstance);
                        marker = L.marker([lat, lng]).addTo(mapInstance).bindPopup(label).openPopup();
                    } else {
                        mapInstance.setView([lat, lng], 17);
                        marker.setLatLng([lat, lng]).bindPopup(label).openPopup();
                    }
                });

                // Reset map ketika modal ditutup
                $('#photoLocationModal').on('hidden.bs.modal', function () {
                    if (mapInstance) {
                        mapInstance.remove();
                        mapInstance = null;
                    }
                });
            });
        });

        $(document).ready(function () {
            // Reinitialize perfect scrollbar jika ada
            if (typeof PerfectScrollbar !== 'undefined') {
                const asideMenu = document.querySelector('#kt_aside_menu');
                if (asideMenu) {
                    new PerfectScrollbar(asideMenu, {wheelPropagation: false});
                }
            }
            // Debug and call header functions
            console.log('Attendance show page loaded');
            if (typeof loadStore === 'function') {
                console.log('loadStore function found, calling...');
                loadStore();
            } else {
                console.log('loadStore function not found');
            }
            if (typeof clockUpdate === 'function') {
                console.log('clockUpdate function found, calling...');
                clockUpdate();
                setInterval(clockUpdate, 1000);
            } else {
                console.log('clockUpdate function not found');
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @include('app._partials.js')

@endsection

{{--@section('scripts')--}}
{{--    <script>--}}
{{--        --}}
{{--    </script>--}}
{{--   --}}
{{--@endsection--}}

