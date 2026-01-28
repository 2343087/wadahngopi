<?php

namespace App\Filament\Widgets;

use App\Models\Cafe;
use App\Models\Menu;
use App\Models\Review;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '15s'; // Real-time feel

    protected function getStats(): array
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';

        $cafeQuery = Cafe::query();
        $menuQuery = Menu::query();
        $reviewQuery = Review::query();

        if (! $isAdmin) {
            $cafeIds = Cafe::where('owner_id', $user->id)->pluck('id');
            $cafeQuery->where('owner_id', $user->id);
            $menuQuery->whereIn('cafe_id', $cafeIds);
            $reviewQuery->whereIn('cafe_id', $cafeIds);
        }

        return [
            Stat::make('Cafe Terdaftar', $cafeQuery->count())
                ->description('Total cafe yang udah join')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('warning')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Menu Tersedia', $menuQuery->count())
                ->description('Koleksi rasa yang siap dinikmati')
                ->descriptionIcon('heroicon-m-cake')
                ->color('success')
                ->chart([3, 10, 5, 12, 8, 15, 10]),

            Stat::make('Rating Rata-rata', number_format($reviewQuery->avg('rating') ?? 0, 1))
                ->description('Kata mereka soal WadahNgopi')
                ->descriptionIcon('heroicon-m-star')
                ->color('amber')
                ->chart([4, 4.5, 3, 5, 4.2, 4.8, 5]),
        ];
    }
}
