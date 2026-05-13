<?php

namespace App\Filament\Resources\Interactions\Tables;

use App\Models\Interaction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class InteractionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            
            ->query(
                Interaction::query()
                    ->with(['office', 'user'])
                    ->latest()
            )
            ->columns([
                Stack::make([ 
                    // HEADER
                    TextColumn::make('office.name')
                        ->label('Kantor')
                        ->weight(FontWeight::Bold),

                    TextColumn::make('user_and_date')
                        ->getStateUsing(fn ($record) => $record->user->name . ' · ' . $record->created_at->format('d M Y H:i'))
                        ->color('gray')
                        ->size(TextSize::ExtraSmall),
                    
                    // BODY
                    // TextColumn::make('experience')
                    //     ->label('Pengalaman')
                    //     ->prefix('📝 ')
                    //     ->limit(120)
                    //     ->wrap()
                    //     ->size(TextSize::Small),

                    // TextColumn::make('positive_notes')
                    //     ->label('Catatan Positif')
                    //     ->prefix('✅ ')
                    //     ->limit(120)
                    //     ->wrap()
                    //     ->color('success')
                    //     ->size(TextSize::Small),
                    TextColumn::make('experience')
                        ->label('Pengalaman')
                        ->prefix('📝 ')
                        ->limit(120)
                        ->wrap()
                        ->size(TextSize::Small)
                        ->getStateUsing(fn ($record) => 
                            collect($record->attributes)
                                ->firstWhere('name', 'experience')['userData'][0] ?? null
                        ),

                    TextColumn::make('positive_notes')
                        ->label('Catatan Positif')
                        ->prefix('✅ ')
                        ->limit(120)
                        ->wrap()
                        ->color('success')
                        ->size(TextSize::Small)
                        ->getStateUsing(fn ($record) => 
                            collect($record->attributes)
                                ->firstWhere('name', 'positive_notes')['userData'][0] ?? null
                        ),

                    // FOOTER
                    Split::make([
                        TextColumn::make('type')
                            ->label('Tipe')
                            ->badge()
                            ->formatStateUsing(fn ($state) => 'Tipe: ' . match ($state) {
                                'review'        => 'Review',
                                'cerita_magang' => 'Cerita Magang',
                                'qna'           => 'Q&A',
                                'menfess'       => 'Menfess',
                                default         => ucfirst($state),
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'review'        => 'danger',
                                'cerita_magang' => 'info',
                                'qna'           => 'warning',
                                'menfess'       => 'primary',
                                default         => 'gray',
                            })
                            ->grow(false),

                        TextColumn::make('is_anonymous')
                            ->label('Anonim')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state ? 'Identitas: Anonim' : 'Identitas: Publik')
                            ->color(fn ($state) => $state ? 'warning' : 'success')
                            ->grow(false),

                        TextColumn::make('is_hidden')
                            ->label('Tampil')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state ? 'Visibilitas: Disembunyikan' : 'Visibilitas: Publik')
                            ->color(fn ($state) => $state ? 'danger' : 'success')
                            ->grow(false),

                        TextColumn::make('deleted_at')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state ? 'Dihapus' : 'Aktif')
                            ->color(fn ($state) => $state ? 'danger' : 'success')
                            ->grow(false),
                    ]),

                ])->space(1),
            ])
            ->contentGrid([
                'default' => 1,
            ])
            ->actions([
                Action::make('lihat_detail')
                    ->label('Lihat detail')
                    ->icon('heroicon-o-eye')
                    ->modalContent(fn ($record) => view('filament.modals.interaction-detail', ['record' => $record]))
                    ->modalHeading(fn ($record) => $record->office?->name ?? 'Detail Review')
                    ->modalWidth('2xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),

                Action::make('toggle_hidden')
                    ->label(fn ($record) => $record->is_hidden ? 'Tampilkan' : 'Sembunyikan')
                    ->color(fn ($record) => $record->is_hidden ? 'success' : 'danger')
                    ->action(function ($record) {
                        $record->update([
                            'is_hidden' => ! $record->is_hidden,
                        ]);
                    }),
            ])
            ->bulkActions([]);
    }
}
