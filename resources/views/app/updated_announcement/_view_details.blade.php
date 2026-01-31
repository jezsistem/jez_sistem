<div class="view-details">
    <div class="mb-4 pb-4 border-b border-gray-200">
        <h5 class="text-lg font-bold text-gray-900">{{ $announcement->title }}</h5>
        <p class="text-sm text-gray-500 mt-1">
            Published: {{ $announcement->published_at ? $announcement->published_at->format('M d, Y H:i') : 'Not published' }}
        </p>
    </div>

    @if($viewers->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Division</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Viewed At</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($viewers as $index => $viewer)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">{{ $viewer['user_name'] }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $viewer['user_nip'] ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $viewer['position_name'] ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $viewer['division_name'] ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-900">{{ $viewer['viewed_date'] }}</span>
                                <span class="text-xs text-gray-500">{{ $viewer['viewed_time'] }}</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-12">
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="cft-standard-stroke cft-eye text-gray-400 text-2xl"></i>
                </div>
                <h6 class="text-gray-600 font-semibold mb-2">No viewers yet</h6>
                <p class="text-sm text-gray-400">This announcement hasn't been viewed by anyone yet.</p>
            </div>
        </div>
    @endif
</div>
