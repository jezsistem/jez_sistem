@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Detail Break Time</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('break-times_v2') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            <a href="{{ route('break-times.edit_v2', $breakTime->id) }}" class="px-4 py-2 text-sm font-medium text-yellow-700 bg-yellow-100 rounded-lg hover:bg-yellow-200">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
        </div>
    </div>
</div>

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

<!-- Detail Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <table class="w-full">
                <tr class="border-b">
                    <td class="py-3 font-semibold text-gray-700 w-1/3">ID</td>
                    <td class="py-3 text-gray-900">:</td>
                    <td class="py-3 text-gray-900">{{ $breakTime->id }}</td>
                </tr>
                <tr class="border-b">
                    <td class="py-3 font-semibold text-gray-700">Tanggal</td>
                    <td class="py-3 text-gray-900">:</td>
                    <td class="py-3 text-gray-900">{{ date('d/m/Y', strtotime($breakTime->bt_date)) }}</td>
                </tr>
                <tr class="border-b">
                    <td class="py-3 font-semibold text-gray-700">Nama Karyawan</td>
                    <td class="py-3 text-gray-900">:</td>
                    <td class="py-3 text-gray-900">{{ $breakTime->u_name }}</td>
                </tr>
                <tr class="border-b">
                    <td class="py-3 font-semibold text-gray-700">NIP</td>
                    <td class="py-3 text-gray-900">:</td>
                    <td class="py-3 text-gray-900">{{ $breakTime->u_nip }}</td>
                </tr>
                <tr class="border-b">
                    <td class="py-3 font-semibold text-gray-700">Divisi</td>
                    <td class="py-3 text-gray-900">:</td>
                    <td class="py-3 text-gray-900">{{ $breakTime->ud_name }}</td>
                </tr>
            </table>
        </div>
        <div>
            <table class="w-full">
                <tr class="border-b">
                    <td class="py-3 font-semibold text-gray-700 w-1/3">Jam Mulai</td>
                    <td class="py-3 text-gray-900">:</td>
                    <td class="py-3 text-gray-900">{{ $breakTime->bt_start_time ? date('H:i', strtotime($breakTime->bt_start_time)) : '-' }}</td>
                </tr>
                <tr class="border-b">
                    <td class="py-3 font-semibold text-gray-700">Jam Selesai</td>
                    <td class="py-3 text-gray-900">:</td>
                    <td class="py-3 text-gray-900">{{ $breakTime->bt_end_time ? date('H:i', strtotime($breakTime->bt_end_time)) : '-' }}</td>
                </tr>
                <tr class="border-b">
                    <td class="py-3 font-semibold text-gray-700">Durasi</td>
                    <td class="py-3 text-gray-900">:</td>
                    <td class="py-3 text-gray-900">
                        @if($breakTime->bt_duration_minutes)
                            @php
                                $hours = floor($breakTime->bt_duration_minutes / 60);
                                $minutes = $breakTime->bt_duration_minutes % 60;
                            @endphp
                            {{ sprintf('%02d:%02d', $hours, $minutes) }}
                        @elseif($breakTime->bt_start_time && $breakTime->bt_end_time)
                            @php
                                $start = \Carbon\Carbon::parse($breakTime->bt_start_time);
                                $end = \Carbon\Carbon::parse($breakTime->bt_end_time);
                                $duration = $end->diffInMinutes($start);
                                $hours = floor($duration / 60);
                                $minutes = $duration % 60;
                            @endphp
                            {{ sprintf('%02d:%02d', $hours, $minutes) }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr class="border-b">
                    <td class="py-3 font-semibold text-gray-700">Tipe</td>
                    <td class="py-3 text-gray-900">:</td>
                    <td class="py-3 text-gray-900">
                        @if($breakTime->bt_type === 'break_1')
                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">Break 1</span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 rounded">Break 2</span>
                        @endif
                    </td>
                </tr>
                <tr class="border-b">
                    <td class="py-3 font-semibold text-gray-700">Status</td>
                    <td class="py-3 text-gray-900">:</td>
                    <td class="py-3 text-gray-900">
                        @if($breakTime->bt_status === 'active')
                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded">Active</span>
                        @elseif($breakTime->bt_status === 'completed')
                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Completed</span>
                        @elseif($breakTime->bt_status === 'cancelled')
                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">Cancelled</span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">{{ ucfirst($breakTime->bt_status) }}</span>
                        @endif
                    </td>
                </tr>
                @if($breakTime->bt_notes)
                <tr class="border-b">
                    <td class="py-3 font-semibold text-gray-700">Catatan</td>
                    <td class="py-3 text-gray-900">:</td>
                    <td class="py-3 text-gray-900">{{ $breakTime->bt_notes }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>
</div>

@push('scripts')
    @include('app._partials.js')
@endpush
@endsection
