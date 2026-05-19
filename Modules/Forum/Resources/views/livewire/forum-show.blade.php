@php
    use Illuminate\Support\Str;
    use Modules\Forum\Support\ForumContent;

    $authorName = $question->user?->name ?? 'Unknown';
    $originalInitial = Str::upper(Str::substr($authorName, 0, 1));
@endphp

<div class="bg-gradient-to-b from-orange-50/40 via-white to-white text-slate-800 dark:from-gray-900 dark:via-gray-900 dark:to-gray-900 dark:text-gray-100">
    <main class="w-full px-4 py-6 sm:px-6 lg:px-8">
        <section class="space-y-6">
            @if (session()->has('forum_success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-300">
                    {{ session('forum_success') }}
                </div>
            @endif

            <article class="border-b border-slate-200/80 pb-6 dark:border-white/10">
                <div class="flex items-start gap-4">
                    <div class="hidden sm:block">
                        <div class="flex size-11 items-center justify-center rounded-full bg-orange-100 text-base font-semibold text-orange-700 dark:bg-orange-500/20 dark:text-orange-300">
                            {{ $originalInitial }}
                        </div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-2">
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $authorName }}</h2>
                            <span class="text-xs text-slate-500 dark:text-gray-400">{{ $question->created_at?->diffForHumans() }}</span>
                            </div>

                            @if ($this->canEditQuestion() || $this->canDeleteQuestion())
                                <div class="flex items-center gap-2">
                                    @if ($this->canEditQuestion())
                                        <button
                                            type="button"
                                            wire:click="openEditQuestionModal"
                                            class="inline-flex items-center rounded-md border border-orange-200 px-2.5 py-1 text-xs font-medium text-orange-700 transition hover:bg-orange-50 hover:text-orange-800 dark:border-orange-500/30 dark:text-orange-300 dark:hover:bg-orange-500/10 dark:hover:text-orange-200"
                                        >
                                            Edit
                                        </button>
                                    @endif
                                    @if ($this->canDeleteQuestion())
                                        <button
                                            type="button"
                                            wire:click="deleteQuestion"
                                            wire:confirm="Hapus pertanyaan ini beserta semua jawaban?"
                                            class="inline-flex items-center rounded-md border border-rose-200 px-2.5 py-1 text-xs font-medium text-rose-600 transition hover:bg-rose-50 dark:border-rose-500/30 dark:text-rose-300 dark:hover:bg-rose-500/10"
                                        >
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="prose prose-slate mt-4 max-w-none break-words text-[15px] leading-7 text-slate-700 [&_code]:break-words [&_code]:whitespace-pre-wrap [&_p]:break-words [&_pre]:max-w-full [&_pre]:overflow-x-auto [&_pre]:whitespace-pre-wrap dark:prose-invert dark:text-gray-200">
                            {!! ForumContent::render($question->body) !!}
                        </div>

                        @if (!empty($question->images))
                            <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                @foreach ($question->images as $image)
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($image) }}" target="_blank" rel="noopener noreferrer">
                                        <img
                                            src="{{ \Illuminate\Support\Facades\Storage::url($image) }}"
                                            alt="Question image"
                                            class="h-40 w-full rounded-lg border border-slate-200 object-cover transition hover:opacity-90 dark:border-white/10"
                                        />
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </article>

            <section class="border-b border-slate-200/80 py-6 dark:border-white/10">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-orange-700 dark:text-orange-300">Tulis jawaban</h2>
                    </div>

                    @guest
                        @if ($this->getLoginUrl())
                            <a
                                href="{{ $this->getLoginUrl() }}"
                                class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-white/10 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                            >
                                Login untuk menjawab
                            </a>
                        @endif
                @endguest
                </div>

                @auth
                    <form wire:submit="createReply" class="mt-6 space-y-4">
                        {{ $this->form }}
                        @error('data.replyBody')
                            <p class="text-sm text-rose-600">{{ $message }}</p>
                        @enderror

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-lg bg-orange-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-600"
                            >
                                Kirim jawaban
                            </button>
                        </div>
                    </form>
                @else
                    <div class="mt-6 rounded-xl border border-dashed border-slate-300 bg-slate-50 px-5 py-8 text-center dark:border-gray-700 dark:bg-gray-800/50">
                        <p class="text-base font-semibold text-slate-700 dark:text-gray-200">Perlu login untuk menjawab pertanyaan ini.</p>
                        <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">Setelah login, editor jawaban akan muncul di sini.</p>
                    </div>
                @endauth
            </section>

            <section class="pt-1">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-lg font-semibold text-orange-700 dark:text-orange-300">Komentar</h2>
                    <span class="inline-flex items-center rounded-full bg-orange-100 px-2.5 py-1 text-xs font-medium text-orange-700 dark:bg-orange-500/20 dark:text-orange-300">
                        {{ $question->comments->count() }} jawaban
                    </span>
                </div>
                <p class="mt-1 text-sm text-orange-700/80 dark:text-orange-200/80">Daftar jawaban dari diskusi ini.</p>
            </section>

            <div class="divide-y divide-slate-200/80 dark:divide-white/10">
                @forelse ($question->comments as $comment)
                    @php
                        $commentAuthor = $comment->user?->name ?? 'Unknown';
                        $commentInitial = Str::upper(Str::substr($commentAuthor, 0, 1));
                    @endphp

                    <article class="py-6">
                        <div class="flex items-start gap-4">
                            <div class="hidden sm:block">
                                <div class="flex size-11 items-center justify-center rounded-full bg-orange-100 text-base font-semibold text-orange-700 dark:bg-orange-500/20 dark:text-orange-300">
                                    {{ $commentInitial }}
                                </div>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-2">
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $commentAuthor }}</h3>
                                        <span class="text-xs text-slate-500 dark:text-gray-400">{{ $comment->created_at?->diffForHumans() }}</span>
                                    </div>

                                    @if ($this->canEditComment($comment->id) || $this->canDeleteComment($comment->id))
                                        <div class="flex items-center gap-2">
                                            @if ($this->canEditComment($comment->id))
                                                <button
                                                    type="button"
                                                    wire:click="openEditCommentModal({{ $comment->id }})"
                                                    class="inline-flex items-center rounded-md border border-orange-200 px-2.5 py-1 text-xs font-medium text-orange-700 transition hover:bg-orange-50 hover:text-orange-800 dark:border-orange-500/30 dark:text-orange-300 dark:hover:bg-orange-500/10 dark:hover:text-orange-200"
                                                >
                                                    Edit
                                                </button>
                                            @endif
                                            @if ($this->canDeleteComment($comment->id))
                                                <button
                                                    type="button"
                                                    wire:click="deleteComment({{ $comment->id }})"
                                                    wire:confirm="Hapus jawaban ini?"
                                                    class="inline-flex items-center rounded-md border border-rose-200 px-2.5 py-1 text-xs font-medium text-rose-600 transition hover:bg-rose-50 dark:border-rose-500/30 dark:text-rose-300 dark:hover:bg-rose-500/10"
                                                >
                                                    Hapus
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="prose prose-slate mt-4 max-w-none break-words text-[15px] leading-7 text-slate-700 [&_code]:break-words [&_code]:whitespace-pre-wrap [&_p]:break-words [&_pre]:max-w-full [&_pre]:overflow-x-auto [&_pre]:whitespace-pre-wrap dark:prose-invert dark:text-gray-200">
                                    {!! ForumContent::render($comment->body) !!}
                                </div>

                                @if (!empty($comment->images))
                                    <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                        @foreach ($comment->images as $image)
                                            <a href="{{ \Illuminate\Support\Facades\Storage::url($image) }}" target="_blank" rel="noopener noreferrer">
                                                <img
                                                    src="{{ \Illuminate\Support\Facades\Storage::url($image) }}"
                                                    alt="Comment image"
                                                    class="h-36 w-full rounded-lg border border-slate-200 object-cover transition hover:opacity-90 dark:border-white/10"
                                                />
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="py-10 text-center">
                        <p class="text-lg font-semibold text-slate-700 dark:text-gray-200">Belum ada komentar.</p>
                        <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">Thread ini masih menunggu balasan pertama.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </main>

    @if ($isEditQuestionModalOpen)
        <div class="fixed inset-0 z-[70] flex items-end justify-center bg-slate-900/50 p-3 sm:items-center sm:p-6">
            <div class="w-full max-w-3xl rounded-xl border border-slate-200 bg-white p-4 shadow-xl dark:border-white/10 dark:bg-gray-900 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Edit pengumuman</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">Perbarui judul dan detail pengumuman.</p>
                    </div>
                    <button
                        type="button"
                        wire:click="closeEditQuestionModal"
                        class="inline-flex size-8 items-center justify-center rounded-md border border-slate-200 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 dark:border-white/10 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
                        aria-label="Tutup modal"
                    >
                        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M4.22 4.22a.75.75 0 0 1 1.06 0L10 8.94l4.72-4.72a.75.75 0 1 1 1.06 1.06L11.06 10l4.72 4.72a.75.75 0 1 1-1.06 1.06L10 11.06l-4.72 4.72a.75.75 0 0 1-1.06-1.06L8.94 10 4.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="saveQuestionEdit" class="mt-5 space-y-4">
                    {{ $this->editQuestionForm }}
                    @error('editQuestionData.body')
                        <p class="text-sm text-rose-600">{{ $message }}</p>
                    @enderror

                    <div class="flex items-center justify-end gap-2">
                        <button
                            type="button"
                            wire:click="closeEditQuestionModal"
                            class="inline-flex items-center rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-white/10 dark:text-gray-200 dark:hover:bg-gray-800"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-600"
                        >
                            Simpan perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($isEditCommentModalOpen)
        <div class="fixed inset-0 z-[70] flex items-end justify-center bg-slate-900/50 p-3 sm:items-center sm:p-6">
            <div class="w-full max-w-3xl rounded-xl border border-slate-200 bg-white p-4 shadow-xl dark:border-white/10 dark:bg-gray-900 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Edit jawaban</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">Perbarui isi jawabanmu.</p>
                    </div>
                    <button
                        type="button"
                        wire:click="closeEditCommentModal"
                        class="inline-flex size-8 items-center justify-center rounded-md border border-slate-200 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 dark:border-white/10 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
                        aria-label="Tutup modal"
                    >
                        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M4.22 4.22a.75.75 0 0 1 1.06 0L10 8.94l4.72-4.72a.75.75 0 1 1 1.06 1.06L11.06 10l4.72 4.72a.75.75 0 1 1-1.06 1.06L10 11.06l-4.72 4.72a.75.75 0 0 1-1.06-1.06L8.94 10 4.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="saveCommentEdit" class="mt-5 space-y-4">
                    {{ $this->editCommentForm }}
                    @error('editCommentData.body')
                        <p class="text-sm text-rose-600">{{ $message }}</p>
                    @enderror

                    <div class="flex items-center justify-end gap-2">
                        <button
                            type="button"
                            wire:click="closeEditCommentModal"
                            class="inline-flex items-center rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-white/10 dark:text-gray-200 dark:hover:bg-gray-800"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-600"
                        >
                            Simpan perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
