<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Validator;

/**
 * Class Either
 *
 * @package App\Rules
 */
class Either implements Rule
{
    /** @var array */
    private $rules;

    /**
     * Create a new rule instance.
     *
     * @param array $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        foreach ($this->rules as $rule) {
            $validator = Validator::make([
                $attribute => $value
            ], [
                $attribute => $rule
            ]);
            if ($validator->passes()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.invalid');
    }
}
