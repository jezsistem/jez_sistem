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

            <style>
                #notification_dropdown {
                    width: 100%; /* Make it responsive */
                    max-width: 320px; /* Limit max width */
                    position: absolute;
                    right: 0;
                    top: 40px;
                    z-index: 1000;
                    background: #fff;
                    border-radius: 8px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }

                #notification_dropdown a.dropdown-item {
                    white-space: normal; /* Allow text wrapping */
                    word-break: break-word; /* Ensure long texts wrap properly */
                }


            </style>

            {{-- Notification --}}
            <div class="topbar-item">
                <div class="btn btn-icon btn-icon-mobile w-auto d-flex align-items-center px-3 position-relative" style="margin-right: 15px">
                    <a href="#" id="notification_btn" class="text-dark">
                        <i class="fa fa-bell fa-lg"></i>
                        <span class="badge badge-danger position-absolute top-0 start-100 translate-middle p-1"
                              id="notification_count" style="font-size: 10px; border-radius: 50%; display: none;">0</span>
                    </a>

                    <!-- Notification Dropdown list -->
                    <div id="notification_dropdown" class="dropdown-menu dropdown-menu-right shadow-lg p-2"
                         style="width: 300px; display: none; position: absolute; top: 40px; right: 0px; z-index: 1000;">
                        <h6 class="dropdown-header">Notifications</h6>
                        <div id="notification_list">
                            <p class="dropdown-item text-muted">Loading...</p>
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
    function fetchNotifications() {
        $.ajax({
            url: "/notifications",
            type: "GET",
            dataType: "json",
            success: function (data) {
                let notificationList = $("#notification_list");
                let notificationCount = $("#notification_count");

                notificationList.empty(); // Clear previous notifications

                if (data.count === 0) {
                    notificationList.append('<p class="dropdown-item text-muted">No new notifications</p>');
                    notificationCount.hide(); // Hide count if no new notifications
                } else {
                    $.each(data.notifications, function (index, notif) {
                        notificationList.append(`<a href="#" class="dropdown-item">📢 ${notif.message}</a>`);
                    });

                    notificationCount.text(data.count).show(); // Show updated count
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching notifications:", error);
            }
        });
    }

    // Run fetchNotifications every 10 seconds
    setInterval(fetchNotifications, 10000);
    $(document).ready(fetchNotifications);

    // Toggle dropdown and mark as read
    $("#notification_btn").click(function () {
        $("#notification_dropdown").toggle();

        {{--// Mark notifications as read--}}
        {{--$.ajax({--}}
        {{--    url: "/notifications/mark-as-read",--}}
        {{--    type: "POST",--}}
        {{--    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },--}}
        {{--    success: function () {--}}
        {{--        $("#notification_count").hide(); // Hide count after marking as read--}}
        {{--    }--}}
        {{--});--}}
    });
</script>