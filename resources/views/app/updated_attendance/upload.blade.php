@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('attendance_v2') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>
</div>

<!-- Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
            <div class="flex items-center justify-between">
                <span>{{ session('error') }}</span>
                <button type="button" onclick="$(this).parent().parent().fadeOut()" class="text-red-700 hover:text-red-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
            <div class="flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="$(this).parent().parent().fadeOut()" class="text-green-700 hover:text-green-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Info Card -->
    <div class="mb-6 p-6 bg-blue-50 border border-blue-200 rounded-lg">
        <h5 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
            <i class="fas fa-info-circle mr-2 text-blue-600"></i>Format File Excel Fingerprint
        </h5>
        <p class="text-sm text-gray-700 mb-3">File Excel harus memiliki format sesuai dengan hasil export dari mesin fingerprint:</p>
        <ul class="list-disc list-inside text-sm text-gray-700 mb-3 space-y-1">
            <li><strong>Kolom A:</strong> Cloud ID</li>
            <li><strong>Kolom B:</strong> ID Karyawan (akan dicocokkan dengan NIP di database)</li>
            <li><strong>Kolom C:</strong> Nama Karyawan</li>
            <li><strong>Kolom D:</strong> Tanggal Absensi (format: YYYY-MM-DD)</li>
            <li><strong>Kolom E:</strong> Jam Absensi (format: HH:MM)</li>
            <li><strong>Kolom F:</strong> Verifikasi (Sidik Jari)</li>
            <li><strong>Kolom G:</strong> Tipe Absensi (Absensi Masuk/Absensi Pulang)</li>
        </ul>
        <p class="text-sm font-semibold text-gray-900 mb-2">Catatan:</p>
        <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
            <li>Baris pertama adalah header (akan diabaikan)</li>
            <li>ID Karyawan harus sesuai dengan NIP di database</li>
            <li>Sistem akan otomatis menggabungkan data masuk dan pulang untuk karyawan yang sama</li>
            <li>Status absensi akan diproses otomatis berdasarkan jadwal</li>
            <li>Data duplikat akan diupdate, bukan ditolak</li>
        </ul>
    </div>

    <!-- Example Table -->
    <div class="mb-6">
        <h5 class="text-md font-semibold text-gray-900 mb-3">Contoh Format:</h5>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 border border-gray-200">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 border border-gray-200">Cloud ID</th>
                        <th class="px-3 py-2 border border-gray-200">ID</th>
                        <th class="px-3 py-2 border border-gray-200">Nama Karyawan</th>
                        <th class="px-3 py-2 border border-gray-200">Tanggal</th>
                        <th class="px-3 py-2 border border-gray-200">Jam</th>
                        <th class="px-3 py-2 border border-gray-200">Verifikasi</th>
                        <th class="px-3 py-2 border border-gray-200">Tipe</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-white border-b">
                        <td class="px-3 py-2 border border-gray-200">C263045107152E23</td>
                        <td class="px-3 py-2 border border-gray-200">24030301</td>
                        <td class="px-3 py-2 border border-gray-200">YAS MELLYSARI DWI ANGIONO PUTRI</td>
                        <td class="px-3 py-2 border border-gray-200">2025-07-31</td>
                        <td class="px-3 py-2 border border-gray-200">08:00</td>
                        <td class="px-3 py-2 border border-gray-200">Sidik Jari</td>
                        <td class="px-3 py-2 border border-gray-200">Absensi Masuk</td>
                    </tr>
                    <tr class="bg-white border-b">
                        <td class="px-3 py-2 border border-gray-200">C263045107152E23</td>
                        <td class="px-3 py-2 border border-gray-200">24030301</td>
                        <td class="px-3 py-2 border border-gray-200">YAS MELLYSARI DWI ANGIONO PUTRI</td>
                        <td class="px-3 py-2 border border-gray-200">2025-07-31</td>
                        <td class="px-3 py-2 border border-gray-200">17:00</td>
                        <td class="px-3 py-2 border border-gray-200">Sidik Jari</td>
                        <td class="px-3 py-2 border border-gray-200">Absensi Pulang</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('attendance.process-upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label for="excel_file" class="block text-sm font-medium text-gray-700 mb-1">File Excel <span class="text-red-500">*</span></label>
            <input type="file" id="excel_file" name="excel_file" accept=".xls,.xlsx" required class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <p class="mt-1 text-sm text-gray-500">Format yang didukung: .xls, .xlsx (Maksimal 2MB)</p>
            @error('excel_file')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end gap-3 mt-6">
            <a href="{{ route('attendance_v2') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                <i class="fas fa-upload mr-2"></i>Upload
            </button>
        </div>
    </form>
</div>

@push('scripts')
    @include('app._partials.js')
@endpush
@endsection
