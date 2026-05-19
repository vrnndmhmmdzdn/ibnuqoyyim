<x-filament-panels::page>
    <div
        x-data="{
            draggingAssetId: null,
            dropTarget: null,
            startDrag(assetId) {
                this.draggingAssetId = assetId
            },
            endDrag() {
                this.draggingAssetId = null
                this.dropTarget = null
            },
            markDropTarget(target) {
                if (this.draggingAssetId === null) {
                    return
                }

                this.dropTarget = target
            },
            clearDropTarget(target) {
                if (this.dropTarget === target) {
                    this.dropTarget = null
                }
            },
            dropTo(folder) {
                if (this.draggingAssetId === null) {
                    return
                }

                $wire.moveAssetToFolder(this.draggingAssetId, folder)
                this.endDrag()
            },
        }"
        x-on:copy-link.window="navigator.clipboard.writeText($event.detail.url)"
        class="space-y-6"
    >
        <section>
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-gray-400">
                    @foreach ($this->folderCrumbs() as $crumb)
                        @if ($loop->first)
                            <button
                                type="button"
                                wire:click="goToRoot"
                                x-on:dragover.prevent
                                x-on:dragenter.prevent="markDropTarget('__root__')"
                                x-on:dragleave.prevent="clearDropTarget('__root__')"
                                x-on:drop.prevent.stop="dropTo(null)"
                                x-bind:class="dropTarget === '__root__' ? 'ring-2 ring-primary-500 ring-offset-2 dark:ring-offset-gray-950' : ''"
                                class="font-medium text-slate-700 transition hover:text-slate-900 dark:text-gray-200 dark:hover:text-white"
                            >
                                {{ $crumb['label'] }}
                            </button>
                        @else
                            <span class="text-slate-400 dark:text-gray-500">/</span>
                            <button
                                type="button"
                                x-on:click="$wire.openFolder(@js($crumb['path']))"
                                class="font-medium text-slate-700 transition hover:text-slate-900 dark:text-gray-200 dark:hover:text-white"
                            >
                                {{ $crumb['label'] }}
                            </button>
                        @endif
                    @endforeach
                </div>

                <div class="w-full lg:max-w-md">
                    <label class="sr-only" for="media-gallery-search">Cari</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 dark:text-gray-500">
                            <x-filament::icon icon="heroicon-o-magnifying-glass" class="h-5 w-5" />
                        </span>
                        <input
                            id="media-gallery-search"
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Cari folder atau image..."
                            class="w-full rounded-lg border border-slate-200 bg-white py-2.5 pl-11 pr-4 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-0 dark:border-white/10 dark:bg-gray-900 dark:text-gray-100 dark:placeholder:text-gray-500 dark:focus:border-gray-600"
                        >
                    </div>
                </div>
            </div>
        </section>

        @if ($this->folders->isNotEmpty())
            <section class="space-y-4">
                <p class="text-sm text-slate-500 dark:text-gray-400">
                    Drag image ke folder untuk memindahkan, atau pakai menu `Move` pada card image.
                </p>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
                    @foreach ($this->folders as $folder)
                        <article
                            wire:key="folder-drop-{{ $folder['path'] }}"
                            x-on:dragover.prevent
                            x-on:dragenter.prevent="markDropTarget(@js($folder['path']))"
                            x-on:dragleave.prevent="clearDropTarget(@js($folder['path']))"
                            x-on:drop.prevent.stop="dropTo(@js($folder['path']))"
                            x-bind:class="dropTarget === @js($folder['path']) ? 'ring-2 ring-primary-500 ring-offset-2 dark:ring-offset-gray-950' : ''"
                            class="group rounded-[1.1rem] border border-slate-200 bg-white p-4 text-left shadow-sm transition hover:border-slate-300 hover:bg-slate-50 hover:shadow-md dark:border-white/10 dark:bg-gray-900 dark:hover:border-gray-700 dark:hover:bg-gray-800"
                        >
                            <div class="flex items-start gap-3">
                                <button
                                    type="button"
                                    x-on:click="$wire.openFolder(@js($folder['path']))"
                                    class="flex min-w-0 flex-1 items-center gap-3 text-left"
                                >
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-500 dark:bg-gray-800 dark:text-gray-300">
                                        <x-filament::icon icon="heroicon-o-folder" class="h-7 w-7" />
                                    </div>
                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-medium text-slate-900 dark:text-gray-100">
                                            {{ \Illuminate\Support\Str::title(str_replace(['-', '_'], ' ', $folder['name'])) }}
                                        </div>
                                    </div>
                                </button>

                                <div class="shrink-0">
                                    <x-filament-actions::group
                                        :actions="[
                                            $this->deleteFolderAction()->arguments(['path' => $folder['path']]),
                                        ]"
                                        icon-button
                                        icon="heroicon-o-ellipsis-vertical"
                                        tooltip="Folder actions"
                                        dropdown-placement="bottom-end"
                                    />
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($this->assets->count() > 0)
            <section class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
                    @foreach ($this->assets as $asset)
                        <article
                            wire:key="asset-card-{{ $asset->getKey() }}"
                            draggable="true"
                            x-on:dragstart="startDrag({{ $asset->getKey() }})"
                            x-on:dragend="endDrag()"
                            x-bind:class="draggingAssetId === {{ $asset->getKey() }} ? 'cursor-grabbing opacity-70 ring-2 ring-primary-500 ring-offset-2 dark:ring-offset-gray-950' : 'cursor-grab'"
                            class="group overflow-hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-sm transition hover:shadow-md dark:border-white/10 dark:bg-gray-900"
                        >
                            <div class="mb-3 flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-400 dark:bg-rose-500/10 dark:text-rose-300">
                                    <x-filament::icon icon="heroicon-o-photo" class="h-5 w-5" />
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="truncate text-sm font-semibold text-slate-900 dark:text-gray-100">
                                        {{ $asset->display_name }}
                                    </div>
                                </div>

                                <div class="shrink-0">
                                    <x-filament-actions::group
                                        :actions="[
                                            $this->previewAssetAction()->arguments(['asset' => $asset->getKey()]),
                                            $this->editAssetAction()->arguments(['asset' => $asset->getKey()]),
                                            $this->moveAssetAction()->arguments(['asset' => $asset->getKey()]),
                                            $this->copyAssetLinkAction()->arguments(['asset' => $asset->getKey()]),
                                            $this->deleteAssetAction()->arguments(['asset' => $asset->getKey()]),
                                        ]"
                                        icon-button
                                        icon="heroicon-o-ellipsis-vertical"
                                        tooltip="Actions"
                                        dropdown-placement="bottom-end"
                                    />
                                </div>
                            </div>

                            <div class="relative aspect-[1/1] overflow-hidden rounded-[1rem] bg-slate-100 dark:bg-gray-800">
                                @if ($asset->image_url)
                                    <img
                                        src="{{ $asset->thumbnail_url ?: $asset->image_url }}"
                                        alt="{{ $asset->display_name }}"
                                        class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                                    >
                                @else
                                    <div class="flex h-full items-center justify-center text-slate-300 dark:text-gray-600">
                                        <x-filament::icon icon="heroicon-o-photo" class="h-12 w-12" />
                                    </div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>

                @php
                    $paginator = $this->assets;
                    $currentPage = $paginator->currentPage();
                    $lastPage = $paginator->lastPage();
                    $pageName = $paginator->getPageName();
                    $pages = collect([1, $currentPage - 1, $currentPage, $currentPage + 1, $lastPage])
                        ->filter(fn (int $page): bool => $page >= 1 && $page <= $lastPage)
                        ->unique()
                        ->sort()
                        ->values();
                @endphp

                @if ($lastPage > 1)
                    <nav class="flex flex-col gap-4 rounded-[1.35rem] border border-slate-200 bg-white/90 px-4 py-4 shadow-sm backdrop-blur sm:px-5 dark:border-white/10 dark:bg-gray-900/90">
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400 dark:text-gray-500">Pagination</p>
                                <p class="mt-1 text-sm text-slate-600 dark:text-gray-300">
                                    Menampilkan
                                    <span class="font-semibold text-slate-900 dark:text-white">{{ $paginator->firstItem() }}</span>
                                    sampai
                                    <span class="font-semibold text-slate-900 dark:text-white">{{ $paginator->lastItem() }}</span>
                                    dari
                                    <span class="font-semibold text-slate-900 dark:text-white">{{ $paginator->total() }}</span>
                                    image
                                </p>
                            </div>

                            <div class="flex items-center gap-2 self-start md:self-auto">
                                <button
                                    type="button"
                                    wire:click="previousPage('{{ $pageName }}')"
                                    wire:loading.attr="disabled"
                                    @disabled($paginator->onFirstPage())
                                    class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 text-slate-500 transition hover:border-slate-300 hover:bg-slate-100 hover:text-slate-700 disabled:cursor-not-allowed disabled:opacity-40 dark:border-white/10 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-gray-600 dark:hover:bg-gray-700"
                                    aria-label="Halaman sebelumnya"
                                >
                                    <x-filament::icon icon="heroicon-o-chevron-left" class="h-5 w-5" />
                                </button>

                                <div class="hidden items-center gap-2 sm:flex">
                                    @foreach ($pages as $index => $page)
                                        @php
                                            $previousPage = $pages[$index - 1] ?? null;
                                        @endphp

                                        @if ($previousPage !== null && ($page - $previousPage) > 1)
                                            <span class="px-1 text-sm font-medium text-slate-400 dark:text-gray-500">…</span>
                                        @endif

                                        <button
                                            type="button"
                                            wire:click="gotoPage({{ $page }}, '{{ $pageName }}')"
                                            class="{{ $page === $currentPage
                                                ? 'bg-slate-900 text-white shadow-sm dark:bg-white dark:text-gray-900'
                                                : 'border border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900 dark:border-white/10 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white' }} inline-flex h-11 min-w-11 items-center justify-center rounded-2xl px-4 text-sm font-semibold transition"
                                            aria-current="{{ $page === $currentPage ? 'page' : 'false' }}"
                                        >
                                            {{ $page }}
                                        </button>
                                    @endforeach
                                </div>

                                <div class="inline-flex h-11 items-center rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-semibold text-slate-700 sm:hidden dark:border-white/10 dark:bg-gray-800 dark:text-gray-200">
                                    {{ $currentPage }} / {{ $lastPage }}
                                </div>

                                <button
                                    type="button"
                                    wire:click="nextPage('{{ $pageName }}')"
                                    wire:loading.attr="disabled"
                                    @disabled(! $paginator->hasMorePages())
                                    class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 text-slate-500 transition hover:border-slate-300 hover:bg-slate-100 hover:text-slate-700 disabled:cursor-not-allowed disabled:opacity-40 dark:border-white/10 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-gray-600 dark:hover:bg-gray-700"
                                    aria-label="Halaman berikutnya"
                                >
                                    <x-filament::icon icon="heroicon-o-chevron-right" class="h-5 w-5" />
                                </button>
                            </div>
                        </div>
                    </nav>
                @endif
            </section>
        @endif

        @if ($this->folders->isEmpty() && $this->assets->count() === 0)
            <section>
                <div class="rounded-[1.5rem] border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center dark:border-gray-700 dark:bg-gray-900">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-white text-slate-400 shadow-sm dark:bg-gray-800 dark:text-gray-500">
                        <x-filament::icon icon="heroicon-o-photo" class="h-9 w-9" />
                    </div>
                    <h4 class="mt-4 text-base font-semibold text-slate-900 dark:text-gray-100">Belum ada media di folder ini</h4>
                    <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">Buat folder baru atau upload image untuk mulai mengisi gallery.</p>
                </div>
            </section>
        @endif
    </div>
</x-filament-panels::page>
