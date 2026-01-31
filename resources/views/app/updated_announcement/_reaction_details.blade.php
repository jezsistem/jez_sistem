<div class="reaction-details space-y-4">
    @if($reactionDetails->count() > 0)
        @foreach($reactionDetails as $reactionId => $detail)
        <div class="mb-4">
            <!-- Reaction header -->
            <div class="flex items-center justify-between mb-3 px-4 py-3 bg-gray-50 rounded-lg">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full text-white text-sm font-medium" 
                          style="background-color: {{ $detail['reaction']->color }};">
                        {{ $detail['reaction']->emoji }}
                    </span>
                    <div>
                        <span class="text-gray-900 font-semibold block">{{ $detail['reaction']->name }}</span>
                    </div>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    {{ $detail['count'] }}
                </span>
            </div>
            
            <!-- Users list -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($detail['users'] as $user)
                <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="cft-standard-stroke cft-user text-blue-600 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-gray-900 truncate">{{ $user->u_name }}</div>
                        <div class="text-xs text-gray-500 truncate">{{ $user->u_nip ?? 'N/A' }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    @else
        <div class="text-center text-gray-500 py-12">
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="cft-standard-stroke cft-heart text-gray-400 text-2xl"></i>
                </div>
                <h6 class="text-gray-600 font-semibold mb-2">No reactions yet</h6>
                <p class="text-sm text-gray-400">Be the first to react to this announcement!</p>
            </div>
        </div>
    @endif
</div>
