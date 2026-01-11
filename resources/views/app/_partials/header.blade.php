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
                <div class="topbar-item btn-sm bg" id="kt_aside_mobile_toggle" style="padding-left: 0px !important;">
                    <div class="d-flex align-items-center">
                        <!-- Mobile Menu Toggle Button -->
                        <i class="ki-outline ki-burger-menu mr-3" style="font-size: 2.3rem; transform: scaleX(-1);"></i>
                        <img alt="Logo" src="{{ asset('logo') }}/jez_pro.png" width="100px"/>
                    </div>  
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
                              id="notification_count" style="font-size: 10px; border-radius: 50%; display: none; width: 16px; height: 16px; text-align: center;">0</span>
                    </a>

                    <!-- Notification Dropdown list -->
                    <div id="notification_dropdown" class="dropdown-menu dropdown-menu-right shadow-lg p-0"
                         style="width: 350px; display: none; position: absolute; top: 40px; right: 0px; z-index: 1000;">
                        <div class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2 bg-light">
                            <h6 class="mb-0">Notifications</h6>
                            <button id="mark_all_read" class="btn btn-sm btn-link text-primary p-0" style="font-size: 12px;">
                                Mark all as read
                            </button>
                        </div>
                        <div id="notification_list" style="max-height: 400px; overflow-y: auto;">
                            <p class="dropdown-item text-muted">Loading...</p>
                        </div>
                        <div class="dropdown-divider m-0"></div>
                        <a href="#" class="dropdown-item text-center text-primary py-2">
                            <small>View All Notifications</small>
                        </a>
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


<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>    

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
                    notificationList.append('<div class="px-3 py-4 text-center text-muted"><i class="fa fa-bell-slash fa-2x mb-2 d-block"></i>No new notifications</div>');
                    notificationCount.hide(); // Hide count if no new notifications
                } else {
                    $.each(data.notifications, function (index, notif) {
                        let icon = getNotificationIcon(notif.type);
                        let timeAgo = formatTimeAgo(notif.created_at);
                        let notifClass = notif.is_read ? 'bg-light' : 'bg-white';
                        let notifLink = getNotificationLink(notif.type, notif.data);
                        
                        // Create clickable notification item
                        let hoverEffect = notifLink ? 'onmouseover="this.style.backgroundColor=\'#f8f9fa\'" onmouseout="this.style.backgroundColor=\'\'"' : '';
                        let cursorStyle = notifLink ? 'cursor: pointer;' : 'cursor: default;';
                        
                        let notificationHtml = `
                            <div class="notification-item ${notifClass} border-bottom" 
                                 data-id="${notif.id}" data-link="${notifLink}" 
                                 ${hoverEffect}
                                 style="${cursorStyle} padding: 12px 15px; text-decoration: none; color: inherit;">
                                <div class="d-flex align-items-start">
                                    <div class="mr-3">
                                        <i class="${icon} text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="notification-message" style="font-size: 13px; line-height: 1.4;">
                                            ${notif.message}
                                            ${notifLink ? '<i class="fa fa-external-link-alt ml-1 text-muted" style="font-size: 10px;"></i>' : ''}
                                        </div>
                                        <small class="text-muted d-block mt-1">${timeAgo}</small>
                                    </div>
                                    ${!notif.is_read ? '<div class="ml-2"><span class="badge badge-primary badge-circle" style="width: 8px; height: 8px; padding: 0;"></span></div>' : ''}
                                </div>
                            </div>
                        `;
                        
                        if (notifLink) {
                            // Wrap in link if we have a target URL
                            notificationHtml = `<a href="${notifLink}" class="dropdown-item p-0 text-decoration-none text-dark" style="display: block;">${notificationHtml}</a>`;
                        } else {
                            // Just add dropdown-item class
                            notificationHtml = notificationHtml.replace('class="notification-item', 'class="dropdown-item notification-item');
                        }
                        
                        notificationList.append(notificationHtml);
                    });

                    notificationCount.text(data.count).show(); // Show updated count
                    
                    // Add click handlers for individual notifications
                    $('.notification-item').on('click', function(e) {
                        let notifId = $(this).data('id');
                        let notifLink = $(this).data('link');
                        
                        // Mark as read first
                        markSingleAsRead(notifId);
                        
                        // If this notification has a link and it's not wrapped in <a> tag
                        if (notifLink && !$(this).closest('a').length) {
                            e.preventDefault();
                            // Small delay to allow mark as read to complete
                            setTimeout(function() {
                                window.location.href = notifLink;
                            }, 200);
                        }
                    });

                    // Add click handlers for notification links (when wrapped in <a> tags)
                    $('.notification-item a, a .notification-item').on('click', function() {
                        let notifId = $(this).find('.notification-item').data('id') || $(this).data('id');
                        if (notifId) {
                            markSingleAsRead(notifId);
                        }
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching notifications:", error);
                let notificationList = $("#notification_list");
                notificationList.html('<div class="px-3 py-2 text-center text-danger">Error loading notifications</div>');
            }
        });
    }

    function getNotificationIcon(type) {
        switch(type) {
            case 'hr':
            case 'leave_request':
            case 'leave_status_change':
                return 'ki-solid ki-user-tick';
            case 'announcement':
                return 'ki-solid ki-messages';
            case 'system':
            default:
                return 'ki-solid ki-notification';
        }
    }

    function getNotificationLink(type, data) {
        switch(type) {
            case 'leave_request':
            case 'leave_status_change':
                return '/leave-requests';
            case 'announcement':
                // If we have announcement ID, link directly to it, otherwise to announcements list
                if (data && data.announcement_id) {
                    return `/announcements#announcement-${data.announcement_id}`;
                }
                return '/announcements';
            case 'system':
            case 'hr':
            default:
                return null; // No specific link
        }
    }

    function formatTimeAgo(dateString) {
        let date = new Date(dateString);
        let now = new Date();
        let diff = Math.floor((now - date) / 1000); // seconds

        if (diff < 60) return 'Just now';
        if (diff < 3600) return Math.floor(diff / 60) + ' minutes ago';
        if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
        if (diff < 604800) return Math.floor(diff / 86400) + ' days ago';
        
        return date.toLocaleDateString();
    }

    function markAllAsRead() {
        $.ajax({
            url: "/notifications/mark-as-read",
            type: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            success: function () {
                $("#notification_count").hide();
                fetchNotifications(); // Refresh the list
                showToast('Success', 'All notifications marked as read', 'success');
            },
            error: function() {
                showToast('Error', 'Failed to mark notifications as read', 'error');
            }
        });
    }

    function markSingleAsRead(notifId) {
        $.ajax({
            url: `/notifications/${notifId}/mark-as-read`,
            type: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            success: function () {
                fetchNotifications(); // Refresh the list
            },
            error: function() {
                console.error('Failed to mark notification as read');
            }
        });
    }

    function showToast(title, message, type) {
        const toastHtml = `
            <div style="position: fixed; top: 20px; right: 20px; z-index: 9999;
                        background: ${type === 'success' ? '#1BC5BD' : '#dc3545'};
                        color: white; padding: 15px 20px; border-radius: 4px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.3); max-width: 300px;"
                 id="notificationToast">
                <strong>${title}</strong><br>
                ${message}
            </div>
        `;
        $('body').append(toastHtml);
        setTimeout(function() {
            $('#notificationToast').fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }

    // Run fetchNotifications every 10 seconds
    setInterval(fetchNotifications, 10000);
    $(document).ready(fetchNotifications);

    // Toggle dropdown
    $("#notification_btn").click(function (e) {
        e.preventDefault();
        $("#notification_dropdown").toggle();
    });

    // Mark all as read button
    $(document).on('click', '#mark_all_read', function(e) {
        e.preventDefault();
        e.stopPropagation();
        markAllAsRead();
    });

    // Close dropdown when clicking outside
    $(document).click(function(e) {
        if (!$(e.target).closest('#notification_btn, #notification_dropdown').length) {
            $("#notification_dropdown").hide();
        }
    });
</script>