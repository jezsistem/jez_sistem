<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Load break allowance
        loadBreakAllowance();
        
        // Load current break status
        loadCurrentBreakStatus();
        
        // Load current break list
        loadCurrentBreakList();
        
        // Set up auto-refresh for current break list
        setInterval(loadCurrentBreakList, 30000); // Refresh every 30 seconds
        
        // Add division filter change event
        $('#divisionFilter').on('change', function() {
            console.log('Division filter changed to:', $(this).val());
            loadCurrentBreakList();
        });
        
        // Test initial load
        console.log('Initial division filter value:', $('#divisionFilter').val());
        
        // Main break button click handler
        $('#mainBreakButton').click(function() {
            var button = $(this);
            var isBreakActive = button.hasClass('break-active');
            
            if (isBreakActive) {
                // End break
                endBreak();
            } else {
                // Start break
                startBreak();
            }
        });
    });
    
    // Load break allowance
    function loadBreakAllowance() {
        $.ajax({
            url: "{{ route('break-times-backup.allowance') }}",
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    var text = response.completed_breaks + '/' + response.break_allowance + ' backups (' + response.shift_type + ')';
                    $('#allowanceText').text(text);
                } else {
                    $('#allowanceText').text(response.message || 'No schedule found for today');
                }
            },
            error: function(xhr, status, error) {
                console.log('Break allowance error:', xhr.responseText);
                if (xhr.status === 302) {
                    $('#allowanceText').text('Please login to view break allowance');
                } else {
                    $('#allowanceText').text('Error loading allowance');
                }
            }
        });
    }
    
    // Timer variables
    var timerStartTime = null;
    var timerInterval = null;
    var breakDurationMinutes = 0;
    var countdownEndTime = null;
    
    // Load current break status
    function loadCurrentBreakStatus() {
        $.ajax({
            url: "{{ route('break-times-backup.current') }}",
            type: 'GET',
            data: {
                user_nip: "{{ Auth::check() ? Auth::user()->u_nip : '25040202' }}"
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    // User is on break
                    breakDurationMinutes = response.total_duration_minutes;
                    setBreakActiveState(response.duration_minutes, response.total_duration_minutes);
                    
                    // Restore countdown timer
                    var remainingMinutes = response.remaining_minutes;
                    if (remainingMinutes <= 0) {
                        // Break time is over, show notification
                        $('#timerDisplay').text('00:00').addClass('text-white');
                        swal({
                            title: 'Break Time Over!',
                            text: 'Your break time has ended ' + Math.abs(remainingMinutes) + ' minutes ago. Please end your break.',
                            icon: 'warning',
                            buttons: {
                                end: {
                                    text: "End Break Now",
                                    value: "end",
                                    className: "btn-danger"
                                }
                            }
                        }).then((value) => {
                            if (value === "end") {
                                endBreak();
                            }
                        });
                    } else {
                        // Continue countdown - set countdown time and start timer
                        countdownEndTime = new Date().getTime() + (remainingMinutes * 60 * 1000);
                        
                        // Clear existing interval if any
                        if (timerInterval) {
                            clearInterval(timerInterval);
                        }
                        
                        // Start timer interval
                        timerInterval = setInterval(updateTimer, 1000);
                        
                        // Update timer immediately
                        updateTimer();
                        
                        console.log('Countdown restored with remaining time:', remainingMinutes, 'minutes');
                    }
                } else {
                    // User is not on break
                    setBreakInactiveState();
                }
            },
            error: function(xhr, status, error) {
                console.log('Current break status error:', xhr.responseText);
                if (xhr.status === 302) {
                    $('#allowanceText').text('Please login to view break status');
                } else {
                    setBreakInactiveState();
                }
            }
        });
    }
    
    // Load current break list
    function loadCurrentBreakList() {
        var divisionId = $('#divisionFilter').val();
        
        // Debug logging
        console.log('Loading current break list with division filter:', {
            divisionId: divisionId,
            divisionIdType: typeof divisionId,
            isEmpty: divisionId === '',
            isNull: divisionId === null,
            isUndefined: divisionId === undefined
        });
        
        // Log the data being sent
        var requestData = {
            division_id: divisionId
        };
        console.log('Request data being sent:', requestData);
        
        $.ajax({
            url: "{{ route('break-times-backup.current-list') }}",
            type: 'GET',
            data: requestData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                console.log('AJAX response received:', response);
                console.log('Response type:', typeof response);
                
                // Check if response is HTML (session expired/redirect)
                if (typeof response === 'string' && response.includes('<script>')) {
                    console.warn('Received HTML response instead of JSON - session likely expired');
                    $('#currentBreakList').html(
                        '<div class="text-center p-4 text-warning">' +
                            '<i class="fas fa-exclamation-triangle"></i> Session expired<br>' +
                            '<small>Please refresh the page to continue</small>' +
                        '</div>'
                    );
                    return;
                }
                
                // Try to parse JSON if it's a string
                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        console.error('Failed to parse response as JSON:', e);
                        $('#currentBreakList').html(
                            '<div class="text-center p-4 text-danger">' +
                                '<i class="fas fa-exclamation-triangle"></i> Invalid response format' +
                            '</div>'
                        );
                        return;
                    }
                }
                
                if (response.success && response.data && response.data.length > 0) {
                    var html = '';
                    response.data.forEach(function(item) {
                        var duration = item.duration_minutes ? Math.floor(item.duration_minutes) + ' min' : '0 min';
                        html += '<div class="d-flex align-items-center px-4 py-3 border-bottom">' +
                                '<div class="symbol symbol-40 symbol-light-warning mr-3">' +
                                    '<span class="symbol-label">' +
                                        '<i class="ki-outline ki-like text-warning"></i>' +
                                    '</span>' +
                                '</div>' +
                                '<div class="d-flex flex-column flex-grow-1">' +
                                    '<div class="text-dark-75 font-weight-bold font-size-sm">' + item.user_name + '</div>' +
                                    '<div class="text-muted font-size-xs">' + 
                                        (item.division_name || 'No Division') + ' • ' + 
                                        (item.shift_code || 'No Shift') + ' • ' + 
                                        item.start_time + 
                                    '</div>' +
                                '</div>' +
                                '<div class="text-right">' +
                                    '<span class="label label-light-warning label-inline font-weight-bold">' + 
                                        duration + 
                                    '</span>' +
                                '</div>' +
                            '</div>';
                    });
                    $('#currentBreakList').html(html);
                } else if (response.success === false) {
                    // Handle authentication or other errors
                    $('#currentBreakList').html(
                        '<div class="text-center p-4 text-warning">' +
                            '<i class="fas fa-exclamation-triangle"></i> ' + (response.message || 'Unable to load data') +
                        '</div>'
                    );
                } else {
                    // No data
                    $('#currentBreakList').html(
                        '<div class="text-center p-4 text-muted">' +
                            '<i class="ki-outline ki-profile-circle text-muted" style="font-size: 2rem;"></i>' +
                            '<div class="mt-2">No one is currently on break</div>' +
                        '</div>'
                    );
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading current break list:', error);
                console.error('Status:', status);
                console.error('XHR status code:', xhr.status);
                console.error('XHR response type:', typeof xhr.responseText);
                console.error('XHR response preview:', xhr.responseText ? xhr.responseText.substring(0, 200) : 'empty');
                
                var errorMessage = 'Error loading data';
                
                // Handle authentication redirect (when server returns HTML instead of JSON)
                if (xhr.status === 200 && xhr.responseText && xhr.responseText.includes('<script>')) {
                    errorMessage = 'Session expired - please refresh the page';
                    console.warn('Server returned HTML instead of JSON - likely authentication redirect');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 401) {
                    errorMessage = 'Authentication required - please login';
                } else if (xhr.status === 419) {
                    errorMessage = 'CSRF token expired - please refresh the page';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error';
                } else if (status === 'parsererror') {
                    errorMessage = 'Invalid response format - please refresh the page';
                }
                
                $('#currentBreakList').html(
                    '<div class="text-center p-4 text-danger">' +
                        '<i class="fas fa-exclamation-triangle"></i> ' + errorMessage +
                        '<br><small class="text-muted mt-2">Error code: ' + xhr.status + '</small>' +
                    '</div>'
                );
            }
        });
    }
    
    // Helper function for sprintf
    function sprintf(format, ...args) {
        return format.replace(/%(\d*)d/g, function(match, width) {
            return args.shift().toString().padStart(width || 0, '0');
        });
    }
    
    // Set break active state (user is on break)
    function setBreakActiveState(durationMinutes, totalDurationMinutes) {
        var button = $('#mainBreakButton');
        var timer = $('#breakTimer');
        var buttonText = $('#breakButtonText');
        
        // Update button state
        button.removeClass('btn-white').addClass('btn-red break-active');
        buttonText.html('<i class="fas fa-stop"></i> End Backup');
        
        // Show timer
        timer.show();
        
        // Set initial duration
        breakDurationMinutes = totalDurationMinutes || 0;
        
        console.log('Break state set to ACTIVE with duration:', durationMinutes, 'minutes');
    }
    
    // Set break inactive state (user is not on break)
    function setBreakInactiveState() {
        var button = $('#mainBreakButton');
        var timer = $('#breakTimer');
        var buttonText = $('#breakButtonText');
        
        // Update button state
        button.removeClass('btn-danger break-active').addClass('btn-white');
        buttonText.html('<i class="fas fa-play"></i> Start Backup');
        
        // Hide timer
        timer.hide();
        
        // Clear timer variables
        timerStartTime = null;
        countdownEndTime = null;
        breakDurationMinutes = 0;
        
        // Clear interval
        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
        }
        
        console.log('Break state set to INACTIVE');
    }
    
    // Start break function
    function startBreak() {
        console.log('Starting break...');
        
        var userNip = "{{ Auth::check() ? Auth::user()->u_nip : '25040202' }}";
        
        $.ajax({
            url: "{{ route('break-times-backup.start') }}",
            type: 'POST',
            data: {
                user_nip: userNip,
                break_type: 'backup_1' // Default backup type
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    // Set break active state
                    setBreakActiveState(0, response.break_duration || 60);
                    
                    // Start timer
                    timerStartTime = new Date();
                    countdownEndTime = new Date().getTime() + ((response.break_duration || 60) * 60 * 1000);
                    
                    // Clear existing interval if any
                    if (timerInterval) {
                        clearInterval(timerInterval);
                    }
                    
                    // Start timer interval
                    timerInterval = setInterval(updateTimer, 1000);
                    
                    // Update timer immediately
                    updateTimer();
                    
                    // Refresh current break list
                    loadCurrentBreakList();
                    
                    // Show success message
                    swal({
                        title: 'Backup Started!',
                        text: 'Your backup has started. Thanks for your backup!',
                        icon: 'success',
                        timer: 2000,
                        buttons: false
                    });
                } else {
                    swal('Error', response.message || 'Failed to start backup', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error starting break:', error);
                swal('Error', 'Failed to start backup. Please try again.', 'error');
            }
        });
    }
    
    // End break function
    function endBreak() {
        console.log('Ending break...');
        
        var userNip = "{{ Auth::check() ? Auth::user()->u_nip : '25040202' }}";
        
        $.ajax({
            url: "{{ route('break-times-backup.end') }}",
            type: 'POST',
            data: {
                user_nip: userNip
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    // Set break inactive state
                    setBreakInactiveState();
                    
                    // Refresh current break list
                    loadCurrentBreakList();
                    
                    // Show success message
                    swal({
                        title: 'Break Ended!',
                        text: 'Your break has ended. Welcome back!',
                        icon: 'success',
                        timer: 2000,
                        buttons: false
                    });
                } else {
                    swal('Error', response.message || 'Failed to end break', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error ending break:', error);
                swal('Error', 'Failed to end break. Please try again.', 'error');
            }
        });
    }
    
    // Update timer function
    function updateTimer() {
        if (!countdownEndTime) return;
        
        var now = new Date().getTime();
        var distance = countdownEndTime - now;
        
        console.log('Timer update - distance:', distance, 'ms, remaining:', Math.ceil(distance / (1000 * 60)), 'minutes');
        
        if (distance <= 0) {
            // Time's up
            $('#timerDisplay').text('00:00').addClass('text-white');
            $('#durationDisplay').text('Time\'s up!');
            
            // Clear interval
            if (timerInterval) {
                clearInterval(timerInterval);
                timerInterval = null;
            }
            
            // Show notification
            swal({
                title: 'Break Time Over!',
                text: 'Your break time has ended. Please end your break.',
                icon: 'warning',
                buttons: {
                    end: {
                        text: "End Break Now",
                        value: "end",
                        className: "btn-danger"
                    }
                }
            }).then((value) => {
                if (value === "end") {
                    endBreak();
                }
            });
            
            return;
        }
        
        // Calculate remaining time
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        // Update display
        var timeString = sprintf('%02d:%02d', minutes, seconds);
        $('#timerDisplay').text(timeString);
        
        // Update duration display
        var remainingMinutes = Math.ceil(distance / (1000 * 60));
        $('#durationDisplay').text(remainingMinutes + ' min remaining');
        
        console.log('Timer display updated:', timeString, 'remaining:', remainingMinutes, 'minutes');
    }
</script>
