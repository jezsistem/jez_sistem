@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage announcement reactions</p>
        </div>
        <div>
            <button onclick="addReaction()" 
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <i class="cft-standard-stroke cft-plus"></i>
                Add Reaction
            </button>
        </div>
    </div>

    <!-- Search Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i class="cft-standard-stroke cft-search text-gray-400"></i>
            </div>
            <input type="text" 
                   id="searchInput"
                   class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                   placeholder="Search reactions...">
        </div>
    </div>

    <!-- Reactions Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="reactionsTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sort Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emoji</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hide Announcement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage Count</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reactions as $reaction)
                    <tr class="hover:bg-gray-50 transition-colors reaction-row" data-name="{{ strtolower($reaction->name) }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $reaction->sort_order }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $reaction->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="text-2xl">{{ $reaction->emoji }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white" 
                                  style="background-color: {{ $reaction->color }};">
                                {{ $reaction->color }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $reaction->hide_announcement ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $reaction->hide_announcement ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $reaction->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($reaction->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $reaction->user_reactions_count ?? 0 }} uses
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="relative inline-block text-left">
                                <button type="button" 
                                        onclick="toggleActionMenu({{ $reaction->id }})"
                                        class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg transition-colors flex items-center gap-1">
                                    Actions
                                    <i class="cft-standard-stroke cft-chevron-down text-xs"></i>
                                </button>
                                <div id="actionMenu-{{ $reaction->id }}" 
                                     class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                    <div class="py-1">
                                        <button onclick="editReaction({{ $reaction->id }}, '{{ addslashes($reaction->name) }}', '{{ addslashes($reaction->emoji ?? '') }}', '{{ $reaction->color }}', {{ $reaction->sort_order }}, {{ $reaction->hide_announcement ? 'true' : 'false' }}, '{{ $reaction->status }}')" 
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="cft-standard-stroke cft-edit mr-2"></i>
                                            Edit
                                        </button>
                                        <button onclick="deleteReaction({{ $reaction->id }})" 
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
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="cft-standard-stroke cft-mansory-grid text-gray-400 text-5xl mb-4"></i>
                                <p class="text-gray-500 text-lg">No reactions found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($reactions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $reactions->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Add/Edit Reaction Modal -->
<div id="reactionModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeReactionModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                <h3 class="text-lg font-semibold text-gray-900" id="reactionModalTitle">Add Reaction</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeReactionModal()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="reactionForm">
                <div class="p-6 space-y-4">
                    <input type="hidden" id="reactionId" name="id">
                    
                    <div>
                        <label for="reactionName" class="block text-sm font-medium text-gray-700 mb-2">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="reactionName" 
                               name="name" 
                               required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="reactionEmoji" class="block text-sm font-medium text-gray-700 mb-2">
                            Emoji
                        </label>
                        <input type="text" 
                               id="reactionEmoji" 
                               name="emoji" 
                               maxlength="10"
                               placeholder="😀"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Enter an emoji character (e.g., 😀, 👍, ❤️)</p>
                    </div>
                    
                    <div>
                        <label for="reactionColor" class="block text-sm font-medium text-gray-700 mb-2">
                            Color <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-3">
                            <input type="color" 
                                   id="reactionColorPicker" 
                                   value="#3b82f6"
                                   class="w-16 h-10 border border-gray-300 rounded-lg cursor-pointer">
                            <input type="text" 
                                   id="reactionColor" 
                                   name="color" 
                                   required
                                   pattern="^#[a-fA-F0-9]{6}$"
                                   placeholder="#3b82f6"
                                   class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Select a color or enter a hex code (e.g., #3b82f6)</p>
                    </div>

                    <div>
                        <label for="reactionSortOrder" class="block text-sm font-medium text-gray-700 mb-2">
                            Sort Order <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="reactionSortOrder" 
                               name="sort_order" 
                               required
                               min="0"
                               value="0"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
                    </div>

                    <div>
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="reactionHideAnnouncement" 
                                   name="hide_announcement" 
                                   value="1"
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="reactionHideAnnouncement" class="ml-2 text-sm text-gray-700">
                                Hide announcement when this reaction is used
                            </label>
                        </div>
                    </div>

                    <div id="reactionStatusGroup" style="display: none;">
                        <label for="reactionStatus" class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>
                        <select id="reactionStatus" 
                                name="status"
                                class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
                    <button type="button" 
                            onclick="closeReactionModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            id="submitReactionBtn"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Search functionality
    $('#searchInput').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.reaction-row').each(function() {
            const name = $(this).data('name') || '';
            if (name.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Color picker sync
    $('#reactionColorPicker').on('input', function() {
        $('#reactionColor').val($(this).val());
    });

    $('#reactionColor').on('input', function() {
        const color = $(this).val();
        if (/^#[a-fA-F0-9]{6}$/.test(color)) {
            $('#reactionColorPicker').val(color);
        }
    });

    // Reaction form submission
    $('#reactionForm').on('submit', function(e) {
        e.preventDefault();
        
        const reactionId = $('#reactionId').val();
        const url = reactionId 
            ? '{{ route("announcement-reactions.update", ":id") }}'.replace(':id', reactionId)
            : '{{ route("announcement-reactions.store") }}';
        const method = reactionId ? 'PUT' : 'POST';
        
        const formData = {
            _token: '{{ csrf_token() }}',
            name: $('#reactionName').val(),
            emoji: $('#reactionEmoji').val(),
            color: $('#reactionColor').val(),
            sort_order: parseInt($('#reactionSortOrder').val()),
            hide_announcement: $('#reactionHideAnnouncement').is(':checked') ? 1 : 0
        };
        
        if (reactionId) {
            formData.status = $('#reactionStatus').val();
        }
        
        const $submitBtn = $('#submitReactionBtn');
        const originalHtml = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('Saving...');
        
        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function(response) {
                if (response.success) {
                    showToast('✓ ' + response.message, 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showToast('Error: ' + (response.message || 'Failed to save reaction'), 'error');
                    $submitBtn.prop('disabled', false).html(originalHtml);
                }
            },
            error: function(xhr) {
                let errorMsg = 'Error saving reaction. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMsg = errors.join('\n');
                }
                showToast(errorMsg, 'error');
                $submitBtn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // Toggle action menu
    window.toggleActionMenu = function(reactionId) {
        $('[id^="actionMenu-"]').addClass('hidden');
        const menu = document.getElementById('actionMenu-' + reactionId);
        if (menu) {
            menu.classList.toggle('hidden');
        }
    };

    $(document).on('click', function(e) {
        if (!$(e.target).closest('[onclick*="toggleActionMenu"]').length && 
            !$(e.target).closest('[id^="actionMenu-"]').length) {
            $('[id^="actionMenu-"]').addClass('hidden');
        }
    });
});

// Add reaction
window.addReaction = function() {
    $('#reactionForm')[0].reset();
    $('#reactionId').val('');
    $('#reactionModalTitle').text('Add Reaction');
    $('#reactionStatusGroup').hide();
    $('#reactionColorPicker').val('#3b82f6');
    $('#reactionColor').val('#3b82f6');
    $('#reactionSortOrder').val('0');
    openReactionModal();
};

// Edit reaction
window.editReaction = function(id, name, emoji, color, sortOrder, hideAnnouncement, status) {
    $('#reactionId').val(id);
    $('#reactionName').val(name);
    $('#reactionEmoji').val(emoji);
    $('#reactionColor').val(color);
    $('#reactionColorPicker').val(color);
    $('#reactionSortOrder').val(sortOrder);
    $('#reactionHideAnnouncement').prop('checked', hideAnnouncement === true || hideAnnouncement === 'true');
    $('#reactionStatus').val(status);
    $('#reactionModalTitle').text('Edit Reaction');
    $('#reactionStatusGroup').show();
    openReactionModal();
};

// Delete reaction
window.deleteReaction = function(id) {
    if (!confirm('Are you sure you want to delete this reaction? This action cannot be undone.')) {
        return;
    }
    
    $.ajax({
        url: '{{ route("announcement-reactions.destroy", ":id") }}'.replace(':id', id),
        method: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                showToast('✓ ' + response.message, 'success');
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                showToast('Error: ' + (response.message || 'Failed to delete reaction'), 'error');
            }
        },
        error: function(xhr) {
            let errorMsg = 'Error deleting reaction. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            showToast(errorMsg, 'error');
        }
    });
};

// Modal functions
function openReactionModal() {
    const modal = document.getElementById('reactionModal');
    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
    $('body').addClass('overflow-hidden');
}

function closeReactionModal() {
    const modal = document.getElementById('reactionModal');
    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');
    $('body').removeClass('overflow-hidden');
}

// Toast notification (reuse from categories)
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
    toast.className = `flex items-center w-full max-w-xs p-4 ${bgColor} ${textColor} rounded-lg shadow-lg border border-gray-200`;
    
    toast.innerHTML = `
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 ${iconColor}">
            ${icon}
        </div>
        <div class="ml-3 text-sm font-medium flex-1">${message}</div>
        <button type="button" class="ml-auto -mx-1.5 -my-1.5 ${bgColor} ${textColor} rounded-lg p-1.5 inline-flex h-8 w-8 items-center justify-center" onclick="document.getElementById('${toastId}').remove()">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </button>
    `;
    
    toastContainer.appendChild(toast);
    
    setTimeout(() => {
        if (document.getElementById(toastId)) {
            document.getElementById(toastId).remove();
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
</script>
@endpush
