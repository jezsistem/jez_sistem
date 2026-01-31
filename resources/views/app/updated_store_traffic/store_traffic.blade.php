@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-8 text-white text-center">
        <h1 class="text-3xl font-bold mb-2">Store Traffic</h1>
        <p class="text-blue-100">Hitung jumlah pengunjung toko secara real-time</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Traffic Buttons Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">
            <i class="fa fa-hand-pointer-o mr-2 text-blue-600"></i>
            Klik Button untuk Menghitung Traffic
        </h2>
        <p class="text-gray-600 mb-8">Klik salah satu button di bawah setiap kali ada pengunjung masuk ke toko.</p>
        
        <div class="space-y-4">
            <!-- Pria Button -->
            <button id="btnPria" class="w-full py-6 text-xl font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-all transform hover:scale-[1.02] active:scale-95 shadow-lg">
                <i class="fa fa-male mr-3 text-2xl"></i>
                PRIA
            </button>
            
            <!-- Wanita Button -->
            <button id="btnWanita" class="w-full py-6 text-xl font-bold text-white bg-pink-500 rounded-xl hover:bg-pink-600 focus:ring-4 focus:ring-pink-300 transition-all transform hover:scale-[1.02] active:scale-95 shadow-lg">
                <i class="fa fa-female mr-3 text-2xl"></i>
                WANITA
            </button>
            
            <!-- Anak-anak Button -->
            <button id="btnAnak" class="w-full py-6 text-xl font-bold text-white bg-green-500 rounded-xl hover:bg-green-600 focus:ring-4 focus:ring-green-300 transition-all transform hover:scale-[1.02] active:scale-95 shadow-lg">
                <i class="fa fa-child mr-3 text-2xl"></i>
                ANAK-ANAK
            </button>
        </div>
    </div>
    
    <!-- Traffic Statistics Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-2">
            <i class="fa fa-bar-chart mr-2 text-blue-600"></i>
            Statistik Hari Ini
        </h2>
        <p class="text-gray-500 mb-6">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}</p>
        
        <div class="space-y-4">
            <!-- Pria Count -->
            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl border border-blue-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 flex items-center justify-center bg-blue-600 rounded-full mr-4">
                        <i class="fa fa-male text-white text-xl"></i>
                    </div>
                    <span class="text-lg font-medium text-gray-900">Pria</span>
                </div>
                <span id="countmale" class="text-3xl font-bold text-blue-600">{{ $male }}</span>
            </div>
            
            <!-- Wanita Count -->
            <div class="flex items-center justify-between p-4 bg-pink-50 rounded-xl border border-pink-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 flex items-center justify-center bg-pink-500 rounded-full mr-4">
                        <i class="fa fa-female text-white text-xl"></i>
                    </div>
                    <span class="text-lg font-medium text-gray-900">Wanita</span>
                </div>
                <span id="countfemale" class="text-3xl font-bold text-pink-500">{{ $female }}</span>
            </div>
            
            <!-- Anak-anak Count -->
            <div class="flex items-center justify-between p-4 bg-green-50 rounded-xl border border-green-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 flex items-center justify-center bg-green-500 rounded-full mr-4">
                        <i class="fa fa-child text-white text-xl"></i>
                    </div>
                    <span class="text-lg font-medium text-gray-900">Anak-anak</span>
                </div>
                <span id="countchild" class="text-3xl font-bold text-green-500">{{ $child }}</span>
            </div>
            
            <!-- Total Count -->
            <div class="mt-6 p-6 bg-gradient-to-r from-gray-800 to-gray-900 rounded-xl text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-300 text-sm uppercase tracking-wide">Total Pengunjung</p>
                        <p class="text-sm text-gray-400">Hari ini</p>
                    </div>
                    <span id="totalCount" class="text-5xl font-bold">{{ $countsTotal }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@push('scripts')
    @include('app._partials.js')
    @include('app.updated_store_traffic.store_traffic_js_v2')
@endpush
@endsection
