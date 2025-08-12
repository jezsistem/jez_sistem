<div class="reaction-details">
    <h6 class="text-dark font-weight-bold mb-4">Reaction Details for "{{ $announcement->title }}"</h6>
    
    @if($reactionDetails->count() > 0)
        @foreach($reactionDetails as $reactionId => $detail)
        <div class="mb-4">
            <div class="d-flex align-items-center mb-2">
                <span class="badge mr-2" style="background-color: {{ $detail['reaction']->color }}; color: white; font-size: 1.2em;">
                    {{ $detail['reaction']->emoji }}
                </span>
                <span class="text-dark font-weight-bold">{{ $detail['reaction']->name }}</span>
                <span class="badge badge-light-primary ml-2">{{ $detail['count'] }} users</span>
            </div>
            
            <div class="pl-4">
                <div class="row">
                    @foreach($detail['users'] as $user)
                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-30 symbol-light-primary mr-3">
                                <span class="symbol-label">
                                    <i class="fas fa-user text-primary"></i>
                                </span>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-dark font-weight-bold fs-7">{{ $user->u_name }}</span>
                                <span class="text-muted fs-8">{{ $user->u_nip }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    @else
        <div class="text-center text-muted py-4">
            <i class="fas fa-comments fs-2x mb-3"></i>
            <p>No reactions yet</p>
        </div>
    @endif
</div>
