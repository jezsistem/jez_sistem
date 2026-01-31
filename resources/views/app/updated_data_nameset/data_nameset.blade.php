@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-white rounded-lg border border-gray-200 p-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Data nameset untuk invoice</p>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Data Nameset</h3>
            </div>
        </div>
        <div class="p-5">
            <!-- Search -->
            <div class="mb-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="search" id="nameset_search" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari invoice atau artikel">
                </div>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto border border-gray-200 rounded-lg" style="max-width: 100%;">
                <div class="datatable-container" style="overflow-x: auto; width: 100%;">
                    <table class="min-w-full divide-y divide-gray-200" id="NamesetDatatb" style="min-width: 800px;">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 5%;">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 12%;">Invoice</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 10%;">Divisi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 30%;">Artikel</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 15%;">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 18%;">Note</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 10%;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Data will be loaded by DataTables -->
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('app.updated_data_nameset.data_nameset_modal')
@endsection

@push('styles')
@include('app.updated_data_nameset.data_nameset_css')
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@include('app.updated_data_nameset.data_nameset_js')
@endpush
