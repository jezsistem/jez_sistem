<div id="kt_header" class="header header-fixed">
    <div class="container-fluid d-flex align-items-stretch justify-content-between">
        <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
            <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                <ul class="menu-nav">
                    <!-- <li class="menu-item menu-item-open menu-item-here menu-item-submenu menu-item-rel menu-item-open menu-item-here menu-item-active" data-menu-toggle="click" aria-haspopup="true">
                       <a style="white-space:nowrap; font-weight:bold;" href="#" class="btn btn-primary" id="upcloud_info_btn" onclick="infoBtn('upcloud')">

                       </a>
                   </li> -->
                    <li class="menu-item menu-item-open menu-item-here menu-item-submenu menu-item-rel menu-item-open menu-item-here menu-item-active"
                        data-menu-toggle="click" aria-haspopup="true">
                        <a href="#" class="btn btn-primary btn-date-info" id="date_info_btn" onclick="infoBtn('date')">
                            <i class="ki-outline ki-calendar fs-2"></i> <span class="date"></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="topbar">
            <div class="header-mobile header-mobile-fixed col-2">
                <div class="topbar-item btn-sm bg" id="kt_aside_mobile_toggle">
                    <img alt="Logo" src="{{ asset('logo') }}/jez_pro.png" width="100px"/>
                </div>

            </div>

            {{-- Finance --}}
            <div class="topbar-item">
                <div class="btn btn-icon btn-icon-mobile w-auto d-flex align-items-center px-3 position-relative" style="margin-right: 15px">
                    <a href="#" id="notification_btn" class="text-dark">
                        <i class="fa fa-bell fa-lg"></i>
                        <span class="badge badge-danger position-absolute top-0 start-100 translate-middle p-1"
                              id="notification_count" style="font-size: 10px; border-radius: 50%;">3</span>
                    </a>

                    <!-- Notification Dropdown list -->
                    <div id="notification_dropdown" class="dropdown-menu dropdown-menu-right shadow-lg p-2"
                         style="width: 300px; display: none; position: absolute; top: 40px; right: 0px; z-index: 1000;">
                        <h6 class="dropdown-header">Notifications</h6>
                        <div id="notification_list">
                            <a href="#" class="dropdown-item">📢 New Order Received</a>
                            <a href="#" class="dropdown-item">🔔 Stock is Running Low</a>
                            <a href="#" class="dropdown-item">⚠️ Server Maintenance Scheduled</a>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item text-center text-primary">View All</a>
                    </div>
                </div>



                <div class="btn btn-icon btn-icon-mobile w-auto btn-clean d-flex align-items-center px-3"
                     id="kt_quick_user_toggle">
                    <span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
                    <span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3">{{ $data['user']->u_name }}</span>
                    <span class="symbol symbol-25 symbol-primary">
                        <span class="symbol-label font-size-h5 font-weight-bold">{{ substr($data['user']->u_name, 0, 1) }}</span>
                    </span>
                </div>

                <div class="btn btn-icon-mobile w-auto d-flex align-items-center pl-2 pr-0">
                    <a style="white-space:nowrap; font-weight:bold; background:#FF5D5D;"
                       href="{{ url('data_stok') }}" class="btn btn-danger" id="load_user_store">
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $("#notification_btn").click(function (event) {
            event.preventDefault();
            $("#notification_dropdown").toggle();
        });

        // Close the dropdown when clicking outside
        $(document).click(function (event) {
            if (!$(event.target).closest("#notification_btn, #notification_dropdown").length) {
                $("#notification_dropdown").hide();
            }
        });
    });
</script>