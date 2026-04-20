<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuditLogger
{
    private const SENSITIVE_FIELD_PATTERNS = [
        'password',
        'token',
        'secret',
        'api_key',
        'apikey',
        'authorization',
        'cookie',
        'stripe_',
    ];

    public function logModelEvent(Model $model, string $acao): void
    {
        $changes = $this->sanitizeChanges($model->getChanges());
        unset($changes['updated_at']);

        $descricao = $acao;
        if ($changes !== []) {
            $descricao .= ' | alterações: '.json_encode($changes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        ActivityLog::query()->create([
            'user_id' => Auth::id(),
            'modulo' => class_basename($model),
            'objeto_id' => (string) $model->getKey(),
            'alteracao' => $descricao,
            'ip' => request()?->ip(),
            'browser' => Arr::get(request()?->server(), 'HTTP_USER_AGENT'),
            'created_at' => now(),
        ]);
    }

    /**
     * @param array<string,mixed> $changes
     * @return array<string,mixed>
     */
    private function sanitizeChanges(array $changes): array
    {
        $sanitized = [];

        foreach ($changes as $key => $value) {
            if ($this->isSensitiveField((string) $key)) {
                $sanitized[$key] = '[protegido]';
                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeChanges($value);
                continue;
            }

            $sanitized[$key] = $value;
        }

        return $sanitized;
    }

    private function isSensitiveField(string $field): bool
    {
        $normalized = Str::lower($field);

        foreach (self::SENSITIVE_FIELD_PATTERNS as $pattern) {
            if (Str::contains($normalized, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
