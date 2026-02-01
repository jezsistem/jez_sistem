@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola konten blog</p>
        </div>
        <div>
            <button id="add_bcc_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                <i class="fas fa-plus mr-2"></i>Data Baru
            </button>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-end gap-4">
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Blog</label>
            <select id="bc_id_filter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option value="">- Pilih Kategori Blog -</option>
                @foreach ($data['bc_id'] as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button id="add_bc_btn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600">
                <i class="fas fa-plus mr-2"></i>Tambah
            </button>
            <button id="edit_bc_btn" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                <i class="fas fa-edit mr-2"></i>Edit
            </button>
            <button id="delete_bc_btn" class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700">
                <i class="fas fa-trash mr-2"></i>Hapus
            </button>
        </div>
    </div>
</div>

<!-- Card -->
<div class="bg-white rounded-lg shadow-md p-6">
    <!-- Filters -->
    <div class="mb-6">
        <input type="search" id="bcc_search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-1/3 p-2.5" placeholder="Cari judul blog...">
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="Bcctb" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Judul</th>
                    <th scope="col" class="px-3 py-3">Gambar</th>
                    <th scope="col" class="px-3 py-3">Dilihat</th>
                    <th scope="col" class="px-3 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody id="bcc_tbody">
                <tr>
                    <td colspan="5" class="px-3 py-4 text-center text-gray-500">
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
        <div id="bcc_pagination_info" class="text-sm text-gray-600"></div>
        <div id="bcc_pagination_controls" class="flex items-center gap-2"></div>
    </div>
</div>

<input type="hidden" id="delete_access" value="{{ $data['user']->delete_access }}">

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tiny.cloud/1/323apjbgqf1hr5qmcz0u8uwvl3oymnrypmtg98wfpvhw0khd/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    @include('app._partials.js')
    @include('app.updated_blog.blog_js_v2')
@endpush

@include('app.updated_blog.blog_modal_v2')
@endsection
