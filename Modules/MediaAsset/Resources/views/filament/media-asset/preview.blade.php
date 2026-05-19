<div class="space-y-4">
    @if ($record->image_url)
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-gray-50 dark:border-white/10 dark:bg-gray-900">
            <img
                src="{{ $record->image_url }}"
                alt="{{ $record->display_name }}"
                class="max-h-[70vh] w-full object-contain"
            >
        </div>
    @else
        <div class="rounded-xl border border-dashed border-gray-300 px-6 py-10 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
            Preview image tidak tersedia.
        </div>
    @endif

    <div class="grid gap-3 md:grid-cols-2">
        <div class="rounded-lg border border-gray-200 px-4 py-3 dark:border-white/10 dark:bg-gray-900">
            <div class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Folder</div>
            <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $record->display_folder }}</div>
        </div>

        <div class="rounded-lg border border-gray-200 px-4 py-3 dark:border-white/10 dark:bg-gray-900">
            <div class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Disk</div>
            <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $record->disk }}</div>
        </div>

        <div class="rounded-lg border border-gray-200 px-4 py-3 md:col-span-2 dark:border-white/10 dark:bg-gray-900">
            <div class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Filename</div>
            <div class="mt-1 break-all text-sm text-gray-900 dark:text-gray-100">{{ $record->display_name }}</div>
        </div>

        <div class="rounded-lg border border-gray-200 px-4 py-3 md:col-span-2 dark:border-white/10 dark:bg-gray-900">
            <div class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Public URL</div>
            <div class="mt-1 break-all text-sm text-gray-900 dark:text-gray-100">{{ $record->public_url ?: '-' }}</div>
        </div>
    </div>
</div>
