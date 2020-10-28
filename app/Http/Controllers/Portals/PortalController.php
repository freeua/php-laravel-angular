<?php

namespace App\Http\Controllers\Portals;

use App\Http\Requests\DefaultListRequest;
use App\Http\Requests\InsuranceRateRequest;
use App\Http\Requests\Portal\CreatePortalRequest;
use App\Http\Requests\Portal\UpdatePortalRequest;
use App\Http\Requests\ServiceRateRequest;
use App\Http\Resources\LeasingSettings\RateResource;
use App\Http\Resources\Portals\PortalResource;
use App\Models\LeasingCondition;
use App\Models\Portal;
use App\Models\Rates\InsuranceRate;
use App\Models\Rates\ServiceRate;
use App\Portal\Http\Requests\V1\HomepageRequest;
use App\Portal\Http\Requests\V1\LeasingConditionRequest;
use App\Portal\Http\Requests\V1\UploadPortalFilesRequest;
use App\Portal\Http\Resources\V1\HomepageResource;
use App\Portal\Models\Homepage;
use App\Services\Companies\LeasingConditionService;
use App\Services\PortalService;
use App\System\Http\Resources\ListCollections\PortalListCollection;
use App\System\Http\Resources\PortalLeasingSettingResource;
use App\System\Http\Resources\PortalSimpleResource;
use App\System\Repositories\PortalRepository;
use App\Traits\UploadsFile;
use Illuminate\Routing\Controller;

class PortalController extends Controller
{
    use UploadsFile;
    /** @var PortalService */
    private $portalService;
    /** @var PortalRepository */
    private $portalRepository;
    /** @var LeasingConditionService */
    private $leasingConditionService;

    public function __construct(
        PortalRepository $portalRepository,
        PortalService $portalService,
        LeasingConditionService $leasingConditionService
    ) {
        $this->portalRepository = $portalRepository;
        $this->portalService = $portalService;
        $this->leasingConditionService = $leasingConditionService;
    }

    public function index(DefaultListRequest $request)
    {
        $portals = $this->portalRepository->list($request->validated());

        return response()->success(new PortalListCollection($portals));
    }

    public function all()
    {
        $portals = $this->portalRepository->all();

        return response()->success(PortalSimpleResource::collection($portals));
    }

    public function view(Portal $portal)
    {
        return response()->json(new PortalResource($portal));
    }

    public function create(CreatePortalRequest $request)
    {
        $portal = $this->portalService->create($request->validated());

        return response()->json(new PortalResource($portal));
    }

    public function update(Portal $portal, UpdatePortalRequest $request)
    {
        $portal = $this->portalService->update($portal, $request->validated());

        return response()->json(new PortalResource($portal));
    }

    public function addInsuranceRate(Portal $portal, InsuranceRateRequest $request)
    {
        return response()->json(new RateResource($this->portalService->addInsuranceRate($portal, $request)->load('productCategory')));
    }

    public function addServiceRate(Portal $portal, ServiceRateRequest $request)
    {
        return response()->json(new RateResource($this->portalService->addServiceRate($portal, $request)->load('productCategory')));
    }

    public function addLeasingCondition(Portal $portal, LeasingConditionRequest $request)
    {
        return response()->json(
            new PortalLeasingSettingResource($this->portalService->addLeasingCondition($portal, $request))
        );
    }

    public function editServiceRate(Portal $portal, ServiceRate $serviceRate, ServiceRateRequest $request)
    {
        $this->portalService->editServiceRate($portal, $serviceRate, $request->validated());
    }

    public function editInsuranceRate(Portal $portal, InsuranceRate $insuranceRate, InsuranceRateRequest $request)
    {
        $this->portalService->editInsuranceRate($portal, $insuranceRate, $request->validated());
    }

    public function editLeasingCondition(
        Portal $portal,
        LeasingCondition $leasingCondition,
        LeasingConditionRequest $request
    ) {
        $this->leasingConditionService->editPortalCondition($portal, $leasingCondition, $request);
    }

    public function deleteInsuranceRate(Portal $portal, InsuranceRate $insuranceRate)
    {
        $this->portalService->deleteInsuranceRate($portal, $insuranceRate);
    }

    public function deleteServiceRate(Portal $portal, ServiceRate $serviceRate)
    {
        $this->portalService->deleteServiceRate($portal, $serviceRate);
    }

    public function deleteLeasingCondition(Portal $portal, LeasingCondition $leasingCondition)
    {
        $this->portalService->deleteLeasingCondition($portal, $leasingCondition);
    }

    public function uploadFiles(Portal $portal, UploadPortalFilesRequest $request)
    {
        $response = [];
        if (isset($request['logo'])) {
            $logo = UploadsFile::handleFile($request->file('logo'), "/portals/{$portal->id}/logos", $portal->logo);
            $portal->logo = $logo;
            $response['logo'] = $logo;
        }
        if (isset($request['leasingablePdf'])) {
            $leasingablePdf = UploadsFile::handleFile($request->file('leasingablePdf'), "/portals/{$portal->id}/leasingable-pdf", $portal->leasingablePdf);
            $portal->leasingablePdf = $leasingablePdf;
            $response['leasingablePdf'] = $leasingablePdf;
        }
        if (isset($request['servicePdf'])) {
            $servicePdf = UploadsFile::handleFile($request->file('servicePdf'), "/portals/{$portal->id}/service-pdf", $portal->servicePdf);
            $portal->servicePdf = $servicePdf;
            $response['servicePdf'] = $servicePdf;
        }
        if (isset($request['imprintPdf'])) {
            $imprintPdf = UploadsFile::handleFile($request->file('imprintPdf'), "/portals/{$portal->id}/imprint-pdf", $portal->imprintPdf);
            $portal->imprintPdf = $imprintPdf;
            $response['imprintPdf'] = $imprintPdf;
        }
        if (isset($request['policyPdf'])) {
            $policyPdf = UploadsFile::handleFile($request->file('policyPdf'), "/portals/{$portal->id}/policy-pdf", $portal->policyPdf);
            $portal->policyPdf = $policyPdf;
            $response['policyPdf'] = $policyPdf;
        }
        \Cache::delete("portals.{$portal->domain}");
        $portal->saveOrFail();
        return response()->success($response);
    }

    public function getHomepage(Portal $portal)
    {
        if ($portal->homepage) {
            return response()->success(new HomepageResource($portal->homepage));
        } else {
            return response()->success(Homepage::getDefaultHomepageByType(Homepage::PORTAL_DEFAULT_HOMEPAGE));
        }
    }

    public function updateHomepage(Portal $portal, HomepageRequest $request)
    {
        $data = $request->validated();
        \DB::beginTransaction();
        if (!empty($data['items']['logo'])) {
            if (!strpos($data['items']['logo'], '/logos/logo.png')) {
                $data['items']['logo'] = UploadsFile::handlePublicJsonFile($data['items']['logo'], "/homepages/portal/{$portal->id}/logos", "logo.png");
            }
        } else {
            unset($data['items']['logo']);
        }
        $portal->homepage()->updateOrCreate(['homepageable_id'=>$portal->id, 'homepageable_type'=>Portal::ENTITY], [
            'items' => $data['items'],
            'type' => Homepage::PORTAL_HOMEPAGE
        ]);
        \Cache::forget(Homepage::PORTAL_HOMEPAGE.'_'.$portal->id);
        \DB::commit();

        return response()->success(new HomepageResource($portal->homepage));
    }
}
