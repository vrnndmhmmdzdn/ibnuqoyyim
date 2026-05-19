@php
    use Illuminate\Support\Str;
    use Modules\Forum\Support\ForumContent;

    $totalDiscussions = (int) $questions->total();
    $focusSlug = request()->query('focus');
@endphp

<div
    x-data
    x-init="
        const slug = @js($focusSlug);
        if (!slug) return;
        requestAnimationFrame(() => {
            const target = document.getElementById(`forum-question-${slug}`);
            if (!target) return;
            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            target.classList.add('ring-2', 'ring-orange-300', 'ring-offset-2', 'ring-offset-white', 'dark:ring-offset-gray-900');
            setTimeout(() => target.classList.remove('ring-2', 'ring-orange-300', 'ring-offset-2', 'ring-offset-white', 'dark:ring-offset-gray-900'), 2200);
            const url = new URL(window.location.href);
            url.searchParams.delete('focus');
            window.history.replaceState({}, '', url.toString());
        });
    "
    class="relative overflow-hidden bg-gradient-to-b from-orange-50/40 via-white to-white text-slate-800 dark:from-gray-900 dark:via-gray-900 dark:to-gray-900 dark:text-gray-100"
>
    <div aria-hidden="true" class="pointer-events-none absolute -top-20 -right-16 h-56 w-56 rounded-full bg-orange-200/35 blur-3xl dark:bg-orange-500/10"></div>
    <div aria-hidden="true" class="pointer-events-none absolute top-52 -left-20 h-48 w-48 rounded-full bg-amber-200/30 blur-3xl dark:bg-amber-500/10"></div>
    <main id="forum-discussions" class="w-full px-4 py-6 sm:px-6 lg:px-8">
        <section id="discussion-list" class="space-y-5">
            @if (session()->has('forum_success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-300">
                    {{ session('forum_success') }}
                </div>
            @endif

            <div class="rounded-xl border border-slate-200 border-t-4 border-t-orange-300 bg-white/95 p-4 shadow-sm dark:border-white/10 dark:border-t-orange-400 dark:bg-gray-900/95 sm:p-5">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                        <h2 class="text-xl font-semibold tracking-tight text-slate-900 dark:text-white">Pengumuman Terbaru</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">{{ number_format($totalDiscussions) }} topik</p>
                        </div>

                        @if ($this->canCreateQuestion())
                            <button
                                type="button"
                                wire:click="openCreateModal"
                                class="inline-flex items-center rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-600"
                            >
                                Buat Pengumuman
                            </button>
                        @endif
                    </div>

                    <div class="relative w-full sm:w-96">
                        <label for="forum-search" class="sr-only">Cari Pengumuman</label>
                        <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-orange-400 dark:text-orange-400/80" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.473 9.765l3.63 3.631a.75.75 0 1 0 1.06-1.061l-3.63-3.63A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                        </svg>
                        <input
                            id="forum-search"
                            type="text"
                            wire:model.live.debounce.400ms="search"
                            placeholder="Cari pengumuman"
                            class="w-full rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-orange-300 focus:ring-2 focus:ring-orange-200 dark:border-white/10 dark:bg-gray-800 dark:text-gray-100 dark:placeholder:text-gray-500 dark:focus:border-orange-400/60 dark:focus:ring-orange-500/20"
                        />
                    </div>
                </div>
            </div>

            @forelse ($questions as $question)
                @php
                    $authorName = $question->user?->name ?? 'Unknown';
                    $initial = Str::upper(Str::substr($authorName, 0, 1));
                    $imageCount = is_array($question->images) ? count($question->images) : 0;
                @endphp

                <a
                    href="{{ $this->getQuestionUrl($question) }}"
                    id="forum-question-{{ $question->slug }}"
                    class="group block rounded-xl border border-slate-200 border-l-4 border-l-orange-200 bg-white/95 px-5 py-5 transition hover:border-orange-300 hover:border-l-orange-400 hover:bg-orange-50/40 dark:border-white/10 dark:border-l-orange-500/30 dark:bg-gray-900/95 dark:hover:border-orange-500/50 dark:hover:bg-gray-800/70"
                >
                    <div class="flex items-start gap-4">
                        <div class="hidden sm:block">
                            <div class="flex size-11 items-center justify-center rounded-full bg-orange-100 text-base font-semibold text-orange-700 dark:bg-orange-500/20 dark:text-orange-300">
                                {{ $initial }}
                            </div>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <h4 class="text-lg font-semibold tracking-tight text-slate-900 dark:text-white">
                                    {{ $question->title }}
                                </h4>
                                <div class="hidden shrink-0 items-center gap-2 sm:flex">
                                    @if ($imageCount > 0)
                                        <span class="inline-flex items-center gap-1 rounded-md border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
                                            <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M3 4.75A1.75 1.75 0 0 1 4.75 3h10.5A1.75 1.75 0 0 1 17 4.75v10.5A1.75 1.75 0 0 1 15.25 17H4.75A1.75 1.75 0 0 1 3 15.25V4.75Zm3.5 1.75a1.25 1.25 0 1 0 0 2.5 1.25 1.25 0 0 0 0-2.5Zm8.849 8.5H4.651l2.89-3.29a.75.75 0 0 1 1.118-.02l1.72 1.867 1.89-2.292a.75.75 0 0 1 1.146 0L15.35 15Z" />
                                            </svg>
                                            {{ $imageCount }} gambar
                                        </span>
                                    @endif
                                    <span class="inline-flex rounded-md border border-orange-200 bg-orange-50 px-2.5 py-1 text-xs font-medium text-orange-700 dark:border-orange-500/30 dark:bg-orange-500/10 dark:text-orange-300">
                                        {{ number_format($question->comments_count) }} balasan
                                    </span>
                                </div>
                            </div>

                            <p class="mt-2 line-clamp-2 max-w-3xl text-sm leading-6 text-slate-600 dark:text-gray-300">
                                {{ ForumContent::preview($question->body, 190) }}
                            </p>

                            <div class="mt-4 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500 dark:text-gray-400">
                                <span class="font-medium text-slate-700 dark:text-gray-200">{{ $authorName }}</span>
                                <span>{{ $question->created_at?->diffForHumans() }}</span>
                                @if ($question->updated_at?->ne($question->created_at))
                                    <span>diupdate {{ $question->updated_at?->diffForHumans() }}</span>
                                @endif
                                @if ($imageCount > 0)
                                    <span class="inline-flex items-center gap-1 rounded-md border border-amber-200 bg-amber-50 px-2 py-0.5 text-[11px] font-medium text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300 sm:hidden">
                                        <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="M3 4.75A1.75 1.75 0 0 1 4.75 3h10.5A1.75 1.75 0 0 1 17 4.75v10.5A1.75 1.75 0 0 1 15.25 17H4.75A1.75 1.75 0 0 1 3 15.25V4.75Zm3.5 1.75a1.25 1.25 0 1 0 0 2.5 1.25 1.25 0 0 0 0-2.5Zm8.849 8.5H4.651l2.89-3.29a.75.75 0 0 1 1.118-.02l1.72 1.867 1.89-2.292a.75.75 0 0 1 1.146 0L15.35 15Z" />
                                        </svg>
                                        {{ $imageCount }} gambar
                                    </span>
                                @endif
                                <span class="sm:hidden">{{ number_format($question->comments_count) }} balasan</span>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center dark:border-gray-700 dark:bg-gray-900">
                    <p class="text-lg font-semibold text-slate-700 dark:text-gray-200">Pengumuman tidak ditemukan.</p>
                    <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">Coba ganti kata kunci pencarian dan lihat pengumuman lain.</p>
                </div>
            @endforelse

            <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-gray-900">
                <x-filament::pagination :paginator="$questions" :extreme-links="true" />
            </div>
        </section>
    </main>

    @if ($isCreateModalOpen)
        <div class="fixed inset-0 z-[70] flex items-end justify-center bg-slate-900/50 p-3 sm:items-center sm:p-6" wire:key="forum-create-modal">
            <div class="w-full max-w-3xl rounded-xl border border-slate-200 bg-white p-4 shadow-xl dark:border-white/10 dark:bg-gray-900 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Buat pengumuman baru</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">Tulis judul dan detail pengumuman, lalu kirim.</p>
                    </div>
                    <button
                        type="button"
                        wire:click="closeCreateModal"
                        class="inline-flex size-8 items-center justify-center rounded-md border border-slate-200 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 dark:border-white/10 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
                        aria-label="Tutup modal"
                    >
                        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M4.22 4.22a.75.75 0 0 1 1.06 0L10 8.94l4.72-4.72a.75.75 0 1 1 1.06 1.06L11.06 10l4.72 4.72a.75.75 0 1 1-1.06 1.06L10 11.06l-4.72 4.72a.75.75 0 0 1-1.06-1.06L8.94 10 4.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="createQuestion" class="mt-5 space-y-4">
                    {{ $this->form }}

                    <div class="flex items-center justify-end gap-2">
                        <button
                            type="button"
                            wire:click="closeCreateModal"
                            class="inline-flex items-center rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-white/10 dark:text-gray-200 dark:hover:bg-gray-800"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-600"
                        >
                            Kirim pertanyaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
