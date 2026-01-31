@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage and organize all announcements</p>
        </div>
        <div>
            <a href="{{ route('announcements_v2.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <i class="cft-standard-stroke cft-plus"></i>
                Create Announcement
            </a>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="GET" action="{{ route('announcements_v2.manage') }}" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search Input -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="cft-standard-stroke cft-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               name="search"
                               id="searchInput"
                               class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Search by title, content, or sender..."
                               value="{{ request('search') }}">
                        @if(request('search'))
                        <button type="button" 
                                id="clearSearchBtn"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                            <i class="cft-standard-stroke cft-cross"></i>
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category_id" 
                            id="categoryFilter"
                            class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Division Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Division</label>
                    <select name="division_id" 
                            id="divisionFilter"
                            class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Divisions</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                {{ $division->ud_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" 
                            id="statusFilter"
                            class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="md:col-span-3 flex items-end gap-3">
                    <button type="submit" 
                            class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                        <i class="cft-standard-stroke cft-search"></i>
                        Search
                    </button>
                    <a href="{{ route('announcements.manage') }}" 
                       class="px-6 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors flex items-center gap-2">
                        <i class="cft-standard-stroke cft-refresh"></i>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Announcements Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pinned</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Published</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reactions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attachments</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($announcements as $announcement)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('announcements_v2.show', $announcement->id) }}" 
                               class="group flex items-center gap-3 cursor-pointer">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="cft-standard-stroke cft-speaker text-blue-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                        {{ $announcement->title }}
                                    </p>
                                    <p class="text-xs text-gray-500 truncate">
                                        {{ Str::limit(strip_tags($announcement->content), 50) }}
                                    </p>
                                </div>
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($announcement->category)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white" 
                                  style="background-color: {{ $announcement->category->color }};">
                                {{ $announcement->category->name }}
                            </span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ ucfirst($announcement->target_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $announcement->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($announcement->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $announcement->is_pinned ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $announcement->is_pinned ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $announcement->published_at ? $announcement->published_at->format('M d, Y H:i') : 'Draft' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $announcement->userReactions->count() ?? 0 }} reactions
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($announcement->attachments->count() > 0)
                            <div class="flex items-center gap-2">
                                @foreach($announcement->attachments->take(2) as $attachment)
                                <button onclick="viewAnnouncementAttachment('{{ $attachment->file_path }}', '{{ $attachment->original_name }}', '{{ $attachment->mime_type }}', '{{ $attachment->file_size }}')" 
                                        class="p-1.5 bg-gray-100 hover:bg-gray-200 rounded text-gray-600 hover:text-gray-900 transition-colors"
                                        title="{{ $attachment->original_name }}">
                                    <i class="cft-standard-stroke cft-eye text-xs"></i>
                                </button>
                                @endforeach
                                @if($announcement->attachments->count() > 2)
                                <span class="text-xs text-gray-500">+{{ $announcement->attachments->count() - 2 }}</span>
                                @endif
                            </div>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $announcement->creator->u_name ?? 'Unknown' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="relative inline-block text-left">
                                <button type="button" 
                                        onclick="toggleActionMenu({{ $announcement->id }})"
                                        class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg transition-colors flex items-center gap-1">
                                    Actions
                                    <i class="cft-standard-stroke cft-chevron-down text-xs"></i>
                                </button>
                                <div id="actionMenu-{{ $announcement->id }}" 
                                     class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                    <div class="py-1">
                                        <a href="{{ route('announcements_v2.edit', $announcement->id) }}" 
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="cft-standard-stroke cft-edit mr-2"></i>
                                            Edit
                                        </a>
                                        <button onclick="deleteAnnouncement({{ $announcement->id }}, '{{ addslashes($announcement->title) }}')" 
                                                class="block w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-gray-100">
                                            <i class="cft-standard-stroke cft-trash mr-2"></i>
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="cft-standard-stroke cft-mansory-grid text-gray-400 text-5xl mb-4"></i>
                                <p class="text-gray-500 text-lg">No announcements found</p>
                                <p class="text-gray-400 text-sm mt-2">Try adjusting your search or filters</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($announcements->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $announcements->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Attachment Viewer Modal -->
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
                <a href="#" id="downloadAttachment" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2" download>
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
    // Clear search button
    $('#clearSearchBtn').on('click', function() {
        $('#searchInput').val('');
        $('#filterForm').submit();
    });

    // Auto submit on filter change
    $('#categoryFilter, #divisionFilter, #statusFilter').on('change', function() {
        $('#filterForm').submit();
    });

    // Toggle action menu
    window.toggleActionMenu = function(announcementId) {
        // Close all menus
        $('[id^="actionMenu-"]').addClass('hidden');
        
        // Toggle current menu
        const menu = document.getElementById('actionMenu-' + announcementId);
        if (menu) {
            menu.classList.toggle('hidden');
        }
    };

    // Close menus when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('[onclick*="toggleActionMenu"]').length && 
            !$(e.target).closest('[id^="actionMenu-"]').length) {
            $('[id^="actionMenu-"]').addClass('hidden');
        }
    });

    // Delete announcement
    window.deleteAnnouncement = function(id, title) {
        if (confirm('Are you sure you want to delete the announcement "' + title + '"? This action cannot be undone.')) {
            $.ajax({
                url: '{{ url("announcements") }}/' + id,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        showToast('✓ Announcement deleted successfully!', 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        showToast('Error: ' + (response.message || 'Failed to delete announcement'), 'error');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Error deleting announcement. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    showToast(errorMsg, 'error');
                }
            });
        }
    };

    // View attachment (reuse from index.blade.php)
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

    // Toast notification (reuse from index.blade.php)
    function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toast-container') || createToastContainer();
        
        let bgColor, textColor, iconColor, icon;
        switch (type) {
            case 'success':
                bgColor = 'bg-green-50';
                textColor = 'text-green-800';
                iconColor = 'text-green-500';
                icon = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
                break;
            case 'error':
                bgColor = 'bg-red-50';
                textColor = 'text-red-800';
                iconColor = 'text-red-500';
                icon = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';
                break;
            default:
                bgColor = 'bg-blue-50';
                textColor = 'text-blue-800';
                iconColor = 'text-blue-500';
                icon = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>';
        }
        
        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `flex items-center w-full max-w-xs p-4 ${bgColor} ${textColor} rounded-lg shadow-lg border border-gray-200 animate-slide-in-right`;
        
        let hoverColor = type === 'success' ? 'hover:bg-green-100' : type === 'error' ? 'hover:bg-red-100' : 'hover:bg-blue-100';
        
        toast.innerHTML = `
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 ${iconColor}">
                ${icon}
            </div>
            <div class="ml-3 text-sm font-medium flex-1">${message}</div>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 ${bgColor} ${textColor} ${hoverColor} rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 inline-flex h-8 w-8 items-center justify-center transition-colors" onclick="document.getElementById('${toastId}').remove()">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        `;
        
        toastContainer.appendChild(toast);
        
        setTimeout(() => {
            if (document.getElementById(toastId)) {
                toast.style.animation = 'slide-in-right 0.3s ease-out reverse';
                setTimeout(() => {
                    if (document.getElementById(toastId)) {
                        document.getElementById(toastId).remove();
                    }
                }, 300);
            }
        }, 3000);
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-5 right-5 z-[9999] space-y-2';
        document.body.appendChild(container);
        return container;
    }
});
</script>
@endpush
