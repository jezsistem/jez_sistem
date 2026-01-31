<aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-[calc(100vh-2rem)] bg-white rounded-xl m-5 border border-gray-100 overflow-y-auto shadow-sm transition-all duration-300">
    <div class="flex flex-col h-full">
        <!-- Top Section: Announcements + Dynamic Menu Items -->
        <div class="flex-1 px-4 py-4 space-y-8">
            <!-- Logo + Toggle Button -->
            <div class="flex items-center justify-between mt-4">
                <a href="/dashboard_new" class="flex items-center sidebar-logo">
                    <img src="{{ asset('logo/jez_pro.png') }}" class="h-6 w-auto transition-all duration-300" alt="JEZ PRO" />
                    <div class="w-9 h-9 bg-red-500 rounded-lg flex items-center justify-center text-white font-bold text-lg sidebar-logo-icon hidden">
                        J
                    </div>
                </a>
                <button id="sidebar-toggle" class="w-9 h-9 flex items-center justify-center hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-chevron-left text-gray-500 text-sm sidebar-toggle-icon"></i>
                </button>
            </div>

            <!-- Announcements Button (Fixed - Always visible) -->
            <div class="mb-4 sidebar-fixed">
                <a href="{{ url('/announcements_v2') }}" class="w-full flex items-center justify-center px-4 py-3 bg-red-500 hover:bg-red-500 text-white rounded-lg font-medium text-sm transition-colors shadow-lg shadow-red-400/50 group">
                    <i class="cft-standard-stroke cft-mansory-grid text-white sidebar-icon-only hidden"></i>
                    <div class="flex items-center justify-between w-full sidebar-text-visible">
                        <span>Announcements</span>
                        <i class="cft-standard-stroke cft-mansory-grid text-white text-lg"></i>
                    </div>
                </a>
            </div>
            
            <div id="sidebar-menu-container" class="space-y-2">
                <div class="menu-section" data-category="dashboard" data-original-title="Dashboard">
                    <div class="mb-2 sidebar-section-title">
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                            Dashboard
                        </h3>
                    </div>
                    
                    <div class="sidebar-menu-item mb-1">
                        <a href="{{ url('/dashboard_new') }}" 
                           class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ request()->is('dashboards*') || request()->is('dashboard_new*') ? 'bg-gray-100 font-semibold' : '' }}"
                           title="Dashboard">
                            <i class="cft-standard-stroke cft-dashboard text-gray-400 flex-shrink-0 text-lg"></i>
                            <span class="sidebar-menu-text">Dashboard</span>
                        </a>
                    </div>
                    <div class="sidebar-menu-item mb-1">
                        <a href="{{ url('/dashboard_v2_v2') }}" 
                           class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ request()->is('dashboard_v2*') ? 'bg-gray-100 font-semibold' : '' }}"
                           title="Dashboard V2">
                            <i class="cft-standard-stroke cft-chart-pie text-gray-400 flex-shrink-0 text-lg"></i>
                            <span class="sidebar-menu-text">Dashboard V2</span>
                        </a>
                    </div>
                    <div class="sidebar-menu-item mb-1">
                        <a href="{{ url('/asset_detail_v2') }}" 
                           class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ request()->is('asset_detail*') ? 'bg-gray-100 font-semibold' : '' }}"
                           title="Asset Detail">
                            <i class="cft-standard-stroke cft-ship-box-2 text-gray-400 flex-shrink-0 text-lg"></i>
                            <span class="sidebar-menu-text">Asset Detail</span>
                        </a>
                    </div>
                    <div class="sidebar-menu-item mb-1">
                        <a href="{{ url('/power_bi_dashboard_v2') }}" 
                           class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ request()->is('power_bi_dashboard*') ? 'bg-gray-100 font-semibold' : '' }}"
                           title="Power BI Dashboard">
                            <i class="cft-standard-stroke cft-chart-bar text-gray-400 flex-shrink-0 text-lg"></i>
                            <span class="sidebar-menu-text">Power BI Dashboard</span>
                        </a>
                    </div>
                </div>
                
                @if (!empty($data['sidebar']))
                    @php
                        // Get current menu title based on current page
                        $currentSegment = request()->segment(1);
                        $currentSlug = str_replace('_v2', '', $currentSegment);
                        
                        // Get menu title ID from current slug
                        $currentMenuAccess = DB::table('menu_accesses')->where('ma_slug', $currentSlug)->first();
                        $currentMenuTitleId = $currentMenuAccess ? $currentMenuAccess->mt_id : null;
                        $currentMenuTitle = $currentMenuTitleId ? DB::table('menu_titles')->where('id', $currentMenuTitleId)->first() : null;
                        $currentMenuTitleName = $currentMenuTitle ? $currentMenuTitle->mt_title : null;
                    @endphp
                    
                    @foreach ($data['sidebar'] as $menuTitle)
                        @php
                            $isDashboard = strtolower($menuTitle->mt_title) === 'dashboard';
                            $isHR = strtolower($menuTitle->mt_title) === 'human resource';
                            // Render all menu sections (not just current one)
                            // JavaScript will handle filtering based on selected category
                            // Skip dashboard (already shown above)
                        @endphp
                        
                        @if (!$isDashboard && !$isHR)
                            <!-- Menu Section (with data-category for filtering) -->
                            <div class="menu-section" data-category="{{ strtolower(str_replace(' ', '-', $menuTitle->mt_title)) }}" data-original-title="{{ $menuTitle->mt_title }}">
                                <!-- Menu Title (Section Header) -->
                                <div class="mb-2 mt-4 sidebar-section-title">
                                    <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                                        {{ $menuTitle->mt_title }}
                                    </h3>
                                </div>
                                
                                <!-- Menu Items -->
                                @if (!empty($menuTitle->ma))
                                    @foreach ($menuTitle->ma as $menuItem)
                                        @php
                                            $menuSlug = $menuItem->ma_slug;
                                            
                                            // Skip dashboard menu item (already shown above)
                                            if (strtolower($menuSlug) === 'dashboard') {
                                                continue;
                                            }
                                            
                                            // Determine link URL - use _v2 for all except dashboard
                                            // Check if slug already has _v2 suffix
                                            $menuUrl = (substr($menuSlug, -3) === '_v2') ? $menuSlug : ($menuSlug . '_v2');
                                            
                                            // Check if current segment matches menu slug (with or without _v2)
                                            $isActive = ($currentSegment === $menuSlug) || 
                                                       ($currentSegment === $menuSlug . '_v2') ||
                                                       (str_replace('_v2', '', $currentSegment) === $menuSlug);
                                        @endphp
                                        <div class="sidebar-menu-item mb-1">
                                            <a href="{{ url('/') }}/{{ $menuUrl }}" 
                                               class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ $isActive ? 'bg-gray-100 font-semibold' : '' }}"
                                               title="{{ $menuItem->ma_title }}">
                                                <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                <span class="sidebar-menu-text">{{ $menuItem->ma_title }}</span>
                                            </a>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endif
                        @if ($isHR)
                            <!-- Menu Section (with data-category for filtering) -->
                            <div class="menu-section" data-category="human-resource" data-original-title="Human Resource">
                                <div class="mb-2 sidebar-section-title mt-4">
                                    <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                                        Human Resource
                                    </h3>
                                </div>
                                @php
    $staffActive = request()->is(
        'user-positions*',
        'user-division*',
        'user-types*',
        'staff*'
    );

    $scheduleActive = request()->is(
        'shift-codes*',
        'daily-schedules/weekly*',
        'daily-schedules/weekly-report*',
        'daily-schedules/monthly-report*'
    );

    $leaveActive = request()->is(
        'leave-types*',
        'leave-requests*'
    );

    $overtimeActive = request()->is(
        'overtime_type*',
        'overtime*'
    );

    $assignmentActive = request()->is(
        'external_assignment_type*',
        'external-assignment*'
    );

    $announcementActive = request()->is(
        'announcements*',
        'announcements/manage*',
        'announcement-categories*',
        'announcement-reactions*'
    );

    $breakTimeActive = request()->is(
        'break-times*',
        'break-times/report*',
        'break-times/summary-report*'
    );

    $breakTimeBackupActive = request()->is(
        'break-times-backup*',
        'break-times-backup/report*',
        'break-times-backup/summary-report*'
    );
    $attendanceActive = request()->is(
        'attendance*',
        'attendance/summary-report*'
    );
@endphp


                                <div class="sidebar-menu-item mb-1">
                                    <a href="#" 
                                    class="sidebar-item group flex items-center gap-3 w-full justify-between px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ $staffActive ? 'bg-gray-100' : '' }}"
                                    title="Staff" aria-controls="staff-menu" data-collapse-toggle="staff-menu" aria-expanded="{{ $staffActive ? 'true' : 'false' }}">
                                        <div class="flex items-center gap-3">
                                            <i class="cft-standard-stroke cft-id-card text-gray-400 flex-shrink-0 text-lg"></i>
                                            <span class="sidebar-menu-text">Staff</span>
                                        </div>
                                        <svg class="w-4 h-4 transition-transform group-aria-expanded:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"></path></svg>
                                    </a>
                                    <ul id="staff-menu" class="{{ $staffActive ? '' : 'hidden' }} py-2 space-y-2 ml-8">
                                        <li>
                                            <a href="{{ url('/user-positions_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                <span class="sidebar-menu-text {{ request()->is('user-position*') ? 'text-red-500' : 'text-gray-700' }}">User Position</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ url('/user-divisions_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                <span class="sidebar-menu-text {{ request()->is('user-division*') ? 'text-red-500' : 'text-gray-700' }}">User Division</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ url('/user-types_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                <span class="sidebar-menu-text {{ request()->is('user-types*') ? 'text-red-500' : 'text-gray-700' }}">User Types</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ url('/staff_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                <span class="sidebar-menu-text {{ request()->is('staff*') ? 'text-red-500' : 'text-gray-700' }}">Staff</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="sidebar-menu-item mb-1">
                                        <a href="#" 
                                        class="sidebar-item group flex items-center gap-3 w-full justify-between px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ $scheduleActive ? 'bg-gray-100' : '' }}"
                                        title="Schedule" aria-controls="schedule-menu" data-collapse-toggle="schedule-menu" aria-expanded="{{ $scheduleActive ? 'true' : 'false' }}">
                                            <div class="flex items-center gap-3">
                                                <i class="cft-standard-stroke cft-calendar-day text-gray-400 flex-shrink-0 text-lg"></i>
                                                <span class="sidebar-menu-text">Schedule</span>
                                            </div>
                                            <svg class="w-4 h-4 transition-transform group-aria-expanded:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"></path></svg>
                                        </a>
                                        <ul id="schedule-menu" class="{{ $scheduleActive ? '' : 'hidden' }} py-2 space-y-2 ml-8">
                                            <li>
                                                <a href="{{ url('/shift-codes_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('shift-codes*') ? 'text-red-500' : 'text-gray-700' }}">Shift</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/daily-schedules/weekly_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('daily-schedules/weekly*') ? 'text-red-500' : 'text-gray-700' }}">Weekly Input</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/daily-schedules/weekly-report_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('daily-schedules/weekly-report*') ? 'text-red-500' : 'text-gray-700' }}">Weekly Report</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/daily-schedules/monthly-report_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('daily-schedules/monthly-report*') ? 'text-red-500' : 'text-gray-700' }}">Monthly Report</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="sidebar-menu-item mb-1">
                                        <a href="#" 
                                        class="sidebar-item group flex items-center gap-3 w-full justify-between px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ $leaveActive ? 'bg-gray-100' : '' }}"
                                        title="Leave" aria-controls="leave-menu" data-collapse-toggle="leave-menu" aria-expanded="{{ $leaveActive ? 'true' : 'false' }}">
                                            <div class="flex items-center gap-3">
                                                <i class="cft-standard-stroke cft-visibilty-off text-gray-400 flex-shrink-0 text-lg"></i>
                                                <span class="sidebar-menu-text">Leave</span>
                                            </div>
                                            <svg class="w-4 h-4 transition-transform group-aria-expanded:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"></path></svg>
                                        </a>
                                        <ul id="leave-menu" class="{{ $leaveActive ? '' : 'hidden' }} py-2 space-y-2 ml-8">
                                            <li>
                                                <a href="{{ url('/leave-types_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('leave-types*') ? 'text-red-500' : 'text-gray-700' }}">Leave Type</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/leave-requests_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('leave-requests*') ? 'text-red-500' : 'text-gray-700' }}">Leave Request</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/leave-requests/summary-report_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('leave-requests/summary-report*') ? 'text-red-500' : 'text-gray-700' }}">Summary Report</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="sidebar-menu-item mb-1">
                                        <a href="#" 
                                        class="sidebar-item group flex items-center gap-3 w-full justify-between px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ $overtimeActive ? 'bg-gray-100' : '' }}"
                                        title="Overtime" aria-controls="overtime-menu" data-collapse-toggle="overtime-menu" aria-expanded="{{ $overtimeActive ? 'true' : 'false' }}">
                                            <div class="flex items-center gap-3">
                                                <i class="cft-standard-stroke cft-clock-square text-gray-400 flex-shrink-0 text-lg"></i>
                                                <span class="sidebar-menu-text">Overtime</span>
                                            </div>
                                            <svg class="w-4 h-4 transition-transform group-aria-expanded:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"></path></svg>
                                        </a>
                                        <ul id="overtime-menu" class="{{ $overtimeActive ? '' : 'hidden' }} py-2 space-y-2 ml-8">
                                            <li>
                                                <a href="{{ url('/overtime_type_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('overtime_type*') ? 'text-red-500' : 'text-gray-700' }}">Overtime Type</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/overtime_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('overtime*') ? 'text-red-500' : 'text-gray-700' }}">Overtime Request</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/leave-requests/summary-report_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('leave-requests/summary-report*') ? 'text-red-500' : 'text-gray-700' }}">Summary Report</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="sidebar-menu-item mb-1">
                                        <a href="#" 
                                        class="sidebar-item group flex items-center gap-3 w-full justify-between px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ $assignmentActive ? 'bg-gray-100' : '' }}"
                                        title="External Assignment" aria-controls="external-assignment-menu" data-collapse-toggle="external-assignment-menu" aria-expanded="{{ $assignmentActive ? 'true' : 'false' }}">
                                            <div class="flex items-center gap-3">
                                                <i class="cft-standard-stroke cft-task text-gray-400 flex-shrink-0 text-lg"></i>
                                                <span class="sidebar-menu-text">External Assignment</span>
                                            </div>
                                            <svg class="w-4 h-4 transition-transform group-aria-expanded:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"></path></svg>
                                        </a>
                                        <ul id="external-assignment-menu" class="{{ $assignmentActive ? '' : 'hidden' }} py-2 space-y-2 ml-8">
                                            <li>
                                                <a href="{{ url('/external_assignment_type_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('external_assignment_type*') ? 'text-red-500' : 'text-gray-700' }}">External Assignment Type</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/external-assignment_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('external-assignment*') ? 'text-red-500' : 'text-gray-700' }}">External Assignment Request</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/external-assignment/summary-report_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('external-assignment/summary-report*') ? 'text-red-500' : 'text-gray-700' }}">Summary Report</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="sidebar-menu-item mb-1">
                                        <a href="#" 
                                        class="sidebar-item group flex items-center gap-3 w-full justify-between px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ $attendanceActive ? 'bg-gray-100' : '' }}"
                                        title="Attendance" aria-controls="attendance-menu" data-collapse-toggle="attendance-menu" aria-expanded="{{ $attendanceActive ? 'true' : 'false' }}">
                                            <div class="flex items-center gap-3">
                                                <i class="cft-standard-stroke cft-user-check text-gray-400 flex-shrink-0 text-lg"></i>
                                                <span class="sidebar-menu-text">Attendance</span>
                                            </div>
                                            <svg class="w-4 h-4 transition-transform group-aria-expanded:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"></path></svg>
                                        </a>
                                        <ul id="attendance-menu" class="{{ $attendanceActive ? '' : 'hidden' }} py-2 space-y-2 ml-8">
                                            <li>
                                                <a href="{{ url('/attendance_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('attendance*') ? 'text-red-500' : 'text-gray-700' }}">Log Attendance</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/attendance/summary-report_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('attendance/summary-report*') ? 'text-red-500' : 'text-gray-700' }}">Summary Report</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="sidebar-menu-item mb-1">
                                        <a href="#" 
                                        class="sidebar-item group flex items-center gap-3 w-full justify-between px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ $breakTimeActive ? 'bg-gray-100' : '' }}"
                                        title="Break Time" aria-controls="break-time-menu" data-collapse-toggle="break-time-menu" aria-expanded="{{ $breakTimeActive ? 'true' : 'false' }}">
                                            <div class="flex items-center gap-3">
                                                <i class="cft-standard-stroke cft-clock-rush text-gray-400 flex-shrink-0 text-lg"></i>
                                                <span class="sidebar-menu-text">Break Time</span>
                                            </div>
                                            <svg class="w-4 h-4 transition-transform group-aria-expanded:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"></path></svg>
                                        </a>
                                        <ul id="break-time-menu" class="{{ $breakTimeActive ? '' : 'hidden' }} py-2 space-y-2 ml-8">
                                            <li>
                                                <a href="{{ url('/break-times_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('break-times*') ? 'text-red-500' : 'text-gray-700' }}">Break Control</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/break-times/report_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('break-times/report*') ? 'text-red-500' : 'text-gray-700' }}">Log Break Time</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/break-times/summary-report_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('break-times/summary-report*') ? 'text-red-500' : 'text-gray-700' }}">Summary Report</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="sidebar-menu-item mb-1">
                                        <a href="#" 
                                        class="sidebar-item group flex items-center gap-3 w-full justify-between px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ $breakTimeBackupActive ? 'bg-gray-100' : '' }}"
                                        title="Backup Time" aria-controls="backup-time-menu" data-collapse-toggle="backup-time-menu" aria-expanded="{{ $breakTimeBackupActive ? 'true' : 'false' }}">
                                            <div class="flex items-center gap-3">
                                                <i class="cft-standard-stroke cft-clock text-gray-400 flex-shrink-0 text-lg"></i>
                                                <span class="sidebar-menu-text">Backup Time</span>
                                            </div>
                                            <svg class="w-4 h-4 transition-transform group-aria-expanded:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"></path></svg>
                                        </a>
                                        <ul id="backup-time-menu" class="{{ $breakTimeBackupActive ? '' : 'hidden' }} py-2 space-y-2 ml-8">
                                            <li>
                                                <a href="{{ url('/break-times-backup_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('break-times-backup*') ? 'text-red-500' : 'text-gray-700' }}">Backup Control</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/break-times-backup/report_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('break-times-backup/report*') ? 'text-red-500' : 'text-gray-700' }}">Log Backup Time</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/break-times-backup/summary-report_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('break-times-backup/summary-report*') ? 'text-red-500' : 'text-gray-700' }}">Summary Report</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="sidebar-menu-item mb-1">
                                        <a href="#" 
                                        class="sidebar-item group flex items-center gap-3 w-full justify-between px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ $announcementActive ? 'bg-gray-100' : '' }}"
                                        title="Announcement" aria-controls="announcement-menu" data-collapse-toggle="announcement-menu" aria-expanded="{{ $announcementActive ? 'true' : 'false' }}">
                                            <div class="flex items-center gap-3">
                                                <i class="cft-standard-stroke cft-notification-loud text-gray-400 flex-shrink-0 text-lg"></i>
                                                <span class="sidebar-menu-text">Announcement</span>
                                            </div>
                                            <svg class="w-4 h-4 transition-transform group-aria-expanded:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"></path></svg>
                                        </a>
                                        <ul id="announcement-menu" class="{{ $announcementActive ? '' : 'hidden' }} py-2 space-y-2 ml-8">
                                            <li>
                                                <a href="{{ url('/announcements_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('announcements*') ? 'text-red-500' : 'text-gray-700' }}">View Announcements</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/announcements_v2/manage') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('announcements_v2/manage*') ? 'text-red-500' : 'text-gray-700' }}">Manage</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/announcement-categories_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('announcement-categories*') ? 'text-red-500' : 'text-gray-700' }}">Categories</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/announcement-reactions_v2') }}" class="sidebar-menu-text text-sm flex items-center px-2 py-1.5 gap-2">
                                                    <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'dot-large') }} text-gray-400 flex-shrink-0"></i>
                                                    <span class="sidebar-menu-text {{ request()->is('announcement-reactions*') ? 'text-red-500' : 'text-gray-700' }}">Reactions</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>                                     
                                </div>                        
                         @endif
                    @endforeach
                @else
                    <!-- Fallback: No menu data available -->
                    <div class="px-3 py-4 text-sm text-gray-500 text-center">
                        No menu items available
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Bottom Section: User, Konfigurasi, Logout (Fixed - Always visible) -->
        <div class="border-t border-gray-200 p-3 space-y-1 sidebar-fixed">
            <!-- User Profile -->
            <a href="{{ url('/user') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ request()->is('user*') ? 'bg-gray-100 font-semibold' : '' }}" title="User">
                <i class="cft-standard-stroke cft-user text-gray-400 flex-shrink-0 text-lg"></i>
                <span class="sidebar-menu-text">User</span>
            </a>
            
            <!-- Konfigurasi -->
            <a href="{{ url('/konfigurasi') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ request()->is('konfigurasi*') ? 'bg-gray-100 font-semibold' : '' }}" title="Konfigurasi">
                <i class="cft-standard-stroke cft-settings text-gray-400 flex-shrink-0 text-lg"></i>
                <span class="sidebar-menu-text">Konfigurasi</span>
            </a>
            
            <!-- Logout -->
            <a href="{{ route('logout') }}" class="flex items-center justify-center gap-3 px-4 py-2.5 mt-2 bg-red-100 hover:bg-red-200 text-red-500 rounded-lg font-medium text-sm transition-colors" title="Logout">
                <i class="cft-standard-stroke cft-logout flex-shrink-0 text-lg"></i>
                <span class="sidebar-menu-text">Logout</span>
            </a>
        </div>
    </div>
</aside>

<script>
// Sidebar Toggle & Filter Logic
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const topbar = document.getElementById('topbar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const mainContent = document.querySelector('main');
    const topbarTabs = document.querySelectorAll('.topbar-tab');
    
    // Get menu sections - refresh this when needed
    function getMenuSections() {
        return document.querySelectorAll('.menu-section');
    }
    let menuSections = getMenuSections();
    
    // Load saved collapsed state
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        toggleSidebar(true);
    }
    
    // Toggle Sidebar
    sidebarToggle.addEventListener('click', function() {
        const willCollapse = !sidebar.classList.contains('sidebar-collapsed');
        toggleSidebar(willCollapse);
        localStorage.setItem('sidebarCollapsed', willCollapse);
    });
    
    function toggleSidebar(collapse) {
        if (collapse) {
            // Collapse sidebar
            sidebar.classList.add('sidebar-collapsed');
            sidebar.classList.remove('w-64');
            sidebar.classList.add('w-24');
            
            // Adjust main content margin
            if (mainContent) {
                mainContent.classList.remove('ml-72');
                mainContent.classList.add('ml-32');
            }
            if (topbar) {
                topbar.classList.remove('ml-72-plus');
                topbar.classList.add('ml-32-plus');
            }

            // Hide all text elements
            document.querySelectorAll('.sidebar-menu-text').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.sidebar-section-title').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.sidebar-text-visible').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.sidebar-icon-only').forEach(el => el.classList.remove('hidden'));
            
            // Switch logo
            document.querySelector('.sidebar-logo img').classList.add('hidden');
            document.querySelector('.sidebar-logo-icon').classList.remove('hidden');
            
            // Rotate toggle icon
            document.querySelector('.sidebar-toggle-icon').classList.add('rotate-180');
            
            // Center menu items
            document.querySelectorAll('.sidebar-item').forEach(el => {
                el.classList.add('justify-center');
                el.classList.remove('gap-3');
            });
            
        } else {
            // Expand sidebar
            sidebar.classList.remove('sidebar-collapsed');
            sidebar.classList.remove('w-24');
            sidebar.classList.add('w-64');
            
            // Adjust main content margin
            if (mainContent) {
                mainContent.classList.remove('ml-32');
                mainContent.classList.add('ml-72');
            }
            if (topbar) {
                topbar.classList.remove('ml-32-plus');
                topbar.classList.add('ml-72-plus');
            }
            
            // Show all text elements
            document.querySelectorAll('.sidebar-menu-text').forEach(el => el.classList.remove('hidden'));
            document.querySelectorAll('.sidebar-section-title').forEach(el => el.classList.remove('hidden'));
            document.querySelectorAll('.sidebar-text-visible').forEach(el => el.classList.remove('hidden'));
            document.querySelectorAll('.sidebar-icon-only').forEach(el => el.classList.add('hidden'));
            
            // Switch logo
            document.querySelector('.sidebar-logo img').classList.remove('hidden');
            document.querySelector('.sidebar-logo-icon').classList.add('hidden');
            
            // Rotate toggle icon back
            document.querySelector('.sidebar-toggle-icon').classList.remove('rotate-180');
            
            // Reset menu items alignment
            document.querySelectorAll('.sidebar-item').forEach(el => {
                el.classList.remove('justify-center');
                el.classList.add('gap-3');
            });
        }
    }
    
    // Category mapping between top bar and sidebar mt_title
    const categoryMapping = {
        'all': [], // Show all
        'dashboard': ['dashboard'],
        'master-data': ['master-data', 'data-master', 'master', 'data'],
        'stock': ['stock', 'inventory', 'warehouse', 'gudang'],
        'sales': ['sales', 'point-of-sales', 'penjualan', 'pos', 'sale'],
        'human-resource': ['human-resource', 'hr', 'sdm', 'human', 'resource', 'break-times-backup', 'break-times'],
        'report': ['report', 'laporan', 'report-v2', 'laporan-v2'],
        'purchase-order': ['purchase-order', 'pembelian', 'purchase-order-v2', 'pembelian-v2'],
        'ecommerce': ['ecommerce', 'toko-online', 'ecommerce-v2', 'toko-online-v2'], 
        'customer': ['customer', 'pelanggan', 'customer-v2', 'pelanggan-v2'], 
        'finance': ['finance', 'keuangan', 'finance-v2', 'keuangan-v2'],
        'user': ['user', 'pengguna', 'user-v2', 'pengguna-v2'],
        'konfigurasi': ['konfigurasi', 'pengaturan', 'konfigurasi-v2', 'pengaturan-v2']
    };
    
    // Filter sidebar menu based on category
    function filterSidebarMenu(category) {
        console.log('Filtering sidebar for category:', category);
        
        // Refresh menu sections to ensure we have all of them
        menuSections = getMenuSections();
        
        if (!category || category === 'all') {
            // Show all menus
            menuSections.forEach(section => {
                section.style.display = 'block';
            });
            return;
        }
        
        const allowedCategories = categoryMapping[category] || [category];
        console.log('Allowed categories:', allowedCategories);
        console.log('Total menu sections found:', menuSections.length);
        
        menuSections.forEach(section => {
            const sectionCategory = section.getAttribute('data-category');
            const originalTitle = section.getAttribute('data-original-title').toLowerCase();
            
            console.log('Checking section:', originalTitle, '(', sectionCategory, ')');
            
            // Check if section matches any of the allowed categories
            const isMatch = allowedCategories.some(cat => {
                // Normalize category for comparison
                const normalizedCat = cat.toLowerCase().replace(/\s+/g, '-');
                const normalizedCatSpaces = cat.toLowerCase().replace(/-/g, ' ');
                
                // Check if sectionCategory matches (exact or contains)
                const categoryMatch = sectionCategory === normalizedCat || 
                                     sectionCategory.includes(normalizedCat) ||
                                     normalizedCat.includes(sectionCategory);
                
                // Check if originalTitle matches (exact or contains)
                const titleMatch = originalTitle === normalizedCatSpaces ||
                                  originalTitle.includes(normalizedCatSpaces) ||
                                  normalizedCatSpaces.includes(originalTitle);
                
                return categoryMatch || titleMatch;
            });
            
            section.style.display = isMatch ? 'block' : 'none';
            console.log('Section', originalTitle, 'match:', isMatch);
        });
    }
    
    // Add click event to top bar tabs
    topbarTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent navigation
            
            // Get category from data-category attribute
            const category = this.getAttribute('data-category') || 'all';
            
            // Filter sidebar
            filterSidebarMenu(category);
            
            // Save selected category to localStorage
            localStorage.setItem('selectedCategory', category);
            
            // Update active state
            topbarTabs.forEach(t => {
                t.classList.remove('bg-gray-900', 'text-white');
                t.classList.add('text-gray-700', 'hover:bg-gray-100');
            });
            this.classList.add('bg-gray-900', 'text-white');
            this.classList.remove('text-gray-700', 'hover:bg-gray-100');
        });
    });
    
    // Load saved category from localStorage or determine from current page
    let savedCategory = localStorage.getItem('selectedCategory');
    
    // If no saved category, try to determine from current page
    if (!savedCategory) {
        const currentSegment = window.location.pathname.split('/')[1];
        const currentSlug = currentSegment ? currentSegment.replace('_v2', '') : '';
        
        // Try to match current page to a category
        // This is a fallback - user can still use tabs to filter
        savedCategory = 'all'; // Default to show all
    }
    
    filterSidebarMenu(savedCategory);
    
    // Set active tab based on saved category
    topbarTabs.forEach(tab => {
        if (tab.getAttribute('data-category') === savedCategory) {
            tab.classList.add('bg-gray-900', 'text-white');
            tab.classList.remove('text-gray-700', 'hover:bg-gray-100');
        }
    });
    
    // Listen for filterSidebar event from topbar dropdown
    window.addEventListener('filterSidebar', function(event) {
        const category = event.detail.category;
        filterSidebarMenu(category);
    });
    
    // Make filterSidebarMenu available globally so topbar can access it
    window.filterSidebarMenu = filterSidebarMenu;
});
</script>
