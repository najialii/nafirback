<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\MentorshipCluster;
use App\Filament\Resources\MentorshipResource\Pages;
use App\Filament\Resources\MentorshipResource\RelationManagers;
use App\Models\Mentorship;
use Filament\Clusters\Cluster;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Models\Department;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class MentorshipResource extends Resource
{
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?string $cluster = MentorshipCluster::class;

    protected static ?string $model = Mentorship::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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



            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('department.name'),
                Tables\Columns\TextColumn::make('mentor.name')->label('Mentor'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMentorships::route('/'),
            'create' => Pages\CreateMentorship::route('/create'),
            'edit' => Pages\EditMentorship::route('/{record}/edit'),
            'view' => Pages\ViewMentorship::route('/{record}'),
        ];
    }
}
