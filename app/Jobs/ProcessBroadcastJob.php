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

        $targets = DB::table('customers')
            ->skip($job->last_offset)
            ->take($job->batch_size)
            ->get();

        foreach ($targets as $t) {
            Http::post('http://localhost:3000/send-message', [
                'phone' => $t->cust_phone,
                'message' => $job->message
            ]);
        }

        // update offset
        $job->last_offset += $job->batch_size;
        $job->save();

        // jika sudah selesai semua
        if ($job->last_offset >= DB::table('customer_contacts')->count()) {
            $job->status = 'completed';
            $job->save();
        }
    }
}
