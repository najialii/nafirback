<!-- resources/views/filament/resources/mentorship-req-resource/pages/show-mentorship-req.blade.php -->

<x-filament-panels::page>
    <x-filament::card>
        <h2 class="text-2xl font-semibold">Mentorship Request Details</h2>

        <div class="grid grid-cols-2 gap-4 mt-4">
            <div>
                <strong>Mentor:</strong>
                <p>{{ $record->mentor->name }}</p>
            </div>
            <div>
                <strong>Mentee:</strong>
                <p>{{ $record->mentee->name }}</p>
            </div>
            <div>
                <strong>Status:</strong>
                <x-badge color="primary">{{ ucfirst($record->status) }}</x-badge>
            </div>
            <div>
                <strong>Created At:</strong>
                <p>{{ $record->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="mt-4">
            <strong>Additional Notes:</strong>
            <p>{{ $record->notes ?? 'No additional notes.' }}</p>
        </div>
    </x-filament::card>
</x-filament-panels::page>
