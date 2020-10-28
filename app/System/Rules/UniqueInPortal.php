<?php

namespace App\System\Rules;

use Illuminate\Contracts\Validation\Rule;
use Validator;

/**
 * Class UniqueInPortal
 *
 * @package App\System\Rules
 */
class UniqueInPortal implements Rule
{
    /** @var string */
    private $sourceId;
    /** @var string */
    private $field;
    /** @var string */
    private $table;
    /** @var int */
    private $portalId;

    /**
     * Create a new rule instance.
     *
     * @param int    $id
     * @param string $table
     * @param string $field
     * @param string $sourceId
     */
    public function __construct(int $id = null, string $table, string $field, string $sourceId = null)
    {
        $this->portalId = $id;
        $this->table = $table;
        $this->field = $field;
        $this->sourceId = $sourceId;
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
        // If it hasn't changed, then it passes
        if (!$value) {
            return true;
        }

        if (!$this->portalId) {
            return true;
        }


        $validator = Validator::make([
            $this->field => $value
        ], [
            $this->field => 'unique:' . $this->table . ',' . $this->field
                . ',deleted_at'
        ]);

        return $validator->passes();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.unique_in_portal');
    }
}
