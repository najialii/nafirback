<x-filament::page>
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md p-6 space-y-6">
        <div class="flex justify-between items-start flex-col md:flex-row md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $record->name }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $record->type }} — {{ $record->date }} at {{ $record->time }}</p>
            </div>
            <a 
                href="{{ route('filament.admin.resources.activities.edit', $record->id) }}" 
                class="inline-block px-4 py-2 mt-4 md:mt-0 text-sm bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition"
            >
                Edit Activity
            </a>
        </div>

        @if($record->description)
            <div>
                <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-200 mb-2">Description</h2>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $record->description }}</p>
            </div>
        @endif

        {{-- Department and Location --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Department</h3>
                <p class="text-lg text-gray-800 dark:text-gray-200">{{ $record->department->name ?? 'N/A' }}</p>
            </div>

            <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Location</h3>
                <p class="text-lg text-gray-800 dark:text-gray-200">{{ $record->location ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Benefits --}}
        @if($record->benifites)
            <div>
                <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-200 mb-2">Benefits</h2>
                <p class="text-gray-700 dark:text-gray-300">{{ $record->benifites }}</p>
            </div>
        @endif

        <div class="flex items-center space-x-4">
            <img src="https://i.pravatar.cc/100?u={{ $record->user_id }}" class="w-12 h-12 rounded-full object-cover" alt="User Avatar">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-300">Added by:</p>
                <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $record->user->name ?? 'N/A' }}</p>
            </div>
        </div>


        <div>
    <img 
        src="{{ $record->img ? Storage::url($record->img) : 'https://source.unsplash.com/800x400/?community,ai,activity' }}" 
        alt="Activity Image" 
        class="rounded-lg max-w-full h-auto border shadow-sm"
    >
</div>
        
    </div>
</x-filament::page>
