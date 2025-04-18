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
use App\Models\Department;
use Filament\Tables\Filters\TernaryFilter;

class ActivitiesResource extends Resource
{


    protected static ?int $navigationSort = 4;

//     public static function getNavigationBadge(): ?string
// {
//     return static::getModel()::count();
// }

    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';


    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $data['user_id'] = auth()->id();
        dd($data);
        return $data;
    }

    public static function form(Form $form): Form
    {
        $user = auth()->user();


        return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->label('Activity Name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('description')
                ->label('Description')
                ->nullable()
                ->maxLength(500),

                Forms\Components\Select::make('department_id')
                ->label('Department')
                ->relationship('department', 'name')
                // ->searchable()
                ->required()
                ->options(function () use ($user) {
                    if ($user && $user->hasRole('super_admin')) {
                        return Department::pluck('name', 'id');
                    }

                    return Department::where('id', $user->department_id)->pluck('name', 'id');
                })
                ->disabled(function () use ($user) {
                    return !$user->hasRole('super_admin');
                })
                ->default(function () use ($user) {
                    return $user->department_id;
                }),

            Forms\Components\TextInput::make('location')
                ->label('Location')
                ->nullable()
                ->maxLength(255),

            Forms\Components\DatePicker::make('date')
                ->label('Date')
                ->required(),

            Forms\Components\TimePicker::make('time')
                ->label('Time')
                ->required(),

            // Forms\Components\TextInput::make('participants')
            //     ->label('Participants')
            //     ->numeric()
            //     ->minValue(1)
            //     ->required(),
            Forms\Components\TextInput::make('user.name')
            ->label('User')
            ->default(auth()->id())
            ->disabled()
            ->required(),


            Forms\Components\TextInput::make('type')
    ->label('Type')
    ->required()
    ->maxLength(100),

            Forms\Components\TextInput::make('benifites')
                ->label('Benefits')
                ->nullable()
                ->maxLength(500),
        ]);
    }




    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('department.name'),
                Tables\Columns\TextColumn::make('location'),
                Tables\Columns\TextColumn::make('participants'),
                Tables\Columns\TextColumn::make('date'),
                Tables\Columns\TextColumn::make('time'),
            ])
            ->filters([
                //
                TernaryFilter::make('email_verified_at')
                ->label('Email verification')
                ->nullable()
                ->placeholder('All users')
                ->trueLabel('Verified users')
                ->falseLabel('Not verified users')

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
