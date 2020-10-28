<?php

namespace App\System\Rules;

use App\Portal\Repositories\UserRepository;
use App\Portal\Models\Supplier;
use App\Portal\Repositories\SupplierRepository;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class DuplicateToPortal
 *
 * @package App\System\Rules
 */
class DuplicateToPortal implements Rule
{
    /** @var SupplierRepository */
    private $supplierRepository;
    /** @var UserRepository */
    private $portalUserRepository;
    /** @var Supplier */
    private $supplier;

    /**
     * Create a new rule instance.
     *
     * @param Supplier $supplier
     */
    public function __construct(Supplier $supplier)
    {
        $this->supplier = $supplier;
        $this->supplierRepository = app()->make(SupplierRepository::class);
        $this->portalUserRepository = app()->make(UserRepository::class);
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
        if (!$value) {
            return false;
        }
        return !$this->portalUserRepository->existsEmail($this->supplier->admin_email);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.duplicate_to_portal');
    }
}
