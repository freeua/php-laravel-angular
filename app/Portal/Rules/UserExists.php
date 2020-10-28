<?php

namespace App\Portal\Rules;

use App\Portal\Models\User;
use App\Portal\Repositories\Base\UserRepository;
use Illuminate\Contracts\Validation\Rule;

class UserExists implements Rule
{
    /** @var string */
    private $field;

    public function __construct(string $field)
    {
        $this->field = $field;
    }

    public function passes($attribute, $value): bool
    {
        /** @var User $user */
        $user = User::query()->where($this->field, $value)->first();

        return !is_null($user);
    }

    public function message(): string
    {
        return __('validation.user_exists');
    }
}
