@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Laporan shift user hari ini</p>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-4">
        <input type="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2 w-64" id="user_shift_search" placeholder="Cari Nama Shift">
    </div>
    <div class="overflow-x-auto">
        <table id="UserShiftTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Nama</th>
                    <th scope="col" class="px-3 py-3">Toko</th>
                    <th scope="col" class="px-3 py-3">Tanggal</th>
                    <th scope="col" class="px-3 py-3">Mulai</th>
                    <th scope="col" class="px-3 py-3 text-right">Total Actual</th>
                </tr>
            </thead>
            <tbody id="user_shift_tbody">
                <tr>
                    <td colspan="6" class="px-3 py-4 text-center text-gray-500">
                        Memuat data...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-4 flex items-center justify-between">
        <div id="user_shift_pagination_info" class="text-sm text-gray-700"></div>
        <div id="user_shift_pagination_controls" class="flex gap-2"></div>
    </div>
</div>

@include('app.updated_current_shift.current_shift_modal')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('app._partials.js')
    @include('app.updated_current_shift.current_shift_js')
@endpush
@endsection
