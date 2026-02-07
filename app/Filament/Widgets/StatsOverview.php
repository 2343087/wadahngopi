<?php

namespace App\Filament\Widgets;

use App\Models\Cafe;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '30s'; // Slightly slower for better heavy-load management

    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }
        $isDeveloper = $user->role === 'developer';

        $cafeQuery = Cafe::query();
        if (!$isDeveloper) {
            $cafeQuery->where('owner_id', $user->id);
        }

        return [
            Stat::make('Total Kafe', $cafeQuery->count())
                ->description('Eksosistem WadahNgopi ☕')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('primary')
                ->chart(
                    \App\Models\Cafe::selectRaw('count(*) as count')
                        ->where('created_at', '>=', now()->subDays(7))
                        ->groupByRaw('DATE(created_at)')
                        ->pluck('count')
                        ->toArray()
                ),

            Stat::make('Kafe Perlu Review', Cafe::where('status', 'review')->count())
                ->description('Butuh sentuhan admin 🔍')
                ->descriptionIcon('heroicon-m-magnifying-glass-circle')
                ->color('warning'),

            Stat::make('Total Pengunjung', number_format(\App\Models\Information::sum('views')))
                ->description('Traffic lagi rame nih! 📈')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('success'),
        ];
    }
}
