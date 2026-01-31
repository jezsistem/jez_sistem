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
        loadCurrentBreakList();
    });
    
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

// Timer variables
var timerStartTime = null;
var timerInterval = null;
var breakDurationMinutes = 0;
var countdownEndTime = null;

// Load break allowance
function loadBreakAllowance() {
    $.ajax({
        url: "{{ route('break-times.allowance') }}",
        type: 'GET',
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                var text = response.completed_breaks + '/' + response.break_allowance + ' breaks (' + response.shift_type + ')';
                $('#allowanceText').text(text);
            } else {
                $('#allowanceText').text(response.message || 'No schedule found for today');
            }
        },
        error: function(xhr, status, error) {
            if (xhr.status === 302) {
                $('#allowanceText').text('Please login to view break allowance');
            } else {
                $('#allowanceText').text('Error loading allowance');
            }
        }
    });
}

// Load current break status
function loadCurrentBreakStatus() {
    $.ajax({
        url: "{{ route('break-times.current') }}",
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
                breakDurationMinutes = response.total_duration_minutes;
                setBreakActiveState(response.duration_minutes, response.total_duration_minutes);
                
                var remainingMinutes = response.remaining_minutes;
                if (remainingMinutes > 0) {
                    var endTime = new Date();
                    endTime.setMinutes(endTime.getMinutes() + remainingMinutes);
                    countdownEndTime = endTime;
                    startCountdown();
                }
            } else {
                setBreakInactiveState();
            }
        },
        error: function() {
            setBreakInactiveState();
        }
    });
}

// Set break active state
function setBreakActiveState(durationMinutes, totalDurationMinutes) {
    var button = $('#mainBreakButton');
    var buttonText = $('#breakButtonText');
    var timer = $('#breakTimer');
    var breakTypeDisplay = $('#breakTypeDisplay');
    var durationDisplay = $('#durationDisplay');
    
    button.addClass('break-active bg-red-700 hover:bg-red-800').removeClass('bg-white');
    buttonText.text('End Break');
    timer.removeClass('hidden');
    
    var hours = Math.floor(durationMinutes / 60);
    var minutes = durationMinutes % 60;
    $('#timerDisplay').text(sprintf('%02d:%02d', hours, minutes));
    
    var totalHours = Math.floor(totalDurationMinutes / 60);
    var totalMins = totalDurationMinutes % 60;
    durationDisplay.text(sprintf('%02d:%02d', totalHours, totalMins));
    
    breakTypeDisplay.text('Break Time');
}

// Set break inactive state
function setBreakInactiveState() {
    var button = $('#mainBreakButton');
    var buttonText = $('#breakButtonText');
    var timer = $('#breakTimer');
    
    button.removeClass('break-active bg-red-700 hover:bg-red-800').addClass('bg-white');
    buttonText.text('Start Break');
    timer.addClass('hidden');
    
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
    countdownEndTime = null;
}

// Start countdown timer
function startCountdown() {
    if (timerInterval) {
        clearInterval(timerInterval);
    }
    
    timerInterval = setInterval(function() {
        if (!countdownEndTime) {
            clearInterval(timerInterval);
            return;
        }
        
        var now = new Date();
        var diff = countdownEndTime - now;
        
        if (diff <= 0) {
            clearInterval(timerInterval);
            $('#timerDisplay').text('00:00');
            return;
        }
        
        var minutes = Math.floor(diff / 60000);
        var seconds = Math.floor((diff % 60000) / 1000);
        $('#timerDisplay').text(sprintf('%02d:%02d', minutes, seconds));
    }, 1000);
}

// Start break
function startBreak() {
    $.ajax({
        url: "{{ route('break-times.clock-in') }}",
        type: 'POST',
        data: {
            break_type: 'break_1'
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Break Started',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                breakDurationMinutes = response.duration_minutes;
                setBreakActiveState(0, response.duration_minutes);
                
                var endTime = new Date();
                endTime.setMinutes(endTime.getMinutes() + response.duration_minutes);
                countdownEndTime = endTime;
                startCountdown();
                
                loadBreakAllowance();
                loadCurrentBreakList();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to start break'
                });
            }
        },
        error: function(xhr) {
            var message = 'Failed to start break';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });
        }
    });
}

// End break
function endBreak() {
    $.ajax({
        url: "{{ route('break-times.clock-out') }}",
        type: 'POST',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Break Ended',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                setBreakInactiveState();
                loadBreakAllowance();
                loadCurrentBreakList();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to end break'
                });
            }
        },
        error: function(xhr) {
            var message = 'Failed to end break';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });
        }
    });
}

// Load current break list
function loadCurrentBreakList() {
    var divisionId = $('#divisionFilter').val();
    
    $.ajax({
        url: "{{ route('break-times.current-list') }}",
        type: 'GET',
        data: {
            division_id: divisionId
        },
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        success: function(response) {
            var container = $('#currentBreakList');
            container.empty();
            
            if (response.success && response.data && response.data.length > 0) {
                response.data.forEach(function(item) {
                    var breakItem = $('<div>').addClass('p-3 border-b border-gray-200 last:border-b-0');
                    breakItem.html(`
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium text-gray-900">${item.user_name || '-'}</div>
                                <div class="text-sm text-gray-500">${item.division_name || '-'}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900">${item.duration_display || '-'}</div>
                                <div class="text-xs text-gray-500">${item.start_time || '-'}</div>
                            </div>
                        </div>
                    `);
                    container.append(breakItem);
                });
            } else {
                container.html('<div class="text-center text-gray-500 py-4">No one is currently on break</div>');
            }
        },
        error: function() {
            $('#currentBreakList').html('<div class="text-center text-red-500 py-4">Error loading break list</div>');
        }
    });
}

// Helper function for sprintf
function sprintf(format) {
    var args = Array.prototype.slice.call(arguments, 1);
    var i = 0;
    return format.replace(/%[sdj%]/g, function(match) {
        if (match === '%%') return '%';
        var type = match.charAt(1);
        var arg = args[i++];
        if (type === 's') return String(arg);
        if (type === 'd') return Number(arg);
        if (type === 'j') return JSON.stringify(arg);
        return match;
    });
}
</script>
