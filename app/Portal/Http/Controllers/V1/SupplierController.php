<?php

namespace App\Portal\Http\Controllers\V1;

use App\Portal\Http\Controllers\Controller;
use App\Http\Requests\DefaultListRequest;
use App\Portal\Http\Requests\V1\CreateSupplierRequest;
use App\Portal\Http\Requests\V1\GetSupplierOffersRequest;
use App\Portal\Http\Requests\V1\GetSupplierOrdersRequest;
use App\Portal\Http\Requests\V1\GetSupplierTechnicalServicesRequest;
use App\Portal\Http\Requests\V1\HomepageRequest;
use App\Portal\Http\Requests\V1\ImportSupplierRequest;
use App\Portal\Http\Requests\V1\SupplierAllRequest;
use App\Portal\Http\Requests\V1\UpdateSupplierRequest;
use App\Portal\Http\Resources\V1\HomepageResource;
use App\Portal\Http\Resources\V1\ListCollections\SupplierListCollection;
use App\Portal\Http\Resources\V1\SupplierCompanyOffersResource;
use App\Portal\Http\Resources\V1\SupplierCompanyOrdersResource;
use App\Portal\Http\Resources\V1\SupplierResource;
use App\Portal\Http\Resources\V1\SupplierSimpleResource;
use App\Portal\Models\Homepage;
use App\Portal\Models\Supplier;
use App\Portal\Repositories\SupplierRepository;
use App\Portal\Services\SupplierService;
use App\Traits\UploadsFile;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class SupplierController
 *
 * @package App\Portal\Http\Controllers\V1
 */
class SupplierController extends Controller
{
    use UploadsFile;
    /** @var SupplierService */
    private $supplierService;
    /** @var SupplierRepository */
    private $supplierRepository;

    /**
     * Create a new controller instance.
     *
     * @param SupplierRepository $supplierRepository
     * @param SupplierService    $supplierService
     */
    public function __construct(SupplierRepository $supplierRepository, SupplierService $supplierService)
    {
        parent::__construct();

        $this->supplierRepository = $supplierRepository;
        $this->supplierService = $supplierService;
    }

    /**
     * Returns list of suppliers
     *
     * @param DefaultListRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(DefaultListRequest $request)
    {
        $suppliers = $this->supplierRepository->list($request->validated());

        return response()->success(new SupplierListCollection($suppliers));
    }

    /**
     * @param SupplierAllRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function all(SupplierAllRequest $request)
    {
        $suppliers = $this->supplierRepository->allFiltered($request->validated());

        return response()->success(SupplierSimpleResource::collection($suppliers));
    }

    /**
     * Create new supplier
     *
     * @param CreateSupplierRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function create(CreateSupplierRequest $request)
    {
        $supplier = $this->supplierService->create($request->validated());

        return $supplier
            ? response()->success(new SupplierResource($supplier))
            : response()->error([__('supplier.create.failed')], __('supplier.create.failed'));
    }

    /**
     * Return supplier by id
     *
     * @param Supplier $supplier
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function view(Supplier $supplier)
    {
        return response()->success(new SupplierResource($supplier));
    }

    /**
     * Update supplier
     *
     * @param Supplier              $supplier
     * @param UpdateSupplierRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Supplier $supplier, UpdateSupplierRequest $request)
    {
        $supplier = $this->supplierService->selfUpdate($supplier, $request->validated());

        return $supplier
            ? response()->success(new SupplierResource($supplier))
            : response()->error([__('supplier.update.failed')], __('supplier.update.failed'));
    }

    /**
     * Returns supplier orders count per company
     *
     * @param Supplier                 $supplier
     * @param GetSupplierOrdersRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function orders(Supplier $supplier, GetSupplierOrdersRequest $request)
    {
        $companyOrders = $this->supplierService->getOrdersPerCompany($supplier->id, $request->get('status'));

        return response()->success(SupplierCompanyOffersResource::collection($companyOrders));
    }

    /**
     * Returns supplier offers count per company
     *
     * @param Supplier                 $supplier
     * @param GetSupplierOffersRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function offers(Supplier $supplier, GetSupplierOffersRequest $request)
    {
        $companyOrders = $this->supplierService->getOffersPerCompany($supplier->id, $request->get('status'));

        return response()->success(SupplierCompanyOrdersResource::collection($companyOrders));
    }

    /**
     * Returns supplier technical services count per company
     *
     * @param Supplier                            $supplier
     * @param GetSupplierTechnicalServicesRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function technicalServices(Supplier $supplier, GetSupplierTechnicalServicesRequest $request)
    {
        $companyTechnicalServices = $this->supplierService->getTechnicalServicesPerCompany($supplier->id, $request->get('status'));

        return response()->pagination(SupplierCompanyOrdersResource::collection($companyTechnicalServices));
    }

    /**
     * @param ImportSupplierRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function import(ImportSupplierRequest $request)
    {

        $this->supplierService->import($request->validated());

        return response()->success(SupplierSimpleResource::collection($this->supplierRepository->allFiltered([])));
    }

    public function getHomepage(Supplier $supplier)
    {
        if ($supplier->homepage) {
            return response()->success(new HomepageResource($supplier->homepage));
        } else {
            return response()->success(Homepage::getDefaultHomepageByType(Homepage::SUPPLIER_DEFAULT_HOMEPAGE));
        }
    }

    public function updateHomepage(Supplier $supplier, HomepageRequest $request)
    {
        $data = $request->validated();
        \DB::beginTransaction();
        if (!empty($data['items']['logo'])) {
            if (!strpos($data['items']['logo'], '/logos/logo.png')) {
                $data['items']['logo'] = UploadsFile::handlePublicJsonFile($data['items']['logo'], "/homepages/supplier/{$supplier->id}/logos", "logo.png");
            }
        } else {
            unset($data['items']['logo']);
        }
        $supplier->homepage()->updateOrCreate(['homepageable_id'=>$supplier->id, 'homepageable_type'=>Supplier::ENTITY], [
            'items' => $data['items'],
            'type' => Homepage::SUPPLIER_HOMEPAGE
        ]);
        \Cache::forget(Homepage::SUPPLIER_HOMEPAGE.'_'.$supplier->id);
        \DB::commit();

        return response()->success(new HomepageResource($supplier->homepage));
    }
}
