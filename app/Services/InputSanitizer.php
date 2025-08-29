<?php

namespace App\Services;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class InputSanitizer
{
    protected HtmlSanitizer $strictSanitizer;
    protected HtmlSanitizer $relaxedSanitizer;

    public function __construct()
    {
        // Strict: no HTML elements allowed
        $strictConfig = new HtmlSanitizerConfig();
        $this->strictSanitizer = new HtmlSanitizer($strictConfig);

        // Relaxed: allow a minimal, safe subset of inline/formatting elements
        $relaxedConfig = (new HtmlSanitizerConfig())
            ->allowSafeElements()
            ->allowLinkSchemes(['http', 'https', 'mailto'])
            ->allowRelativeLinks()
            ->allowElement('a', ['href', 'title'])
            ->allowElement('b')
            ->allowElement('strong')
            ->allowElement('i')
            ->allowElement('em')
            ->allowElement('u')
            ->allowElement('p')
            ->allowElement('br')
            ->allowElement('ul')
            ->allowElement('ol')
            ->allowElement('li')
            ->allowElement('span'); // no attributes allowed by default

        $this->relaxedSanitizer = new HtmlSanitizer($relaxedConfig);
    }

    public function strict(?string $value): ?string
    {
        if ($value === null) return null;
        $clean = $this->strictSanitizer->sanitize($value);
        return $this->normalize($clean);
    }

    public function relaxed(?string $value): ?string
    {
        if ($value === null) return null;
        $clean = $this->relaxedSanitizer->sanitize($value);
        return $this->normalize($clean);
    }

    public function minimal(?string $value): ?string
    {
        if ($value === null) return null;
        // Remove ASCII control characters except common whitespace, then trim and normalize spaces
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
        return $this->normalize($value);
    }

    protected function normalize(string $value): string
    {
        // Collapse various whitespace to a single space and trim
        $value = preg_replace('/[ \t\f\x{00A0}\x{1680}\x{2000}-\x{200A}\x{202F}\x{205F}\x{3000}]+/u', ' ', $value);
        return trim($value);
    }
}