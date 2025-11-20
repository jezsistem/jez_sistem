<?php

namespace App\Console;

use App\Jobs\ProcessBroadcastJob;
use App\Models\WaBroadcastJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {

            $jobs = WaBroadcastJob::where('status', '!=', 'completed')
                ->get();

            foreach ($jobs as $job) {

                // cek apakah sudah masuk jadwal
                if (now()->between($job->start_at, $job->end_at)) {

                    // cek apakah waktunya kirim
                    if ($job->next_run_at <= now()) {

                        dispatch(new ProcessBroadcastJob($job));

                        // set next run
                        $job->next_run_at = now()->addHours($job->interval_hours);
                        $job->status = 'running';
                        $job->save();
                    }
                }
            }

        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
