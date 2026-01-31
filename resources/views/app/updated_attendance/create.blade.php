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
    <form action="{{ route('attendance.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Staff <span class="text-red-500">*</span></label>
                <select id="user_id" name="user_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">Select Staff</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->u_name }} ({{ $user->u_nip }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="at_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" id="at_date" name="at_date" value="{{ old('at_date', date('Y-m-d')) }}" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                @error('at_date')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="at_time_in" class="block text-sm font-medium text-gray-700 mb-1">Jam Masuk</label>
                <input type="time" id="at_time_in" name="at_time_in" value="{{ old('at_time_in') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                @error('at_time_in')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="at_time_out" class="block text-sm font-medium text-gray-700 mb-1">Jam Keluar</label>
                <input type="time" id="at_time_out" name="at_time_out" value="{{ old('at_time_out') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                @error('at_time_out')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="at_status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select id="at_status" name="at_status" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">Pilih Status</option>
                    <option value="present" {{ old('at_status') == 'present' ? 'selected' : '' }}>Hadir</option>
                    <option value="late" {{ old('at_status') == 'late' ? 'selected' : '' }}>Terlambat</option>
                    <option value="absent" {{ old('at_status') == 'absent' ? 'selected' : '' }}>Tidak Hadir</option>
                    <option value="early_leave" {{ old('at_status') == 'early_leave' ? 'selected' : '' }}>Pulang Awal</option>
                    <option value="scan_once" {{ old('at_status') == 'scan_once' ? 'selected' : '' }}>Scan 1 Kali</option>
                    @foreach($leaveTypes ?? [] as $leaveType)
                        <option value="leave_{{ $leaveType->lt_code }}" {{ old('at_status') == 'leave_' . $leaveType->lt_code ? 'selected' : '' }}>
                            {{ $leaveType->lt_name }}
                        </option>
                    @endforeach
                </select>
                @error('at_status')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="at_notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                <textarea id="at_notes" name="at_notes" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">{{ old('at_notes') }}</textarea>
                @error('at_notes')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-6">
            <a href="{{ route('attendance_v2') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                <i class="fas fa-save mr-2"></i>Simpan
            </button>
        </div>
    </form>
</div>

@push('scripts')
    @include('app._partials.js')
@endpush
@endsection
