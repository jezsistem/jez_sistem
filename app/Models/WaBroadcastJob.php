<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class WaBroadcastJob extends Model
{
    use HasFactory;

    protected $table = 'wa_broadcast_jobs';

    protected $fillable = [
        'job_name',
        'start_at',
        'end_at',
        'interval_hours',
        'batch_size',
        'message',
        'status',
        'last_offset',
        'next_run_at'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'next_run_at' => 'datetime',
        'last_offset' => 'integer',
        'interval_hours' => 'integer',
        'batch_size' => 'integer',
    ];

    protected $attributes = [
        'status' => 'pending',
        'last_offset' => 0,
    ];

    /**
     * Cek apakah job sudah selesai berdasarkan total target (harus diberikan dari caller).
     */
    public function isCompleted(int $totalTargets): bool
    {
        return $this->last_offset >= $totalTargets;
    }

    /**
     * Apakah job saat ini jatuh tempo untuk dijalankan
     */
    public function isDue(): bool
    {
        $now = Carbon::now();

        // jika next_run_at diset, pakai itu untuk pengecekan
        if ($this->next_run_at) {
            return $now->greaterThanOrEqualTo($this->next_run_at)
                && $now->between($this->start_at, $this->end_at);
        }

        // jika belum ada next_run_at, jadwalkan berdasarkan start/end
        return $now->between($this->start_at, $this->end_at);
    }

    /**
     * Set next run berdasarkan interval_hours
     */
    public function scheduleNextRun(): void
    {
        $this->next_run_at = Carbon::now()->addHours($this->interval_hours);
        $this->save();
    }

    /**
     * Increment offset setelah mengirim satu batch
     */
    public function incrementOffset(int $by = null): void
    {
        $by = $by ?? $this->batch_size;
        $this->last_offset = $this->last_offset + $by;
        $this->save();
    }

    /**
     * Mark job sebagai completed
     */
    public function markCompleted(): void
    {
        $this->status = 'completed';
        $this->save();
    }

    /**
     * Mark job sebagai running
     */
    public function markRunning(): void
    {
        $this->status = 'running';
        $this->save();
    }

    /**
     * Reset job menjadi pending (mis. ketika dibatalkan atau di-retry)
     */
    public function markPending(): void
    {
        $this->status = 'pending';
        $this->save();
    }
}