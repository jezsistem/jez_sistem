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
        // Scheduler WA Broadcast Job
        $schedule->call(function () {

            // Ambil semua job yang belum selesai
            $jobs = WaBroadcastJob::where('status', '!=', 'completed')->get();

            foreach ($jobs as $job) {

                // Pastikan job sudah masuk waktu aktif
                if (now()->between($job->start_at, $job->end_at)) {

                    // Pastikan waktunya mengirim
                    if ($job->next_run_at <= now()) {

                        // Jalankan job broadcast
                        dispatch(new ProcessBroadcastJob($job));

                        // Set jadwal berikutnya
                        $job->next_run_at = now()->addHours($job->interval_hours);
                        $job->status = 'running';
                        $job->save();
                    }
                }
            }

        })->everyMinute(); // dijalankan setiap menit
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