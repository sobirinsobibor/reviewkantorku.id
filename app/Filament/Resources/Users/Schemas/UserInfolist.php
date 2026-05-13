<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Office;
use App\Models\Review;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
        {
            return $schema
                ->schema([
                    Section::make('Informasi Pengguna')
                        ->schema([
                            Grid::make(3)->schema([
                                TextEntry::make('name')->label('Nama'),
                                TextEntry::make('email')->label('Email'),
                                TextEntry::make('created_at')
                                    ->label('Bergabung')
                                    ->html()
                                    ->formatStateUsing(fn ($state) =>
                                        $state?->format('d M Y') . '<br>' .
                                        '<span style="color:#9ca3af;font-size:11px;">' . $state?->format('H:i') . '</span>'
                                    ),
                            ]),
                            Grid::make(2)->schema([
                                IconEntry::make('is_admin')->label('Admin')->boolean(),
                                IconEntry::make('is_active')->label('Aktif')->boolean(),
                            ]),
                        ]),
                ]);
        }
}
