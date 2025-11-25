<?php

namespace Tests;

class Env
{
    private array $variables;

    public function __construct()
    {
        $variables = [];

        $filePath = __DIR__ . '/../.env';
        if (! file_exists($filePath)) {
            echo "File not found: $filePath" . PHP_EOL;
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Skip comments
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            // Parse the line into a key and value
            list($name, $value) = explode('=', $line, 2);

            // Trim whitespace
            $name = trim($name ?? '');
            $value = trim($value ?? '');

            // Remove surrounding quotes from the value if present
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }

            $variables[$name] = $value;
        }

        $this->variables = $variables;
    }

    public function get(string $key): ?string
    {
        return $this->variables[$key] ?? null;
    }
}