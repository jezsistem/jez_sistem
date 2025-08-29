@extends('app.structure')
@section('content')


<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-12 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Page Heading-->
                <div class="d-flex align-items-baseline justify-content-between">
                    <!--begin::Page Title-->
                    <h5 class="text-dark font-weight-bold my-1 mr-5">{{ $data['subtitle'] }}</h5>
                    <!--end::Page Title-->
                    
                </div>
                <!--end::Page Heading-->
            </div>
            <!--end::Info-->
            <div style="width: 45%;">
                <div style="position: relative;">
                    <input type="text" 
                    class="form-control" 
                    id="searchInput"
                    placeholder="Search announcements by title, content, or sender name..." 
                    value="{{ request('search') }}"
                    style="border-radius: 0.475rem; padding-right: 2rem;">
                    
                    <!-- Tombol clear -->
                    <button type="button" 
                    id="clearSearchBtn" 
                    style="position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%); border: none; background: transparent; cursor: pointer;">
                    <i class="ki-outline ki-cross"></i>
                    </button>
                </div>
            </div>
            <!--begin::Toolbar-->
            <div class="d-flex align-items-center mr-3">
                <a href="{{ route('announcements.create') }}" class="btn btn-primary font-weight-bolder btn-mobile-sm">
                    <i class="ki-outline ki-plus"></i>
                    New Announcement
                </a>
                <a href="{{ route('announcements.manage') }}" class="btn btn-light-primary font-weight-bolder ml-2 btn-mobile-sm">
                    <i class="ki-outline ki-setting-4"></i>
                    Manage
                </a>
            </div>
            <!--end::Toolbar-->
        </div>
    </div>
    <!--end::Subheader-->
    
    <!--begin::Container-->
    <div class="container pt-6">
        <div class="row">
            <!-- Mobile Dropdown Categories -->
            <div class="d-lg-none col-12 mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="text-dark font-weight-bold">Categories</h4>
                    <select class="form-control" id="mobileCategorySelect" style="width: 40%;">
                        <option value="all">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" data-color="{{ $category->color }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!--begin::Main Content-->
            <div class="col-lg-9 col-md-12 col-12">
                <!--begin::Pinned Announcements-->
                @if($pinnedAnnouncements->count() > 0)
                <div class="mb-6 pinned-announcements-section">
                    <h4 class="text-dark font-weight-bold mb-6">
                        Pinned Announcements
                    </h4>
                    @foreach($pinnedAnnouncements as $announcement)
                        @include('app.announcement._announcement_card', ['announcement' => $announcement])
                    @endforeach
                </div>
                @endif
                <!--end::Pinned Announcements-->
                
                <!--begin::Regular Announcements-->
                <div class="regular-announcements-section mt-12">
                    <h4 class="text-dark font-weight-bold mb-6">Recent Announcements</h4>
                    @if($regularAnnouncements->count() > 0)
                        @foreach($regularAnnouncements as $announcement)
                            @include('app.announcement._announcement_card', ['announcement' => $announcement])
                        @endforeach
                    @else
                        <div class="card card-custom">
                            <div class="card-body text-center py-8 bg-light">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p class="font-size-h6">No announcements found</p>
                                    <p class="font-size-h5">There are no announcements to display at the moment.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <!--end::Regular Announcements-->
            </div>
            <!--end::Main Content-->
            
            <!--begin::Sidebar-->
            <div class="col-lg-3 col-md-12 col-12">
                <div class="d-none d-lg-block">
                    <div class="card card-custom">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">Categories</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush fs-6">
                                <a href="#" class="list-group-item list-group-item-action rounded mb-2 active" data-category="all">
                                    All Categories
                                </a>
                                @foreach($categories as $category)
                                    <a href="#" class="list-group-item list-group-item-action rounded mb-2" data-category="{{ $category->id }}" style="border-left: 4px solid {{ $category->color }};">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Sidebar-->
        </div>
    </div>
    <!--end::Container-->
</div>
<!--end::Content-->

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" role="dialog" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailsModalLabel">Announcement Viewers</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="viewDetailsContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Reaction Details Modal -->
<div class="modal fade" id="reactionDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reactionDetailsModalTitle">Reaction Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="reactionDetailsContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light font-weight-bold" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Image Viewer Modal -->
<div class="modal fade" id="imageViewerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageViewerTitle">Image Viewer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="imageViewerImage" src="" alt="" class="img-fluid" style="max-height: 70vh;">
            </div>
            <div class="modal-footer">
                <a id="imageDownloadLink" href="" class="btn btn-light" download>
                    <i class="fas fa-download"></i> Download
                </a>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
 /* Compact Card Styling */
.compact-card {
    transition: all 0.3s ease;
    border: 1px solid #e1e5e9;
}

.cursor-pointer {
    cursor: pointer;
}

.content-preview {
    line-height: 1.4;
    color: #6c757d;
}

.expandable-content {
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.chevron-rotate {
    transform: rotate(180deg);
    transition: transform 0.3s ease;
}

/* Compact layout improvements */
.announcement-card {
    margin-bottom: 1rem !important;
}

.symbol-35 {
    width: 35px;
    height: 35px;
}

.fs-7 {
    font-size: 0.875rem;
}

.fs-7 {
    font-size: 0.75rem;
}

/* Hover effects */
.announcement-card:hover .card-body {
    /* background-color: #f8f9fa; */
}

/* Read more button styling */
.btn-light-primary {
    background-color: #e3f2fd;
    border-color: #2196f3;
    color: #1976d2;
}

.btn-light-primary:hover {
    background-color: #bbdefb;
    border-color: #1976d2;
    color: #1565c0;
}

/* Compact stats visibility control */
.announcement-card .compact-stats {
    display: flex !important;
}

.announcement-card.expanded-content .compact-stats {
    display: none !important;
}

/* Ensure icons stay in one line */
.compact-stats {
    flex-wrap: nowrap !important;
    overflow: hidden;
}

.compact-stats > div {
    white-space: nowrap !important;
    flex-shrink: 0;
}

/* Pinned announcement styling */
.pinned-announcements-section .announcement-card {
    background-color: #e7eff6 !important;
    border-color: #e2f3fe !important;
}

.pinned-announcements-section .announcement-card:hover {
    background-color:rgb(225, 237, 248) !important;
    border-color: #e2f3fe !important;
}

.pinned-announcements-section .announcement-card .btn {
    background-color: #ffffff;
}

/* Basic mobile responsiveness */
@media (max-width: 767.98px) {
    .container {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .announcement-card .card-body {
        padding: 1rem !important;
    }
    
    .compact-title {
        max-width: 100% !important;
    }
    
    .btn-icon {
        min-width: 44px;
        min-height: 44px;
    }
}

.list-group-item {
    border: none !important;
    background-color: #f8f9fa !important;
    color: #6c757d !important;
    transition: all 0.2s ease;
    margin-bottom: 8px !important;
}

.list-group-item:hover {
    background-color: #e9ecef !important;
    color: #495057 !important;
    transform: translateX(2px);
}

.list-group-item.active {
    background-color: #e9ecef !important;
    color: #495057 !important;
    border-left: 4px solid #007bff !important;
    font-weight: 600;
}

.list-group-item[data-category]:not(.active) {
    background-color: #f8f9fa !important;
    color: #6c757d !important;
}

.list-group-item[data-category]:not(.active):hover {
    background-color: #e9ecef !important;
    color: #495057 !important;
}

/* Announcement card styling */
.announcement-card {
    border: 1px solid #e9ecef !important;
    /* box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important; */
    transition: all 0.2s ease;
    max-width: 100% !important;
}

.announcement-card:hover {
    box-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;
    transform: translateY(-2px);
}

/* Reaction buttons */
.reaction-btn {
    border-radius: 20px !important;
    padding: 6px 12px !important;
    font-size: 0.875rem !important;
    transition: all 0.2s ease;
}

.reaction-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.reaction-btn.active {
    background-color: #007bff !important;
    color: white !important;
}

/* Pinned announcements section */
.pinned-announcements-section {
    /* background: #f8f9fa !important;
    border: 1px solid #e9ecef !important;
    border-radius: 8px !important; */
}

/* Attachments styling */
.bg-light.rounded {
    border: none !important;
    transition: all 0.2s ease;
}

.bg-light.rounded:hover {
    background-color: #e9ecef !important;
    transform: translateY(-1px);
}

/* Category badge styling */
.badge {
    border-radius: 20px !important;
    font-weight: 500 !important;
}

/* Button styling */
.btn-light {
    background-color: #f8f9fa !important;
    border: 1px solid #e9ecef !important;
    color: #6c757d !important;
    transition: all 0.2s ease;
}

.btn-light:hover {
    background-color: #e9ecef !important;
    border-color: #dee2e6 !important;
    color: #495057 !important;
}

/* No announcements section */
.card-body.bg-light {
    border: none !important;
    border-radius: 8px !important;
}

/* Modal styling */
.modal-content {
    border: none !important;
    border-radius: 8px !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

.modal-header {
    border-bottom: 1px solid #e9ecef !important;
    background-color: #f8f9fa !important;
}

.modal-footer {
    border-top: 1px solid #e9ecef !important;
    background-color: #f8f9fa !important;
}

/* Pin icon styling */
.fas.fa-thumbtack.text-primary {
    color: #007bff !important;
}

.fas.fa-thumbtack.text-secondary {
    color: #6c757d !important;
}

/* Ensure icon colors are visible */
.btn-icon .fas,
.btn-icon .ki-solid,
.btn-icon .ki-outline {
    font-size: 1rem !important;
}

/* Override button styling for icon colors */
.btn-light-primary .fas.fa-thumbtack {
    color: #007bff !important;
}

.btn-light-secondary .fas.fa-thumbtack {
    color: #6c757d !important;
}

/* Force icon colors in buttons */
.btn-icon .fas,
.btn-icon .ki-solid,
.btn-icon .ki-outline {
    color: inherit !important;
}

/* Override Keenthemes button styling for pin icon */
.btn.btn-light-primary .fas.fa-thumbtack {
    color: #007bff !important;
}

.btn.btn-light-secondary .fas.fa-thumbtack {
    color: #6c757d !important;
}

/* Even more specific overrides */
button.btn.btn-light-primary .fas.fa-thumbtack {
    color: #007bff !important;
}

button.btn.btn-light-secondary .fas.fa-thumbtack {
    color: #6c757d !important;
}

/* Use !important to ensure our styles take precedence */
.btn-icon .fas.fa-thumbtack.text-primary {
    color: #007bff !important;
}

.btn-icon .fas.fa-thumbtack.text-secondary {
    color: #6c757d !important;
}

/* Highest specificity override for pin icon */
button.btn.btn-sm.btn-icon.btn-light-primary .fas.fa-thumbtack {
    color: #007bff !important;
}

button.btn.btn-sm.btn-icon.btn-light-secondary .fas.fa-thumbtack {
    color: #6c757d !important;
}

/* Override any existing button icon styling */
.btn-icon .fas.fa-thumbtack[style*="color"] {
    color: inherit !important;
}

/* Compact layout styling */
.compact-title {
    font-weight: 600 !important;
    color: #495057 !important;
    transition: all 0.3s ease;
}
.compact-title-mobile {
    display: none;
}
.fw-bold {
    font-weight: 600 !important;
}
@media (max-width: 768px) {
    .compact-title {
        display: none;
    }
    .compact-title-mobile {
        display: block;
    }
}
.compact-content {
    transition: all 0.3s ease;
}

.expanded-title {
    transition: all 0.3s ease;
}

/* Smooth transitions for layout changes */
.announcement-card {
    transition: all 0.3s ease;
}

/* Ensure proper spacing in compact mode */
.compact-card .compact-content {
    margin-bottom: 1rem;
}

/* Responsive adjustments for compact title */
@media (max-width: 768px) {
    .compact-title {
        max-width: 200px !important;
    }
}

@media (max-width: 576px) {
    .compact-title {
        max-width: 150px !important;
    }
}

/* Viewed announcement styling */
.announcement-card.viewed {
    /* Optional: subtle visual indicator that announcement has been viewed */
    opacity: 0.95;
}

.announcement-card.viewed .compact-title {
    /* Optional: different styling for viewed announcements */
    color: #6c757d !important;
}
.reactions-container-mobile {
    display: none !important;
}
@media (max-width: 767px) {
  .btn-mobile-sm {
    padding: 0.375rem 0.75rem; /* sekitar ukuran btn-sm */
    font-size: 0.9rem;
  }
  .reactions-container-mobile {
    display: block;
  }
  .reactions-container {
    display: none !important;
  }
}

</style>

<script>
// Vanilla JS version with multiple fallbacks
(function() {
    function initializeAnnouncementPage() {
        console.log('Initializing announcement index page...');
        
        // Category filter functionality
        var categoryItems = document.querySelectorAll('.list-group-item[data-category]');
        categoryItems.forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all items
                categoryItems.forEach(function(otherItem) {
                    otherItem.classList.remove('active');
                });
                
                // Add active class to clicked item
                this.classList.add('active');
                
                var categoryId = this.getAttribute('data-category');
                console.log('Filter by category:', categoryId);
                
                // Implement category filtering
                filterAnnouncementsByCategory(categoryId);
            });
        });
        
        // Add event listeners for modal close buttons
        var closeButtons = document.querySelectorAll('[data-dismiss="modal"]');
        closeButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var modal = this.closest('.modal');
                if (modal) {
                    closeModal(modal.id);
                }
            });
        });
        
        // Add click outside to close modal
        var modals = document.querySelectorAll('.modal');
        modals.forEach(function(modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(this.id);
                }
            });
        });
        
        // Add view modal close functionality
        const viewModal = document.getElementById('viewDetailsModal');
        if (viewModal) {
            // Close button
            const closeBtn = viewModal.querySelector('.close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    closeViewModal();
                });
            }
            
            // Close button in footer
            const footerCloseBtn = viewModal.querySelector('.btn-secondary');
            if (footerCloseBtn) {
                footerCloseBtn.addEventListener('click', function() {
                    closeViewModal();
                });
            }
            
            // Click outside modal to close
            viewModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeViewModal();
                }
            });
        }
    }
    
    // Function to close view modal
    function closeViewModal() {
        const modal = document.getElementById('viewDetailsModal');
        const backdrop = document.getElementById('manual-backdrop-viewers');
        
        // Hide modal
        modal.style.display = 'none';
        modal.classList.remove('show');
        modal.removeAttribute('aria-modal');
        modal.removeAttribute('role');
        
        // Remove backdrop
        if (backdrop) {
            backdrop.remove();
            document.body.classList.remove('modal-open');
        }
    }
    
    // Global function definitions
    window.reactToAnnouncement = function(announcementId, reactionId) {
        console.log('React to announcement:', announcementId, reactionId);
        
        var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch('/announcements/react', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                announcement_id: announcementId,
                reaction_id: reactionId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update reaction button state
                var allButtons = document.querySelectorAll(`.reaction-btn[data-announcement="${announcementId}"]`);
                allButtons.forEach(btn => btn.classList.remove('active'));
                
                var activeButton = document.querySelector(`.reaction-btn[data-announcement="${announcementId}"][data-reaction="${reactionId}"]`);
                if (activeButton) activeButton.classList.add('active');
                
                // Update reaction counts in real-time
                if (data.reaction_counts) {
                    Object.keys(data.reaction_counts).forEach(function(reactionIdKey) {
                        var count = data.reaction_counts[reactionIdKey];
                        var countElement = document.querySelector(`.reaction-btn[data-announcement="${announcementId}"][data-reaction="${reactionIdKey}"] .reaction-count`);
                        if (countElement) {
                            countElement.textContent = count;
                        }
                    });
                }
                
                // Update total reactions count
                if (data.total_reactions !== undefined) {
                    var totalElement = document.querySelector(`.announcement-${announcementId} .total-reactions`);
                    if (totalElement) {
                        totalElement.textContent = data.total_reactions + ' reactions';
                    }
                }
                
                // Show success message
                var successMsg = document.createElement('div');
                successMsg.innerHTML = '✓ Reaction saved!';
                successMsg.style.cssText = 'position:fixed;top:20px;right:20px;background:#28a745;color:white;padding:10px;border-radius:5px;z-index:9999;';
                document.body.appendChild(successMsg);
                setTimeout(() => document.body.removeChild(successMsg), 2000);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving reaction');
        });
    };
    
    window.togglePin = function(announcementId) {
        console.log('Toggle pin for announcement:', announcementId);
        
        var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/announcements/${announcementId}/pin`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating pin status');
        });
    };
    
    window.showReactionDetails = function(announcementId) {
        
        fetch(`/announcements/${announcementId}/reactions`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('reactionDetailsContent').innerHTML = data.html;
                
                var modal = document.getElementById('reactionDetailsModal');
                if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
                    $(modal).modal('show');
                } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    var modalInstance = new bootstrap.Modal(modal);
                    modalInstance.show();
                } else {
                    console.warn('No modal framework available, using basic display');
                    modal.style.display = 'block';
                    modal.classList.add('show');
                    modal.setAttribute('aria-modal', 'true');
                    modal.setAttribute('role', 'dialog');
                    
                    if (!document.getElementById('manual-backdrop')) {
                        var backdrop = document.createElement('div');
                        backdrop.className = 'modal-backdrop fade show';
                        backdrop.id = 'manual-backdrop';
                        document.body.appendChild(backdrop);
                        document.body.classList.add('modal-open');
                    }
                }
            } else {
                alert('Error loading reaction details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading reaction details');
        });
    };
    
    // Global function to close modals
    window.closeModal = function(modalId) {
        var modal = document.getElementById(modalId);
        if (!modal) return;
        
        if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
            $(modal).modal('hide');
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
            }
        } else {
            // Manual close
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
            var backdrop = document.getElementById('manual-backdrop');
            if (backdrop) backdrop.remove();
        }
    };
    
    // Global function to filter announcements by category
    window.filterAnnouncementsByCategory = function(categoryId) {
        console.log('Filtering by category:', categoryId);
        
        // Pinned Announcements - ALWAYS SHOW (no filtering)
        var pinnedCards = document.querySelectorAll('.pinned-announcements-section .announcement-card, .pinned-announcements-section [class*="announcement-"]');
        pinnedCards.forEach(function(card) {
            card.style.display = 'block';
            card.classList.remove('d-none');
        });
        
        // Recent Announcements - APPLY FILTER
        var recentCards = document.querySelectorAll('.regular-announcements-section .announcement-card, .regular-announcements-section [class*="announcement-"]');
        
        recentCards.forEach(function(card) {
            var cardCategoryId = card.getAttribute('data-category-id');
            
            // Handle empty or null category IDs
            if (!cardCategoryId || cardCategoryId === 'null' || cardCategoryId === '') {
                cardCategoryId = '';
            }
            
            if (categoryId === 'all' || !categoryId || categoryId === cardCategoryId) {
                card.style.display = 'block';
                card.classList.remove('d-none');
            } else {
                card.style.display = 'none';
                card.classList.add('d-none');
            }
        });
        
        // Update section visibility
        var pinnedSection = document.querySelector('.pinned-announcements-section');
        var regularSection = document.querySelector('.regular-announcements-section');
        
        // Pinned section - always show if has content
        if (pinnedSection) {
            var pinnedCards = pinnedSection.querySelectorAll('.card');
            if (pinnedCards.length > 0) {
                pinnedSection.style.display = 'block';
            } else {
                pinnedSection.style.display = 'none';
            }
        }
        
        // Regular section - show/hide based on visible cards
        if (regularSection) {
            var visibleRegular = regularSection.querySelectorAll('.card:not(.d-none)').length;
            if (visibleRegular === 0) {
                regularSection.style.display = 'none';
            } else {
                regularSection.style.display = 'block';
            }
        }
    };
    
    window.viewImage = function(imageUrl, imageName) {
        console.log('View image:', imageUrl, imageName);
        
        document.getElementById('imageViewerTitle').textContent = imageName;
        document.getElementById('imageViewerImage').src = imageUrl;
        document.getElementById('imageViewerImage').alt = imageName;
        document.getElementById('imageDownloadLink').href = imageUrl;
        
        var modal = document.getElementById('imageViewerModal');
        if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
            $(modal).modal('show');
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
        } else {
            console.warn('No modal framework available, using basic display');
            modal.style.display = 'block';
            modal.classList.add('show');
            modal.setAttribute('aria-modal', 'true');
            modal.setAttribute('role', 'dialog');
            
            if (!document.getElementById('manual-backdrop')) {
                var backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.id = 'manual-backdrop';
                document.body.appendChild(backdrop);
                document.body.classList.add('modal-open');
            }
        }
    };
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeAnnouncementPage);
    } else {
        initializeAnnouncementPage();
    }
    
    window.addEventListener('load', initializeAnnouncementPage);
    
    // Initialize mobile category select functionality
    function initializeMobileCategorySelect() {
        console.log('Initializing mobile category select...');
        
        // Wait for jQuery to be available
        if (typeof $ !== 'undefined') {
            // Mobile Category Select Handler
            $('#mobileCategorySelect').on('change', function() {
                const selectedCategory = $(this).val();
                console.log('Mobile select changed to category:', selectedCategory);
                
                // Update active state in all category lists
                $('.list-group-item').removeClass('active');
                $(`.list-group-item[data-category="${selectedCategory}"]`).addClass('active');
                
                // Call the existing filter function
                if (typeof window.filterAnnouncementsByCategory === 'function') {
                    window.filterAnnouncementsByCategory(selectedCategory);
                } else {
                    console.warn('filterAnnouncementsByCategory function not found');
                }
                
                // Visual feedback
                $(this).addClass('is-valid');
                setTimeout(() => {
                    $(this).removeClass('is-valid');
                }, 1000);
            });

            // Sync mobile select with list item clicks
            $('.list-group-item').on('click', function() {
                const categoryId = $(this).data('category');
                console.log('List item clicked, category:', categoryId);
                
                // Update mobile select
                $('#mobileCategorySelect').val(categoryId);
                
                // Update active states
                $('.list-group-item').removeClass('active');
                $(this).addClass('active');
            });

            // Initialize mobile select with current active category
            const activeCategory = $('.list-group-item.active').data('category');
            if (activeCategory) {
                $('#mobileCategorySelect').val(activeCategory);
                console.log('Initialized mobile select with category:', activeCategory);
            }
            
            // Debug: Log all available categories
            console.log('Available categories in mobile select:', $('#mobileCategorySelect option').map(function() {
                return { value: $(this).val(), text: $(this).text() };
            }).get());
        } else {
            console.warn('jQuery not available, retrying in 100ms...');
            setTimeout(initializeMobileCategorySelect, 100);
        }
    }
    
    // Initialize mobile category select after page loads
    window.addEventListener('load', function() {
        setTimeout(initializeMobileCategorySelect, 500);
    });
    
    // Toggle announcement content with view tracking
    window.toggleAnnouncementContent = function(announcementId) {
        console.log('toggleAnnouncementContent called with ID:', announcementId);
        
        const contentDiv = document.getElementById(`content-${announcementId}`);
        const chevron = document.getElementById(`chevron-${announcementId}`);
        const readMoreBtn = document.getElementById(`read-more-btn-${announcementId}`);
        // Try multiple selectors to find the card
        let card = document.querySelector(`[data-announcement-id="${announcementId}"]`);
        if (!card) {
            card = document.querySelector(`.announcement-${announcementId}`);
        }
        if (!card) {
            card = document.querySelector(`#content-${announcementId}`).closest('.announcement-card');
        }
        
        console.log('Found elements:', { contentDiv, chevron, readMoreBtn, card });
        console.log('Card selector used:', `[data-announcement-id="${announcementId}"]`);
        
        // New elements for compact/expanded layout
        const compactTitle = document.getElementById(`compact-title-${announcementId}`);
        const compactContent = document.getElementById(`compact-content-${announcementId}`);
        const expandedTitle = document.getElementById(`expanded-title-${announcementId}`);
        
        // Check if we have all required elements
        if (!card) {
            console.error('Card element not found for announcement ID:', announcementId);
            return;
        }
        
        if (contentDiv.style.display === 'none') {
            console.log('Expanding content...');
            // Expand content
            contentDiv.style.display = 'block';
            if (chevron) chevron.classList.add('chevron-rotate');
            
            // Hide Read More button and show Show Less button
            readMoreBtn.style.display = 'none';
            const showLessBtn = document.getElementById(`show-less-btn-${announcementId}`);
            if (showLessBtn) showLessBtn.style.display = 'inline-block';
            
            // Hide compact elements, show expanded title
            if (compactTitle) compactTitle.style.display = 'none';
            if (compactContent) compactContent.style.display = 'none';
            if (expandedTitle) expandedTitle.style.display = 'block';
            
            // Track view when expanding
            trackAnnouncementView(announcementId);
            
            // Add expanded class for styling
            card.classList.add('expanded');
            card.classList.add('expanded-content');
            console.log('Added expanded-content class, card classes:', card.className);
            
            // Hide compact stats
            const compactStats = card.querySelector('.compact-stats');
            console.log('Found compact-stats:', compactStats);
            if (compactStats) {
                compactStats.style.display = 'none';
                console.log('Hidden compact-stats');
            }
            
            // Button text already changed to "Show Less" above, no need to move
            console.log('Button text changed to Show Less');
        } else {
            // Collapse content
            contentDiv.style.display = 'none';
            if (chevron) chevron.classList.remove('chevron-rotate');
            
            // Show Read More button and hide Show Less button
            readMoreBtn.style.display = 'inline-block';
            const showLessBtn = document.getElementById(`show-less-btn-${announcementId}`);
            if (showLessBtn) showLessBtn.style.display = 'none';
            
            // Show compact elements, hide expanded title
            if (compactTitle) compactTitle.style.display = 'inline';
            if (compactContent) compactContent.style.display = 'block';
            if (expandedTitle) expandedTitle.style.display = 'none';
            
            // Remove expanded class
            card.classList.remove('expanded');
            card.classList.remove('expanded-content');
            console.log('Removed expanded-content class, card classes:', card.className);
            
            // Show compact stats
            const compactStats = card.querySelector('.compact-stats');
            console.log('Found compact-stats:', compactStats);
            if (compactStats) {
                compactStats.style.display = 'block';
                console.log('Showed compact-stats');
            }
            
            // Button text already changed to "Read More" above, no need to move
            console.log('Button text changed to Read More');
        }
    };
    
    // Track announcement view
    window.trackAnnouncementView = function(announcementId) {
        // Check if this announcement has already been viewed by current user
        const card = document.querySelector(`[data-announcement-id="${announcementId}"]`);
        if (card.classList.contains('viewed')) {
            console.log('Announcement already viewed, skipping view tracking');
            return;
        }
        
        fetch(`/announcements/${announcementId}/view`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mark as viewed to prevent duplicate tracking
                card.classList.add('viewed');
                
                // Only update view count if this is a new view
                if (data.message && data.message.includes('first time')) {
                    const viewCountElement = document.querySelector(`[data-announcement-id="${announcementId}"] .fa-eye + .text-muted`);
                    if (viewCountElement) {
                        const currentCount = parseInt(viewCountElement.textContent) || 0;
                        viewCountElement.textContent = currentCount + 1;
                    }
                    console.log('View count updated for first time view');
                } else {
                    console.log('View already tracked previously, count not updated');
                }
            }
        })
        .catch(error => {
            console.error('Error tracking view:', error);
        });
    };
    
    // Show combined stats modal (reactions + viewers)
    window.showCombinedStats = function(announcementId) {
        // Show loading state
        document.getElementById('viewDetailsContent').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
        
        // Show modal - try multiple approaches
        const modal = document.getElementById('viewDetailsModal');
        
        // Try jQuery first
        if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
            $('#viewDetailsModal').modal('show');
        }
        // Try Bootstrap 5
        else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
        }
        // Fallback to vanilla JavaScript
        else {
            modal.style.display = 'block';
            modal.classList.add('show');
            modal.setAttribute('aria-modal', 'true');
            modal.setAttribute('role', 'dialog');
            
            // Add backdrop
            if (!document.getElementById('manual-backdrop-viewers')) {
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.id = 'manual-backdrop-viewers';
                document.body.appendChild(backdrop);
                document.body.classList.add('modal-open');
            }
        }
        
        // Fetch both reactions and viewers data
        Promise.all([
            fetch(`/announcements/${announcementId}/reactions`),
            fetch(`/announcements/${announcementId}/viewers`)
        ])
        .then(responses => Promise.all(responses.map(r => r.json())))
        .then(([reactionsData, viewersData]) => {
            if (reactionsData.success && viewersData.success) {
                displayCombinedStats(reactionsData.reactions, viewersData.viewers, reactionsData.announcement);
            } else {
                document.getElementById('viewDetailsContent').innerHTML = '<div class="alert alert-danger">Error loading data</div>';
            }
        })
        .catch(error => {
            console.error('Error fetching combined stats:', error);
            document.getElementById('viewDetailsContent').innerHTML = '<div class="alert alert-danger">Error loading data</div>';
        });
    };
    
    // Show view details modal
    window.showViewDetails = function(announcementId) {
        // Show loading state
        document.getElementById('viewDetailsContent').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
        
        // Show modal - try multiple approaches
        const modal = document.getElementById('viewDetailsModal');
        
        // Try jQuery first
        if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
            $('#viewDetailsModal').modal('show');
        }
        // Try Bootstrap 5
        else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
        }
        // Fallback to vanilla JavaScript
        else {
            modal.style.display = 'block';
            modal.classList.add('show');
            modal.setAttribute('aria-modal', 'true');
            modal.setAttribute('role', 'dialog');
            
            // Add backdrop
            if (!document.getElementById('manual-backdrop-viewers')) {
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.id = 'manual-backdrop-viewers';
                document.body.appendChild(backdrop);
                document.body.classList.add('modal-open');
            }
        }
        
        // Fetch view details
        fetch(`/announcements/${announcementId}/viewers`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayViewDetails(data.viewers, data.announcement);
                } else {
                    document.getElementById('viewDetailsContent').innerHTML = '<div class="alert alert-danger">Error loading view details</div>';
                }
            })
            .catch(error => {
                console.error('Error fetching view details:', error);
                document.getElementById('viewDetailsContent').innerHTML = '<div class="alert alert-danger">Error loading view details</div>';
            });
    };
    
    // Display view details in modal
    function displayViewDetails(viewers, announcement) {
        let html = `
            <div class="mb-3">
                <h6 class="text-dark font-weight-bold">${announcement.title}</h6>
                <small class="text-muted">Published: ${announcement.published_at}</small>
            </div>
        `;
        
        if (viewers.length === 0) {
            html += '<div class="alert alert-info">No viewers yet</div>';
        } else {
            html += `
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Position</th>
                                <th>Division</th>
                                <th>Viewed At</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            viewers.forEach((viewer, index) => {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-30 symbol-light-primary mr-3">
                                    <span class="symbol-label">
                                        <i class="fas fa-user text-primary"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="font-weight-bold">${viewer.user_name}</div>
                                    <small class="text-muted">${viewer.user_nip || 'N/A'}</small>
                                </div>
                            </div>
                        </td>
                        <td>${viewer.position_name || 'N/A'}</td>
                        <td>${viewer.division_name || 'N/A'}</td>
                        <td>
                            <div class="font-weight-bold">${viewer.viewed_date}</div>
                            <small class="text-muted">${viewer.viewed_time}</small>
                        </td>
                        <td>
                            <small class="text-muted">${viewer.ip_address}</small>
                        </td>
                    </tr>
                `;
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
            `;
        }
        
        document.getElementById('viewDetailsContent').innerHTML = html;
    }
    
    // Display combined stats in modal (reactions + viewers)
    function displayCombinedStats(reactions, viewers, announcement) {
        let html = `
            <div class="mb-3">
                <h6 class="text-dark font-weight-bold">${announcement.title}</h6>
                <small class="text-muted">Published: ${announcement.published_at}</small>
            </div>
        `;
        
        // Reactions Section
        html += `
            <div class="mb-4">
                <h6 class="text-dark font-weight-bold mb-3">
                    <i class="fas fa-heart text-danger mr-2"></i>Reactions (${reactions.length})
                </h6>
        `;
        
        if (reactions.length === 0) {
            html += '<div class="alert alert-info">No reactions yet</div>';
        } else {
            html += `
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Reaction</th>
                                <th>User</th>
                                <th>Position</th>
                                <th>Division</th>
                                <th>Reacted At</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            reactions.forEach((reaction, index) => {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <span class="badge badge-pill" style="background-color: #6c757d20; color: #6c757d;">
                                ${reaction.emoji} ${reaction.name}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-30 symbol-light-primary mr-3">
                                    <span class="symbol-label">
                                        <i class="fas fa-user text-primary"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="font-weight-bold">${reaction.user_name}</div>
                                    <small class="text-muted">${reaction.user_nip || 'N/A'}</small>
                                </div>
                            </div>
                        </td>
                        <td>${reaction.position_name || 'N/A'}</td>
                        <td>${reaction.division_name || 'N/A'}</td>
                        <td>
                            <div class="font-weight-bold">${reaction.reacted_date}</div>
                            <small class="text-muted">${reaction.reacted_time}</small>
                        </td>
                    </tr>
                `;
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
            `;
        }
        
        html += '</div>';
        
        // Viewers Section
        html += `
            <div class="mb-4">
                <h6 class="text-dark font-weight-bold mb-3">
                    <i class="fas fa-eye text-info mr-2"></i>Viewers (${viewers.length})
                </h6>
        `;
        
        if (viewers.length === 0) {
            html += '<div class="alert alert-info">No viewers yet</div>';
        } else {
            html += `
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Position</th>
                                <th>Division</th>
                                <th>Viewed At</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            viewers.forEach((viewer, index) => {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-30 symbol-light-primary mr-3">
                                    <span class="symbol-label">
                                        <i class="fas fa-user text-primary"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="font-weight-bold">${viewer.user_name}</div>
                                    <small class="text-muted">${viewer.user_nip || 'N/A'}</small>
                                </div>
                            </div>
                        </td>
                        <td>${viewer.position_name || 'N/A'}</td>
                        <td>${viewer.division_name || 'N/A'}</td>
                        <td>
                            <div class="font-weight-bold">${viewer.viewed_date}</div>
                            <small class="text-muted">${viewer.viewed_time}</small>
                        </td>
                        <td>
                            <small class="text-muted">${viewer.ip_address}</small>
                        </td>
                    </tr>
                `;
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
            `;
        }
        
        html += '</div>';
        
        document.getElementById('viewDetailsContent').innerHTML = html;
    }
})();
</script>

<!-- Attachment Preview JavaScript -->
<script>
    // Modal functions for attachment preview
    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
            document.body.classList.add('modal-open');
        }
    }

    function hideModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
        }
    }

    // Function to view announcement attachment
    function viewAnnouncementAttachment(filePath, fileName, fileType, fileSize) {
        const modal = document.getElementById('attachmentModal');
        const content = document.getElementById('attachmentContent');
        const downloadLink = document.getElementById('downloadAttachment');
        const modalTitle = document.getElementById('attachmentModalLabel');
        
        // Set modal title
        modalTitle.textContent = `View Attachment: ${fileName}`;
        
        // Set download link
        downloadLink.href = `/storage/${filePath}`;
        downloadLink.download = fileName;
        
        // Clear previous content
        content.innerHTML = '';
        
        // Show loading
        content.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading attachment...</p></div>';
        
        // Show modal
        showModal('attachmentModal');
        
        // Load attachment content based on file type
        if (fileType && fileType.includes('image')) {
            // For images, show directly
            content.innerHTML = `
                <div class="text-center">
                    <img src="/storage/${filePath}" alt="${fileName}" class="img-fluid" style="max-height: 500px;">
                    <p class="mt-2"><strong>${fileName}</strong></p>
                    <small class="text-muted">File size: ${formatFileSize(fileSize)}</small>
                </div>
            `;
        } else if (fileType && fileType.includes('pdf')) {
            // For PDFs, show in iframe
            content.innerHTML = `
                <div class="text-center">
                    <iframe src="/storage/${filePath}" width="100%" height="500" frameborder="0"></iframe>
                    <p class="mt-2"><strong>${fileName}</strong></p>
                    <small class="text-muted">File size: ${formatFileSize(fileSize)}</small>
                </div>
            `;
        } else {
            // For other file types, show file info
            content.innerHTML = `
                <div class="text-center">
                    <div class="alert alert-info">
                        <i class="fas fa-file fa-3x mb-3"></i>
                        <h5>${fileName}</h5>
                        <p>This file type cannot be previewed directly.</p>
                        <p>Please download the file to view its contents.</p>
                        <small class="text-muted">File size: ${formatFileSize(fileSize)}</small>
                    </div>
                </div>
            `;
        }
    }

    // Function to format file size
    function formatFileSize(bytes) {
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

    // Handle modal backdrop click
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.modal').forEach(function(modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    hideModal(this.id);
                }
            });
        });

        // Handle escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal').forEach(function(modal) {
                    if (modal.classList.contains('show')) {
                        hideModal(modal.id);
                    }
                });
            }
        });
    });
</script>

<!-- Responsive Categories Styling and Functionality -->
<style>
    /* Mobile Category Select Styling */
    #mobileCategorySelect {
        border: 2px solid #e1e5e9;
        border-radius: 8px;
        font-size: 1rem;
        padding: 8px 12px;
        background: #fff;
        transition: all 0.3s ease;
        height: 40px;
    }

    #mobileCategorySelect:focus {
        border-color: #3699ff;
        box-shadow: 0 0 0 0.2rem rgba(54, 153, 255, 0.25);
        outline: none;
    }


    /* Modal styles for attachment preview */
    .modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
    }

    .modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background-color: #fefefe;
        padding: 0;
        border: 1px solid #888;
        width: 90%;
        max-width: 1200px;
        max-height: 800px;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        padding: 15px;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-body {
        padding: 15px;
    }

    .modal-footer {
        padding: 15px;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
    }

    .close:hover {
        color: #000;
    }

    .modal-open {
        overflow: hidden;
    }

    /* Responsive Adjustments */
    @media (max-width: 991px) {
        .d-lg-none {
            display: block !important;
        }
        
        .d-none.d-lg-block {
            display: none !important;
        }
    }

    @media (max-width: 767px) {
        .d-md-block {
            display: none !important;
        }
        
        .d-lg-none {
            display: block !important;
        }
    }

    @media (min-width: 992px) {
        .d-lg-none {
            display: none !important;
        }
        
        .d-none.d-lg-block {
            display: block !important;
        }
    }
</style>

<!-- Attachment Preview Modal -->
<div id="attachmentModal" class="modal">
    <div class="modal-content" style="max-width: 1200px; max-height: 800px;">
        <div class="modal-header">
            <h5 class="modal-title" id="attachmentModalLabel">View Attachment</h5>
            <button type="button" class="close" onclick="hideModal('attachmentModal')" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div id="attachmentContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="hideModal('attachmentModal')">Close</button>
            <a href="#" id="downloadAttachment" class="btn btn-primary" download>Download</a>
        </div>
    </div>
</div>

@endsection

@include('app.announcement.ajax-search')

@include('app._partials.js')
