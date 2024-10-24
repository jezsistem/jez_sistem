<!--begin::Aside-->
<div class="aside aside-left aside-fixed d-flex flex-column flex-row-auto" id="kt_aside" style="background:#ffffff;">
    <!--begin::Brand-->
    <div class="brand flex-column-auto" id="kt_brand">
        <!--begin::Logo-->
        <a href="{{ url('/dashboard') }}" class="brand-logo">
            <img style="width:80px;" alt="Logo" src="{{ asset('logo') }}/LOGOJEZ.png" />
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
                @foreach ($row->ma as $crow)
                <li class="menu-item" aria-haspopup="true" data-menu-toggle="hover">
                    <a href="{{ url('/') }}/{{ $crow->ma_slug }}" class="menu-link menu-toggle">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>                        <span class="menu-text">{{ $crow->ma_title }}</span>
                    </a>
                </li>
                @endforeach
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
