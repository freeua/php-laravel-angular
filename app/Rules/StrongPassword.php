<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class StrongPassword
 *
 * @package App\Rules
 */
class StrongPassword implements Rule
{
    /** @var int */
    private $max;
    /** @var int */
    private $min;

    /**
     * Create a new rule instance.
     *
     * @param int $min
     * @param int $max
     */
    public function __construct(int $min = 8, int $max = 15)
    {
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value) : bool
    {
        return (bool) preg_match("/(?=^.{{$this->min},{$this->max}}$)(?:(?=.*\W)(?=.*(?:(\p{Ll}+.*\p{Lu})|(\p{Lu}+.*\p{Ll})))(?=.*\pL).*)$/u", $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() : string
    {
        return __('validation.strong_password', ['min' => $this->min, 'max' => $this->max]);
    }
}
