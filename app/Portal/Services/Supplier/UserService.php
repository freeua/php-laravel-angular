<?php

namespace App\Portal\Services\Supplier;

use App\Exceptions\PendingUserException;
use App\Exceptions\RejectedUserException;
use App\Exceptions\UserNotFoundException;
use App\Models\Companies\Company;
use App\Portal\Helpers\AuthHelper;
use App\Helpers\PortalHelper;
use App\Portal\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserService extends \App\Portal\Services\Base\UserService
{
    public function selfCreate(array $params)
    {
        $params['supplier_id'] = AuthHelper::supplierId();

        return $this->createSupplierAdmin($params, PortalHelper::getPortal());
    }

    public function selfUpdate(User $user, array $params)
    {
        return $this->userRepository->update($user->id, $params) ? $user->fresh() : false;
    }

    public function search($request)
    {
        $companies = AuthHelper::supplier()->companies->pluck('id');
        $user = User::query()
            ->whereHas('company', function (Builder $query) use ($companies) {
                $query->whereIn('id', $companies);
            })
            ->where('portal_id', PortalHelper::getPortal()->id)
        ->where($request)->first();
        if (!$user) {
            throw new UserNotFoundException();
        } elseif ($user->status_id == User::STATUS_INACTIVE) {
            throw new RejectedUserException();
        } elseif ($user->status_id == User::STATUS_PENDING) {
            throw new PendingUserException();
        }
        return $user;
    }

    public function searchByCompany(Company $company, $request)
    {
        $user = User::query()
            ->where('company_id', $company->id)
            ->where('portal_id', PortalHelper::getPortal()->id)
            ->where('status_id', User::STATUS_ACTIVE)
            ->where($request)->first();
        if (!$user) {
            throw new UserNotFoundException();
        }
        return $user;
    }
}
