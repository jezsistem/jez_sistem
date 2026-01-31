@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola artikel website</p>
        </div>
    </div>
</div>

<!-- Card -->
<div class="bg-white rounded-lg shadow-md p-6">
    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div>
            <select id="pc_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option value="">- Kategori -</option>
                @foreach ($data['pc_id'] as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <select id="img_filter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option value="">- Status Foto -</option>
                <option value="1">Sudah Upload</option>
                <option value="0">Belum Upload</option>
            </select>
        </div>
        <div>
            <input type="search" id="article_search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Cari brand artikel warna...">
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="Articletb" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Article ID</th>
                    <th scope="col" class="px-3 py-3">Brand</th>
                    <th scope="col" class="px-3 py-3">Artikel</th>
                    <th scope="col" class="px-3 py-3">Warna</th>
                    <th scope="col" class="px-3 py-3">Berat(gr)</th>
                    <th scope="col" class="px-3 py-3">Gambar</th>
                    <th scope="col" class="px-3 py-3">SizeChart</th>
                    <th scope="col" class="px-3 py-3">slug</th>
                    <th scope="col" class="px-3 py-3">Deskripsi</th>
                    <th scope="col" class="px-3 py-3">Stok</th>
                    <th scope="col" class="px-3 py-3"></th>
                </tr>
            </thead>
            <tbody id="article_tbody">
                <tr>
                    <td colspan="12" class="px-3 py-4 text-center text-gray-500">
                        <div class="flex justify-center items-center">
                            <svg class="animate-spin h-5 w-5 mr-3 text-blue-500" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memuat data...
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-between items-center mt-4">
        <div id="article_pagination_info" class="text-sm text-gray-600"></div>
        <div id="article_pagination_controls" class="flex items-center gap-2"></div>
    </div>
</div>

@push('scripts')
    @include('app._partials.js')
    @include('app.updated_web_article.web_article_js_v2')
@endpush

@include('app.updated_web_article.web_article_modal_v2')
@endsection
