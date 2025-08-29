<?php

namespace App\Http\Middleware;

use App\Services\InputSanitizer;
use Closure;
use Illuminate\Http\Request;

class InputSanitizationMiddleware
{
    protected InputSanitizer $sanitizer;

    // Keys treated with strict sanitization (no HTML)
    protected array $strictKeys = [
        'title', 'name', 'subject', 'summary', 'short_description', 'username', 'first_name', 'last_name'
    ];

    // Keys treated with relaxed sanitization (limited safe HTML)
    protected array $relaxedKeys = [
        'description', 'body', 'content', 'notes', 'note', 'message', 'messages', 'comment', 'comments', 'bio', 'address'
    ];

    // Keys excluded from sanitization entirely (secrets/credentials/tokens)
    protected array $excludedKeys = [
        'password', 'password_confirmation', 'current_password', 'new_password', 'email',
        '_token', 'token', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes',
    ];

    // File-related keys that should not be touched
    protected array $excludedFileKeys = [
        'file', 'files', 'avatar', 'image', 'images', 'attachment', 'attachments', 'photo', 'photos'
    ];

    public function __construct(InputSanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();
        $files = $request->allFiles();

        $sanitized = $this->sanitizeArray($input, $files);
        $request->merge($sanitized);

        return $next($request);
    }

    protected function sanitizeArray($data, $files, string $path = '')
    {
        if (!is_array($data)) {
            return $data;
        }

        $result = [];
        foreach ($data as $key => $value) {
            $fullKey = $path === '' ? $key : $path . '.' . $key;
            $lowerKey = strtolower((string) $key);

            if ($this->isExcludedKey($lowerKey, $fullKey) || array_key_exists($key, $files)) {
                $result[$key] = $value;
                continue;
            }

            if (is_array($value)) {
                $result[$key] = $this->sanitizeArray($value, $files[$key] ?? [], $fullKey);
                continue;
            }

            if (is_string($value)) {
                if ($this->isRelaxedKey($lowerKey)) {
                    $result[$key] = $this->sanitizer->relaxed($value);
                } elseif ($this->isStrictKey($lowerKey)) {
                    $result[$key] = $this->sanitizer->strict($value);
                } else {
                    // Minimal sanitization for unknown string fields
                    $result[$key] = $this->sanitizer->minimal($value);
                }
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    protected function isExcludedKey(string $basename, string $fullKey): bool
    {
        if (in_array($basename, $this->excludedKeys, true)) return true;
        if (in_array($basename, $this->excludedFileKeys, true)) return true;
        // Common non-text keys patterns
        if (preg_match('/(^id$|_id$|_ids$|_at$|^created_at$|^updated_at$|^deleted_at$)/', $basename)) return true;
        return false;
    }

    protected function isStrictKey(string $basename): bool
    {
        return in_array($basename, $this->strictKeys, true);
    }

    protected function isRelaxedKey(string $basename): bool
    {
        return in_array($basename, $this->relaxedKeys, true);
    }
}