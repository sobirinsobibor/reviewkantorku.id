<?php

namespace App\Filament\Resources\Offices\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Auth;

class OfficeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ═══════════════════════════════════════════
                // SECTION 1: Informasi Kantor
                // ═══════════════════════════════════════════
                Section::make('Informasi Kantor')
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nama Kantor / Perusahaan')
                                    ->weight(FontWeight::Bold)
                                    // ->size(TextEntry\TextEntrySize::Large)
                                    ->columnSpanFull(),

                                TextEntry::make('address')
                                    ->label('Alamat Lengkap')
                                    ->icon('heroicon-o-map-pin')
                                    ->columnSpanFull(),

                                TextEntry::make('location')
                                    ->label('Kota / Kabupaten')
                                    ->icon('heroicon-o-map')
                                    ->state(fn ($record) =>
                                        $record->regency->name . ', ' . $record->regency->province->name
                                    ),

                                TextEntry::make('industries.name')
                                    ->label('Industri')
                                    ->icon('heroicon-o-tag')
                                    ->listWithLineBreaks()
                                    ->bulleted(),
                                
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('status')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn ($state) => match ($state) {
                                                'approved' => 'success',
                                                'pending'  => 'warning',
                                                'rejected' => 'danger',
                                                default    => 'gray',
                                            })
                                            ->formatStateUsing(fn ($record) => $record->getStatusLabel()),

                                        TextEntry::make('ulid')
                                            ->label('ID Kantor')
                                            ->copyable()
                                            ->copyMessage('ID disalin!')
                                            ->fontFamily('mono')
                                            ->color('gray'),

                                        TextEntry::make('created_at')
                                            ->label('Tanggal Didaftarkan')
                                            ->dateTime('d M Y, H:i')
                                            ->color('gray'),
                                    ])
                                    ->columnSpanFull(),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('creator.name')
                                            ->label('Didaftarkan Oleh')
                                            ->icon('heroicon-o-user'),

                                        TextEntry::make('creator.email')
                                            ->label('Email Pengaju')
                                            ->icon('heroicon-o-envelope')
                                            ->copyable()
                                            ->copyMessage('Email disalin!'),
                                    ])
                                    ->columnSpanFull(),

                                // Actions::make([
                                Grid::make(2)
                                    ->schema([
                                        Action::make('approve')
                                            ->label('Setujui Kantor')
                                            ->icon('heroicon-o-check-circle')
                                            ->color('success')
                                            ->visible(fn ($record) => $record->status !== 'approved')
                                            ->requiresConfirmation()
                                            ->modalHeading('Setujui Kantor Ini?')
                                            ->modalDescription('Kantor akan langsung aktif dan bisa diakses publik.')
                                            ->modalSubmitActionLabel('Ya, Setujui')
                                            ->action(function ($record) {
                                                $record->update([
                                                    'status'      => 'approved',
                                                    'reviewed_by' => Auth::id(),
                                                    'reviewed_at' => now(),
                                                    'rejection_reason' => null,
                                                ]);
    
                                                Notification::make()
                                                    ->title('Kantor Disetujui')
                                                    ->body("{$record->name} berhasil diverifikasi.")
                                                    ->success()
                                                    ->send();
                                            }),

                                        Action::make('reject')
                                            ->label('Tolak Kantor')
                                            ->icon('heroicon-o-x-circle')
                                            ->color('danger')
                                            ->visible(fn ($record) => $record->status !== 'rejected')
                                            ->modalHeading('Tolak Kantor Ini?')
                                            ->modalDescription('Berikan alasan penolakan agar pengaju bisa melakukan perbaikan.')
                                            ->modalSubmitActionLabel('Tolak')
                                            ->form([
                                                Textarea::make('rejection_reason')
                                                    ->label('Alasan Penolakan')
                                                    ->placeholder('Contoh: Dokumen tidak lengkap, alamat tidak valid, dll.')
                                                    ->required()
                                                    ->minLength(10)
                                                    ->rows(4),
                                            ])
                                            ->action(function ($record, array $data) {
                                                $record->update([
                                                    'status'           => 'rejected',
                                                    'reviewed_by'      => Auth::id(),
                                                    'reviewed_at'      => now(),
                                                    'rejection_reason' => $data['rejection_reason'],
                                                ]);
    
                                                Notification::make()
                                                    ->title('Kantor Ditolak')
                                                    ->body("{$record->name} telah ditolak.")
                                                    ->danger()
                                                    ->send();
                                            }),

                                            Action::make('reset_to_pending')
                                                ->label('Reset ke Pending')
                                                ->icon('heroicon-o-arrow-path')
                                                ->color('warning')
                                                ->visible(fn ($record) => in_array($record->status, ['approved', 'rejected']))
                                                ->requiresConfirmation()
                                                ->modalHeading('Reset Status ke Pending?')
                                                ->modalDescription('Status kantor akan dikembalikan ke pending untuk direview ulang.')
                                                ->modalSubmitActionLabel('Ya, Reset')
                                                ->action(function ($record) {
                                                    $record->update([
                                                        'status'           => 'pending',
                                                        'reviewed_by'      => null,
                                                        'reviewed_at'      => null,
                                                        'rejection_reason' => null,
                                                    ]);
        
                                                    Notification::make()
                                                        ->title('Status Direset')
                                                        ->body("{$record->name} dikembalikan ke status pending.")
                                                        ->warning()
                                                        ->send();
                                                }),
                                    ])
                            ]),
                    ]),

                // ═══════════════════════════════════════════
                // SECTION 2: Foto Kantor
                // ═══════════════════════════════════════════
                Section::make('Foto Kantor')
                    ->icon('heroicon-o-photo')
                    ->collapsible()
                    ->schema([
                        ImageEntry::make('files')
                            ->label('')
                            ->state(fn ($record) =>
                                $record->files
                                    ->where('pivot.collection', 'office_photos')
                                    ->pluck('path')
                                    ->toArray()
                            )
                            ->disk('public')
                            ->height(180)
                            ->square(false)
                            ->extraImgAttributes(['class' => 'object-cover rounded-xl'])
                            ->extraAttributes(['class' => 'flex flex-wrap gap-3'])
                            ->columnSpanFull(),
                    ]),
            ]);

    }
}
