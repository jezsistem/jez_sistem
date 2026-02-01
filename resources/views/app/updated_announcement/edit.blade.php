@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Edit announcement details and settings</p>
        </div>
        <div>
            <a href="{{ route('announcements_v2.show', $announcement->id) }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors flex items-center gap-2">
                <i class="cft-standard-stroke cft-arrow-left"></i>
                Back
            </a>
        </div>
    </div>

    <form id="announcementForm" method="POST" action="{{ route('announcements.update', $announcement->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information Card -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Basic Information</h2>
                    
                    <div class="space-y-5">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   required
                                   value="{{ $announcement->title }}"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter announcement title">
                        </div>

                        <!-- Content -->
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                Content <span class="text-red-500">*</span>
                            </label>
                            <textarea id="content" 
                                      name="content" 
                                      rows="10" 
                                      required
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Enter announcement content">{{ $announcement->content }}</textarea>
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select id="category_id" 
                                    name="category_id" 
                                    required
                                    class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ $category->id == $announcement->category_id ? 'selected' : '' }}
                                            data-color="{{ $category->color }}">
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Current Attachments -->
                        @if($announcement->attachments->count() > 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Attachments</label>
                            <div class="space-y-2">
                                @foreach($announcement->attachments as $attachment)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            @if($attachment->is_image)
                                                <i class="cft-standard-stroke cft-picture text-blue-600"></i>
                                            @else
                                                <i class="cft-standard-stroke cft-file text-blue-600"></i>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment->original_name }}</p>
                                            <p class="text-xs text-gray-500">
                                                @if($attachment->file_size)
                                                    {{ number_format($attachment->file_size / 1024, 2) }} KB
                                                @endif
                                                • {{ strtoupper($attachment->file_type ?? 'Unknown') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" 
                                                onclick="viewAnnouncementAttachment('{{ $attachment->file_path }}', '{{ $attachment->original_name }}', '{{ $attachment->mime_type }}', '{{ $attachment->file_size }}')"
                                                class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg transition-colors">
                                            View
                                        </button>
                                        <button type="button" 
                                                onclick="removeAttachment({{ $attachment->id }})"
                                                class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 text-sm rounded-lg transition-colors">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- New Attachments -->
                        <div>
                            <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">
                                @if($announcement->attachments->count() > 0)
                                    Add More Attachments (Optional)
                                @else
                                    Attachments (Optional)
                                @endif
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <i class="cft-standard-stroke cft-paper-clip text-gray-400 text-3xl"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="attachments" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                            <span>Upload files</span>
                                            <input id="attachments" 
                                                   name="attachments[]" 
                                                   type="file" 
                                                   multiple 
                                                   accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                                                   class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX up to 10MB</p>
                                </div>
                            </div>
                            <div id="attachment-preview" class="mt-3 space-y-2"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Target & Settings Card -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Target & Settings</h2>
                    
                    <div class="space-y-5">
                        <!-- Target Type -->
                        <div>
                            <label for="target_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Target Audience <span class="text-red-500">*</span>
                            </label>
                            <select id="target_type" 
                                    name="target_type" 
                                    required
                                    class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Target</option>
                                <option value="all" {{ $announcement->target_type == 'all' ? 'selected' : '' }}>All Users</option>
                                <option value="division" {{ $announcement->target_type == 'division' ? 'selected' : '' }}>Specific Division</option>
                                <option value="individual" {{ $announcement->target_type == 'individual' ? 'selected' : '' }}>Specific Users</option>
                            </select>
                        </div>

                        <!-- Target Date -->
                        <div>
                            <label for="target_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Target Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   id="target_date" 
                                   name="target_date" 
                                   required
                                   value="{{ $announcement->target_date ? \Carbon\Carbon::parse($announcement->target_date)->format('Y-m-d') : '' }}"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Division Selection -->
                        <div id="divisionGroup" style="display: {{ $announcement->target_type == 'division' ? 'block' : 'none' }};">
                            <label for="division_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Select Division <span class="text-red-500">*</span>
                            </label>
                            <select id="division_id" 
                                    name="division_id"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Division</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}"
                                            {{ $announcement->recipients->where('recipient_type', 'division')->where('recipient_id', $division->id)->first() ? 'selected' : '' }}>
                                        {{ $division->ud_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Individual Users Selection -->
                        <div id="usersGroup" class="bg-gray-50 p-4 rounded-lg border border-gray-200" style="display: {{ $announcement->target_type == 'individual' ? 'block' : 'none' }};">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Select Users <span class="text-red-500">*</span>
                            </label>
                            
                            <!-- Filter by Division -->
                            <div class="mb-3">
                                <label for="user_division_filter" class="block text-xs text-gray-600 mb-1">Filter by Division (Optional)</label>
                                <select id="user_division_filter" 
                                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Divisions</option>
                                    <option value="no_division">No Division</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->ud_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Search Users -->
                            <div class="mb-3">
                                <input type="text" 
                                       id="user_search" 
                                       placeholder="Search users by name or NIP..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-xs text-gray-500 mt-1">
                                    <span id="visibleUserCount">{{ count($users) }}</span> users available
                                </p>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex gap-2 mb-3">
                                <button type="button" 
                                        id="selectAllBtn"
                                        class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors">
                                    Select All
                                </button>
                                <button type="button" 
                                        id="clearAllBtn"
                                        class="flex-1 px-3 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                                    Clear All
                                </button>
                            </div>
                            
                            <!-- Users List -->
                            <div class="border border-gray-200 bg-white rounded-lg p-2 max-h-64 overflow-y-auto">
                                <div id="usersList" class="space-y-1">
                                    @foreach($users as $user)
                                        @php
                                            $division = $divisions->firstWhere('id', $user->ud_id);
                                            $divisionName = $division ? $division->ud_name : 'No Division';
                                            $divisionId = $user->ud_id ?? '';
                                            $isSelected = $announcement->recipients
                                                ->where('recipient_type', 'user')
                                                ->where('recipient_id', $user->id)
                                                ->first() ? true : false;
                                        @endphp
                                        <div class="user-item flex items-center gap-2 p-2 hover:bg-gray-50 rounded transition-colors" 
                                             data-user-id="{{ $user->id }}" 
                                             data-user-name="{{ strtolower($user->u_name) }}" 
                                             data-user-nip="{{ $user->u_nip }}" 
                                             data-division-id="{{ $divisionId }}" 
                                             data-division-name="{{ $divisionName }}">
                                            <input type="checkbox" 
                                                   name="user_ids[]" 
                                                   value="{{ $user->id }}" 
                                                   class="user-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                   {{ $isSelected ? 'checked' : '' }}>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900">{{ $user->u_name }}</p>
                                                <p class="text-xs text-gray-500">{{ $user->u_nip }} • {{ $divisionName }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Selected Count -->
                            <div class="mt-3">
                                <p class="text-xs text-gray-600">
                                    <span id="selectedCount">{{ $announcement->recipients->where('recipient_type', 'user')->count() }}</span> user(s) selected
                                </p>
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select id="status" 
                                    name="status"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="active" {{ $announcement->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $announcement->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <!-- Pinned -->
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="is_pinned" 
                                   name="is_pinned" 
                                   value="1"
                                   {{ $announcement->is_pinned ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="is_pinned" class="ml-2 text-sm text-gray-700">
                                Pin this announcement
                            </label>
                        </div>

                        <!-- Publish Now -->
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="publish_now" 
                                   name="publish_now" 
                                   value="1" 
                                   {{ !$announcement->published_at ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="publish_now" class="ml-2 text-sm text-gray-700">
                                Publish immediately
                            </label>
                        </div>

                        <!-- Publish Date -->
                        <div id="publishDateGroup" style="display: {{ $announcement->published_at ? 'block' : 'none' }};">
                            <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">
                                Publish Date & Time
                            </label>
                            <input type="datetime-local" 
                                   id="published_at" 
                                   name="published_at"
                                   value="{{ $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i') : '' }}"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Created Info -->
                        <div class="pt-4 border-t border-gray-200">
                            <p class="text-xs text-gray-500 mb-1">Created by</p>
                            <p class="text-sm font-medium text-gray-900">{{ $announcement->creator->u_name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $announcement->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
            <button type="button" 
                    onclick="history.back()"
                    class="px-6 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                Cancel
            </button>
            <button type="submit" 
                    id="submitBtn"
                    class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors flex items-center gap-2">
                <i class="cft-standard-stroke cft-save"></i>
                Update Announcement
            </button>
        </div>
    </form>
</div>

<!-- Attachment Viewer Modal (reuse from show.blade.php) -->
<div id="attachmentModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeAttachmentModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2" id="attachmentModalLabel">
                    <i class="cft-standard-stroke cft-paper-clip text-blue-600"></i>
                    View Attachment
                </h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeAttachmentModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="max-h-[70vh] overflow-y-auto p-6" id="attachmentContent"></div>
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors" onclick="closeAttachmentModal()">
                    Close
                </button>
                <a href="#" id="downloadAttachment" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600 transition-colors flex items-center gap-2" download>
                    <i class="cft-standard-stroke cft-cloud-download"></i>
                    Download
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Target type change handler
    $('#target_type').on('change', function() {
        const targetType = $(this).val();
        $('#divisionGroup').addClass('hidden');
        $('#usersGroup').addClass('hidden');
        
        if (targetType === 'division') {
            $('#divisionGroup').removeClass('hidden');
        } else if (targetType === 'individual') {
            $('#usersGroup').removeClass('hidden');
        }
    });

    // Publish now checkbox handler
    $('#publish_now').on('change', function() {
        if ($(this).is(':checked')) {
            $('#publishDateGroup').addClass('hidden');
        } else {
            $('#publishDateGroup').removeClass('hidden');
        }
    });

    // File upload handling
    $('#attachments').on('change', function() {
        const files = Array.from(this.files);
        const previewContainer = $('#attachment-preview');
        previewContainer.empty();
        
        files.forEach(function(file, index) {
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            const isImage = file.type.startsWith('image/');
            
            const filePreview = $(`
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200" data-file-index="${index}">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="cft-standard-stroke ${isImage ? 'cft-picture' : 'cft-file'} text-blue-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">${file.name}</p>
                        <p class="text-xs text-gray-500">${fileSize} MB</p>
                    </div>
                    <button type="button" class="px-2 py-1 text-red-500 hover:text-red-700" onclick="removeFilePreview(${index})">
                        <i class="cft-standard-stroke cft-cross"></i>
                    </button>
                </div>
            `);
            
            previewContainer.append(filePreview);
        });
    });

    // User selection functionality
    function initializeUserSelection() {
        // Division filter
        $('#user_division_filter').on('change', filterUsers);
        
        // Search
        $('#user_search').on('input', filterUsers);
        
        // Select all
        $('#selectAllBtn').on('click', function() {
            getVisibleUserCheckboxes().forEach(function(checkbox) {
                $(checkbox).prop('checked', true);
            });
            updateSelectedCount();
        });
        
        // Clear all
        $('#clearAllBtn').on('click', function() {
            $('.user-checkbox').prop('checked', false);
            updateSelectedCount();
        });
        
        // Update count on checkbox change
        $('.user-checkbox').on('change', updateSelectedCount);
        
        updateSelectedCount();
    }

    function filterUsers() {
        const selectedDivision = $('#user_division_filter').val();
        const searchTerm = $('#user_search').val().toLowerCase();
        let visibleCount = 0;
        
        $('.user-item').each(function() {
            const $item = $(this);
            const userName = $item.data('user-name') || '';
            const userNip = $item.data('user-nip') || '';
            const divisionId = $item.data('division-id') || '';
            
            let matchesDivision = true;
            if (selectedDivision) {
                if (selectedDivision === 'no_division') {
                    matchesDivision = divisionId === '';
                } else if (divisionId === '') {
                    matchesDivision = false;
                } else {
                    matchesDivision = divisionId === selectedDivision;
                }
            }
            
            const matchesSearch = !searchTerm || 
                userName.includes(searchTerm) || 
                userNip.includes(searchTerm);
            
            if (matchesDivision && matchesSearch) {
                $item.show();
                visibleCount++;
            } else {
                $item.hide();
            }
        });
        
        $('#visibleUserCount').text(visibleCount);
        updateSelectedCount();
    }

    function getVisibleUserCheckboxes() {
        return $('.user-item:visible .user-checkbox').toArray();
    }

    function updateSelectedCount() {
        const checkedCount = $('.user-checkbox:checked').length;
        $('#selectedCount').text(checkedCount);
    }

    // Form submission
    $('#announcementForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate required fields
        const title = $('#title').val().trim();
        const content = $('#content').val().trim();
        const categoryId = $('#category_id').val();
        const targetType = $('#target_type').val();
        
        if (!title || !content || !categoryId || !targetType) {
            alert('Please fill in all required fields.');
            return false;
        }
        
        // Validate target-specific fields
        if (targetType === 'division') {
            const divisionId = $('#division_id').val();
            if (!divisionId) {
                alert('Please select a division.');
                return false;
            }
        }
        
        if (targetType === 'individual') {
            const checkedCount = $('.user-checkbox:checked').length;
            if (checkedCount === 0) {
                alert('Please select at least one user.');
                return false;
            }
        }
        
        // Show loading state
        const $submitBtn = $('#submitBtn');
        const originalHtml = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<i class="cft-standard-stroke cft-loading"></i> Updating...');
        
        // Submit form
        const formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    window.location.href = '{{ route("announcements_v2.show", $announcement->id) }}';
                }
            },
            error: function(xhr) {
                let errorMsg = 'Error updating announcement. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMsg = errors.join('\n');
                }
                alert(errorMsg);
                $submitBtn.prop('disabled', false).html(originalHtml);
            }
        });
        
        return false;
    });

    // Initialize user selection
    initializeUserSelection();
});

// Remove attachment
function removeAttachment(attachmentId) {
    if (!confirm('Are you sure you want to remove this attachment?')) {
        return;
    }
    
    $.ajax({
        url: '{{ url("announcements/attachment") }}/' + attachmentId + '/remove',
        method: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error removing attachment: ' + (response.message || 'Unknown error'));
            }
        },
        error: function() {
            alert('Error removing attachment. Please try again.');
        }
    });
}

// View attachment (reuse from show.blade.php)
function formatFileSize(bytes) {
    if (!bytes) return 'Unknown size';
    if (bytes >= 1073741824) {
        return (bytes / 1073741824).toFixed(2) + ' GB';
    } else if (bytes >= 1048576) {
        return (bytes / 1048576).toFixed(2) + ' MB';
    } else if (bytes >= 1024) {
        return (bytes / 1024).toFixed(2) + ' KB';
    } else {
        return bytes + ' bytes';
    }
}

window.viewAnnouncementAttachment = function(filePath, fileName, mimeType, fileSize) {
    const modalElement = document.getElementById('attachmentModal');
    const contentElement = document.getElementById('attachmentContent');
    const downloadLink = document.getElementById('downloadAttachment');
    const modalTitle = document.getElementById('attachmentModalLabel');
    
    const fileSizeNum = typeof fileSize === 'string' ? parseInt(fileSize) : fileSize;
    modalTitle.innerHTML = '<i class="cft-standard-stroke cft-paper-clip text-blue-600"></i> View Attachment: ' + fileName;
    
    const storageUrl = '{{ url("storage") }}/' + filePath;
    downloadLink.href = storageUrl;
    downloadLink.download = fileName;
    
    modalElement.classList.remove('hidden');
    modalElement.setAttribute('aria-hidden', 'false');
    $('body').addClass('overflow-hidden');
    
    contentElement.innerHTML = '<div class="flex items-center justify-center py-8"><div class="text-center"><div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-blue-600 border-r-transparent"></div><p class="mt-2 text-sm text-gray-500">Loading attachment...</p></div></div>';
    
    setTimeout(function() {
        if (mimeType && mimeType.includes('image')) {
            contentElement.innerHTML = `
                <div class="text-center">
                    <img src="${storageUrl}" alt="${fileName}" class="mx-auto rounded-lg shadow-lg" style="max-height: 60vh; max-width: 100%;">
                    <p class="mt-4 font-semibold text-gray-900">${fileName}</p>
                    <p class="text-sm text-gray-500">File size: ${formatFileSize(fileSizeNum)}</p>
                </div>
            `;
        } else if (mimeType && mimeType.includes('pdf')) {
            contentElement.innerHTML = `
                <div class="text-center">
                    <iframe src="${storageUrl}" width="100%" height="600" frameborder="0" class="rounded-lg"></iframe>
                    <p class="mt-4 font-semibold text-gray-900">${fileName}</p>
                    <p class="text-sm text-gray-500">File size: ${formatFileSize(fileSizeNum)}</p>
                </div>
            `;
        } else {
            contentElement.innerHTML = `
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                        <i class="cft-standard-stroke cft-file text-blue-600 text-2xl"></i>
                    </div>
                    <h5 class="text-lg font-semibold text-gray-900 mb-2">${fileName}</h5>
                    <p class="text-gray-600 mb-2">This file type cannot be previewed directly.</p>
                    <p class="text-gray-500 text-sm mb-4">Please download the file to view its contents.</p>
                    <p class="text-sm text-gray-500">File size: ${formatFileSize(fileSizeNum)}</p>
                </div>
            `;
        }
    }, 100);
};

window.closeAttachmentModal = function() {
    const modalElement = document.getElementById('attachmentModal');
    modalElement.classList.add('hidden');
    modalElement.setAttribute('aria-hidden', 'true');
    $('body').removeClass('overflow-hidden');
};

// Remove file preview
function removeFilePreview(index) {
    const input = document.getElementById('attachments');
    const dt = new DataTransfer();
    const files = Array.from(input.files);
    
    files.forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    input.files = dt.files;
    $('#attachments').trigger('change');
}
</script>
@endpush
