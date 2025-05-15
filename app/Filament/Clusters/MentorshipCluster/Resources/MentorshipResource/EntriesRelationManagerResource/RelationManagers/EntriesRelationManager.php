<?php

namespace App\Filament\Clusters\MentorshipCluster\Resources\MentorshipResource\EntriesRelationManagerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'entries';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('start_date')->required(),
                Forms\Components\TextInput::make('duration')->required(),
                Forms\Components\Select::make('status')->options([
                    'pending' => 'Pending',
                    // 'in_progress' => 'In Progress',
                    'accepted' => 'Accepted',
                    'rejected' => 'Rejected',
                    // 'scheduled' => 'Scheduled',
                ])->required(),
                Forms\Components\TextInput::make('link')->url()->nullable(),
                
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('entries')
            ->columns([
                Tables\Columns\TextColumn::make('start_date')->date(),
                Tables\Columns\TextColumn::make('duration'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('link')->url(),                
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
