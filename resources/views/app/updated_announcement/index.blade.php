@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Stay updated with the latest announcements</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('announcements_v2.create') }}" class="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                <i class="cft-standard-stroke cft-plus"></i>
                New Announcement
            </a>
            <a href="{{ route('announcements.manage') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors flex items-center gap-2">
                <i class="cft-standard-stroke cft-settings"></i>
                Manage
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Search Section -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Announcements</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="cft-standard-stroke cft-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                        id="searchInput" 
                        class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-red-500 focus:border-red-500" 
                        placeholder="Search by title, content, or sender name..."
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


            <!-- Pinned Announcements Section -->
            @if ($pinnedAnnouncements->count() > 0)
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <i class="cft-standard-stroke cft-pin text-red-500"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Pinned Announcements</h2>
                </div>
                <div class="space-y-4" id="pinnedAnnouncements">
                    @foreach ($pinnedAnnouncements as $announcement)
                        @include('app.updated_announcement._announcement_card', ['announcement' => $announcement, 'reactions' => $reactions])
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Regular Announcements Section -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">All Announcements</h2>
                    <span class="text-sm text-gray-500" id="announcementCount">{{ $regularAnnouncements->count() }} announcements</span>
                </div>
                <div class="space-y-4" id="regularAnnouncements">
                    @if ($regularAnnouncements->count() > 0)
                        @foreach ($regularAnnouncements as $announcement)
                            @include('app.updated_announcement._announcement_card', ['announcement' => $announcement, 'reactions' => $reactions])
                        @endforeach
                    @else
                        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                            <i class="cft-standard-stroke cft-mansory-grid text-gray-400 text-5xl mb-4"></i>
                            <p class="text-gray-500 text-lg">No announcements found</p>
                            <p class="text-gray-400 text-sm mt-2">Try adjusting your search or filters</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar - Category Filter -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 p-6 sticky top-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Categories</h3>
                <div class="space-y-2">
                    <button type="button" 
                            class="category-filter-btn w-full text-left px-4 py-2.5 rounded-xl text-sm font-medium transition-colors bg-gray-100 text-gray-700 border border-gray-200"
                            data-category="all">
                        All Categories
                    </button>
                    @foreach ($categories as $category)
                        <button type="button" 
                                class="category-filter-btn w-full text-left px-4 py-2.5 rounded-xl text-sm font-medium transition-colors bg-gray-100 text-gray-700 border border-gray-200"
                                data-category="{{ $category->id }}"> 
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reaction Details Modal -->
<div id="reactionDetailsModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeReactionModal()"></div>
    
    <!-- Modal Container -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">Reaction Details</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeReactionModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="max-h-[60vh] overflow-y-auto px-6 py-4" id="reactionDetailsContent">
                <div class="flex items-center justify-center py-8">
                    <div class="text-center">
                        <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-red-500 border-r-transparent"></div>
                        <p class="mt-2 text-sm text-gray-500">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Viewers Modal -->
<div id="viewersModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeViewersModal()"></div>
    
    <!-- Modal Container -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">Viewers</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeViewersModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="max-h-[60vh] overflow-y-auto px-6 py-4" id="viewersContent">
                <div class="flex items-center justify-center py-8">
                    <div class="text-center">
                        <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-red-500 border-r-transparent"></div>
                        <p class="mt-2 text-sm text-gray-500">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container" class="fixed top-5 right-5 z-[9999] space-y-2"></div>

<!-- Attachment Viewer Modal -->
<div id="attachmentModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeAttachmentModal()"></div>
    
    <!-- Modal Container -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2" id="attachmentModalLabel">
                    <i class="cft-standard-stroke cft-paper-clip text-red-500"></i>
                    View Attachment
                </h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeAttachmentModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="max-h-[70vh] overflow-y-auto p-6" id="attachmentContent">
                <div class="flex items-center justify-center py-8">
                    <div class="text-center">
                        <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-red-500 border-r-transparent"></div>
                        <p class="mt-2 text-sm text-gray-500">Loading attachment...</p>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors" onclick="closeAttachmentModal()">
                    Close
                </button>
                <a href="#" id="downloadAttachment" class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2" download>
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
    // Search functionality
    let searchTimeout;
    $('#searchInput').on('keyup', function() {
        clearTimeout(searchTimeout);
        const search = $(this).val();
        
        searchTimeout = setTimeout(function() {
            if (search.length >= 2 || search.length === 0) {
                filterAnnouncements();
            }
        }, 500);
    });

    // Clear search
    $('#clearSearchBtn').on('click', function() {
        $('#searchInput').val('');
        filterAnnouncements();
    });

    // Category filter buttons
    $('.category-filter-btn').on('click', function() {
        // Remove active class from all buttons
        $('.category-filter-btn').removeClass('bg-red-50 text-red-500 border border-red-200 border-l-4 border-l-red-500').addClass('text-gray-700');
        // Add active class to clicked button
        $(this).removeClass('text-gray-700').addClass('bg-red-50 text-red-500 border border-red-200 border-l-4 border-l-red-500');
        
        filterAnnouncements();
    });

    // Filter announcements
    function filterAnnouncements() {
        const search = $('#searchInput').val().toLowerCase();
        const activeCategoryBtn = $('.category-filter-btn.bg-red-50');
        const categoryId = activeCategoryBtn.length > 0 ? activeCategoryBtn.data('category') : 'all';
        
        // Pinned announcements - always show
        $('#pinnedAnnouncements .announcement-card').each(function() {
            const $card = $(this);
            const title = $card.find('.announcement-title').text().toLowerCase();
            const content = $card.find('.announcement-content').text().toLowerCase();
            
            const matchesSearch = !search || title.includes(search) || content.includes(search);
            
            if (matchesSearch) {
                $card.removeClass('hidden');
            } else {
                $card.addClass('hidden');
            }
        });
        
        // Regular announcements - apply both filters
        $('#regularAnnouncements .announcement-card').each(function() {
            const $card = $(this);
            const title = $card.find('.announcement-title').text().toLowerCase();
            const content = $card.find('.announcement-content').text().toLowerCase();
            const cardCategoryId = $card.data('category-id') || '';
            
            const matchesSearch = !search || title.includes(search) || content.includes(search);
            const matchesCategory = categoryId === 'all' || cardCategoryId == categoryId;
            
            if (matchesSearch && matchesCategory) {
                $card.removeClass('hidden');
            } else {
                $card.addClass('hidden');
            }
        });
        
        // Update count (only regular announcements)
        const visibleCount = $('#regularAnnouncements .announcement-card:not(.hidden)').length;
        $('#announcementCount').text(visibleCount + ' announcements');
    }

    // Show toast notification
    function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) return;
        
        // Determine colors based on type
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
            case 'warning':
                bgColor = 'bg-yellow-50';
                textColor = 'text-yellow-800';
                iconColor = 'text-yellow-500';
                icon = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>';
                break;
            default:
                bgColor = 'bg-red-50';
                textColor = 'text-red-800';
                iconColor = 'text-red-500';
                icon = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>';
        }
        
        // Create toast element
        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `flex items-center w-full max-w-xs p-4 ${bgColor} ${textColor} rounded-lg shadow-lg border border-gray-200 animate-slide-in-right`;
        
        // Determine hover color for close button
        let hoverColor = '';
        switch (type) {
            case 'success':
                hoverColor = 'hover:bg-green-100';
                break;
            case 'error':
                hoverColor = 'hover:bg-red-100';
                break;
            case 'warning':
                hoverColor = 'hover:bg-yellow-100';
                break;
            default:
                hoverColor = 'hover:bg-red-100';
        }
        
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
        
        // Add animation styles
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slide-in-right {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            .animate-slide-in-right {
                animation: slide-in-right 0.3s ease-out;
            }
        `;
        if (!document.getElementById('toast-animation-style')) {
            style.id = 'toast-animation-style';
            document.head.appendChild(style);
        }
        
        // Add toast to container
        toastContainer.appendChild(toast);
        
        // Auto remove after 3 seconds
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

    // Reaction functionality with toast notification and real-time update
    window.reactToAnnouncement = function(announcementId, reactionId) {
        $.ajax({
            url: '{{ route("announcements.react") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                announcement_id: announcementId,
                reaction_id: reactionId
            },
            success: function(response) {
                if (response.success) {
                    // Update reaction button states
                    // Remove active state from all buttons for this announcement
                    $(`.reaction-btn[data-announcement="${announcementId}"]`).each(function() {
                        $(this).removeClass('bg-red-100 text-red-700 border border-red-300')
                               .addClass('bg-gray-100 text-gray-700');
                        $(this).find('.reaction-count').removeClass('text-red-500').addClass('text-gray-500');
                    });
                    
                    // Add active state to clicked button
                    const activeButton = $(`.reaction-btn[data-announcement="${announcementId}"][data-reaction="${reactionId}"]`);
                    if (activeButton.length) {
                        activeButton.removeClass('bg-gray-100 text-gray-700')
                                   .addClass('bg-red-100 text-red-700 border border-red-300');
                        activeButton.find('.reaction-count').removeClass('text-gray-500').addClass('text-red-500');
                    }
                    
                    // Update reaction counts in real-time
                    if (response.reaction_counts) {
                        Object.keys(response.reaction_counts).forEach(function(reactionIdKey) {
                            const count = response.reaction_counts[reactionIdKey];
                            const countElement = $(`.reaction-btn[data-announcement="${announcementId}"][data-reaction="${reactionIdKey}"] .reaction-count`);
                            if (countElement.length) {
                                countElement.text('(' + count + ')');
                            }
                        });
                    }
                    
                    // Update total reactions count
                    if (response.total_reactions !== undefined) {
                        // Find the card by data-announcement-id and update total reactions
                        const card = $(`.announcement-card[data-announcement-id="${announcementId}"]`);
                        const totalElement = card.find('.total-reactions');
                        if (totalElement.length) {
                            totalElement.text(response.total_reactions);
                        }
                    }
                    
                    // Show success toast
                    showToast('✓ Reaction saved!', 'success');
                } else {
                    showToast('Error: ' + (response.message || 'Failed to save reaction'), 'error');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Error saving reaction. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showToast(errorMsg, 'error');
            }
        });
    };

    // Modal instances
    let reactionModalInstance = null;
    let viewersModalInstance = null;
    
    // Function to get or create modal instance
    function getOrCreateModal(modalId) {
        const modalElement = document.getElementById(modalId);
        if (!modalElement) return null;
        
        if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
            // Try to get existing instance
            let instance = Flowbite.Modal.getInstance(modalElement);
            
            // If no instance exists, create one
            if (!instance) {
                try {
                    instance = new Flowbite.Modal(modalElement, {
                        backdrop: true,
                        closable: true,
                        placement: 'center'
                    });
                    modalElement.setAttribute('data-flowbite-initialized', 'true');
                } catch(err) {
                    console.error('Error creating Flowbite modal:', err);
                    return null;
                }
            }
            
            return instance;
        }
        
        return null;
    }

    // Show reaction details
    window.showReactionDetails = function(announcementId) {
        const modalElement = document.getElementById('reactionDetailsModal');
        const contentElement = document.getElementById('reactionDetailsContent');
        
        // Show modal first with loading state
        modalElement.classList.remove('hidden');
        modalElement.setAttribute('aria-hidden', 'false');
        $('body').addClass('overflow-hidden');
        
        // Reset content to loading state
        contentElement.innerHTML = '<div class="flex items-center justify-center py-8"><div class="text-center"><div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-red-500 border-r-transparent"></div><p class="mt-2 text-sm text-gray-500">Loading...</p></div></div>';
        
        // Load content via AJAX
        $.ajax({
            url: '{{ url("announcements") }}/' + announcementId + '/reactions',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response && response.success && response.html) {
                    contentElement.innerHTML = response.html;
                } else {
                    contentElement.innerHTML = '<div class="text-center py-8"><p class="text-gray-500">No reaction details available</p></div>';
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading reaction details:', error, xhr);
                let errorMsg = 'Error loading reaction details. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                contentElement.innerHTML = '<div class="text-center py-8"><p class="text-red-500">' + errorMsg + '</p></div>';
            }
        });
    };

    // Show viewers
    window.showViewDetails = function(announcementId) {
        const modalElement = document.getElementById('viewersModal');
        const contentElement = document.getElementById('viewersContent');
        
        // Show modal first with loading state
        modalElement.classList.remove('hidden');
        modalElement.setAttribute('aria-hidden', 'false');
        $('body').addClass('overflow-hidden');
        
        // Reset content to loading state
        contentElement.innerHTML = '<div class="flex items-center justify-center py-8"><div class="text-center"><div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-red-500 border-r-transparent"></div><p class="mt-2 text-sm text-gray-500">Loading...</p></div></div>';
        
        // Load content via AJAX
        $.ajax({
            url: '{{ url("announcements") }}/' + announcementId + '/viewers',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response && response.success && response.html) {
                    contentElement.innerHTML = response.html;
                } else {
                    contentElement.innerHTML = '<div class="text-center py-8"><p class="text-gray-500">No viewers data available</p></div>';
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading viewers:', error, xhr);
                let errorMsg = 'Error loading viewers. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                contentElement.innerHTML = '<div class="text-center py-8"><p class="text-red-500">' + errorMsg + '</p></div>';
            }
        });
    };

    // Close reaction modal
    window.closeReactionModal = function() {
        const modalElement = document.getElementById('reactionDetailsModal');
        modalElement.classList.add('hidden');
        modalElement.setAttribute('aria-hidden', 'true');
        $('body').removeClass('overflow-hidden');
    };

    // Close viewers modal
    window.closeViewersModal = function() {
        const modalElement = document.getElementById('viewersModal');
        modalElement.classList.add('hidden');
        modalElement.setAttribute('aria-hidden', 'true');
        $('body').removeClass('overflow-hidden');
    };
    
    // Format file size helper function
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

    // View announcement attachment
    window.viewAnnouncementAttachment = function(filePath, fileName, mimeType, fileSize) {
        const modalElement = document.getElementById('attachmentModal');
        const contentElement = document.getElementById('attachmentContent');
        const downloadLink = document.getElementById('downloadAttachment');
        const modalTitle = document.getElementById('attachmentModalLabel');
        
        // Convert fileSize to number if it's a string
        const fileSizeNum = typeof fileSize === 'string' ? parseInt(fileSize) : fileSize;
        
        // Set modal title
        modalTitle.innerHTML = '<i class="cft-standard-stroke cft-paper-clip text-red-500"></i> View Attachment: ' + fileName;
        
        // Set download link
        const storageUrl = '{{ url("storage") }}/' + filePath;
        downloadLink.href = storageUrl;
        downloadLink.download = fileName;
        
        // Show modal first with loading state
        modalElement.classList.remove('hidden');
        modalElement.setAttribute('aria-hidden', 'false');
        $('body').addClass('overflow-hidden');
        
        // Clear previous content and show loading
        contentElement.innerHTML = '<div class="flex items-center justify-center py-8"><div class="text-center"><div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-red-500 border-r-transparent"></div><p class="mt-2 text-sm text-gray-500">Loading attachment...</p></div></div>';
        
        // Load attachment content based on file type
        setTimeout(function() {
            if (mimeType && mimeType.includes('image')) {
                // For images, show directly
                contentElement.innerHTML = `
                    <div class="text-center">
                        <img src="${storageUrl}" alt="${fileName}" class="mx-auto rounded-lg shadow-lg" style="max-height: 60vh; max-width: 100%;">
                        <p class="mt-4 font-semibold text-gray-900">${fileName}</p>
                        <p class="text-sm text-gray-500">File size: ${formatFileSize(fileSizeNum)}</p>
                    </div>
                `;
            } else if (mimeType && mimeType.includes('pdf')) {
                // For PDFs, show in iframe
                contentElement.innerHTML = `
                    <div class="text-center">
                        <iframe src="${storageUrl}" width="100%" height="600" frameborder="0" class="rounded-lg"></iframe>
                        <p class="mt-4 font-semibold text-gray-900">${fileName}</p>
                        <p class="text-sm text-gray-500">File size: ${formatFileSize(fileSizeNum)}</p>
                    </div>
                `;
            } else {
                // For other file types, show file info
                contentElement.innerHTML = `
                    <div class="text-center py-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                            <i class="cft-standard-stroke cft-file text-red-500 text-2xl"></i>
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

    // Close attachment modal
    window.closeAttachmentModal = function() {
        const modalElement = document.getElementById('attachmentModal');
        modalElement.classList.add('hidden');
        modalElement.setAttribute('aria-hidden', 'true');
        $('body').removeClass('overflow-hidden');
    };
    
    // Close modals with Escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!$('#reactionDetailsModal').hasClass('hidden')) {
                closeReactionModal();
            }
            if (!$('#viewersModal').hasClass('hidden')) {
                closeViewersModal();
            }
            if (!$('#attachmentModal').hasClass('hidden')) {
                closeAttachmentModal();
            }
        }
    });

    // Toggle announcement content
    window.toggleAnnouncementContent = function(announcementId) {
        const $content = $('#content-' + announcementId);
        const $readMoreBtn = $('#read-more-btn-' + announcementId);
        const $showLessBtn = $('#show-less-btn-' + announcementId);
        
        if ($content.is(':visible')) {
            $content.slideUp();
            $readMoreBtn.show();
            $showLessBtn.hide();
        } else {
            $content.slideDown();
            $readMoreBtn.hide();
            $showLessBtn.show();
        }
    };
});
</script>
@endpush
