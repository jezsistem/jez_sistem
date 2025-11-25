<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ProcessBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $jobData;

    public function __construct($jobData)
    {
        $this->jobData = $jobData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $job = $this->jobData;

//        $targets = DB::table('customers')
//            ->skip($job->last_offset)
//            ->take($job->batch_size)
//            ->get();

        $dummyPhones = [
            '6282197711866',
            '6285159011402',
        ];

        // Ambil batch sesuai batch_size dan last_offset
        $targets = array_slice($dummyPhones, $job->last_offset, $job->batch_size);

        foreach ($targets as $phone) {

            Http::post('http://localhost:3000/send-message', [
                'phone' => $phone,
                'message' => $job->message
            ]);
        }

        // Update offset
        $job->last_offset += count($targets);
        $job->save();

        // Jika sudah kirim semua dummy
        if ($job->last_offset >= count($dummyPhones)) {
            $job->status = 'completed';
            $job->save();
        }
    }
}
