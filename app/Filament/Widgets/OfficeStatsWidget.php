<?php

namespace App\Filament\Widgets;

use App\Models\Office;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OfficeStatsWidget extends BaseWidget
{
    public function getHeading(): ?string
    {
        return 'Statistik Kantor — ' . now()->format('d M Y'); // dinamis
    }

    public function getDescription(): ?string
    {
        return 'Data diperbarui setiap hari';
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Total Kantor', Office::count())
                ->description('Semua kantor terdaftar')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary'),

            Stat::make('Kantor Pending', Office::where('status', 'pending')->count())
                ->description('Menunggu verifikasi')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),


            Stat::make('Kantor Approved', Office::where('status', 'approved')->count())
                ->description('Terverifikasi')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Kantor Rejected', Office::where('status', 'rejected')->count())
                ->description('Ditolak')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}