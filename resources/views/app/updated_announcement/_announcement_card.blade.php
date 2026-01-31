<div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow announcement-card" 
     data-category-id="{{ $announcement->category_id ?? '' }}" 
     data-announcement-id="{{ $announcement->id }}">
    
    <!-- Header -->
    <div class="flex items-start justify-between mb-4">
        <div class="flex items-start gap-4 flex-1">
            <!-- Avatar -->
            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-red-500 font-semibold text-lg">
                    {{ substr($announcement->creator->u_name ?? 'U', 0, 1) }}
                </span>
            </div>
            
            <!-- Info -->
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap mb-1">
                    <span class="text-sm font-semibold text-gray-900">{{ $announcement->creator->u_name ?? 'Unknown' }}</span>
                    <span class="text-xs font-medium px-1.5 py-0.5 rounded mr-2 bg-red-100 text-red-500">{{ $announcement->creator->userPosition->up_name ?? 'Unknown Position' }}</span>
                    <span class="text-gray-400">•</span>
                    <span class="text-sm text-gray-500">{{ $announcement->published_at->format('M d, Y H:i') }}</span>
                </div>
                                <!-- Badges -->
                <div class="flex items-center gap-2 flex-wrap">
                    @if($announcement->is_pinned)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="cft-standard-stroke cft-pin"></i>
                            Pinned
                        </span>
                    @endif
                    @if($announcement->category)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                              style="background-color: {{ $announcement->category->color }}15; color: {{ $announcement->category->color }};">
                            {{ $announcement->category->name }}
                        </span>
                    @endif
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                        {{ ucfirst($announcement->target_type) }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="flex items-center gap-2">
            @if(auth()->id() == $announcement->created_by)
                <div class="relative">
                    <button type="button" 
                            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                            onclick="togglePin({{ $announcement->id }})"
                            title="{{ $announcement->is_pinned ? 'Unpin' : 'Pin' }}">
                        <i class="fas fa-thumbtack {{ $announcement->is_pinned ? 'text-red-500' : '' }}"></i>
                    </button>
                    
                    <div class="relative">
                        <button type="button" 
                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                                onclick="toggleDropdown({{ $announcement->id }})">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        
                        <div id="dropdown-{{ $announcement->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                            <a href="{{ route('announcements_v2.edit', $announcement->id) }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Edit
                            </a>
                            <button onclick="deleteAnnouncement({{ $announcement->id }})" 
                                    class="block w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-gray-100">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Content Preview -->
    <div class="announcement-content mb-4">
        <!-- Title -->
        <a href="{{ route('announcements_v2.show', $announcement->id) }}" 
            class="announcement-title text-lg font-semibold text-gray-900 hover:text-red-500 transition-colors block mb-1">
            {{ $announcement->title }}
        </a>

        <p class="text-gray-600 line-clamp-3" style="font-size: 0.9rem;">
            {{ Str::limit(strip_tags($announcement->content), 200) }}
        </p>
    </div>
    
    <!-- Expandable Content (Hidden by default) -->
    <div id="content-{{ $announcement->id }}" class="hidden mb-4">
        <div class="prose max-w-none mb-4 text-gray-600" style="font-size: 0.9rem;">
            {!! nl2br(e($announcement->content)) !!}
        </div>
        
        <!-- Attachments -->
        @if($announcement->attachments->count() > 0)
        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
            <h6 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                <i class="cft-standard-stroke cft-paper-clip"></i>
                Attachments ({{ $announcement->attachments->count() }})
            </h6>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($announcement->attachments as $attachment)
                <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200">
                    <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                        @if($attachment->is_image)
                            <i class="cft-standard-stroke cft-image text-gray-900"></i>
                        @else
                            <i class="cft-standard-stroke cft-file text-gray-900"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment->original_name }}</p>
                        <p class="text-xs text-gray-500">{{ $attachment->file_size_human }}</p>
                    </div>
                    <button onclick="viewAnnouncementAttachment('{{ $attachment->file_path }}', '{{ $attachment->original_name }}', '{{ $attachment->mime_type }}', '{{ $attachment->file_size }}')" 
                            class="px-3 py-1.5 bg-red-500 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition-colors">
                        View
                    </button>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Reaction Buttons (Only visible when expanded) -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <!-- Reaction Buttons -->
                <div class="flex items-center gap-2 flex-wrap">
                    @foreach($reactions as $reaction)
                    @php
                        $userReaction = $announcement->userReactions->where('user_id', auth()->id())->first();
                        $isActive = $userReaction && $userReaction->reaction_id == $reaction->id;
                        $reactionCount = $announcement->userReactions->where('reaction_id', $reaction->id)->count();
                    @endphp
                    <button onclick="reactToAnnouncement({{ $announcement->id }}, {{ $reaction->id }})" 
                            class="reaction-btn px-3 py-1.5 text-sm rounded-lg transition-colors flex items-center gap-1 {{ $isActive ? 'bg-red-100 text-red-700 border border-red-300' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' }}"
                            data-announcement="{{ $announcement->id }}"
                            data-reaction="{{ $reaction->id }}">
                        <span>{{ $reaction->emoji }}</span>
                        <span>{{ $reaction->name }}</span>
                        <span class="reaction-count text-xs {{ $isActive ? 'text-red-500' : 'text-gray-500' }}">
                            ({{ $reactionCount }})
                        </span>
                    </button>
                    @endforeach
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                    <button onclick="showReactionDetails({{ $announcement->id }})" 
                            class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg transition-colors flex items-center gap-1">
                        <i class="cft-standard-stroke cft-emoji-happy"></i>
                        <span>View Reactions ({{ $announcement->userReactions->count() }})</span>
                    </button>
                    <button onclick="showViewDetails({{ $announcement->id }})" 
                            class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg transition-colors flex items-center gap-1">
                        <i class="cft-standard-stroke cft-eye"></i>
                        <span>Viewers ({{ $announcement->views_count ?? 0 }})</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer Stats & Actions -->
    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
        <div class="flex items-center gap-4">
            <!-- Reactions Summary -->
            @if($announcement->userReactions->count() > 0)
            <button onclick="showReactionDetails({{ $announcement->id }})" 
                    class="flex items-center gap-1 text-sm text-gray-600 hover:text-gray-900">
                <i class="cft-standard-stroke cft-happy text-base"></i>
                <span class="total-reactions">{{ $announcement->userReactions->count() }}</span>
            </button>
            @endif
            
            <!-- Views -->
            <button onclick="showViewDetails({{ $announcement->id }})" 
                    class="flex items-center gap-1 text-sm text-gray-600 hover:text-gray-900">
                <i class="cft-standard-stroke cft-eye text-base"></i>
                <span>{{ $announcement->views_count ?? 0 }}</span>
            </button>
            
            <!-- Attachments Count -->
            @if($announcement->attachments->count() > 0)
            <div class="flex items-center gap-1 text-sm text-gray-600">
                <i class="cft-standard-stroke cft-attachment text-base"></i>
                <span>{{ $announcement->attachments->count() }}</span>
            </div>
            @endif
        </div>
        
        <div class="flex items-center gap-2">
            <!-- Read More/Less Button -->
            <button onclick="toggleAnnouncementContent({{ $announcement->id }})" 
                    id="read-more-btn-{{ $announcement->id }}"
                    class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                Read More
            </button>
            <button onclick="toggleAnnouncementContent({{ $announcement->id }})" 
                    id="show-less-btn-{{ $announcement->id }}"
                    class="hidden px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                Show Less
            </button>
        </div>
    </div>
</div>

<script>
function toggleDropdown(id) {
    const dropdown = document.getElementById('dropdown-' + id);
    dropdown.classList.toggle('hidden');
    
    // Close other dropdowns
    document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
        if (el.id !== 'dropdown-' + id) {
            el.classList.add('hidden');
        }
    });
}

function togglePin(id) {
    $.ajax({
        url: '{{ url("announcements") }}/' + id + '/pin',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            location.reload();
        }
    });
}

function deleteAnnouncement(id) {
    if (confirm('Are you sure you want to delete this announcement?')) {
        $.ajax({
            url: '{{ url("announcements") }}/' + id,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                location.reload();
            }
        });
    }
}

// Function moved to index.blade.php for global access

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick*="toggleDropdown"]') && !event.target.closest('[id^="dropdown-"]')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
            el.classList.add('hidden');
        });
    }
});
</script>
