<?php

namespace App\Filament\Widgets;

use App\Models\Interaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InteractionStatsWidget extends StatsOverviewWidget
{
    protected function getColumns(): int
    {
        return 5;
    }

    public function getHeading(): ?string
    {
        return 'Statistik Interaksi — ' . now()->format('d M Y'); // dinamis
    }

    public function getDescription(): ?string
    {
        return 'Data diperbarui setiap hari';
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total', Interaction::count())
                ->description('Semua Hasil Interaksi')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Ulasan', Interaction::where('type', 'review')->count())
                ->description('Total Ulasan')
                ->descriptionIcon('heroicon-m-star')
                ->color('info'),

            Stat::make('Cerita Magang', Interaction::where('type', 'cerita_magang')->count())
                ->description('Total Cerita Magang')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),

            Stat::make('Menfess', Interaction::where('type', 'menfess')->count())
                ->description('Total Menfess')
                ->descriptionIcon('heroicon-m-chat-bubble-left')
                ->color('pink'),

            Stat::make('QnA', Interaction::where('type', 'qna')->count())
                ->description('Total QnA')
                ->descriptionIcon('heroicon-m-question-mark-circle')
                ->color('info'),

            // Stat::make('Disembunyikan', Review::where('is_hidden', true)->count())
            //     ->descriptionIcon('heroicon-m-eye-slash')
            //     ->color('danger'),

            // Stat::make('Anonim', Review::where('is_anonymous', true)->count())
            //     ->descriptionIcon('heroicon-m-user')
            //     ->color('gray'),
        ];
    }

}
