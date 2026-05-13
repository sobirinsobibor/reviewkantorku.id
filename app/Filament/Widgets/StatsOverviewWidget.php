<?php

namespace App\Filament\Widgets;

use App\Models\Office;
use App\Models\Review;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    // protected function getStats(): array
    // {
    //     return [
    //         // OFFICE
    //         Stat::make('Total Kantor', Office::count())
    //             ->description('Semua kantor terdaftar')
    //             ->descriptionIcon('heroicon-m-building-office')
    //             ->color('primary'),

    //         Stat::make('Kantor Pending', Office::where('status', 'pending')->count())
    //             ->description('Menunggu review')
    //             ->descriptionIcon('heroicon-m-clock')
    //             ->color('warning'),

    //         Stat::make('Kantor Approved', Office::where('status', 'approved')->count())
    //             ->description('Sudah disetujui')
    //             ->descriptionIcon('heroicon-m-check-circle')
    //             ->color('success'),

    //         Stat::make('Kantor Rejected', Office::where('status', 'rejected')->count())
    //             ->description('Ditolak')
    //             ->descriptionIcon('heroicon-m-x-circle')
    //             ->color('danger'),

    //         // REVIEW
    //         Stat::make('Total Review', Review::count())
    //             ->description('Semua konten review')
    //             ->descriptionIcon('heroicon-m-document-text')
    //             ->color('primary'),

    //         Stat::make('Review Aktif', Review::where('is_hidden', false)->whereNull('deleted_at')->count())
    //             ->description('Tampil ke publik')
    //             ->descriptionIcon('heroicon-m-eye')
    //             ->color('success'),

    //         Stat::make('Disembunyikan', Review::where('is_hidden', true)->count())
    //             ->description('Tidak tampil')
    //             ->descriptionIcon('heroicon-m-eye-slash')
    //             ->color('danger'),

    //         Stat::make('Review Anonim', Review::where('is_anonymous', true)->count())
    //             ->description('Identitas tersembunyi')
    //             ->descriptionIcon('heroicon-m-user')
    //             ->color('gray'),
    //     ];
    // }
}