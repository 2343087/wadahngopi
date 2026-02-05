<?php

namespace App\Console\Commands;

use App\Models\Information;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SyncInformationViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-information-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync information view counts from Cache/Redis to the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting sync of information views...');

        // In production with many items, we would use Redis::scan
        // For now, we fetch all published information and check their cache
        $informations = Information::where('is_published', true)->get();

        foreach ($informations as $info) {
            $key = "info_views:{$info->id}";
            $views = Cache::pull($key);

            if ($views) {
                DB::table('information')
                    ->where('id', $info->id)
                    ->increment('views', (int) $views);

                $this->line("Synced {$views} views for: {$info->title}");
            }
        }

        $this->info('Sync completed!');
    }
}
