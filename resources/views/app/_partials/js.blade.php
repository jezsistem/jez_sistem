<script>
    var KTAppSettings = {
        "breakpoints": {
            "sm": 576,
            "md": 768,
            "lg": 992,
            "xl": 1200,
            "xxl": 1400
        },
        "colors": {
            "theme": {
                "base": {
                    "white": "#ffffff",
                    "primary": "#3699FF",
                    "secondary": "#E5EAEE",
                    "success": "#1BC5BD",
                    "info": "#8950FC",
                    "warning": "#FFA800",
                    "danger": "#F64E60",
                    "light": "#E4E6EF",
                    "dark": "#181C32"
                },
                "light": {
                    "white": "#ffffff",
                    "primary": "#E1F0FF",
                    "secondary": "#EBEDF3",
                    "success": "#C9F7F5",
                    "info": "#EEE5FF",
                    "warning": "#FFF4DE",
                    "danger": "#FFE2E5",
                    "light": "#F3F6F9",
                    "dark": "#D6D6E0"
                },
                "inverse": {
                    "white": "#ffffff",
                    "primary": "#ffffff",
                    "secondary": "#3F4254",
                    "success": "#ffffff",
                    "info": "#ffffff",
                    "warning": "#ffffff",
                    "danger": "#ffffff",
                    "light": "#464E5F",
                    "dark": "#ffffff"
                }
            },
            "gray": {
                "gray-100": "#F3F6F9",
                "gray-200": "#EBEDF3",
                "gray-300": "#E4E6EF",
                "gray-400": "#D1D3E0",
                "gray-500": "#B5B5C3",
                "gray-600": "#7E8299",
                "gray-700": "#5E6278",
                "gray-800": "#3F4254",
                "gray-900": "#181C32"
            }
        },
        "font-family": "Poppins"
    };
</script>
<script src="{{ asset('app') }}/assets/plugins/global/plugins.bundle.js"></script>
<script src="{{ asset('app') }}/assets/js/scripts.bundle.js?v1"></script>
<script src="{{ asset('app') }}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="{{ asset('cdn/jszip.min.js') }}"></script>
<script src="{{ asset('cdn/sweetalert.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('cdn/jquery.toast.min.js') }}"></script>
<script>
    // Global DataTables availability check and fix

    let route = window.location.pathname;
    console.log(route); // contoh: "/helper_backup"

    if (route !== "/helper_backup" && route !== "/helper_backup_v1") {
        function ensureDataTablesAvailable() {
            if (typeof $.fn.DataTable === 'undefined') {
                console.warn('DataTables not available, attempting to reload...');

                // Try to reload DataTables bundle
                var script = document.createElement('script');
                script.src = "{{ asset('app/assets/plugins/custom/datatables/datatables.bundle.js') }}";
                script.defer = true;

                script.onload = function() {
                    console.log('DataTables reloaded successfully');
                    $(document).trigger('datatables:loaded');
                };

                script.onerror = function() {
                    console.error('Failed to reload DataTables');
                };

                document.head.appendChild(script);
                return false;
            }
            return true;
        }

        $(document).ready(function() {
            if (!ensureDataTablesAvailable()) {
                $(document).on('datatables:loaded', function() {
                    console.log('DataTables is now available globally');
                });
            }
        });
    }



    const current = window.location.href;
    document.querySelectorAll("#kt_aside_menu a").forEach(function(elem) {
        if (elem.href === current) {
            elem.classList.add("active");
        }
    });

    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
    ];

    $(document).ready(function() {
        loadStore();
        clockUpdate();
        // getBalance();
    })

    function loadStore()
    {
        $.ajax({
            type: "GET",
            dataType: 'json',
            url: "{{ url('load_user_store')}}",
            success: function(r) {
                if (r.status == '200') {
                    $('#load_user_store').text(r.store);
                }
            }
        });
    }

    function getBalance() {
        $.ajax({
            type: "GET",
            dataType: 'json',
            url: "{{ url('get_upcloud_balance')}}",
            cache: false,
            contentType: false,
            processData: false,
            success: function(r) {
                if (r.status == '200') {
                    $('#upcloud_info_btn').text('$ ' + r.balance);
                    if (r.balance < 5.92) {
                        jQuery.noConflict();
                        $('#BalanceNotifModal').modal('show');
                        setTimeout(() => {
                            $('#BalanceNotifModal').modal('hide');
                        }, 5000);
                    }
                }
            }
        });
    }

    function infoBtn(str) {
        if (str == 'date') {
            swal('Tanggal', 'Tanggal pada hari ini', 'info');
        }
        if (str == 'upcloud') {
            swal('Saldo Server', 'Merupakan saldo server realtime saat ini, jangan sampai kehabisan agar server terus berjalan', 'info');
        }
    }

    function clockUpdate() {
        var date = new Date();
        $('.digital-clock').css({
            'color': '#fff',
            'text-shadow': '0 0 6px #ff0'
        });

        function addZero(x) {
            if (x < 10) {
                return x = '0' + x;
            } else {
                return x;
            }
        }

        function twelveHour(x) {
            if (x > 12) {
                return x = x - 12;
            } else if (x == 0) {
                return x = 12;
            } else {
                return x;
            }
        }

        var h = addZero(twelveHour(date.getHours()));
        var m = addZero(date.getMinutes());
        var s = addZero(date.getSeconds());
        var strDate = date.getDate() + "/" + monthNames[(date.getMonth())] + "/" + date.getFullYear();

        $('.date').text(strDate);
    }

    function toast(title, subtitle, type) {
        jQuery.toast({
            heading: title,
            text: subtitle,
            icon: type,
            loader: true,
            loaderBg: '#072544',
            position: 'top-right',
            stack: false,
            hideAfter: 1000
        });
    }

    function paidToast(title, subtitle, type) {
        jQuery.toast({
            heading: title,
            text: subtitle,
            icon: type,
            loader: true,
            loaderBg: '#072544',
            position: 'top-left',
            stack: false,
            hideAfter: 120000,
            onClick: function() {
                window.location.href = "{{ url('website_transaction') }}";
            }
        });
    }

    function confirmationToast(title, subtitle, type) {
        jQuery.toast({
            heading: title,
            text: subtitle,
            icon: type,
            loader: true,
            loaderBg: '#072544',
            position: 'bottom-left',
            stack: false,
            hideAfter: 120000,
            onClick: function() {
                window.location.href = "{{ url('konfirmasi') }}";
            }
        });
    }

    function crossOrderToast(title, subtitle, type) {
        jQuery.toast({
            heading: title,
            text: subtitle,
            icon: type,
            loader: true,
            loaderBg: '#072544',
            position: 'bottom-left',
            stack: false,
            hideAfter: 20000,
            onClick: function() {
                window.location.href = "{{ url('cross_order') }}";
            }
        });
    }

    function checkConfirmation() {
        $.ajax({
            type: "GET",
            dataType: 'json',
            url: "{{ url('check_web_confirmation')}}",
            cache: false,
            contentType: false,
            processData: false,
            success: function(r) {
                if (r.status == '200') {
                    if (r.total > 0) {
                        confirmationToast('Check Konfirmasi', 'Ada ' + r.total + ' konfirmasi menunggu', 'warning');
                    }
                }
            }
        });
    }

    function checkPaid() {
        $.ajax({
            type: "GET",
            dataType: 'json',
            url: "{{ url('check_web_paid')}}",
            cache: false,
            contentType: false,
            processData: false,
            success: function(r) {
                if (r.status == '200') {
                    if (r.total > 0) {
                        paidToast('Check Web Transaksi', 'Ada ' + r.total + ' invoice menunggu diprint', 'info');
                    }
                }
            }
        });
    }

    $(document).delegate('#f_password', 'submit', function(e) {
        e.preventDefault();
        var password = $('#password').val();
        var old_password = $('#old_password').val();
        var confirm_password = $('#confirm_password').val();
        if (password !== confirm_password) {
            swal('Konfirmasi Password', 'Konfirmasi Password Tidak Sama', 'warning');
            return false;
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {
                _password: password,
                _old_password: old_password,
            },
            dataType: 'json',
            url: "{{ url('change_password')}}",
            success: function(r) {
                jQuery.noConflict();
                if (r.status == '200') {
                    $('#ChangePasswordModal').modal('hide');
                    $('#f_password')[0].reset();
                    swal('Berhasil', r.message, 'success');
                } else {
                    swal('Gagal', r.message, 'warning');
                }
            }
        });
    });

    // Header Break Time Functions
    $(document).ready(function() {
        // Check current break status on page load
        checkHeaderBreakStatus();
        
        // Set up timer interval
        setInterval(updateHeaderTimer, 1000);
    });

    // Check current break status
    function checkHeaderBreakStatus() {
        $.get('{{ url("break-times/current") }}')
            .done(function(response) {
                if (response.success) {
                    $('#headerClockIn').hide();
                    $('#headerClockOutContainer').show();
                    $('#headerTimerContainer').show();
                    updateHeaderTimerDisplay(response.duration_minutes);
                } else {
                    $('#headerClockIn').show();
                    $('#headerClockOutContainer').hide();
                    $('#headerTimerContainer').hide();
                }
            })
            .fail(function() {
                $('#headerClockIn').show();
                $('#headerClockOutContainer').hide();
                $('#headerTimerContainer').hide();
            });
    }

    // Header Clock In
    $('#headerClockIn').click(function() {
        $.post('{{ url("break-times/clock-in") }}', {
            _token: '{{ csrf_token() }}',
            break_type: 'break_1'
        })
        .done(function(response) {
            if (response.success) {
                swal('Success', 'Break started successfully!', 'success');
                checkHeaderBreakStatus();
            } else {
                swal('Error', response.message, 'error');
            }
        })
        .fail(function(xhr) {
            let response = xhr.responseJSON;
            swal('Error', response ? response.message : 'Unknown error', 'error');
        });
    });

    // Header Clock Out
    $('#headerClockOut').click(function() {
        $.post('{{ url("break-times/clock-out") }}', {
            _token: '{{ csrf_token() }}',
            break_type: 'break_1'
        })
        .done(function(response) {
            if (response.success) {
                swal('Success', 'Break ended successfully!', 'success');
                checkHeaderBreakStatus();
            } else {
                swal('Error', response.message, 'error');
            }
        })
        .fail(function(xhr) {
            let response = xhr.responseJSON;
            swal('Error', response ? response.message : 'Unknown error', 'error');
        });
    });

    // Header Timer functions
    let headerTimerStartTime = null;

    function updateHeaderTimer() {
        if (headerTimerStartTime) {
            let now = new Date();
            let diff = Math.floor((now - headerTimerStartTime) / 1000);
            let minutes = Math.floor(diff / 60);
            let seconds = diff % 60;
            $('#headerTimerDisplay').text(sprintf('%02d:%02d', minutes, seconds));
        }
    }

    function updateHeaderTimerDisplay(durationMinutes) {
        let minutes = Math.floor(durationMinutes / 60);
        let seconds = durationMinutes % 60;
        $('#headerTimerDisplay').text(sprintf('%02d:%02d', minutes, seconds));
        headerTimerStartTime = new Date(Date.now() - (durationMinutes * 60 * 1000));
    }

    function sprintf(format, ...args) {
        return format.replace(/%(\d*)d/g, function(match, width) {
            return args.shift().toString().padStart(width || 0, '0');
        });
    }

    // Sub-menu functionality
    $(document).ready(function() {
        // Handle sub-menu toggle (only for main accordion items)
        $('.menu-item.menu-accordion > .menu-link').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $menuItem = $(this).closest('.menu-item');
            var $subMenu = $menuItem.find('.menu-sub-accordion');
            
            // Close other open menus
            $('.menu-item.menu-accordion').not($menuItem).removeClass('menu-item-open');
            
            // Toggle current menu
            $menuItem.toggleClass('menu-item-open');
            
            // Smooth animation for sub-menu
            if ($menuItem.hasClass('menu-item-open')) {
                $subMenu.slideDown(300);
            } else {
                $subMenu.slideUp(300);
            }
        });
        
        // Handle sub-menu item clicks (prevent event bubbling)
        $('.menu-subnav .menu-link').on('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            
            // Remove active class from all sub-menu items
            $('.menu-subnav .menu-link').removeClass('active');
            // Add active class to clicked item
            $(this).addClass('active');
            
            // Navigate to the href
            var href = $(this).attr('href');
            if (href && href !== 'javascript:;') {
                window.location.href = href;
            }
        });
        
        // Highlight active sub-menu item
        var currentPath = window.location.pathname;
        var activeFound = false;
        
        // First pass: find exact matches
        $('.menu-subnav .menu-link').each(function() {
            var href = $(this).attr('href');
            if (href && href !== 'javascript:;') {
                var urlPath = href.replace(window.location.origin, '');
                if (currentPath === urlPath) {
                    $(this).addClass('active');
                    $(this).closest('.menu-item.menu-accordion').addClass('menu-item-open');
                    $(this).closest('.menu-sub-accordion').show();
                    activeFound = true;
                    return false; // Break the loop
                }
            }
        });
        
        // Second pass: if no exact match, find partial matches (for backward compatibility)
        if (!activeFound) {
            $('.menu-subnav .menu-link').each(function() {
                var href = $(this).attr('href');
                if (href && href !== 'javascript:;') {
                    var urlPath = href.replace(window.location.origin, '');
                    var pathSegments = urlPath.split('/').filter(segment => segment.length > 0);
                    var currentSegments = currentPath.split('/').filter(segment => segment.length > 0);
                    
                    // Check if current path starts with the menu path
                    var isMatch = pathSegments.every((segment, index) => 
                        currentSegments[index] === segment
                    );
                    
                    if (isMatch && pathSegments.length > 0) {
                        $(this).addClass('active');
                        $(this).closest('.menu-item.menu-accordion').addClass('menu-item-open');
                        $(this).closest('.menu-sub-accordion').show();
                        activeFound = true;
                        return false; // Break the loop
                    }
                }
            });
        }
        
        // Highlight active main menu item
        $('.menu-item.menu-accordion').each(function() {
            var $subMenu = $(this).find('.menu-subnav .menu-link.active');
            if ($subMenu.length > 0) {
                $(this).addClass('menu-item-open');
                $(this).find('.menu-sub-accordion').show();
            }
        });
    });
</script>