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
use Livewire\WithPagination;
use Modules\Forum\Filament\Pages\ForumQuestionDetailPage;
use Modules\Forum\Models\ForumQuestion;
use Modules\Forum\Support\ForumContent;

class ForumIndex extends Component implements HasForms
{
    use InteractsWithForms;
    use WithPagination;

    public string $search = '';

    public ?array $data = [];

    public bool $isCreateModalOpen = false;

    public function mount(): void
    {
        $this->form->fill([
            'newQuestionTitle' => '',
            'newQuestionBody' => '',
            'newQuestionImages' => [],
        ]);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        abort_unless($this->canCreateQuestion(), 403);

        $this->resetValidation();
        $this->isCreateModalOpen = true;
    }

    public function closeCreateModal(): void
    {
        $this->isCreateModalOpen = false;
    }

    public function createQuestion(): mixed
    {
        abort_unless(Auth::check(), 403);

        Gate::authorize('create', ForumQuestion::class);

        $validated = $this->validate([
            'data.newQuestionTitle' => ['required', 'string', 'min:10', 'max:255'],
        ], [], [
            'data.newQuestionTitle' => 'judul pertanyaan',
        ]);

        $state = $this->form->getState();
        $rawBody = data_get($state, 'newQuestionBody');
        $body = is_string($rawBody) ? $rawBody : '';
        $sanitizedBody = ForumContent::sanitize($body);
        $images = array_values(array_filter((array) data_get($state, 'newQuestionImages', [])));

        if (mb_strlen(trim(strip_tags($sanitizedBody))) < 20) {
            $this->addError('data.newQuestionBody', 'Detail pertanyaan minimal 20 karakter.');

            return null;
        }

        $question = ForumQuestion::query()->create([
            'user_id' => Auth::id(),
            'title' => $validated['data']['newQuestionTitle'],
            'body' => $sanitizedBody,
            'images' => $images,
        ]);

        $this->form->fill([
            'newQuestionTitle' => '',
            'newQuestionBody' => '',
            'newQuestionImages' => [],
        ]);
        $this->isCreateModalOpen = false;
        session()->flash('forum_success', 'Pertanyaan berhasil dibuat.');

        return redirect()->to($this->getQuestionUrl($question));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('newQuestionTitle')
                    ->label('Judul pertanyaan')
                    ->required()
                    ->minLength(10)
                    ->maxLength(255)
                    ->placeholder('Contoh: Cara handle eager loading di halaman listing?'),
                RichEditor::make('newQuestionBody')
                    ->label('Detail pertanyaan')
                    ->required()
                    ->json(false)
                    ->placeholder('Jelaskan masalah, langkah yang sudah dicoba, dan hasil yang diharapkan...')
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'strike'],
                        ['h2', 'h3', 'blockquote', 'codeBlock'],
                        ['bulletList', 'orderedList'],
                        ['link'],
                        ['undo', 'redo'],
                    ]),
                FileUpload::make('newQuestionImages')
                    ->label('Gambar (opsional)')
                    ->image()
                    ->multiple()
                    ->disk('public')
                    ->directory('forum/questions')
                    ->visibility('public')
                    ->maxFiles(5)
                    ->reorderable()
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function render()
    {
        $questions = ForumQuestion::query()
            ->with('user')
            ->withCount('comments')
            ->when(
                filled($this->search),
                fn ($query) => $query->where('title', 'like', '%' . $this->search . '%')
            )
            ->latest()
            ->paginate(10);

        return view('forum::livewire.forum-index', [
            'questions' => $questions,
        ])->title('Forum');
    }

    public function getQuestionUrl(ForumQuestion $question): string
    {
        $page = max((int) request()->query('page', 1), 1);

        return ForumQuestionDetailPage::getUrl([
            'slug' => $question->slug,
            'from_page' => $page,
        ]);
    }

    public function canCreateQuestion(): bool
    {
        return Auth::check() && Gate::allows('create', ForumQuestion::class);
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
}
