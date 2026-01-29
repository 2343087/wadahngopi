<?php

namespace App\Filament\Widgets;

use App\Models\Cafe;
use App\Models\Review;
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
        $reviewQuery = Review::query();

        if (!$isDeveloper) {
            $cafeQuery->where('owner_id', $user->id);
            $reviewQuery->whereHas('cafe', fn($q) => $q->where('owner_id', $user->id));
        }

        return [
            Stat::make('Cafe Terdaftar', $cafeQuery->count())
                ->description('Total cafe yang udah join')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('warning')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Rating Rata-rata', number_format($reviewQuery->avg('rating') ?? 0, 1))
                ->description('Kata mereka soal WadahNgopi')
                ->descriptionIcon('heroicon-m-star')
                ->color('amber')
                ->chart([4, 4.5, 3, 5, 4.2, 4.8, 5]),
        ];
    }
}
