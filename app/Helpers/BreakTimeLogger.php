<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BreakTimeLogger
{
    /**
     * Log duplicate entry error untuk break time
     *
     * @param \Exception $exception
     * @param array $data
     * @param string $operation
     * @return void
     */
    public static function logDuplicateError(\Exception $exception, array $data, string $operation = 'start_break')
    {
        $errorMessage = $exception->getMessage();
        
        // Extract informasi dari error message
        $duplicateKey = null;
        $duplicateValue = null;
        
        if (preg_match("/Duplicate entry '([^']+)' for key '([^']+)'/", $errorMessage, $matches)) {
            $duplicateValue = $matches[1];
            $duplicateKey = $matches[2];
        }

        // Ambil informasi user
        $userInfo = null;
        if (isset($data['user_id'])) {
            $userInfo = DB::table('users')
                ->select('id', 'u_name', 'u_nip')
                ->where('id', $data['user_id'])
                ->first();
        }

        // Ambil record yang sudah ada
        $existingRecord = null;
        if (isset($data['user_id']) && isset($data['bt_date']) && isset($data['bt_type'])) {
            $existingRecord = DB::table('break_times')
                ->where('user_id', $data['user_id'])
                ->where('bt_date', $data['bt_date'])
                ->where('bt_type', $data['bt_type'])
                ->first();
        }

        // Log error dengan detail lengkap
        Log::error('🚨 BREAK TIME DUPLICATE ENTRY ERROR', [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'operation' => $operation,
            'error_message' => $errorMessage,
            'duplicate_key' => $duplicateKey,
            'duplicate_value' => $duplicateValue,
            'user_info' => $userInfo,
            'attempted_data' => $data,
            'existing_record' => $existingRecord,
            'request_info' => [
                'url' => request()->fullUrl() ?? 'N/A',
                'method' => request()->method() ?? 'N/A',
                'ip' => request()->ip() ?? 'N/A',
                'user_agent' => request()->userAgent() ?? 'N/A'
            ]
        ]);

        // Log summary untuk monitoring mudah
        Log::warning('📊 BREAK TIME DUPLICATE SUMMARY', [
            'user_id' => $data['user_id'] ?? 'unknown',
            'user_name' => $userInfo->u_name ?? 'unknown',
            'user_nip' => $userInfo->u_nip ?? 'unknown',
            'date' => $data['bt_date'] ?? 'unknown',
            'break_type' => $data['bt_type'] ?? 'unknown',
            'existing_record_id' => $existingRecord->id ?? null,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Log operasi break time yang berhasil
     *
     * @param string $operation
     * @param mixed $result
     * @param array $data
     * @return void
     */
    public static function logSuccess(string $operation, $result, array $data = [])
    {
        Log::info("✅ BREAK TIME SUCCESS: {$operation}", [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'operation' => $operation,
            'result' => $result,
            'data' => $data,
            'user_id' => auth()->id() ?? 'system',
            'user_name' => auth()->user()->u_name ?? 'system'
        ]);
    }

    /**
     * Log validasi yang gagal
     *
     * @param string $operation
     * @param string $reason
     * @param array $data
     * @return void
     */
    public static function logValidationFailure(string $operation, string $reason, array $data = [])
    {
        Log::warning("⚠️ BREAK TIME VALIDATION FAILED: {$operation}", [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'operation' => $operation,
            'reason' => $reason,
            'data' => $data,
            'user_id' => auth()->id() ?? 'system',
            'user_name' => auth()->user()->u_name ?? 'system'
        ]);
    }

    /**
     * Log error sistem umum
     *
     * @param string $operation
     * @param \Exception $exception
     * @param array $data
     * @return void
     */
    public static function logSystemError(string $operation, \Exception $exception, array $data = [])
    {
        Log::error("❌ BREAK TIME SYSTEM ERROR: {$operation}", [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'operation' => $operation,
            'error_message' => $exception->getMessage(),
            'error_code' => $exception->getCode(),
            'data' => $data,
            'user_id' => auth()->id() ?? 'system',
            'user_name' => auth()->user()->u_name ?? 'system',
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        ]);
    }

    /**
     * Log attempt untuk memulai break
     *
     * @param array $data
     * @return void
     */
    public static function logBreakAttempt(array $data)
    {
        Log::info("🔄 BREAK TIME ATTEMPT", [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'data' => $data,
            'user_id' => auth()->id() ?? 'system',
            'user_name' => auth()->user()->u_name ?? 'system',
            'request_url' => request()->fullUrl() ?? 'N/A'
        ]);
    }

    /**
     * Log informasi untuk debugging
     *
     * @param string $message
     * @param array $data
     * @return void
     */
    public static function logDebug(string $message, array $data = [])
    {
        Log::debug("🔍 BREAK TIME DEBUG: {$message}", [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'data' => $data,
            'user_id' => auth()->id() ?? 'system'
        ]);
    }
}
