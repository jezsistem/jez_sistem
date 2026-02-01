@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-white rounded-lg border border-gray-200 p-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Data stok produk</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <select id="st_id_filter" name="st_id_filter" class="px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg border-0 focus:ring-2 focus:ring-blue-500">
                <option value="">- Storage -</option>
                @foreach ($data['st_id'] as $key => $value)
                    @if ($key == $data['user']->st_id)
                        <option value="{{ $key }}" selected>{{ $value }}</option>
                    @else
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endif
                @endforeach
            </select>
            <button type="button" id="change_display_btn" class="px-4 py-2.5 bg-cyan-500 text-white text-sm font-medium rounded-lg hover:bg-cyan-600 transition-colors">
                Ganti Display
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-200 bg-red-50">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Category -->
                <div>
                    <select id="pc_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach ($data['pc_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <div id="pc_id_parent"></div>
                </div>
                <!-- Sub Category -->
                <div>
                    <select id="psc_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach ($data['psc_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <div id="psc_id_parent"></div>
                </div>
                <!-- Sub Sub Category -->
                <div>
                    <select id="pssc_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach ($data['pssc_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <div id="pssc_id_parent"></div>
                </div>
                <!-- Brand -->
                <div>
                    <select id="br_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach ($data['br_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <div id="br_id_parent"></div>
                </div>
                <!-- Size -->
                <div>
                    <select id="sz_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach ($data['sizes'] as $value)
                            <option value="{{ $value->sz_name }}">{{ $value->sz_name }} ({{ $value->pc_name }})</option>
                        @endforeach
                    </select>
                    <div id="sz_id_parent"></div>
                </div>
                <!-- Min Price -->
                <div>
                    <select id="min_price_filter" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option></option>
                        @for ($i = 100000; $i <= 1000000; $i += 100000)
                            <option value="{{ $i }}">{{ number_format($i, 0, ',', '.') }}</option>
                        @endfor
                    </select>
                    <div id="min_price_filter_parent"></div>
                </div>
                <!-- Max Price -->
                <div>
                    <select id="max_price_filter" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option></option>
                        @for ($i = 100000; $i <= 1000000; $i += 100000)
                            <option value="{{ $i }}">{{ number_format($i, 0, ',', '.') }}</option>
                        @endfor
                        <option value=">1000000">&gt; 1.000.000</option>
                    </select>
                    <div id="max_price_filter_parent"></div>
                </div>
                <!-- Main Color -->
                <div>
                    <select id="main_color_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach ($data['main_color_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <div id="main_color_id_parent"></div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2 mt-4">
                <button type="button" id="reset_btn" class="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors flex items-center gap-2">
                    <i class="fas fa-redo"></i>
                    Reset
                </button>
                <button type="button" id="pickup_list_btn" class="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors flex items-center gap-2">
                    <i class="fas fa-list"></i>
                    Pickup List
                </button>
                <button type="button" id="waiting_list_btn" class="px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-lg hover:bg-yellow-600 transition-colors flex items-center gap-2">
                    <i class="fas fa-clock"></i>
                    Waiting List
                </button>
                <button type="button" id="filter_list_btn" class="px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-lg hover:bg-yellow-600 transition-colors flex items-center gap-2">
                    <i class="fas fa-filter"></i>
                    Filter Product
                </button>
            </div>
        </div>

        <div class="p-5">
            <!-- QR Scanner -->
            <div id="reader_main" class="rounded-lg mb-4" style="max-width: 500px;"></div>
            <div id="result"></div>
            
            <!-- Request Count -->
            <div class="mb-4">
                <span class="text-base font-bold text-gray-700">Request count:</span>
                <span class="text-base font-bold text-blue-600" id="request_count">0</span>
            </div>

            <!-- Alert -->
            <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4">
                <strong>Important!</strong> Menu (Data Stok V2 Beta) masih dalam tahap pengembangan dan pengujian. Jika kamu menemukan error atau kejanggalan, jangan ragu untuk 
                <a href="https://wa.me/6285649888272" class="text-blue-600 underline hover:text-blue-800">hubungi saya via WhatsApp</a> 🤘🤘🤘🤘
            </div>

            <!-- Search -->
            <form id="f_search">
                <div class="relative mb-4">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="stock_data_search" class="block w-full pl-10 pr-4 py-3 border-2 border-gray-400 rounded-lg text-sm bg-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ketik 3 huruf pertama Nama Produk atau Kode Artikel">
                </div>
                <p class="text-sm text-gray-500 mb-4">Please don't click any buttons; just wait to get the data you want.</p>
            </form>

            <!-- Stock Type Buttons -->
            <div class="flex flex-wrap gap-2 mb-4">
                <button type="button" id="pickZeroBtn" class="px-4 py-2 bg-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-600 transition-colors">
                    Stok Semua Varian
                </button>
                <button type="button" id="pickAvailableBtn" class="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors">
                    Stok Tersedia
                </button>
                <input type="hidden" value="" id="is_zero">
            </div>

            <!-- Legend -->
            <div class="mb-4">
                <h5 class="text-sm font-semibold text-gray-700 mb-2">Keterangan</h5>
                <div class="flex flex-wrap items-center gap-3 text-sm">
                    <span class="flex items-center gap-1">
                        <span class="w-4 h-4 bg-cyan-500 rounded"></span>
                        Stok Toko
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-4 h-4 bg-green-500 rounded"></span>
                        Stok Gudang
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-4 h-4 rounded" style="background-color: #784800;"></span>
                        Stok Defect
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-4 h-4 bg-yellow-500 rounded"></span>
                        Stok Special Sale
                    </span>
                </div>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200" id="StockDatatb">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 80%;">Available Stock</th>
                            <th class="hidden"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('app.updated_data_stok.data_stok_modal')
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@include('app.updated_data_stok.data_stok_css')
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
@include('app.updated_data_stok.data_stok_js')
@endpush
