@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
        </div>
    </div>

    <!-- Power BI Dashboard Container -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="w-full overflow-hidden rounded-lg">
            <iframe 
                title="DAILY REPORT" 
                width="100%" 
                height="720"
                style="min-height: 720px; border: none;"
                src="https://report.jezpro.id/reports/?rs:embed=true"
                frameborder="0" 
                allowFullScreen="true">
            </iframe>
        </div>
    </div>
</div>
@endsection
