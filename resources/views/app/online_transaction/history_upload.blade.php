@if ($logs->count() > 0)
    <table class="table table-striped table-bordered">
        <thead class="table-light">
        <tr>
            <th>#</th>
            <th>File Asli</th>
            <th>Hasil Split</th>
            <th>User</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($logs as $index => $log)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $log->original_file }}</td>
                <td>{{ $log->split_file }}</td>
                <td>{{ $log->user?->name ?? '-' }}</td>
                <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                <td>
                    <a href="{{ asset('storage/split_resi/' . $log->split_file) }}" target="_blank" class="btn btn-sm btn-primary">👁️ Lihat</a>
                    <a href="{{ asset('storage/split_resi/' . $log->split_file) }}" download class="btn btn-sm btn-success">⬇️ Unduh</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <div class="text-center p-4 text-muted">
        <p>Tidak ada riwayat upload.</p>
    </div>
@endif