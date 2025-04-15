<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\Users;
class UserResource extends Resource
{
    protected static ?int $navigationSort = 0;





    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
protected static ?string $cluster = Users::class;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->unique(User::class, 'email'),

            Forms\Components\TextInput::make('password')
                ->password()
                ->required()
                ->minLength(8)
                ->maxLength(255),

            Forms\Components\Select::make('role')
                ->options([
                    'superadmin' => 'superadmin',
                    'admin' => 'admin',
                    'mentor' => 'Mentor',
                    'mentee' => 'Mentee',
                ])
                ->required(),

            Forms\Components\Select::make('department_id')
                ->relationship('department', 'name')
                ->label('Department')
                ->nullable(),

            Forms\Components\TextInput::make('phone')
                ->tel()
                ->maxLength(15),

            Forms\Components\TextInput::make('skills')
                ->maxLength(255),

            Forms\Components\TextInput::make('exp_years')
                ->label('Experience Years')
                ->numeric()
                ->rules('min:0')
                ->maxLength(355),

            Forms\Components\TextInput::make('country')
                ->maxLength(255),

            Forms\Components\TextInput::make('expertise')
                ->maxLength(255),

            Forms\Components\Textarea::make('education')
                ->label('Education')
                ->nullable(),

            Forms\Components\Textarea::make('certificates')
                ->label('Certificates')
                ->nullable(),

            Forms\Components\Toggle::make('isActive')
                ->label('Active')
                ->default(false),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('role'),
                Tables\Columns\TextColumn::make('department.name'),
                Tables\Columns\TextColumn::make('country'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
