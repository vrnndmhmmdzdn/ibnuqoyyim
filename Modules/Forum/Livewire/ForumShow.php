<?php

namespace Modules\Forum\Livewire;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Modules\Forum\Filament\Pages\ForumQuestionDetailPage;
use Modules\Forum\Filament\Pages\ForumQuestionsPage;
use Modules\Forum\Models\ForumComment;
use Modules\Forum\Models\ForumQuestion;
use Modules\Forum\Support\ForumContent;

class ForumShow extends Component implements HasForms
{
    use InteractsWithForms;

    public ForumQuestion $question;

    public ?array $data = [];
    public ?array $editQuestionData = [];
    public ?array $editCommentData = [];
    public bool $isEditQuestionModalOpen = false;
    public bool $isEditCommentModalOpen = false;
    public ?int $editingCommentId = null;

    public function mount(string $slug): void
    {
        $this->loadQuestion($slug);
        $this->form->fill([
            'replyBody' => '',
            'replyImages' => [],
        ]);
        $this->editQuestionForm->fill([
            'title' => '',
            'body' => '',
            'images' => [],
        ]);
        $this->editCommentForm->fill([
            'body' => '',
            'images' => [],
        ]);
    }

    public function render()
    {
        return view('forum::livewire.forum-show')->title($this->question->title);
    }

    public function getBackUrl(): string
    {
        return ForumQuestionsPage::getUrl();
    }

    protected function getForms(): array
    {
        return [
            'form',
            'editQuestionForm',
            'editCommentForm',
        ];
    }

    public function createReply(): void
    {
        abort_unless(Auth::check(), 403);

        Gate::authorize('create', ForumComment::class);

        $state = $this->form->getState();

        $sanitizedBody = ForumContent::sanitize((string) ($state['replyBody'] ?? ''));
        $images = array_values(array_filter((array) data_get($state, 'replyImages', [])));

        if (mb_strlen(trim(strip_tags($sanitizedBody))) < 5) {
            $this->addError('data.replyBody', 'Jawaban minimal 5 karakter.');

            return;
        }

        ForumComment::query()->create([
            'question_id' => $this->question->id,
            'user_id' => Auth::id(),
            'body' => $sanitizedBody,
            'images' => $images,
        ]);

        $this->form->fill([
            'replyBody' => '',
            'replyImages' => [],
        ]);
        $this->loadQuestion($this->question->slug);
        session()->flash('forum_success', 'Jawaban berhasil dikirim.');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                RichEditor::make('replyBody')
                    ->label('Jawaban')
                    ->placeholder('Tulis jawabanmu')
                    ->required()
                    ->minLength(5)
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'strike'],
                        ['h2', 'h3', 'blockquote', 'codeBlock'],
                        ['bulletList', 'orderedList'],
                        ['link'],
                        ['undo', 'redo'],
                    ]),
                FileUpload::make('replyImages')
                    ->label('Gambar jawaban (opsional)')
                    ->image()
                    ->multiple()
                    ->disk('public')
                    ->directory('forum/comments')
                    ->visibility('public')
                    ->maxFiles(5)
                    ->reorderable()
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function editQuestionForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul pertanyaan')
                    ->required()
                    ->minLength(10)
                    ->maxLength(255),
                RichEditor::make('body')
                    ->label('Detail pertanyaan')
                    ->required()
                    ->minLength(20)
                    ->json(false)
                    ->placeholder('Jelaskan masalah secara ringkas dan jelas...')
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'strike'],
                        ['h2', 'h3', 'blockquote', 'codeBlock'],
                        ['bulletList', 'orderedList'],
                        ['link'],
                        ['undo', 'redo'],
                    ]),
                FileUpload::make('images')
                    ->label('Gambar pertanyaan (opsional)')
                    ->image()
                    ->multiple()
                    ->disk('public')
                    ->directory('forum/questions')
                    ->visibility('public')
                    ->maxFiles(5)
                    ->reorderable()
                    ->columnSpanFull(),
            ])
            ->statePath('editQuestionData');
    }

    public function editCommentForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                RichEditor::make('body')
                    ->label('Jawaban')
                    ->required()
                    ->minLength(5)
                    ->json(false)
                    ->placeholder('Perbarui jawabanmu...')
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'strike'],
                        ['h2', 'h3', 'blockquote', 'codeBlock'],
                        ['bulletList', 'orderedList'],
                        ['link'],
                        ['undo', 'redo'],
                    ]),
                FileUpload::make('images')
                    ->label('Gambar jawaban (opsional)')
                    ->image()
                    ->multiple()
                    ->disk('public')
                    ->directory('forum/comments')
                    ->visibility('public')
                    ->maxFiles(5)
                    ->reorderable()
                    ->columnSpanFull(),
            ])
            ->statePath('editCommentData');
    }

    public function openEditQuestionModal(): void
    {
        abort_unless(Auth::check(), 403);
        Gate::authorize('update', $this->question);

        $this->resetValidation();
        $this->isEditQuestionModalOpen = true;
        $this->editQuestionForm->fill([
            'title' => $this->question->title,
            'body' => $this->question->body,
            'images' => $this->question->images ?? [],
        ]);
    }

    public function closeEditQuestionModal(): void
    {
        $this->isEditQuestionModalOpen = false;
    }

    public function saveQuestionEdit(): mixed
    {
        abort_unless(Auth::check(), 403);
        Gate::authorize('update', $this->question);

        $validated = $this->validate([
            'editQuestionData.title' => ['required', 'string', 'min:10', 'max:255'],
        ], [], [
            'editQuestionData.title' => 'judul pertanyaan',
        ]);

        $state = $this->editQuestionForm->getState();
        $sanitizedBody = ForumContent::sanitize((string) data_get($state, 'body', ''));
        $images = array_values(array_filter((array) data_get($state, 'images', [])));

        if (mb_strlen(trim(strip_tags($sanitizedBody))) < 20) {
            $this->addError('editQuestionData.body', 'Detail pertanyaan minimal 20 karakter.');

            return null;
        }

        $this->question->update([
            'title' => $validated['editQuestionData']['title'],
            'body' => $sanitizedBody,
            'images' => $images,
        ]);

        $updatedQuestion = $this->question->fresh();
        $this->isEditQuestionModalOpen = false;
        session()->flash('forum_success', 'Pertanyaan berhasil diperbarui.');

        return redirect()->to($updatedQuestion?->slug
            ? ForumQuestionDetailPage::getUrl(['slug' => $updatedQuestion->slug])
            : $this->getBackUrl());
    }

    public function deleteQuestion(): mixed
    {
        abort_unless(Auth::check(), 403);
        Gate::authorize('delete', $this->question);

        $this->question->delete();
        session()->flash('forum_success', 'Pertanyaan berhasil dihapus.');

        return redirect()->to($this->getBackUrl());
    }

    public function openEditCommentModal(int $commentId): void
    {
        abort_unless(Auth::check(), 403);

        $comment = $this->question->comments->firstWhere('id', $commentId);
        abort_unless($comment instanceof ForumComment, 404);
        Gate::authorize('update', $comment);

        $this->resetValidation();
        $this->editingCommentId = $comment->id;
        $this->isEditCommentModalOpen = true;
        $this->editCommentForm->fill([
            'body' => $comment->body,
            'images' => $comment->images ?? [],
        ]);
    }

    public function closeEditCommentModal(): void
    {
        $this->isEditCommentModalOpen = false;
        $this->editingCommentId = null;
    }

    public function saveCommentEdit(): void
    {
        abort_unless(Auth::check(), 403);
        abort_unless($this->editingCommentId !== null, 404);

        $comment = ForumComment::query()->whereKey($this->editingCommentId)->firstOrFail();
        Gate::authorize('update', $comment);

        $state = $this->editCommentForm->getState();
        $sanitizedBody = ForumContent::sanitize((string) data_get($state, 'body', ''));
        $images = array_values(array_filter((array) data_get($state, 'images', [])));

        if (mb_strlen(trim(strip_tags($sanitizedBody))) < 5) {
            $this->addError('editCommentData.body', 'Jawaban minimal 5 karakter.');

            return;
        }

        $comment->update([
            'body' => $sanitizedBody,
            'images' => $images,
        ]);

        $this->closeEditCommentModal();
        $this->loadQuestion($this->question->slug);
        session()->flash('forum_success', 'Jawaban berhasil diperbarui.');
    }

    public function deleteComment(int $commentId): void
    {
        abort_unless(Auth::check(), 403);

        $comment = ForumComment::query()->whereKey($commentId)->firstOrFail();
        Gate::authorize('delete', $comment);

        $comment->delete();

        $this->loadQuestion($this->question->slug);
        session()->flash('forum_success', 'Jawaban berhasil dihapus.');
    }

    public function canEditQuestion(): bool
    {
        return Auth::check() && Gate::allows('update', $this->question);
    }

    public function canDeleteQuestion(): bool
    {
        return Auth::check() && Gate::allows('delete', $this->question);
    }

    public function canEditComment(int $commentId): bool
    {
        if (! Auth::check()) {
            return false;
        }

        $comment = $this->question->comments->firstWhere('id', $commentId);

        return $comment instanceof ForumComment && Gate::allows('update', $comment);
    }

    public function canDeleteComment(int $commentId): bool
    {
        if (! Auth::check()) {
            return false;
        }

        $comment = $this->question->comments->firstWhere('id', $commentId);

        return $comment instanceof ForumComment && Gate::allows('delete', $comment);
    }

    public function getLoginUrl(): ?string
    {
        if (Route::has('filament.admin.auth.login')) {
            return route('filament.admin.auth.login');
        }

        if (Route::has('login')) {
            return route('login');
        }

        return null;
    }

    protected function loadQuestion(string $slug): void
    {
        $this->question = ForumQuestion::query()
            ->where('slug', $slug)
            ->with([
                'user',
                'comments' => fn ($query) => $query->with('user')->oldest(),
            ])
            ->firstOrFail();
    }
}
