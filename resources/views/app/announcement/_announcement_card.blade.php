<div class="card card-custom mb-3 announcement-{{ $announcement->id }} announcement-card compact-card" data-category-id="{{ $announcement->category_id ?? '' }}" data-announcement-id="{{ $announcement->id }}">
    <div class="card-body p-4">
        <!--begin::Compact Header-->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center flex-grow-1">
                <!-- Avatar -->
                <div class="symbol symbol-35 symbol-light-primary mr-3">
                    <span class="symbol symbol-lg-35 symbol-25 symbol-primary">
                        <span class="symbol-label font-size-h5 font-weight-bold">{{ substr($announcement->creator->u_name, 0, 1) }}</span>
                    </span>
                </div>
                
                <!-- Sender Info and Title (1 baris saat minimize) -->
                <div class="d-flex flex-column flex-grow-1">
                    <div class="d-flex align-items-center">
                        <span class="text-dark fw-bold fs-6 mr-3">{{ $announcement->creator->u_name ?? 'Unknown' }}</span>
                        <span class="text-dark fw-bold fs-6 mr-3" style="color: #6c757d !important;">•</span>
                        <span class="text-dark fw-bold fs-5 mr-3 compact-title" id="compact-title-{{ $announcement->id }}" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $announcement->title }}</span>
                        @if($announcement->category)
                            <span class="badge badge-pill font-weight-bold mr-2" 
                                  style="background-color: {{ $announcement->category->color }}20; color: {{ $announcement->category->color }}; border: 1px solid {{ $announcement->color }}40; font-size: 0.9rem; padding: 0.3rem 0.6rem;">
                                {{ $announcement->category->name }}
                            </span>
                        @endif
                    </div>
                    <span class="text-muted fs-6">{{ $announcement->creator->userPosition->up_name ?? 'Unknown Position' }} • {{ $announcement->published_at->format('M d, H:i') }}</span>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="d-flex align-items-center">
                <button class="btn btn-sm btn-icon btn-light-secondary mr-2" 
                        onclick="togglePin({{ $announcement->id }})" 
                        title="{{ $announcement->is_pinned ? 'Unpin' : 'Pin' }} announcement">
                    <i class="fas fa-thumbtack" style="color: {{ $announcement->is_pinned ? '#007bff' : '#6c757d' }} !important;"></i>
                </button>
            </div>
        </div>
        <!--end::Compact Header-->
        
        <!--begin::Content Preview (saat minimize)-->
        <div class="mb-3 compact-content" id="compact-content-{{ $announcement->id }}">
            <span class="text-dark fw-bold fs-5 mb-3 compact-title-mobile" id="compact-title-{{ $announcement->id }}" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $announcement->title }}</span>
            <div class="text-gray-600 fs-5 content-preview" id="preview-{{ $announcement->id }}">
                {{ Str::limit(strip_tags($announcement->content), 100) }}
                @if(strlen(strip_tags($announcement->content)) > 100)
                    <span class="text-primary fw-bold">... <a href="#" onclick="toggleAnnouncementContent({{ $announcement->id }})" class="text-primary">read more</a></span>
                @endif
            </div>
        </div>
        <!--end::Content Preview-->
        
        <!--begin::Expanded Title (hidden saat minimize)-->
        <div class="mt-4 expanded-title" id="expanded-title-{{ $announcement->id }}" style="display: none;">
            <h5 class="text-dark fw-bold cursor-pointer fs-4" onclick="toggleAnnouncementContent({{ $announcement->id }})">
                {{ $announcement->title }}
            </h5>
        </div>
        <!--end::Expanded Title-->
        
        <!--begin::Compact Footer-->
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center compact-stats" style="flex-wrap: nowrap;">
                <!-- Reaction Summary -->
                @if($announcement->userReactions->count() > 0)
                    <div class="d-flex align-items-center mr-3" style="white-space: nowrap;">
                        <i class="ki-solid ki-emoji-happy mr-1" style="font-size: 1.2rem;"></i>
                        <span class="text-muted fs-6">{{ $announcement->userReactions->count() }}</span>
                    </div>
                @endif
                
                <!-- View Count -->
                <div class="d-flex align-items-center mr-3" style="white-space: nowrap;">
                    <i class="ki-solid ki-eye mr-1" style="font-size: 1.2rem;"></i>
                    <span class="text-muted fs-6">{{ $announcement->views_count ?? 0 }}</span>
                </div>
                
                <!-- Attachment Count -->
                @if($announcement->attachments->count() > 0)
                    <div class="d-flex align-items-center mr-3" style="white-space: nowrap;">
                        <i class="ki-solid ki-paper-clip mr-1" style="font-size: 1.2rem;"></i>
                        <span class="text-muted fs-6">{{ $announcement->attachments->count() }}</span>
                    </div>
                @endif
            </div>
            
            <!-- Read More Button -->
            <button class="btn btn-sm btn-light-primary" 
                    onclick="toggleAnnouncementContent({{ $announcement->id }})" 
                    id="read-more-btn-{{ $announcement->id }}">
                Read More
            </button>
        </div>
        <!--end::Compact Footer-->
        
        <!--begin::Expandable Content (Hidden by default)-->
        <div class="expandable-content" id="content-{{ $announcement->id }}" style="display: none;">
            <hr class="my-4">
            
            <!--begin::Full Content-->
            <div class="mb-4">
                <div class="text-gray-800 mb-4 fs-5">{!! nl2br(e($announcement->content)) !!}</div>
                
                <!-- Attachments -->
                @if($announcement->attachments->count() > 0)
                <div class="mt-4">
                    <h6 class="text-dark font-weight-semibold mb-3 fs-6"><i class="ki-outline ki-paper-clip"></i> Attachments</h6>
                    <div class="row">
                        @foreach($announcement->attachments as $attachment)
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <div class="mr-3">
                                    @if($attachment->is_image)
                                        <i class="ki-outline ki-picture text-primary"></i>
                                    @else
                                        <i class="ki-outline ki-file text-secondary"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="font-weight-bold" style="max-width: 280px">{{ $attachment->original_name }}</div>
                                    <small class="text-muted">{{ $attachment->file_size_human }}</small>
                                </div>
                                <div class="ml-2">
                                    <button class="btn btn-sm btn-light-primary" onclick="viewAnnouncementAttachment('{{ $attachment->file_path }}', '{{ $attachment->original_name }}', '{{ $attachment->mime_type }}', '{{ $attachment->file_size }}')">
                                        <i class="ki-outline ki-eye"></i> View/Download
                                    </button>
                                    <!-- <a href="{{ $attachment->file_url }}" class="btn btn-sm btn-light ml-1" target="_blank" download>
                                        <i class="ki-outline ki-cloud-download"></i> Download
                                    </a> -->
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            <!--end::Full Content-->
            
            <!--begin::Reactions-->
            <div class="d-flex align-items-center justify-content-between reactions-container">
                <div class="d-flex align-items-center">
                    @foreach($reactions as $reaction)
                    <button class="btn btn-sm mr-2 reaction-btn" 
                            data-announcement="{{ $announcement->id }}" 
                            data-reaction="{{ $reaction->id }}"
                            onclick="reactToAnnouncement({{ $announcement->id }}, {{ $reaction->id }})"
                            style="background-color: #6c757d20; color: #6c757d; border: none;">
                        {{ $reaction->emoji }} {{ $reaction->name }}
                        <span class="reaction-count">{{ $announcement->userReactions->where('reaction_id', $reaction->id)->count() }}</span>
                    </button>
                    @endforeach
                </div>
                
                <div class="d-flex align-items-center">
                    <button class="btn btn-sm btn-light mr-2" onclick="showReactionDetails({{ $announcement->id }})">
                        <i class="ki-outline ki-eye"></i> View Reactions ({{ $announcement->userReactions->count() }})
                    </button>
                    <button class="btn btn-sm btn-light mr-2" onclick="showViewDetails({{ $announcement->id }})">
                        <i class="fas fa-users"></i> Viewers ({{ $announcement->views_count ?? 0 }})
                    </button>
                    <button class="btn btn-sm btn-light-primary" 
                            onclick="toggleAnnouncementContent({{ $announcement->id }})" 
                            id="show-less-btn-{{ $announcement->id }}" 
                            style="display: none;">
                        Show Less
                    </button>
                </div>
            </div>
            <!--end::Reactions-->
            <!--begin::Reactions-->
            <div class="d-flex align-items-center reactions-container-mobile mb-4">
                @foreach($reactions as $reaction)
                <button class="btn btn-sm mr-2 reaction-btn" 
                        data-announcement="{{ $announcement->id }}" 
                        data-reaction="{{ $reaction->id }}"
                        onclick="reactToAnnouncement({{ $announcement->id }}, {{ $reaction->id }})"
                        style="background-color: #6c757d20; color: #6c757d; border: none;">
                     {{ $reaction->name }}
                    <span class="reaction-count">{{ $announcement->userReactions->where('reaction_id', $reaction->id)->count() }}</span>
                </button>
                @endforeach
            </div>
                
            <div class="d-flex align-items-center reactions-container-mobile">
                <button class="btn btn-sm btn-light mr-2" onclick="showReactionDetails({{ $announcement->id }})">
                    <i class="ki-outline ki-eye"></i> View Reactions ({{ $announcement->userReactions->count() }})
                </button>
                <button class="btn btn-sm btn-light mr-2" onclick="showViewDetails({{ $announcement->id }})">
                    <i class="fas fa-users"></i> Viewers ({{ $announcement->views_count ?? 0 }})
                </button>
                <button class="btn btn-sm btn-light-primary" 
                        onclick="toggleAnnouncementContent({{ $announcement->id }})" 
                        id="show-less-btn-{{ $announcement->id }}" 
                        style="display: none;">
                    Show Less
                </button>
            </div>
            <!--end::Reactions-->
        </div>
        <!--end::Expandable Content-->
    </div>
</div>
