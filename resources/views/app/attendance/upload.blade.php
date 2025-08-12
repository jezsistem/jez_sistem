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
        </div>
    </div>
    <!--end::Subheader-->
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container-fluid">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Upload Data Absensi</h3>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Format File Excel Fingerprint</h5>
                        <p>File Excel harus memiliki format sesuai dengan hasil export dari mesin fingerprint:</p>
                        <ul>
                            <li><strong>Kolom A:</strong> Cloud ID</li>
                            <li><strong>Kolom B:</strong> ID Karyawan (akan dicocokkan dengan NIP di database)</li>
                            <li><strong>Kolom C:</strong> Nama Karyawan</li>
                            <li><strong>Kolom D:</strong> Tanggal Absensi (format: YYYY-MM-DD)</li>
                            <li><strong>Kolom E:</strong> Jam Absensi (format: HH:MM)</li>
                            <li><strong>Kolom F:</strong> Verifikasi (Sidik Jari)</li>
                            <li><strong>Kolom G:</strong> Tipe Absensi (Absensi Masuk/Absensi Pulang)</li>
                        </ul>
                        <p><strong>Catatan:</strong></p>
                        <ul>
                            <li>Baris pertama adalah header (akan diabaikan)</li>
                            <li>ID Karyawan harus sesuai dengan NIP di database</li>
                            <li>Sistem akan otomatis menggabungkan data masuk dan pulang untuk karyawan yang sama</li>
                            <li>Status absensi akan diproses otomatis berdasarkan jadwal</li>
                            <li>Data duplikat akan diupdate, bukan ditolak</li>
                        </ul>
                        <p><strong>Contoh Format:</strong></p>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Cloud ID</th>
                                        <th>ID</th>
                                        <th>Nama Karyawan</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Verifikasi</th>
                                        <th>Tipe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>C263045107152E23</td>
                                        <td>24030301</td>
                                        <td>YAS MELLYSARI DWI ANGIONO PUTRI</td>
                                        <td>2025-07-31</td>
                                        <td>08:00</td>
                                        <td>Sidik Jari</td>
                                        <td>Absensi Masuk</td>
                                    </tr>
                                    <tr>
                                        <td>C263045107152E23</td>
                                        <td>24030301</td>
                                        <td>YAS MELLYSARI DWI ANGIONO PUTRI</td>
                                        <td>2025-07-31</td>
                                        <td>17:00</td>
                                        <td>Sidik Jari</td>
                                        <td>Absensi Pulang</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <form action="{{ route('attendance.process-upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="excel_file">File Excel <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control @error('excel_file') is-invalid @enderror" 
                                           id="excel_file" name="excel_file" accept=".xls,.xlsx" required>
                                    <small class="form-text text-muted">Format yang didukung: .xls, .xlsx (Maksimal 2MB)</small>
                                    @error('excel_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload"></i> Upload dan Proses
                                </button>
                                <a href="{{ route('attendance.index') }}" class="btn btn-secondary">
                                    <i class="ki-outline ki-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </form>
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
#kt_aside { overflow-y: auto !important; height: 100vh !important; }
#kt_aside_menu { overflow-y: auto !important; max-height: calc(100vh - 100px) !important; }
.aside-menu-wrapper { overflow-y: auto !important; height: calc(100vh - 100px) !important; }
.aside { overflow-y: auto !important; }
.aside-menu { overflow-y: auto !important; }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Reinitialize perfect scrollbar jika ada
    if (typeof PerfectScrollbar !== 'undefined') {
        const asideMenu = document.querySelector('#kt_aside_menu');
        if (asideMenu) {
            new PerfectScrollbar(asideMenu, { wheelPropagation: false });
        }
    }
    // Debug and call header functions
    console.log('Attendance upload page loaded');
    if (typeof loadStore === 'function') { console.log('loadStore function found, calling...'); loadStore(); } else { console.log('loadStore function not found'); }
    if (typeof clockUpdate === 'function') { console.log('clockUpdate function found, calling...'); clockUpdate(); setInterval(clockUpdate, 1000); } else { console.log('clockUpdate function not found'); }
    console.log('Date element:', $('.date').length);
    console.log('Store element:', $('#load_user_store').length);
    if ($('.date').length > 0 && typeof clockUpdate !== 'function') {
        var date = new Date();
        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        var strDate = date.getDate() + "/" + monthNames[(date.getMonth())] + "/" + date.getFullYear();
        $('.date').text(strDate);
    }
});
</script>
@endsection 