<?php

namespace App\Filament\Resources\Interactions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class InteractionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ulid')
                    ->required(),
                TextInput::make('parent_id')
                    ->numeric(),
                TextInput::make('office_id')
                    ->required()
                    ->numeric(),
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('attributes'),
                Textarea::make('experience')
                    ->columnSpanFull(),
                Textarea::make('positive_notes')
                    ->columnSpanFull(),
                Select::make('type')
                    ->options([
                        'review' => 'Review',
                        'cerita_magang' => 'Cerita magang',
                        'menfess' => 'Menfess',
                        'qna' => 'Qna',
                    ])
                    ->default('review')
                    ->required(),
                Toggle::make('is_anonymous')
                    ->required(),
                Toggle::make('is_hidden')
                    ->required(),
                DateTimePicker::make('reported_at'),
            ]);
    }
}
