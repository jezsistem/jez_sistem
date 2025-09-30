<div class="reaction-details">
    <!-- Header with announcement title -->
    <!-- <div class="mb-4 pb-3 border-bottom">
        <h6 class="text-dark font-weight-bold mb-1">{{ $announcement->title }}</h6>
        <small class="text-muted">
            <i class="fas fa-calendar mr-1"></i>Published: {{ $announcement->created_at->format('d M Y, H:i') }}
        </small>
    </div> -->
    
    @if($reactionDetails->count() > 0)
        @foreach($reactionDetails as $reactionId => $detail)
        <div class="mb-3">
            <!-- Reaction header -->
            <div class="d-flex align-items-center justify-content-between mb-3 px-4 py-3 rounded" style="background-color: #f5f6f8;">
                <div class="d-flex align-items-center">
                    <span class="d-flex align-items-center justify-content-center rounded-circle mr-3" 
                          style="width: 30px; height: 30px; background-color: {{ $detail['reaction']->color }}; color: white; font-size: 1em;">
                        {{ $detail['reaction']->emoji }}
                    </span>
                    <div>
                        <span class="text-dark font-weight-bold d-block">{{ $detail['reaction']->name }}</span>
                    </div>
                </div>
                <span class="badge badge-light-red">{{ $detail['count'] }}</span>
            </div>
            
            <!-- Users list -->
            <div class="row mx-0">
                @foreach($detail['users'] as $user)
                <div class="col-12 col-sm-6 col-md-4 mb-2 p-3">
                    <div class="d-flex align-items-center p-2 bg-white rounded border" style="min-height: 50px;">
                        <div class="symbol symbol-25 symbol-light-primary mr-2 flex-shrink-0">
                            <span class="symbol-label">
                                <i class="fas fa-user text-primary" style="font-size: 0.875rem;"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="text-dark font-weight-semibold" style="font-size: 1rem;">{{ $user->u_name }}</div>
                            <div class="text-muted text-truncate" style="font-size: 0.875rem;">{{ $user->u_nip ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    @else
        <div class="text-center text-muted py-5">
            <div class="d-flex flex-column align-items-center">
                <div class="symbol symbol-60 symbol-light-muted mb-3">
                    <span class="symbol-label">
                        <i class="fas fa-heart text-muted" style="font-size: 1.5rem;"></i>
                    </span>
                </div>
                <h6 class="text-muted mb-2">No reactions yet</h6>
                <p class="text-muted mb-0" style="font-size: 0.875rem;">Be the first to react to this announcement!</p>
            </div>
        </div>
    @endif
</div>

<!-- Custom styles for reaction details -->
<style>
.reaction-details .symbol-label {
    border-radius: 50%;
}

.reaction-details .min-width-0 {
    min-width: 0;
}

.reaction-details .text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.reaction-details .border {
    border: 1px solid #e4e6ea !important;
    transition: all 0.2s ease;
}

.reaction-details .border:hover {
    border-color: #b8daff !important;
    background-color: #f8f9fa !important;
}

@media (max-width: 576px) {
    .reaction-details .col-md-4 {
        flex: 0 0 50%;
        max-width: 50%;
    }
}
</style>
