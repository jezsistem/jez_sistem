@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-white rounded-lg border border-gray-200 p-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Editor invoice untuk perubahan data transaksi</p>
        </div>
        <button type="button" id="add_btn" class="px-4 py-2.5 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition-colors">
            <i class="fas fa-plus mr-2"></i>Data Baru
        </button>
    </div>

    <!-- Permission Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Data Permission</h3>
            </div>
        </div>
        <div class="p-5">
            <!-- Search -->
            <div class="mb-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="search" id="data_search" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari store / penanggung jawab">
                </div>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200" id="PermissionDatatb">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Store</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Divisi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Penanggung Jawab</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Data will be loaded by DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Invoice Input Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <input type="text" placeholder="Input nomor invoice INVxxx" id="pos_invoice" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-green-50">
                </div>
                <div>
                    <button type="button" id="exec_btn" class="w-full px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Tampilkan
                    </button>
                </div>
            </div>

            <!-- Editor Panel (hidden by default) -->
            <div class="mt-4 editor_panel hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <select name="reason" id="reason" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-green-50">
                            <option value="" disabled selected>Select an option</option>
                            <option value="RETUR">RETUR</option>
                            <option value="CANCEL">CANCEL</option>
                            <option value="TUKAR">TUKAR</option>
                            <option value="SALAH TRX">SALAH TRX</option>
                            <option value="SALAH KIRIM">SALAH KIRIM</option>
                        </select>
                    </div>
                    <div>
                        <input type="text" placeholder="Catatan Tambahan (Optional)" id="note" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-green-50">
                    </div>
                </div>
                <div class="mt-4">
                    <button type="button" id="done_btn" class="w-full px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Data Table (hidden by default) -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm editor_panel hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Data Invoice</h3>
        </div>
        <div class="p-5">
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200" id="Invoicetb">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Invoice</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kasir</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Divisi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">SubDivisi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Metode 1</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bayar 1</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Metode 2</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bayar 2</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Admin</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total<br /><span class="text-xs">(-admin)</span></th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tgl<br /><span class="text-xs">(Thn-Bln-Tgl Jam-Mnt-Dtk)</span></th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Data will be loaded by DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detail and Tracking Tables (hidden by default) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 editor_panel hidden">
        <!-- Detail Invoice Table -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Detail Invoice</h3>
            </div>
            <div class="p-5">
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="InvoiceDetailtb">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Artikel</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nameset</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tracking Table -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Detail Tracking</h3>
            </div>
            <div class="p-5">
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="Trackingtb">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Lokasi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Artikel</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
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

    <!-- History Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">History Edit</h3>
        </div>
        <div class="p-5">
            <!-- Search -->
            <div class="mb-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="search" id="history_search" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari invoice / user">
                </div>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto border border-gray-200 rounded-lg" style="max-width: 100%;">
                <div class="datatable-container" style="overflow-x: auto; width: 100%;">
                    <table class="min-w-full divide-y divide-gray-200" id="Historytb" style="min-width: 1000px;">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Invoice</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aktifitas</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Catatan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tgl Edit</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tgl Update</th>
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

@include('app.updated_invoice_editor.invoice_editor_modal')
@endsection

@push('styles')
@include('app.updated_invoice_editor.invoice_editor_css')
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@include('app.updated_invoice_editor.invoice_editor_js')
@endpush
