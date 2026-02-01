@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Edit Backup Time</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('break-times-backup_v2') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-2"></i>Back
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

<!-- Form Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
    <form action="{{ route('break-times-backup.update_v2', $breakTime->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Staff <span class="text-red-500">*</span>
                </label>
                <select id="user_id" name="user_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">Pilih Staff</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $breakTime->user_id == $user->id ? 'selected' : '' }}>
                            {{ $user->u_name }} ({{ $user->u_nip }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="bt_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal <span class="text-red-500">*</span>
                </label>
                <input type="date" id="bt_date" name="bt_date" value="{{ $breakTime->bt_date }}" required
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="bt_start_time" class="block text-sm font-medium text-gray-700 mb-2">
                    Jam Mulai <span class="text-red-500">*</span>
                </label>
                <input type="time" id="bt_start_time" name="bt_start_time" value="{{ $breakTime->bt_start_time }}" required
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div>
                <label for="bt_end_time" class="block text-sm font-medium text-gray-700 mb-2">
                    Jam Selesai <span class="text-red-500">*</span>
                </label>
                <input type="time" id="bt_end_time" name="bt_end_time" value="{{ $breakTime->bt_end_time }}" required
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="bt_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Jenis Backup
                </label>
                <input type="text" id="bt_type" name="bt_type" value="{{ $breakTime->bt_type }}" 
                       placeholder="backup_1, backup_2, backup_3, etc."
                       readonly
                       class="bg-gray-100 border border-gray-300 text-gray-500 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed">
                <small class="text-gray-500 mt-1 block">Backup type ditentukan otomatis oleh sistem</small>
            </div>
            <div>
                <label for="bt_status" class="block text-sm font-medium text-gray-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select id="bt_status" name="bt_status" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="active" {{ $breakTime->bt_status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ $breakTime->bt_status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $breakTime->bt_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label for="bt_notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
            <textarea id="bt_notes" name="bt_notes" rows="3" 
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">{{ $breakTime->bt_notes }}</textarea>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600">
                <i class="fas fa-save mr-2"></i>Update Backup Time
            </button>
            <a href="{{ route('break-times-backup_v2') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
        </div>
    </form>
</div>

@push('scripts')
    @include('app._partials.js')
@endpush
@endsection
