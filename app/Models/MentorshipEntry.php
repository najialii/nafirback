<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\MentorshipCluster;
use App\Filament\Resources\MentorshipResource\Pages;
use App\Models\Mentorship;
use App\Models\Department;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MentorshipResource extends Resource
{
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $cluster = MentorshipCluster::class;
    protected static ?string $model = Mentorship::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        $user = auth()->user();

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(4)
                    ->nullable(),

                Forms\Components\Select::make('mentor_id')
                    ->label('Mentor')
                    ->relationship('mentor', 'name')
                    ->required()
                    ->searchable()
                    ->options(User::role('mentor')->pluck('name', 'id')),

                Forms\Components\Select::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name')
                    ->required()
                    ->options(function () use ($user) {
                        if ($user && $user->hasRole('super_admin')) {
                            return Department::pluck('name', 'id');
                        }

                        return Department::where('id', $user->department_id)->pluck('name', 'id');
                    })
                    ->disabled(! $user->hasRole('super_admin'))
                    ->default($user->department_id),

                Forms\Components\TextInput::make('location')
                    ->label('Location')
                    ->nullable()
                    ->maxLength(255),

                Forms\Components\DatePicker::make('start_date')
                    ->label('Start Date')
                    ->required(),

                Forms\Components\DatePicker::make('end_date')
                    ->label('End Date')
                    ->required(),

                Forms\Components\TimePicker::make('time')
                    ->label('Time')
                    ->required(),

                Forms\Components\FileUpload::make('img')
                    ->label('Mentorship Image')
                    ->image()
                    ->directory('mentorship-images')
                    ->nullable(),

                Forms\Components\KeyValue::make('benefits')
                    ->label('Benefits')
                    ->addButtonLabel('Add Benefit')
                    ->keyLabel('Title')
                    ->valueLabel('Description')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('department.name')->sortable(),
                Tables\Columns\TextColumn::make('mentor.name')->label('Mentor')->sortable(),
                Tables\Columns\TextColumn::make('start_date')->date(),
                Tables\Columns\TextColumn::make('end_date')->date(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Add relation managers here if needed
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
