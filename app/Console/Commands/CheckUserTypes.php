<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckUserTypes extends Command
{
    protected $signature = 'user:check-types';
    protected $description = 'Check users by store type (Online/Offline)';

    public function handle()
    {
        $users = DB::table('users')
            ->leftJoin('stores', 'stores.id', '=', 'users.st_id')
            ->leftJoin('store_types', 'store_types.id', '=', 'users.stt_id')
            ->select('users.id', 'users.u_name', 'users.email', 'stores.st_name', 'store_types.stt_name')
            ->where('users.u_delete', '!=', '1')
            ->orderBy('store_types.stt_name')
            ->orderBy('users.u_name')
            ->get();

        $this->info('=== USER LIST BY STORE TYPE ===');
        $this->newLine();

        $online = 0;
        $offline = 0;
        $null = 0;

        foreach ($users as $user) {
            $type = $user->stt_name ?? 'NOT SET';
            $store = $user->st_name ?? 'No Store';
            
            if (strtolower($type) == 'online') {
                $online++;
                $color = 'info';
            } elseif (strtolower($type) == 'offline') {
                $offline++;
                $color = 'comment';
            } else {
                $null++;
                $color = 'error';
            }
            
            $this->line(sprintf(
                "ID: <fg=cyan>%-3s</> | %-30s | %-25s | Type: <fg=%s>%-10s</>", 
                $user->id, 
                substr($user->u_name, 0, 30),
                substr($store, 0, 25),
                $color,
                $type
            ));
        }

        $this->newLine();
        $this->info('=== SUMMARY ===');
        $this->line("<fg=cyan>Online Users :</> <fg=green>$online</>");
        $this->line("<fg=yellow>Offline Users:</> <fg=green>$offline</>");
        $this->line("<fg=red>Not Set      :</> <fg=green>$null</>");
        $this->line("<fg=white>Total        :</> <fg=green>" . ($online + $offline + $null) . "</>");
        
        return 0;
    }
}
