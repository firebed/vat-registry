<?php

namespace Firebed\VatRegistry;

use Countable;

trait Helpers
{
    /**
     * Determine if the given value is "blank".
     *
     * @param  mixed  $value
     * @return bool
     */
    public function blank(mixed $value): bool
    {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return false;
        }

        if ($value instanceof Countable) {
            return count($value) === 0;
        }

        return empty($value);
    }

    /**
     * Determine if a value is "filled".
     *
     * @param  mixed  $value
     * @return bool
     */
    public function filled(mixed $value): bool
    {
        return !$this->blank($value);
    }

    /**
     * Trim the given string.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function trim(?string $value): ?string
    {
        return $value === null ? null : trim($value);
    }

    /**
     * Get the portion of a string after the last occurrence of a given value.
     *
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    public function afterLast(string $subject, string $search): string
    {
        $position = mb_strrpos($subject, $search);

        return $position === false ? $subject : mb_substr($subject, $position + strlen($search), null, 'UTF-8');

    }

    /**
     * Get the portion of a string before the last occurrence of a given value.
     *
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    public function beforeLast(string $subject, string $search): string
    {
        $position = mb_strrpos($subject, $search);

        return $position === false ? $subject : mb_substr($subject, 0, $position, 'UTF-8');
    }

    /**
     * Wrap the given value in an array if it is not already an array.
     *
     * @param  mixed  $value
     * @return array
     */
    public function wrapArray(mixed $value): array
    {
        if (is_null($value)) {
            return [];
        }

        return is_array($value) ? $value : [$value];
    }
}