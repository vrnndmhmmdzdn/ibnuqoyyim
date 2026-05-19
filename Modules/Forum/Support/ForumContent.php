<?php

namespace Modules\Forum\Support;

use DOMDocument;
use DOMElement;
use Illuminate\Support\Str;

class ForumContent
{
    /**
     * @var list<string>
     */
    protected static array $allowedTags = [
        'p',
        'br',
        'strong',
        'b',
        'em',
        'i',
        'u',
        'ul',
        'ol',
        'li',
        'blockquote',
        'code',
        'pre',
        'a',
        'h1',
        'h2',
        'h3',
        'h4',
    ];

    public static function sanitize(?string $content): string
    {
        $content = trim((string) $content);

        if ($content === '') {
            return '';
        }

        $content = preg_replace('#<(script|style)[^>]*>.*?</\1>#is', '', $content) ?? '';
        $content = strip_tags($content, self::allowedTagString());

        if ($content === '') {
            return '';
        }

        if (! class_exists(DOMDocument::class)) {
            return $content;
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $html = mb_convert_encoding('<div>' . $content . '</div>', 'HTML-ENTITIES', 'UTF-8');

        libxml_use_internal_errors(true);
        $loaded = $document->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        if (! $loaded) {
            return $content;
        }

        foreach ($document->getElementsByTagName('*') as $element) {
            if (! $element instanceof DOMElement) {
                continue;
            }

            $attributes = [];

            foreach ($element->attributes ?? [] as $attribute) {
                $attributes[] = $attribute->name;
            }

            foreach ($attributes as $attributeName) {
                if (Str::startsWith(Str::lower($attributeName), 'on')) {
                    $element->removeAttribute($attributeName);

                    continue;
                }

                if ($element->tagName !== 'a' || ! in_array($attributeName, ['href', 'target', 'rel'], true)) {
                    $element->removeAttribute($attributeName);
                }
            }

            if ($element->tagName === 'a') {
                $href = trim((string) $element->getAttribute('href'));

                if (! self::isSafeHref($href)) {
                    $element->removeAttribute('href');
                } else {
                    $element->setAttribute('rel', 'nofollow noopener noreferrer');

                    if (Str::startsWith($href, ['http://', 'https://'])) {
                        $element->setAttribute('target', '_blank');
                    }
                }
            }
        }

        $wrapper = $document->getElementsByTagName('div')->item(0);

        if (! $wrapper instanceof DOMElement) {
            return $content;
        }

        $html = '';

        foreach ($wrapper->childNodes as $childNode) {
            $html .= $document->saveHTML($childNode);
        }

        return trim($html);
    }

    public static function render(?string $content): string
    {
        $content = trim((string) $content);

        if ($content === '') {
            return '';
        }

        // Render escaped HTML snippets as code blocks, not executable HTML.
        if (self::containsEscapedHtmlTags($content)) {
            return self::renderEscapedHtmlAsCode($content);
        }

        if (! self::containsHtml($content)) {
            return nl2br(e($content));
        }

        return self::sanitize($content);
    }

    public static function preview(?string $content, int $limit = 220): string
    {
        $sanitized = self::sanitize($content);
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($sanitized)) ?? '');

        if ($text === '') {
            $text = trim((string) $content);
        }

        return Str::limit($text, $limit);
    }

    protected static function containsHtml(string $content): bool
    {
        return $content !== strip_tags($content);
    }

    protected static function allowedTagString(): string
    {
        return '<' . implode('><', self::$allowedTags) . '>';
    }

    protected static function isSafeHref(string $href): bool
    {
        if ($href === '') {
            return false;
        }

        if (Str::startsWith($href, ['#', '/', 'mailto:', 'tel:'])) {
            return true;
        }

        return Str::startsWith($href, ['http://', 'https://']);
    }

    protected static function decodeHtmlEntitiesRecursively(string $content, int $maxDepth = 3): string
    {
        $decoded = $content;

        for ($i = 0; $i < $maxDepth; $i++) {
            $next = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            if ($next === $decoded) {
                break;
            }

            $decoded = $next;
        }

        return $decoded;
    }

    protected static function containsEscapedHtmlTags(string $content): bool
    {
        $decoded = self::decodeHtmlEntitiesRecursively($content);

        return (bool) preg_match('/&lt;\/?[a-z!][^&]*&gt;/i', $content)
            || (bool) preg_match('/<(?:!doctype|html|head|body|title|script|style|meta|link)\b/i', $decoded);
    }

    protected static function renderEscapedHtmlAsCode(string $content): string
    {
        $normalized = str_replace(["\r\n", "\r"], "\n", $content);
        $normalized = preg_replace('#<br\s*/?>#i', "\n", $normalized) ?? $normalized;
        $normalized = preg_replace('#<(/?(p|div|h[1-6]|li|ul|ol|pre|blockquote))[^>]*>#i', "\n", $normalized) ?? $normalized;
        $normalized = strip_tags($normalized);
        $normalized = self::decodeHtmlEntitiesRecursively($normalized);
        $normalized = trim(preg_replace("/\n{3,}/", "\n\n", $normalized) ?? $normalized);

        return '<pre class="max-w-full overflow-x-auto whitespace-pre-wrap break-words rounded-md border border-slate-200 bg-slate-100 p-3 text-slate-800 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"><code class="whitespace-pre-wrap break-words text-inherit">' . e($normalized) . '</code></pre>';
    }
}
