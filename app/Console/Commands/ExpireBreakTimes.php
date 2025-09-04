<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\BreakTime;

class ExpireBreakTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'break:expire {--force : Force expire all active breaks}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically expire break times that have been active for too long';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting break time expiration check...');
        
        $today = date('Y-m-d');
        $now = Carbon::now();
        
        // Get all active breaks
        $activeBreaks = DB::table('break_times as bt')
            ->leftJoin('daily_schedules as ds', 'bt.user_id', '=', 'ds.user_id')
            ->leftJoin('shift_codes as sc', 'ds.sc_id', '=', 'sc.id')
            ->where('bt.bt_status', 'active')
            ->where('bt.bt_date', '<=', $today)
            ->select([
                'bt.id',
                'bt.user_id',
                'bt.bt_date',
                'bt.bt_start_time',
                'bt.bt_type',
                'sc.sc_type'
            ])
            ->get();
        
        $this->info("Found {$activeBreaks->count()} active breaks to check");
        
        $expiredCount = 0;
        $forceExpiredCount = 0;
        
        foreach ($activeBreaks as $break) {
            $startTime = Carbon::parse($break->bt_start_time);
            $elapsedMinutes = $startTime->diffInMinutes($now);
            
            // Get break allowance for this user type
            $breakTime = new BreakTime();
            $breakAllowance = $breakTime->getBreakAllowance($break->sc_type ?? 'Part Time');
            
            // Determine max duration for this user type
            $maxDuration = 30; // Default
            if (isset($breakAllowance['break_1'])) {
                $maxDuration = $breakAllowance['break_1']['duration'];
            }
            
            // Check if break should be expired
            $shouldExpire = false;
            $reason = '';
            
            // Rule 1: Break exceeds max duration by 2x (e.g., 60 min break for 30 min allowance)
            if ($elapsedMinutes > ($maxDuration * 2)) {
                $shouldExpire = true;
                $reason = "Exceeded max duration by 2x (Max: {$maxDuration}m, Elapsed: {$elapsedMinutes}m)";
            }
            
            // Rule 2: Break has been active for more than 4 hours (240 minutes)
            elseif ($elapsedMinutes > 240) {
                $shouldExpire = true;
                $reason = "Break active for more than 4 hours (Elapsed: {$elapsedMinutes}m)";
            }
            
            // Rule 3: Break from previous days
            elseif ($break->bt_date < $today) {
                $shouldExpire = true;
                $reason = "Break from previous day ({$break->bt_date})";
            }
            
            // Force expire if --force flag is used
            if ($this->option('force')) {
                $shouldExpire = true;
                $reason = "Force expired by command";
            }
            
            if ($shouldExpire) {
                // Calculate final duration
                $finalDuration = $elapsedMinutes;
                
                // Update break record
                $updated = DB::table('break_times')
                    ->where('id', $break->id)
                    ->update([
                        'bt_end_time' => $now->format('H:i:s'),
                        'bt_duration_minutes' => $finalDuration,
                        'bt_status' => 'expired',
                        'bt_notes' => "AUTO-EXPIRED: {$reason}",
                        'updated_by' => 'system',
                        'updated_at' => $now
                    ]);
                
                if ($updated) {
                    if ($this->option('force')) {
                        $forceExpiredCount++;
                        $this->warn("Force expired break ID {$break->id} for user {$break->user_id}");
                    } else {
                        $expiredCount++;
                        $this->info("Expired break ID {$break->id} for user {$break->user_id} - {$reason}");
                    }
                    
                    // Log the expiration
                    \Log::info('Break auto-expired', [
                        'break_id' => $break->id,
                        'user_id' => $break->user_id,
                        'break_date' => $break->bt_date,
                        'start_time' => $break->bt_start_time,
                        'end_time' => $now->format('H:i:s'),
                        'duration_minutes' => $finalDuration,
                        'reason' => $reason,
                        'shift_type' => $break->sc_type,
                        'max_duration' => $maxDuration
                    ]);
                }
            }
        }
        
        $this->info("Expiration check completed!");
        $this->info("Auto-expired: {$expiredCount} breaks");
        if ($this->option('force')) {
            $this->info("Force expired: {$forceExpiredCount} breaks");
        }
        
        return 0;
    }
}
