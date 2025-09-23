<div class="d-flex flex-wrap">
    @forelse ($merged as $payment)
    <div class="flex-fill mr-3 mb-3" style="min-width: 200px; max-width: 200px;">
        <div class="card bg-light">
            <div class="card-body p-3">
                <h6 class="text-muted mb-1">{{$payment->pm_name}}</h6>
                <h4 class="text-dark font-weight-bold mb-1">Rp {{ number_format((int)$payment->total_payment, 0, ',', '.') }}</h4>
                <small class="text-muted d-flex justify-content-between mt-3">Settled: <span class="text-success">Rp {{ number_format((int)$payment->settled_payment, 0, ',', '.') }}</span></small>
                <small class="text-muted d-flex justify-content-between">Unsettle: <span class="text-warning">Rp {{ number_format((int)$payment->unsettled_payment, 0, ',', '.') }}</span></small>
            </div>
        </div>
    </div>
    @empty
        
    @endforelse
    
</div>