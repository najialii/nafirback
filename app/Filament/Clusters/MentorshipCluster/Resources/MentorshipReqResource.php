<?php

namespace App\Filament\Clusters\MentorshipCluster\Resources;

use App\Filament\Clusters\MentorshipCluster;
use App\Filament\Clusters\MentorshipCluster\Resources\MentorshipReqResource\Pages;
use App\Filament\Clusters\MentorshipCluster\Resources\MentorshipReqResource\RelationManagers;
use App\Models\MentorshipReq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\SelectColumn;



class MentorshipReqResource extends Resource
{
    protected static ?string $model = MentorshipReq::class;


    protected static ?string $navigationLabel = ' Sessions Requests';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = MentorshipCluster::class;
    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('super_admin') === false;
    }

    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Forms\Components\TextInput::make('mentee.name')
                    ->label('Activity Name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('message')
                    ->label('Description')
                    ->nullable()
                    ->maxLength(500),

                Forms\Components\TextInput::make('message')
                    ->label('Description')
                    ->nullable()
                    ->maxLength(500),

            ]);

        SelectColumn::make('status')
            ->options([
                'draft' => 'Draft',
                'reviewing' => 'Reviewing',
                'published' => 'Published',
            ]);
    }



    public static function table(Table $table): Table
    {

        $user = auth()->user();

        return $table
            ->columns([

                Tables\Columns\TextColumn::make('mentor.name')->searchable(),
                Tables\Columns\TextColumn::make('mentee.name')->searchable(),
                Tables\Columns\IconColumn::make('status')->boolean()->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn(string $state) => ucfirst($state))
                    ->colors([
                        'secondary' => fn($state) => $state === 'pending',
                        'success' => fn($state) => $state === 'accepted',
                        'danger' => fn($state) => $state === 'rejected',
                    ])
                    ->sortable(),


            ])
            ->filters([
                //
            ])
            ->actions([

                Tables\Actions\Action::make('preview')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->modalHeading('Review Mentorship Request')
                ->visible(fn ($record) => auth()->user()->hasRole('mentor') && $record->mentor_id === auth()->id())
                ->modalSubmitAction(false)  
                ->modalCancelActionLabel('Close')
                ->form(fn ($record) => [
                    Forms\Components\Placeholder::make('mentorship_id')
                        ->label('Mentorship ID')
                        ->content($record->mentorship_id),

                        Forms\Components\Placeholder::make('mentor')
                            ->label('Mentor')
                            ->content($record->mentor?->name ?? 'N/A'),

                        Forms\Components\Placeholder::make('mentee')
                            ->label('Mentee')
                            ->content($record->mentee?->name ?? 'N/A'),

                        Forms\Components\Placeholder::make('selecteday')
                            ->label('Selected Day')
                            ->content($record->selecteday ?? 'N/A'),

                        Forms\Components\Placeholder::make('selectedtime')
                            ->label('Selected Time')
                            ->content($record->selectedtime ?? 'N/A'),

                        Forms\Components\Placeholder::make('message')
                            ->label('Message')
                            ->content($record->message ?? 'No message'),

                        Forms\Components\Placeholder::make('status')
                            ->label('Status')
                            ->content(ucfirst($record->status ?? 'Pending')),
                    ])
                    ->extraModalFooterActions(fn($record) => [
                        Tables\Actions\Action::make('accept')
                            ->label('Accept')
                            ->color('success')
                            ->visible(fn() => $record->status === 'pending')
                            ->action(function () use ($record) {
                                $record->update(['status' => 'accepted']);
                                \Filament\Notifications\Notification::make()
                                    ->title('Request Accepted')
                                    ->success()
                                    ->send();
                            }),

                        Tables\Actions\Action::make('reject')
                            ->label('Reject')
                            ->color('danger')
                            ->visible(fn() => $record->status === 'pending')
                            ->action(function () use ($record) {
                                $record->update(['status' => 'rejected']);
                                \Filament\Notifications\Notification::make()
                                    ->title('Request Rejected')
                                    ->danger()
                                    ->send();
                            }),
                    ])



                // Tables\Actions\Action::make('accept')
                //     ->label('Accept')
                //     ->icon('heroicon-o-check')
                //     ->visible(fn ($record) => $user->hasRole('mentor')&& $record->mentor_id === auth()->user()->id && $record->status === 'pending')
                //     ->action(fn ($record) => $record->update(['status' => 'accepted'])),

                //     // Tables\Actions\Action::make('view')
                //     // ->label('View')
                //     // ->icon('heroicon-o-eye')
                //     // ->action(fn ($record) => redirect()->route('filament.resources.mentorship-reqs.show', $record)) // Redirect to show page
                //     // ->visible(fn ($record) => true),
                //     Tables\Actions\ViewAction::make(),










                // Tables\Actions\Action::make('reject')
                //     ->label('Reject')
                //     ->icon('heroicon-o-x-mark')
                //     ->color('danger')
                //     ->visible(fn ($record) => auth()->user()->role === 'mentor' && $record->mentor_id === auth()->user()->id && $record->status === 'pending')
                //     ->action(fn ($record) => $record->update(['status' => 'rejected'])),
                // Tables\Actions\EditAction::make()->visible(fn () => auth()->user()->role === 'super_admin'),

                // Tables\Actions\Action::make('delete')
                // ->requiresConfirmation()
                // ->action(fn (Post $record) => $record->delete())
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
            'index' => Pages\ListMentorshipReqs::route('/'),
            'create' => Pages\CreateMentorshipReq::route('/create'),
            'edit' => Pages\EditMentorshipReq::route('/{record}/edit'),
            // 'view' => Pages\ViewMentorshipReq::route('/{record}'),

        ];
    }
}
