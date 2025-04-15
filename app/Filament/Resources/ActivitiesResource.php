<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivitiesResource\Pages;
use App\Filament\Resources\ActivitiesResource\RelationManagers;
use App\Models\Activities;
use App\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivitiesResource extends Resource
{
    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
{
    return static::getModel()::count();
}

    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->label('Activity Name')
                ->required()
                ->maxLength(255),

            Forms\Components\Select::make('mentor_id')
                ->label('Assign Mentor')
                ->relationship('mentor', 'name') // Ensure 'mentor' matches the relationship in the Activity model
                ->searchable()
                ->required(),

            Forms\Components\Select::make('department_id')
                ->label('Department')
                ->multiple()
                ->relationship('departments', 'name')
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('location')
                ->label('Location')
                ->required()
                ->maxLength(255),

            Forms\Components\DatePicker::make('date')
                ->label('Date')
                ->required(),

            Forms\Components\TimePicker::make('time')
                ->label('Time')
                ->required(),

            Forms\Components\TextInput::make('participants')
                ->label('Participants')
                ->numeric()
                ->minValue(1)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('department.name'),
                Tables\Columns\TextColumn::make('location'),
                Tables\Columns\TextColumn::make('participants'),
                Tables\Columns\TextColumn::make('date'),
                Tables\Columns\TextColumn::make('time'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [


        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivities::route('/create'),
            'edit' => Pages\EditActivities::route('/{record}/edit'),
        ];
    }
}
