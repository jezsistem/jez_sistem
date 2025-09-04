<div class="mb-4">
    <h5 class="text-dark font-weight-bold">{{ $announcement->title }}</h5>
    <p class="text-muted fs-7">Published: {{ $announcement->published_at->format('M d, Y H:i') }}</p>
</div>

@if($viewers->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="bg-light">
                <tr>
                    <th class="text-dark font-weight-bold">No</th>
                    <th class="text-dark font-weight-bold">Name</th>
                    <th class="text-dark font-weight-bold">NIP</th>
                    <th class="text-dark font-weight-bold">Position</th>
                    <th class="text-dark font-weight-bold">Division</th>
                    <th class="text-dark font-weight-bold">Viewed At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($viewers as $index => $viewer)
                    <tr>
                        <td class="text-muted">{{ $index + 1 }}</td>
                        <td class="text-dark font-weight-bold">{{ $viewer['user_name'] }}</td>
                        <td class="text-muted">{{ $viewer['user_nip'] ?? 'N/A' }}</td>
                        <td class="text-muted">{{ $viewer['position_name'] ?? 'N/A' }}</td>
                        <td class="text-muted">{{ $viewer['division_name'] ?? 'N/A' }}</td>
                        <td class="text-muted">
                            <div class="d-flex flex-column">
                                <span class="fs-7">{{ $viewer['viewed_date'] }}</span>
                                <span class="fs-8 text-muted">{{ $viewer['viewed_time'] }}</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
<!--     
    <div class="text-center mt-3">
        <span class="badge badge-light-red fs-7">Total Viewers: {{ $viewers->count() }}</span>
    </div> -->
@else
    <div class="text-center py-5">
        <i class="ki-outline ki-eye text-muted" style="font-size: 3rem;"></i>
        <h6 class="text-muted mt-3">No viewers yet</h6>
        <p class="text-muted fs-7">This announcement hasn't been viewed by anyone yet.</p>
    </div>
@endif
