<?php

namespace App\Portal\Rules;

use App\Portal\Models\User;
use App\Portal\Repositories\Base\UserRepository;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class HasRole
 *
 * @package App\Portal\Rules
 */
class HasRole implements Rule
{
    /** @var bool */
    private $filterParams;
    /** @var array */
    private $params;
    /** @var array|null */
    private $companies;
    /** @var string */
    private $field;
    /** @var string */
    private $role;

    /**
     * Create a new rule instance.
     *
     * @param string     $field
     * @param string     $role
     * @param array      $params
     * @param array|null $companies
     * @param bool       $filterParams
     */
    public function __construct(string $field, string $role)
    {
        $this->field = $field;
        $this->role = $role;
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
        /** @var User $user */
        $user = User::query()->where($this->field, $value)->first();

        if (!$user) {
            return false;
        }

        return $user->isRole($this->role);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.wrong_role');
    }
}
