<nav id="topbar" class="bg-white shadow-sm sticky top-5 z-30 rounded-xl mt-5 mb-5 mr-5 border border-gray-100 ml-72-plus">
    <div class="px-6 py-5">
        <div class="flex items-center justify-between">
            <!-- Left: Logo + Main Menu Tabs -->
            <div class="flex items-center gap-8">                
                <!-- Main Menu Tabs (Horizontal) - Max 5 Main Menu -->
                <div class="flex items-center gap-1">
                    <!-- 1. Dashboard -->
                    <a href="#" data-category="dashboard" class="topbar-tab px-4 py-2 text-sm font-medium rounded-lg {{ request()->is('dashboard_new*') || request()->is('dashboards*') || request()->is('dashboard_v2*') || request()->is('asset_detail*') || request()->is('power_bi_dashboard*') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        Dashboard
                    </a>
                    
                    <!-- 2. Master Data -->
                    <a href="#" data-category="master-data" class="topbar-tab px-4 py-2 text-sm font-medium rounded-lg {{ request()->is('master-data*') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        Master Data
                    </a>
                    
                    <!-- 3. Stock -->
                    <a href="#" data-category="stock" class="topbar-tab px-4 py-2 text-sm font-medium rounded-lg {{ request()->is('stock*') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        Stock
                    </a>
                    
                    <!-- 4. Sales -->
                    <a href="#" data-category="sales" class="topbar-tab px-4 py-2 text-sm font-medium rounded-lg {{ request()->is('sales*') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        Sales
                    </a>
                    
                    <!-- 5. Human Resource -->
                    <a href="#" data-category="human-resource" class="topbar-tab px-4 py-2 text-sm font-medium rounded-lg {{ request()->is('human-resource*') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        Human Resource
                    </a>
                    
                    <!-- Others Dropdown (Sisanya) -->
                    <div class="relative">
                        <button id="others-dropdown-button" data-dropdown-toggle="others-dropdown" data-category="all" type="button" class="topbar-tab flex items-center gap-1 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">
                            Others
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        
                        <!-- Others Dropdown Menu -->
                        <div id="others-dropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-lg w-56 border border-gray-200">
                            <ul class="py-2 text-sm text-gray-700">
                                <!-- Purchase Order -->
                                <li>
                                    <a href="/purchase-order" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
                                        <i class="cft-standard-stroke cft-receipt text-gray-500"></i>
                                        <span>Purchase Order</span>
                                    </a>
                                </li>
                                
                                <!-- Reports -->
                                <li>
                                    <a href="/reports" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
                                        <i class="cft-standard-stroke cft-chart text-gray-500"></i>
                                        <span>Reports</span>
                                    </a>
                                </li>
                                
                                <!-- Finance -->
                                <li>
                                    <a href="/finance" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
                                        <i class="cft-standard-stroke cft-wallet text-gray-500"></i>
                                        <span>Finance</span>
                                    </a>
                                </li>
                                
                                <!-- Warehouse -->
                                <li>
                                    <a href="/warehouse" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
                                        <i class="cft-standard-stroke cft-package text-gray-500"></i>
                                        <span>Warehouse</span>
                                    </a>
                                </li>
                                
                                <li><hr class="my-2 border-gray-200"></li>
                                
                                <!-- Settings -->
                                <li>
                                    <a href="/settings" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
                                        <i class="cft-standard-stroke cft-settings text-gray-500"></i>
                                        <span>Settings</span>
                                    </a>
                                </li>
                                
                                <!-- Utilities -->
                                <li>
                                    <a href="/utilities" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
                                        <i class="cft-standard-stroke cft-tool text-gray-500"></i>
                                        <span>Utilities</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right: Notifications + User Profile + Store Button -->
            <div class="flex items-center gap-3">
                <!-- Notifications -->
                <div class="relative">
                    <button type="button" id="notification_btn" class="relative p-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors h-10 w-10">
                        <i class="cft-standard-solid cft-notification text-lg"></i>
                        <span id="notification_count" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                            0
                        </span>
                    </button>
                    
                    <!-- Notification Dropdown -->
                    <div id="notification_dropdown" class="hidden absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
                            <h6 class="font-semibold text-gray-900">Notifications</h6>
                            <button id="mark_all_read" class="text-xs text-red-600 hover:text-red-800 font-medium">
                                Mark all as read
                            </button>
                        </div>
                        <div id="notification_list" class="max-h-96 overflow-y-auto">
                            <p class="px-4 py-3 text-sm text-gray-500">Loading...</p>
                        </div>
                        <div class="border-t border-gray-200">
                            <a href="#" class="block text-center py-3 text-sm text-red-600 hover:bg-gray-50">
                                View All Notifications
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Store Button -->
                <a href="{{ url('data_stok') }}" id="load_user_store" class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-lg transition-colors whitespace-nowrap">
                </a>

                <!-- User Profile -->
                <button id="user-menu-button" data-dropdown-toggle="user-dropdown" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                    <span class="hidden md:inline text-sm text-gray-500 font-medium">Hi,</span>
                    <span class="hidden md:inline text-sm text-gray-900 font-semibold">{{ $data['user']->u_name }}</span>
                    @php
                        $userName = $data['user']->u_name;
                        $userInitial = substr($userName, 0, 1);
                    @endphp
                    <div class="w-8 h-8 bg-red-500 rounded-xl flex items-center justify-center text-white font-bold text-sm">
                        {{ $userInitial }}
                    </div>
                </button>
                
                <!-- User Dropdown menu -->
                <div id="user-dropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-lg w-48 border border-gray-200">
                    <div class="px-4 py-3">
                        <div class="text-sm font-semibold text-gray-900 truncate">{{ $data['user']->u_name }}</div>
                        <div class="text-xs text-gray-500 truncate">{{ $data['user']->u_email ?? 'No email' }}</div>
                    </div>
                    <ul class="py-2 text-sm text-gray-700">
                        <li>
                            <a href="/profile" class="flex items-center px-4 py-2 hover:bg-gray-100">
                                <i class="cft-standard-stroke cft-user mr-2 text-gray-500"></i>
                                Profile
                            </a>
                        </li>
                        <li>
                            <a href="/settings" class="flex items-center px-4 py-2 hover:bg-gray-100">
                                <i class="cft-standard-stroke cft-settings mr-2 text-gray-500"></i>
                                Settings
                            </a>
                        </li>
                    </ul>
                    <div class="py-2">
                        <a href="{{ route('logout') }}" class="flex items-center px-4 py-2 text-red-600 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Logout
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Notification Dropdown Toggle
    const notifBtn = document.getElementById('notification_btn');
    const notifDropdown = document.getElementById('notification_dropdown');
    
    if (notifBtn && notifDropdown) {
        notifBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notifDropdown.classList.toggle('hidden');
            
            // Fetch notifications when opening
            if (!notifDropdown.classList.contains('hidden')) {
                fetchNotifications();
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
                notifDropdown.classList.add('hidden');
            }
        });
    }
    
    // Fetch Notifications
    function fetchNotifications() {
        $.ajax({
            url: "/notifications",
            type: "GET",
            dataType: "json",
            success: function (data) {
                let notificationList = $("#notification_list");
                let notificationCount = $("#notification_count");

                notificationList.empty();

                if (data.count === 0) {
                    notificationList.append(`
                        <div class="px-4 py-8 text-center text-gray-500">
                            <i class="fa fa-bell-slash text-3xl mb-2 block"></i>
                            <p class="text-sm">No new notifications</p>
                        </div>
                    `);
                    notificationCount.addClass('hidden');
                } else {
                    $.each(data.notifications, function (index, notif) {
                        let icon = getNotificationIcon(notif.type);
                        let timeAgo = formatTimeAgo(notif.created_at);
                        let bgClass = notif.is_read ? 'bg-gray-50' : 'bg-white';
                        let notifLink = getNotificationLink(notif.type, notif.data);
                        
                        let notificationHtml = `
                            <div class="notification-item ${bgClass} border-b border-gray-100 hover:bg-gray-50 cursor-pointer px-4 py-3"
                                 data-id="${notif.id}" data-link="${notifLink}">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <i class="${icon} text-red-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-900 line-clamp-2">${notif.message}</p>
                                        <p class="text-xs text-gray-500 mt-1">${timeAgo}</p>
                                    </div>
                                    ${!notif.is_read ? '<div class="flex-shrink-0"><span class="w-2 h-2 bg-red-600 rounded-full block"></span></div>' : ''}
                                </div>
                            </div>
                        `;
                        
                        notificationList.append(notificationHtml);
                    });

                    notificationCount.text(data.count).removeClass('hidden');
                    
                    // Add click handlers
                    $('.notification-item').on('click', function() {
                        let notifId = $(this).data('id');
                        let notifLink = $(this).data('link');
                        
                        markSingleAsRead(notifId);
                        
                        if (notifLink) {
                            setTimeout(function() {
                                window.location.href = notifLink;
                            }, 200);
                        }
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching notifications:", error);
                $("#notification_list").html('<div class="px-4 py-3 text-center text-red-500 text-sm">Error loading notifications</div>');
            }
        });
    }
    
    function getNotificationIcon(type) {
        switch(type) {
            case 'hr':
            case 'leave_request':
            case 'leave_status_change':
                return 'fa fa-calendar-check';
            case 'purchase_order':
                return 'fa fa-shopping-cart';
            case 'stock':
            case 'inventory':
                return 'fa fa-boxes';
            case 'sales':
                return 'fa fa-cash-register';
            case 'customer':
                return 'fa fa-users';
            case 'system':
                return 'fa fa-cog';
            default:
                return 'fa fa-bell';
        }
    }
    
    function getNotificationLink(type, data) {
        try {
            if (typeof data === 'string') {
                data = JSON.parse(data);
            }
            
            switch(type) {
                case 'leave_request':
                case 'leave_status_change':
                    return data.link || '/leave_requests';
                case 'purchase_order':
                    return data.link || '/purchase_orders';
                case 'stock':
                case 'inventory':
                    return data.link || '/inventory';
                default:
                    return data.link || null;
            }
        } catch(e) {
            return null;
        }
    }
    
    function formatTimeAgo(dateString) {
        const now = new Date();
        const date = new Date(dateString);
        const seconds = Math.floor((now - date) / 1000);
        
        if (seconds < 60) return 'Just now';
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return minutes + ' min ago';
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return hours + ' hour' + (hours > 1 ? 's' : '') + ' ago';
        const days = Math.floor(hours / 24);
        if (days < 7) return days + ' day' + (days > 1 ? 's' : '') + ' ago';
        const weeks = Math.floor(days / 7);
        if (weeks < 4) return weeks + ' week' + (weeks > 1 ? 's' : '') + ' ago';
        const months = Math.floor(days / 30);
        return months + ' month' + (months > 1 ? 's' : '') + ' ago';
    }
    
    function markSingleAsRead(notifId) {
        $.ajax({
            url: "/notifications/" + notifId + "/read",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                fetchNotifications();
            }
        });
    }
    
    // Mark all as read
    $('#mark_all_read').on('click', function(e) {
        e.preventDefault();
        $.ajax({
            url: "/notifications/mark-all-read",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                fetchNotifications();
            }
        });
    });
    
    // Load user store
    $.ajax({
        type: 'GET',
        url: '{{ url("load_user_store") }}',
        dataType: 'json',
        success: function(data) {
            if (data.store) {
                $('#load_user_store').html('<i class="fa fa-store mr-2"></i>' + data.store);
            } else {
                $('#load_user_store').html('<i class="fa fa-store mr-2"></i>Store Not Set');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading store:', error);
            $('#load_user_store').html('<i class="fa fa-store mr-2"></i>Error');
        }
    });
    
    // Poll notifications every 30 seconds
    setInterval(function() {
        if ($('#notification_dropdown').hasClass('hidden')) {
            // Only fetch count if dropdown is closed
            $.ajax({
                url: "/notifications",
                type: "GET",
                dataType: "json",
                success: function(data) {
                    let notificationCount = $("#notification_count");
                    if (data.count > 0) {
                        notificationCount.text(data.count).removeClass('hidden');
                    } else {
                        notificationCount.addClass('hidden');
                    }
                }
            });
        }
    }, 30000);
    
    // Initial fetch
    fetchNotifications();
});
</script>

