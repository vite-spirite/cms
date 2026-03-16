<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidBlockContent implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail('The :attribute must be an array.');
            return;
        }

        foreach ($value as $index => $block) {
            if (!is_array($block)) {
                $fail("The block at index {$index} must be an array.");
                return;
            }

            if (!isset($block['type']) || !is_string($block['type'])) {
                $fail("The block at index {$index} must have a valid 'type' field.");
                return;
            }

            if (!isset($block['id']) || !is_string($block['id'])) {
                $fail("The block at index {$index} must have a valid 'id' field.");
                return;
            }
        }
    }
}
