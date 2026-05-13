<?php

namespace App\Filament\Resources\Offices\Tables;

use App\Models\Province;
use App\Models\Regency;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OfficesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('#')->rowIndex(),

                TextColumn::make('name')
                    ->label('Kantor')
                    ->html()
                    ->searchable(query: function ($query, string $search) {
                        $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhereHas('regency', fn ($q) =>
                                $q->where('name', 'like', "%{$search}%")
                            )
                            ->orWhereHas('regency.province', fn ($q) =>
                                $q->where('name', 'like', "%{$search}%")
                            );
                    })
                    ->state(function ($record) {
                        $location = optional($record->regency)->name
                            . ', '
                            . optional($record->regency?->province)->name;

                        return '
                            <div class="space-y-0.5">
                                <div class="font-medium text-gray-900">
                                    ' . e($record->name) . '
                                </div>
                                <div class="text-xs text-gray-500">
                                    ' . e($location) . '
                                </div>
                            </div>
                        ';
                    }),

                TextColumn::make('address')
                    ->label('Alamat Lengkap')
                    ->wrap()
                    ->searchable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn ($record) => $record->getStatusLabel()),

                TextColumn::make('files_count')
                    ->label('Foto')
                    ->alignCenter()
                    ->counts('files'),

                TextColumn::make('interactions_count')
                    ->label('Interaksi')
                    ->counts('interactions')
                    ->badge()
                    ->alignCenter()
                    ->color('success'),

                TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->html()
                    ->searchable(query: function ($query, string $search) {
                        $query->whereHas('creator', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                        });
                    })
                    ->state(function ($record) {
                        $creator = $record->creator;

                        if (! $creator) {
                            return '<span class="text-xs text-gray-400">—</span>';
                        }

                        return '
                            <div class="space-y-0.5">
                                <div class="font-medium text-gray-900">
                                    ' . e($creator->name) . '
                                </div>
                                <div class="text-xs text-gray-500">
                                    ' . e($creator->email) . '
                                </div>
                            </div>
                        ';
                    }),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->formatStateUsing(fn ($state) => 
                        $state->format('d M Y') . '<br>' . $state->format('H:i')
                    )
                    ->html()
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Kantor')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Terverifkasi',
                        'rejected' => 'Ditolak',
                    ])
                    ->indicateUsing(function (array $data) {
                        if (! $data['value']) {
                            return null;
                        }

                        return 'Status: ' . ucfirst($data['value']);
                    }),

                Filter::make('location')
                    ->form([
                        Select::make('province_id')
                            ->label('Provinsi')
                            ->options(
                                Province::orderBy('name')->pluck('name', 'id')
                            )
                            ->searchable()
                            ->reactive(),

                        Select::make('regency_id')
                            ->label('Kota / Kabupaten')
                            ->options(fn (callable $get) =>
                                $get('province_id')
                                    ? Regency::where('province_id', $get('province_id'))
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                    : []
                            )
                            ->searchable()
                            ->disabled(fn (callable $get) => blank($get('province_id'))),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['province_id'] ?? null,
                                fn ($q, $provinceId) =>
                                    $q->whereHas(
                                        'regency',
                                        fn ($rq) => $rq->where('province_id', $provinceId)
                                    )
                            )
                            ->when(
                                $data['regency_id'] ?? null,
                                fn ($q, $regencyId) =>
                                    $q->where('regency_id', $regencyId)
                            );
                    })
                    ->indicateUsing(function (array $data) {
                        if (empty($data['province_id']) && empty($data['regency_id'])) {
                            return null;
                        }

                        $labels = [];

                        if (! empty($data['regency_id'])) {
                            $regency = Regency::find($data['regency_id']);
                            $labels[] = $regency?->name;
                        }

                        if (! empty($data['province_id'])) {
                            $province = Province::find($data['province_id']);
                            $labels[] = $province?->name;
                        }

                        return 'Lokasi: ' . implode(', ', $labels);
                    }),
            ])
            ->actions([
                ViewAction::make(),
                // EditAction::make(),
                // DeleteAction::make(),
            ])
            ->bulkActions([
                // DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
