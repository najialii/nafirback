<?php

namespace App\Filament\Clusters\MentorshipCluster\Resources\MentorshipResource\Pages;

use App\Filament\Resources\MentorshipResource;
use App\Models\Mentorship;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class PreviewMentorship extends ViewRecord implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = MentorshipResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Mentorship::query()
                    ->where(function ($q) {
                        $q->where('mentor_id', $this->record->mentor_id)
                            ->orWhere('mentee_id', $this->record->mentee_id);
                    })
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('date'),
                Tables\Columns\TextColumn::make('time'),
                Tables\Columns\TextColumn::make('mentor.name')->label('Mentor'),
                Tables\Columns\TextColumn::make('mentee.name')->label('Mentee'),
                Tables\Columns\TextColumn::make('status'),
            ]);
    }
}
