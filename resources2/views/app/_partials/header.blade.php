<div id="kt_header" class="header header-fixed">
    <div class="container-fluid d-flex align-items-stretch justify-content-between">
        <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
            <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                <ul class="menu-nav">
                     <!-- <li class="menu-item menu-item-open menu-item-here menu-item-submenu menu-item-rel menu-item-open menu-item-here menu-item-active" data-menu-toggle="click" aria-haspopup="true">
                        <a style="white-space:nowrap; font-weight:bold;" href="#" class="btn btn-primary" id="upcloud_info_btn" onclick="infoBtn('upcloud')">

                        </a>
                    </li> -->
                    <li class="menu-item menu-item-open menu-item-here menu-item-submenu menu-item-rel menu-item-open menu-item-here menu-item-active" data-menu-toggle="click" aria-haspopup="true">
                        <a style="white-space:nowrap; font-weight:bold;" href="#" class="btn btn-primary" id="date_info_btn" onclick="infoBtn('date')">
                        <i class="fa fa-calendar"></i> <span class="date"></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="topbar">
            <div class="header-mobile header-mobile-fixed col-2">
                <div class="topbar-item btn-sm bg-dark" id="kt_aside_mobile_toggle">
                    <img alt="Logo" src="{{ asset('logo') }}/jez_pro.png" width="100px"/>
                </div>
            </div>
            <div class="topbar-item">
                <div class="btn btn-icon btn-icon-mobile w-auto btn-clean d-flex align-items-center btn-lg px-2" id="kt_quick_user_toggle">
                    <span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
                    <span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3">{{ $data['user']->u_name }}</span>
                    <span class="symbol symbol-lg-35 symbol-25 symbol-primary">
                        <span class="symbol-label font-size-h5 font-weight-bold">{{ substr($data['user']->u_name, 0, 1) }}</span>
                    </span>
                </div>
                <div class="btn btn-icon btn-icon-mobile w-auto btn-clean d-flex align-items-center btn-lg px-2">
                    <a style="white-space:nowrap; font-weight:bold; background: rgb(212,18,21);
background: linear-gradient(171deg, rgba(212,18,21,1) 50%, rgba(209,122,0,1) 100%);" 
                        href="{{ url('data_stok') }}" class="btn btn-danger"  id="load_user_store">
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>