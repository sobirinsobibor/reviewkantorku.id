<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;


class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    // protected static string $view = '';
    public function getView(): string
    {
        return 'filament.views.view-user';
    }


    protected function getHeaderWidgets(): array
    {
        return [
            // kosong, kita pakai custom view
        ];
    }

    public function getViewData(): array
    {
        $record = $this->getRecord();

        // Statistik Office
        $totalOffices   = $record->offices()->count();
        $pendingOffices  = $record->offices()->where('status', 'pending')->count();
        $approvedOffices = $record->offices()->where('status', 'approved')->count();
        $rejectedOffices = $record->offices()->where('status', 'rejected')->count();

        // Statistik Review
        $totalReviews  = $record->interactions()->count();
        $reviewCount   = $record->interactions()->where('type', 'review')->count();
        $magangCount   = $record->interactions()->where('type', 'cerita_magang')->count();
        $menfessCount  = $record->interactions()->where('type', 'menfess')->count();
        $qnaCount      = $record->interactions()->where('type', 'qna')->count();
        $hiddenReviews = $record->interactions()->where('is_hidden', true)->count();
        $anonReviews   = $record->interactions()->where('is_anonymous', true)->count();

        // Data tabel
        $offices = $record->offices()
            ->with('creator')
            ->latest()
            ->get();

        $reviews = $record->interactions()
            ->with('office')
            ->latest()
            ->get();

        return compact(
            'totalOffices', 'pendingOffices', 'approvedOffices', 'rejectedOffices',
            'totalReviews', 'reviewCount', 'magangCount', 'menfessCount', 'qnaCount',
            'hiddenReviews', 'anonReviews', 'offices', 'reviews'
        );
    }

    

    protected function getFooterWidgets(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return $this->getRecord()->name;
    }
}