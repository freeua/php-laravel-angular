<?php

namespace App\Rules;

use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class NewPassword
 *
 * @package App\Rules
 */
class NewPassword implements Rule
{
    /** @var int */
    private $userId;

    /**
     * Create a new rule instance.
     *
     * @param int|null $userId
     */
    public function __construct(?int $userId = null)
    {
        $this->userId = $userId ?: \Auth::id();
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
        $passwords = \DB::table('password_histories')
            ->select('password')
            ->where('user_id', $this->userId)
            ->get()
            ->pluck('password')
            ->toArray();

        foreach ($passwords as $password) {
            if (Hash::check($value, $password)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() : string
    {
        return __('validation.new_password');
    }
}
