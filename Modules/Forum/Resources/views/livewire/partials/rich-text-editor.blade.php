@props([
    'model',
    'placeholder' => 'Tulis isi konten...',
    'minHeight' => '12rem',
    'inputId' => 'forum-editor',
])

<div
    x-data="forumRichTextEditor({
        initialValue: @js((string) data_get($this, $model)),
        placeholder: @js($placeholder),
        minHeight: @js($minHeight),
    })"
    x-init="init()"
    class="overflow-hidden rounded-3xl border border-orange-100 bg-white shadow-sm shadow-orange-100/70"
>
    <div class="flex flex-wrap items-center gap-2 border-b border-orange-100 bg-orange-50/70 p-3">
        <button type="button" @click="format('bold')" class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-white hover:text-orange-700">Bold</button>
        <button type="button" @click="format('italic')" class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-white hover:text-orange-700">Italic</button>
        <button type="button" @click="format('underline')" class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-white hover:text-orange-700">Underline</button>
        <button type="button" @click="format('insertUnorderedList')" class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-white hover:text-orange-700">Bullet</button>
        <button type="button" @click="format('insertOrderedList')" class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-white hover:text-orange-700">Number</button>
        <button type="button" @click="format('formatBlock', 'blockquote')" class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-white hover:text-orange-700">Quote</button>
        <button type="button" @click="format('formatBlock', 'pre')" class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-white hover:text-orange-700">Code</button>
        <button type="button" @click="insertLink()" class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-white hover:text-orange-700">Link</button>
        <button type="button" @click="clearFormatting()" class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-white hover:text-orange-700">Clear</button>
    </div>

    <div class="relative bg-white p-4">
        <div
            x-ref="editor"
            contenteditable="true"
            @input.debounce.150ms="syncFromEditor()"
            @blur="syncFromEditor()"
            @paste="handlePaste($event)"
            class="prose prose-slate max-w-none rounded-2xl border border-transparent px-1 py-1 text-base leading-7 text-slate-700 outline-none focus:border-orange-200"
            :style="{ minHeight }"
        ></div>

        <p
            x-show="isEmpty"
            x-cloak
            class="pointer-events-none absolute left-5 top-5 text-sm text-slate-400"
            x-text="placeholder"
        ></p>
    </div>

    <textarea id="{{ $inputId }}" wire:model.live.debounce.300ms="{{ $model }}" x-model="value" class="hidden"></textarea>
</div>

@once
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('forumRichTextEditor', (config) => ({
                value: config.initialValue || '',
                placeholder: config.placeholder || '',
                minHeight: config.minHeight || '12rem',
                isEmpty: true,
                init() {
                    this.renderValue();

                    this.$watch('value', () => {
                        if (this.$refs.editor.innerHTML !== this.value) {
                            this.renderValue();
                        }
                    });
                },
                renderValue() {
                    this.$refs.editor.innerHTML = this.value || '';
                    this.updateEmptyState();
                },
                syncFromEditor() {
                    this.value = this.$refs.editor.innerHTML.trim();
                    this.updateEmptyState();
                },
                updateEmptyState() {
                    const text = this.$refs.editor.innerText.replace(/\u00a0/g, ' ').trim();
                    this.isEmpty = text.length === 0;
                },
                format(command, value = null) {
                    this.$refs.editor.focus();
                    document.execCommand(command, false, value);
                    this.syncFromEditor();
                },
                insertLink() {
                    const url = window.prompt('Masukkan URL');

                    if (!url) {
                        return;
                    }

                    this.format('createLink', url);
                },
                clearFormatting() {
                    this.$refs.editor.focus();
                    document.execCommand('removeFormat', false, null);
                    this.syncFromEditor();
                },
                handlePaste(event) {
                    event.preventDefault();
                    const text = (event.clipboardData || window.clipboardData).getData('text/plain');
                    document.execCommand('insertText', false, text);
                    this.syncFromEditor();
                },
            }));
        });
    </script>
@endonce
