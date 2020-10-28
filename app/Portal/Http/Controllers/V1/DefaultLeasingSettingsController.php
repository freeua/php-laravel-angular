<?php

namespace App\Portal\Http\Controllers\V1;

use App\Helpers\PortalHelper;
use App\Http\Requests\UpdateLeasingRequest;
use App\Http\Resources\LeasingSettings\LeasingConditionResource;
use App\Http\Resources\LeasingSettings\RateResource;
use App\Models\LeasingCondition;
use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Requests\V1\GetLeasingConditionRequest;
use App\Services\Companies\LeasingConditionService;
use App\System\Http\Resources\PortalLeasingSettingResource;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DefaultLeasingSettingsController extends Controller
{
    /** @var LeasingConditionService */
    private $leasingConditionService;

    public function __construct(LeasingConditionService $leasingConditionService)
    {
        parent::__construct();
        $this->leasingConditionService = $leasingConditionService;
    }

    public function index(GetLeasingConditionRequest $request)
    {
        $portal = PortalHelper::getPortal();
        $leasingConditions = $portal->leasingConditions()
            ->with('productCategory')
            ->where($request->validated())
            ->whereHas('productCategory')
            ->get();
        $groupedLeasingConditions = $leasingConditions
            ->groupBy('productCategory.id');
        $groupedLeasingConditions->each(function ($productCategory) use ($groupedLeasingConditions) {
            $productCategory->each(function ($leasingCondition) {
                $leasingCondition->deactivate();
            });
            $productCategory->first()->activate(Carbon::now());
        });
        $insuranceRates = $portal->insuranceRates()
            ->with('productCategory')
            ->where($request->validated())
            ->whereHas('productCategory')->get();
        $serviceRates = $portal->serviceRates()
            ->with('productCategory')
            ->where($request->validated())
            ->whereHas('productCategory')->get();
        return response()->json([
            'leasingConditions' => LeasingConditionResource::collection($leasingConditions),
            'insuranceRates' => RateResource::collection($insuranceRates),
            'serviceRates' => RateResource::collection($serviceRates),
        ]);
    }
}
