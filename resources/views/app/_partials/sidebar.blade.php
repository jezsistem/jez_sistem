<!--begin::Aside-->
<div class="aside aside-left aside-fixed d-flex flex-column flex-row-auto" id="kt_aside" style="background:#ffffff;">
    <!--begin::Brand-->
    <div class="brand flex-column-auto" id="kt_brand">
        <!--begin::Logo-->
        <a href="{{ url('/dashboard') }}" class="brand-logo">
            <img style="width:100px; margin-left: 15px;" alt="Logo" src="{{ asset('logo') }}/jez_pro.png" />
        </a>
        <!--end::Logo-->
        <!--begin::Toggle-->
        <div id="kt_aside_toggle" class="app-sidebar-toggle btn btn-sm btn-icon bg-light btn-color-gray-700 btn-active-color-primary d-none d-lg-flex rotate " data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize">
            <i class="ki-outline ki-text-align-right rotate-180 fs-1"></i> 
        </div>
        <!--end::Toolbar-->
    </div>
    <!--end::Brand-->
    <!--begin::Aside Menu-->
    <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper" style="background:#ffffff;">
        <!--begin::Menu Container-->
        <div id="kt_aside_menu" class="aside-menu mb-4" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500" style="background:#ffffff;">
            <!--begin::Menu Nav-->
            <ul class="menu-nav">
                @if (!empty($data['sidebar']))
                @foreach ($data['sidebar'] as $row)
                <li class="menu-section border-bottom separator separator-secondary my-3">
                    <h4 class="menu-text fs-7">{{ $row->mt_title }}</h4>
                    <i class="menu-icon ki ki-bold-more-hor icon-md"></i>
                </li>
                @if (!empty($row->ma))
                    @if ($row->mt_title == 'Human Resource')
                        <!-- HR Menu with Sub-menus -->
                        <li class="menu-item menu-accordion" data-menu-toggle="hover" aria-haspopup="true">
                            <a href="javascript:;" class="menu-link menu-toggle">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-text">Staff</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="menu-sub menu-sub-accordion">
                                <ul class="menu-subnav">
                                    <li class="menu-item">
                                        <a href="{{ url('/user-positions') }}" class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-text">User Position</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ url('/user-divisions') }}" class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-text">User Division</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ url('/user-types') }}" class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-text">User Type</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ url('/staff') }}" class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-text">Staff</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                        <li class="menu-item menu-accordion" data-menu-toggle="hover" aria-haspopup="true">
                            <a href="javascript:;" class="menu-link menu-toggle">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-text">Schedule</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="menu-sub menu-sub-accordion">
                                <ul class="menu-subnav">
                                    <li class="menu-item">
                                        <a href="{{ url('/shift-codes') }}" class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-text">Shift</span>
                                        </a>
                                    </li>
                                    <!-- <li class="menu-item">
                                        <a href="{{ url('/daily-schedules') }}" class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-text">Schedule</span>
                                        </a>
                                    </li> -->
                                    <li class="menu-item">
                                        <a href="{{ url('/daily-schedules/weekly') }}" class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-text">Weekly Input</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ url('/daily-schedules/weekly-report') }}" class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-text">Weekly Report</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ url('/daily-schedules/monthly-report') }}" class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-text">Monthly Report</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                        <li class="menu-item menu-accordion {{ request()->is('leave-types*') || request()->is('leave-requests*') ? 'active' : '' }}" data-menu-toggle="hover" aria-haspopup="true">
                            <a href="javascript:;" class="menu-link menu-toggle">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-text">Leave</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="menu-sub menu-sub-accordion">
                                <ul class="menu-subnav">
                                    <li class="menu-item {{ request()->is('leave-types*') ? 'active' : '' }}">
                                        <a href="{{ url('/leave-types') }}" class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-text">Leave Type</span>
                                        </a>
                                    </li>
                                    <li class="menu-item {{ request()->is('leave-requests*') ? 'active' : '' }}">
                                        <a href="{{ url('/leave-requests') }}" class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-text">Leave Request</span>
                                        </a>
                                    </li>
                                    <li class="menu-item {{ request()->is('leave-requests/summary-report*') ? 'active' : '' }}">
                                        <a href="{{ url('/leave-requests/summary-report') }}" class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-text">Summary Report</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="menu-item menu-accordion {{ request()->is('attendance*') || request()->is('leave-requests*') ? 'active' : '' }}" data-menu-toggle="hover" aria-haspopup="true">
                            <a href="javascript:;" class="menu-link menu-toggle">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-text">Attendance</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="menu-sub menu-sub-accordion">
                                <ul class="menu-subnav">
                                    <li class="menu-item {{ request()->is('attendance*') ? 'active' : '' }}">
                                        <a href="{{ url('/attendance') }}" class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-text">Log Attendance</span>
                                        </a>
                                    </li>
                                    <li class="menu-item {{ request()->is('attendance/summary-report*') ? 'active' : '' }}">
                                        <a href="{{ url('/attendance/summary-report') }}" class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-text">Summary Report</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        
                        <!-- <li class="menu-item" aria-haspopup="true" data-menu-toggle="hover">
                            <a href="{{ url('/attendance') }}" class="menu-link menu-toggle {{ request()->is('attendance*') ? 'active' : '' }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-text">Attendance</span>
                            </a>
                        </li> -->
                        
                        <li class="menu-item menu-accordion" data-menu-toggle="hover" aria-haspopup="true">
                            <a href="javascript:;" class="menu-link menu-toggle">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-text">Break Time</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="menu-sub menu-sub-accordion">
                                <ul class="menu-subnav">
                                    <li class="menu-item">
                                        <a href="{{ url('/break-times') }}" class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-text">Break Control</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ url('/break-times/report') }}" class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-text">Log Break Time</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ url('/break-times/summary-report') }}" class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-text">Summary Report</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                        <li class="menu-item menu-accordion" data-menu-toggle="hover" aria-haspopup="true">
                            <a href="javascript:;" class="menu-link menu-toggle">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-text">Announcement</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="menu-sub menu-sub-accordion">
                                <ul class="menu-subnav">
                                    <li class="menu-item">
                                        <a href="{{ url('/announcements') }}" class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-text">View Announcements</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ url('/announcements/manage') }}" class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-text">Manage</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ url('/announcement-categories') }}" class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-text">Category</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ url('/announcement-reactions') }}" class="menu-link">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-text">Reaction</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @else
                        <!-- Other menus without sub-menus -->
                @foreach ($row->ma as $crow)
                <li class="menu-item" aria-haspopup="true" data-menu-toggle="hover">
                    <a href="{{ url('/') }}/{{ $crow->ma_slug }}" class="menu-link menu-toggle">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-text">{{ $crow->ma_title }}</span>
                    </a>
                </li>
                @endforeach
                    @endif
                @endif
                @endforeach
                @endif
            </ul>
            <!--end::Menu Nav-->
        </div>
        <!--end::Menu Container-->
    </div>
    <!--end::Aside Menu-->
</div>
<!--end::Aside-->
