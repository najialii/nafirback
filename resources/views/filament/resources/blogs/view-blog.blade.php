<x-filament::page>
    <div class="max-w-5xl mx-auto p-6 text-black dark:text-white">
        <div class="mb-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Blog ID: #{{ $record->id }} • Published: {{ $record->created_at->format('F j, Y') }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 mb-6 shadow">
            <h1 class="text-3xl md:text-4xl font-extrabold mb-4">{{ $record->title }}</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-center space-x-4">
                    <img src="https://i.pravatar.cc/150?img=3" alt="Author Photo" class="w-10 h-10 rounded-full object-cover shadow">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Author</p>
                        <p class="font-medium text-gray-800 dark:text-gray-200">
                            {{ $record->author->name ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                <div class="space-y-2">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Department</p>
                        <p class="font-medium text-gray-800 dark:text-gray-200">
                            {{ $record->department->name ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Featured</p>
                        <p class="font-medium text-gray-800 dark:text-gray-200">
                            {{ $record->featured ? 'Yes' : 'No' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-xl overflow-hidden shadow mb-6">
            <img src="{{ $record->image ? asset('storage/' . $record->image) : 'https://picsum.photos/800/400?grayscale&random=' . $record->id }}"
                alt="Blog Image"
                class="w-full h-auto object-cover">
        </div>

        <div class="prose dark:prose-invert max-w-none mb-8">
            {!! $record->content !!}
        </div>

        <div class="flex justify-between items-center border-t pt-4 text-sm text-gray-500 dark:text-gray-400">
            <span>Blog ID: #{{ $record->id }}</span>
            <a href="{{ route('filament.admin.resources.blogs.index') }}"
               class="text-blue-500 hover:underline font-semibold">
                ← Back to all blogs
            </a>
        </div>
    </div>
</x-filament::page>
