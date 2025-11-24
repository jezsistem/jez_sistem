<?php

namespace App\Http\Controllers;

use App\Models\ModalLockAllowedModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LockController extends Controller
{
    // Durasi lock dalam menit
    const LOCK_DURATION_MINUTES = 5;

    // "Daftar putih" model yang boleh di-lock (untuk keamanan)
    private function getAllowedModels(): array
    {
        $allowedModels = ModalLockAllowedModel::all();

        $allowedModelsArray = [];
        foreach ($allowedModels as $allowedModel) {
            $allowedModelsArray[$allowedModel->model_type] = "\\App\\Models\\" . $allowedModel->model_name;
        }

        return $allowedModelsArray;
    }

    // Fungsi untuk mencari model berdasarkan tipe dan ID dari request
    private function resolveLockable(string $type, int $id)
    {
        $allowedModels = $this->getAllowedModels();
        if (!array_key_exists($type, $allowedModels)) {
            return null;
        }
        $modelClass = $allowedModels[$type];
        return $modelClass::find($id);
    }

    // Metode utama untuk mencoba mendapatkan lock
    public function acquireLock(Request $request)
    {
        $validated = $request->validate([
            'lockable_type' => ['required', 'string', Rule::in(array_keys($this->getAllowedModels()))],
            'lockable_id'   => 'required|integer',
            'identifier'    => 'required|string|max:255', // <-- VALIDASI BARU
        ]);

        $lockable = $this->resolveLockable($validated['lockable_type'], $validated['lockable_id']);
        if (!$lockable) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        // Kueri sekarang menyertakan 'identifier'
        $queryBase = $lockable->locks()->where('identifier', $validated['identifier']);

        // Hapus lock kedaluwarsa yang spesifik
        (clone $queryBase)->where('expires_at', '<', now())->delete();

        // Cek lock aktif yang spesifik
        $existingLock = (clone $queryBase)->first();

        if ($existingLock) {
            if ($existingLock->user_id == Auth::id()) {
                $existingLock->update(['expires_at' => now()->addMinutes(self::LOCK_DURATION_MINUTES)]);
                return response()->json(['status' => 'success', 'message' => 'Lock diperpanjang.']);
            }
            return response()->json([
                'status' => 'locked',
                'message' => 'Sesi ini sedang diakses oleh pengguna lain.',
                'locked_by' => $existingLock->user->u_name,
            ], 423);
        }

        // Buat lock baru dengan 'identifier'
        $lockable->locks()->create([
            'user_id'    => Auth::id(),
            'expires_at' => now()->addMinutes(self::LOCK_DURATION_MINUTES),
            'identifier' => $validated['identifier'], // <-- TAMBAHKAN DI SINI
        ]);

        return response()->json(['status' => 'success', 'message' => 'Lock berhasil didapatkan.']);
    }

    // Metode untuk melepas lock saat modal ditutup
    public function releaseLock(Request $request)
    {
        $validated = $request->validate([
            'lockable_type' => ['required', 'string', Rule::in(array_keys($this->getAllowedModels()))],
            'lockable_id'   => 'required|integer',
            'identifier'    => 'required|string|max:255', // <-- VALIDASI BARU
        ]);

        $lockable = $this->resolveLockable($validated['lockable_type'], $validated['lockable_id']);
        if ($lockable) {
            // Hapus lock yang spesifik milik user ini
            $lockable->locks()
                ->where('user_id', Auth::id())
                ->where('identifier', $validated['identifier']) // <-- TAMBAHKAN DI SINI
                ->delete();
        }

        return response()->json(['status' => 'success', 'message' => 'Lock dilepaskan.']);
    }

    public function extendLock(Request $request)
    {
        // Validasi input, termasuk identifier
        $validated = $request->validate([
            'lockable_type' => ['required', 'string', Rule::in(array_keys($this->getAllowedModels()))],
            'lockable_id'   => 'required|integer',
            'identifier'    => 'required|string|max:255', // <-- VALIDASI BARU
        ]);

        $lockable = $this->resolveLockable($validated['lockable_type'], $validated['lockable_id']);
        if (!$lockable) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        // Cari lock yang aktif, milik user ini, DAN dengan identifier yang cocok
        $existingLock = $lockable->locks()
            ->where('user_id', Auth::id())
            ->where('identifier', $validated['identifier']) // <-- CARI LOCK YANG SPESIFIK
            ->first();

        if ($existingLock) {
            // Jika ketemu, perpanjang waktunya
            $existingLock->update(['expires_at' => now()->addMinutes(self::LOCK_DURATION_MINUTES)]);
            return response()->json(['status' => 'success', 'message' => 'Lock diperpanjang.']);
        }

        // Jika tidak ada lock spesifik yang cocok, kembalikan error
        return response()->json(['status' => 'error', 'message' => 'Tidak ada lock yang aktif untuk sesi ini.'], 404);
    }
}
