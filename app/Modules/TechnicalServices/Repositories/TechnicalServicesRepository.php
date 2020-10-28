<?php

namespace App\Modules\TechnicalServices\Repositories;

use App\ExternalLogin\Exceptions\WrongRoleError;
use App\Helpers\PaginationHelper;
use App\Helpers\PortalHelper;
use App\Http\Requests\ApiRequest;
use App\Models\Rates\ServiceRate;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Modules\TechnicalServices\Transformers\TechnicalServiceListTransformer;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\Contract;
use App\Portal\Models\User;
use App\System\Models\User as SystemUser;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TechnicalServicesRepository
{
    /** @var TechnicalService */
    protected $model;

    public function __construct(TechnicalService $technicalService)
    {
        $this->model = $technicalService;
    }

    public function getPerSupplierCompanyCount(int $supplierId, int $limit, ?string $status = null): Collection
    {
        $query = TechnicalService::query();

        $query->select(['c.name as name', 'c.id as id', DB::raw('COUNT(technical_services.id) as total')])
            ->join('companies as c', 'c.id', '=', 'technical_services.company_id')
            ->where('technical_services.portal_id', PortalHelper::id())
            ->where('technical_services.supplier_id', $supplierId);

        if ($status) {
            $query->where('technical_services.status_id', $status);
        }

        $query->groupBy('c.id')
            ->limit($limit);

        return $query->get();
    }

    public static function list(TechnicalServiceListTransformer $request)
    {
        $module = $request->getRequest()->getModule();
        $user = AuthHelper::user();
        if ($user instanceof User) {
            switch (true) {
                case $user->isSupplier():
                    return self::listForSupplier($request);
                case $user->isAdmin():
                    return self::listForPortal($request);
                case $user->isCompanyAdmin() && $module === ApiRequest::COMPANY_ADMIN:
                    return self::listForCompanyAdmin($request);
                case $user->isEmployee() && $module === ApiRequest::EMPLOYEE:
                    return self::listForEmployee($request);
            }
        } elseif ($user instanceof SystemUser) {
            return self::listForSystemAdmin($request);
        }
        throw new WrongRoleError();
    }

    public static function getFullServiceContracts(): Collection
    {
        $query = Contract::query()
            ->where('portal_id', PortalHelper::id())
            ->where('user_id', AuthHelper::id())
            ->where('end_date', '>', Carbon::now())
            ->where('start_date', '<', Carbon::now())
            ->where('status_id', '!=', Contract::STATUS_CANCELED)
            ->where('service_rate_modality', ServiceRate::FULL_SERVICE)
            ->with(['technicalServices']);

        return $query->get();
    }

    public static function listForSupplier(TechnicalServiceListTransformer $request): LengthAwarePaginator
    {

        $query = TechnicalService::query()
            ->where('supplier_id', AuthHelper::supplierId());

        if (!empty($request->serviceModality)) {
            $query->where(['service_modality' => $request->serviceModality]);
        }

        if (!empty($request->statusId)) {
            $query->where(['status_id' => $request->statusId]);
        }

        return PaginationHelper::processList($query, $request);
    }

    public static function listForCompanyAdmin(TechnicalServiceListTransformer $request): LengthAwarePaginator
    {
        $companyId = AuthHelper::companyId();
        $query = TechnicalService::query()
            ->whereHas('user', function (Builder $query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->with(['supplier', 'user', 'product']);
        if (!empty($request->statusId)) {
            $query->where(['technical_services.status_id' => $request->statusId]);
        }

        return PaginationHelper::processList($query, $request);
    }

    public static function listForEmployee(TechnicalServiceListTransformer $request): LengthAwarePaginator
    {
        $employeeId = AuthHelper::id();
        $query = TechnicalService::query()
            ->where('technical_services.user_id', $employeeId)
            ->with(['supplier', 'user', 'product']);
        if (!empty($request->statusId)) {
            $query->where(['technical_services.status_id' => $request->statusId]);
        }

        return PaginationHelper::processList($query, $request);
    }

    public static function listForSystemAdmin(TechnicalServiceListTransformer $request): LengthAwarePaginator
    {
        $query = TechnicalService::query()
            ->with(['supplier', 'user', 'product']);
        if (!empty($request->statusId)) {
            $query->where(['technical_services.status_id' => $request->statusId]);
        }

        return PaginationHelper::processList($query, $request);
    }

    public static function listForPortal(TechnicalServiceListTransformer $request): LengthAwarePaginator
    {
        $query = TechnicalService::query()
            ->whereHas('user', function (Builder $query) {
                $query->where('portal_id', PortalHelper::id());
            })
            ->with(['supplier', 'user', 'product']);
        if (!empty($request->statusId)) {
            $query->where(['technical_services.status_id' => $request->statusId]);
        }

        return PaginationHelper::processList($query, $request);
    }
}
