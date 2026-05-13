<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('#')->rowIndex(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->html()
                    ->formatStateUsing(function ($state, $record) {
                        $name = $record->name ?? '-';
                        $photo = $record->profile_photo_path;
                        
                        // Buat inisial
                        $words = explode(' ', $name);
                        $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1] ?? '', 0, 1));

                        if ($photo) {
                            $avatar = '<img src="' . asset('storage/' . $photo) . '" 
                                style="width:32px; height:32px; border-radius:50%; object-fit:cover; flex-shrink:0;">';
                        } else {
                            $avatar = '
                                <div style="width:32px; height:32px; border-radius:50%; background:#eff6ff; 
                                    display:flex; align-items:center; justify-content:center; 
                                    font-size:11px; font-weight:600; color:#1d4ed8; flex-shrink:0;">
                                    ' . $initials . '
                                </div>';
                        }

                        return '
                            <div style="display:flex; align-items:center; gap:8px;">
                                ' . $avatar . '
                                <span style="font-size:13px; font-weight:500; color:#111827;">' . e($name) . '</span>
                            </div>';
                    }),
                TextColumn::make('email')
                    ->label('Alamat Email')
                    ->searchable(),
                TextColumn::make('username')
                    ->searchable(), 

                TextColumn::make('offices_count')
                    ->label('Kantor')
                    ->counts('offices')
                    ->badge()
                    ->alignCenter()
                    ->color('info'),

                TextColumn::make('interactions_count')
                    ->label('Interaksi')
                    ->counts('interactions')
                    ->badge()
                    ->alignCenter()
                    ->color('success'),
                
                ToggleColumn::make('is_admin')
                    ->label('Admin')
                    ->afterStateUpdated(function ($record, $state) {
                        Notification::make()
                            ->title($state ? 'User dijadikan Admin' : 'User dicopot dari Admin')
                            ->body($record->name)
                            ->color($state ? 'success' : 'warning')
                            ->icon($state ? 'heroicon-o-shield-check' : 'heroicon-o-shield-exclamation')
                            ->send();
                    }),

                ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->afterStateUpdated(function ($record, $state) {
                        Notification::make()
                            ->title($state ? 'User diaktifkan' : 'User dinonaktifkan')
                            ->body($record->name)
                            ->color($state ? 'success' : 'danger')
                            ->icon($state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                            ->send();
                    }),

                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->html()
                    ->formatStateUsing(fn ($state) => 
                        $state?->format('d M Y') . '<br>' . 
                        '<span style="color:#9ca3af; font-size:11px;">' . $state?->format('H:i') . '</span>'
                    ),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make()
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
