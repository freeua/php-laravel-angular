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
class CompanyAssociated implements Rule
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
    private $companyId;

    public function __construct(string $field, int $companyId)
    {
        $this->field = $field;
        $this->companyId = $companyId;
    }

    public function passes($attribute, $value): bool
    {
        /** @var User $user */
        $user = User::query()->where($this->field, $value)->first();

        return $user->company_id === $this->companyId;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.company_associated');
    }
}
