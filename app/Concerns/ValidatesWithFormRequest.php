<?php

namespace App\Concerns;

use Illuminate\Foundation\Http\FormRequest;

trait ValidatesWithFormRequest
{
    /**
     * Build validation rules from a Form Request, prefixing form
     * properties with "form." for Livewire data binding.
     */
    protected function rulesFrom(FormRequest $request, array $excludeFromForm = []): array
    {
        $rules = [];

        foreach ($request->rules() as $key => $value) {
            $target = in_array($key, $excludeFromForm, true) ? $key : "form.$key";

            if (is_array($value)) {
                $value = array_map(
                    fn ($rule) => is_string($rule)
                        ? preg_replace('/^(lte|lt|gte|gt|same|required_with|required_if|different):([a-z_.]+)$/', '$1:form.$2', $rule)
                        : $rule,
                    $value,
                );
            }

            $rules[$target] = $value;
        }

        return $rules;
    }

    /**
     * Validate and return the validated data, with form fields
     * unwrapped from the "form." prefix.
     */
    protected function validatedData(): array
    {
        $validated = $this->validate();

        return $validated['form'] ?? [];
    }
}
