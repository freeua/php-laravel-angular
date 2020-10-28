<?php

namespace App\Traits;

use Carbon\Carbon;

/**
 * Trait PasswordAge
 *
 * @package App\Traits
 *
 * @property Carbon $password_updated_at
 */
trait PasswordAge
{
    /**
     * @return bool
     */
    public function isPasswordExpired(): bool
    {
        return $this->password_updated_at->addDays(config('auth.password_age')) < Carbon::now();
    }
}
