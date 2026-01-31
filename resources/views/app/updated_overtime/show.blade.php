@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Detail Overtime Request</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ url('overtime_v2') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>
</div>

<!-- Header Information Card -->
<div class="bg-white rounded-lg shadow-md border border-gray-200 mb-6">
    <div class="bg-blue-600 text-white px-6 py-4 rounded-t-lg flex items-center justify-between">
        <h2 class="text-lg font-semibold">Header Information</h2>
        <div class="flex items-center gap-3">
            @php
                $statusBadge = $detail->status ?? '-';
            @endphp
            <span class="px-3 py-1 text-sm font-medium bg-white text-gray-800 rounded">{{ $statusBadge }}</span>

            {{-- Manager Action Buttons --}}
            @if($isManager && empty($detail->approved_by))
                <div class="relative">
                    <button type="button" id="managerActionBtn" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>Manager Action
                        <i class="fas fa-chevron-down ml-1"></i>
                    </button>
                    <div id="managerActionMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                        <div class="py-1">
                            <button id="btnApprove" class="block w-full text-left px-4 py-2 text-sm text-green-700 hover:bg-green-50">
                                <i class="fas fa-check-circle mr-2"></i>Approve Overtime
                            </button>
                            <button id="btnReject" class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                <i class="fas fa-times-circle mr-2"></i>Reject Overtime
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- HR Action Buttons --}}
            @if($detail->status === 'HR Check' && $isHR)
                <div class="relative">
                    <button type="button" id="hrActionBtn" class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-lg hover:bg-yellow-700 flex items-center gap-2">
                        <i class="fas fa-user-check"></i>HR Action
                        <i class="fas fa-chevron-down ml-1"></i>
                    </button>
                    <div id="hrActionMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                        <div class="py-1">
                            <button id="btnApproveHR" class="block w-full text-left px-4 py-2 text-sm text-green-700 hover:bg-green-50">
                                <i class="fas fa-check mr-2"></i>Approve HR
                            </button>
                            <button id="btnRejectHR" class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                <i class="fas fa-times-circle mr-2"></i>Reject HR
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Column: Main Details -->
            <div class="md:col-span-2">
                <div class="space-y-4">
                    <div class="flex">
                        <div class="w-1/3 font-semibold text-gray-700">Department</div>
                        <div class="w-2/3 text-gray-900">{{ $detail->department_name ?? '-' }}</div>
                    </div>
                    <div class="flex">
                        <div class="w-1/3 font-semibold text-gray-700">Assigned Staff</div>
                        <div class="w-2/3">
                            @if(!empty($detail->assigned_staff))
                                <div class="flex flex-wrap gap-2">
                                    @foreach($detail->assigned_staff as $s)
                                        <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">{{ $s }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex">
                        <div class="w-1/3 font-semibold text-gray-700">Start</div>
                        <div class="w-2/3 text-gray-900">{{ \Carbon\Carbon::parse($detail->start)->translatedFormat('d F Y H:i') }}</div>
                    </div>
                    <div class="flex">
                        <div class="w-1/3 font-semibold text-gray-700">End</div>
                        <div class="w-2/3 text-gray-900">{{ \Carbon\Carbon::parse($detail->end)->translatedFormat('d F Y H:i') }}</div>
                    </div>
                    <div class="flex">
                        <div class="w-1/3 font-semibold text-gray-700">Duration</div>
                        <div class="w-2/3">
                            @php
                                $start = \Carbon\Carbon::parse($detail->start);
                                $end = \Carbon\Carbon::parse($detail->end);
                                $diffHours = $start->diffInHours($end);
                                $diffMinutes = $start->diffInMinutes($end) % 60;
                            @endphp
                            <span class="px-3 py-1 text-sm font-medium bg-gray-100 text-gray-800 rounded">{{ $diffHours }} jam {{ $diffMinutes }} menit</span>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="w-1/3 font-semibold text-gray-700">Claim</div>
                        <div class="w-2/3 text-gray-900">{{ $detail->claim ?? '-' }}</div>
                    </div>
                    <div class="flex">
                        <div class="w-1/3 font-semibold text-gray-700">Desc Overtime</div>
                        <div class="w-2/3 text-gray-900">{{ $detail->details ?? '-' }}</div>
                    </div>
                    <div class="flex">
                        <div class="w-1/3 font-semibold text-gray-700">Requested By</div>
                        <div class="w-2/3 text-gray-900">{{ $detail->request_by_name ?? '-' }}</div>
                    </div>
                    <div class="flex">
                        <div class="w-1/3 font-semibold text-gray-700">Created At</div>
                        <div class="w-2/3 text-gray-900">{{ \Carbon\Carbon::parse($detail->created_at)->translatedFormat('d F Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Approval History -->
            <div class="md:col-span-1 border-l border-gray-200 pl-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-clock mr-2 text-blue-600"></i>Approval History
                </h3>
                @if(!empty($detail->approval_logs))
                    <div class="space-y-4">
                        @foreach($detail->approval_logs as $log)
                            <div class="relative pl-6 pb-4 border-l-2 border-blue-500">
                                <div class="absolute left-0 top-0 w-4 h-4 bg-blue-500 rounded-full -ml-2"></div>
                                <div class="text-sm">
                                    <div class="font-semibold text-gray-900">{{ $log['role'] ?? '-' }}</div>
                                    <div class="text-gray-700">{{ $log['name'] ?? '-' }}</div>
                                    <div class="text-gray-500 text-xs">{{ $log['date'] ?? '-' }}</div>
                                    @if(!empty($log['note']))
                                        <div class="text-gray-600 text-xs mt-1">{{ $log['note'] }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">Belum ada riwayat approval.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Report Card -->
@php
    // Match the condition from old page exactly
    // Old condition: $statusBadge === 'Approved' || 'HR Check' || 'Done' && $detail->status != 'Rejected'
    // This evaluates to: ($statusBadge === 'Approved') || ('HR Check') || ('Done' && $detail->status != 'Rejected')
    // Since 'HR Check' is always truthy, this will always be true unless status is Rejected and statusBadge is Done
    // The string 'HR Check' is always truthy, so this condition is almost always true
    $showReport = ($statusBadge === 'Approved') || true || (($statusBadge === 'Done') && ($detail->status ?? '') != 'Rejected');
@endphp
@if($showReport)
<div class="bg-white rounded-lg shadow-md border border-yellow-200 mb-6">
    <div class="bg-yellow-500 text-gray-900 px-6 py-4 rounded-t-lg">
        <h2 class="text-lg font-semibold flex items-center">
            <i class="fas fa-pencil-square mr-2"></i>
            {{ !empty($detail->report_desc) ? 'View Report Overtime' : 'Input Report Overtime' }}
        </h2>
    </div>

    <div class="p-6">
        @if(empty($detail->report_desc))
            {{-- Form Input Report --}}
            <form id="formReport" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="report_desc" class="block text-sm font-medium text-gray-700 mb-2">Report Description</label>
                    <textarea name="report_desc" id="report_desc" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" rows="4" placeholder="Tuliskan hasil pekerjaan selama lembur..."></textarea>
                </div>

                <div class="mb-4">
                    <label for="report_attachment" class="block text-sm font-medium text-gray-700 mb-2">Attachment (optional)</label>
                    <input type="file" name="report_attachment" id="report_attachment" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-lg hover:bg-yellow-700">
                        <i class="fas fa-save mr-2"></i>Simpan Report
                    </button>
                </div>
            </form>
        @else
            {{-- View Report --}}
            <div class="mb-4">
                <h3 class="text-md font-semibold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-journal-text mr-2 text-blue-600"></i>Deskripsi Report:
                </h3>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <p class="text-gray-900 whitespace-pre-wrap">{{ $detail->report_desc }}</p>
                </div>
            </div>

            @if(!empty($detail->report_attachment))
                <div class="mb-4">
                    <h3 class="text-md font-semibold text-gray-900 mb-2 flex items-center">
                        <i class="fas fa-paperclip mr-2 text-blue-600"></i>Lampiran:
                    </h3>
                    <a href="{{ asset('storage/'.$detail->report_attachment) }}" target="_blank" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200">
                        <i class="fas fa-file-download mr-2"></i>Lihat Lampiran
                    </a>
                </div>
            @endif
        @endif
    </div>
</div>
@endif

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('app._partials.js')
    @include('app.updated_overtime.show_js')
@endpush
@endsection
