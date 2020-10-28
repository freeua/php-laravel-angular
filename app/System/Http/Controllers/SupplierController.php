<?php

namespace App\System\Http\Controllers;

use App\System\Http\Requests\CreateSupplierRequest;
use App\System\Http\Requests\UpdateSupplierRequest;
use App\System\Http\Requests\DuplicateSupplierRequest;
use App\Http\Requests\DefaultListRequest;
use App\System\Http\Resources\ListCollections\SupplierListCollection;
use App\System\Http\Resources\SupplierResource;
use App\Portal\Models\Supplier;
use App\System\Repositories\SupplierRepository;
use App\System\Services\SupplierService;

/**
 * Class SupplierController
 *
 * @package App\System\Http\Controllers
 */
class SupplierController extends Controller
{
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
        $portals = $this->supplierRepository->list($request->validated());

        return response()->success(new SupplierListCollection($portals));
    }

    /**
     * Create new portal
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
     * @param Supplier $supplier
     * @param UpdateSupplierRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(Supplier $supplier, UpdateSupplierRequest $request)
    {
        $supplier = $this->supplierService->update($supplier, $request->validated());

        return $supplier
            ? response()->success(new SupplierResource($supplier))
            : response()->error([__('supplier.update.failed')], __('supplier.update.failed'));
    }

    /**
     * Duplicate supplier to another portal
     *
     * @param Supplier $supplier
     * @param DuplicateSupplierRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function duplicate(Supplier $supplier, DuplicateSupplierRequest $request)
    {
        $supplier = $this->supplierService->duplicate($supplier, $request->get('portal_id'));

        return $supplier
            ? response()->success(new SupplierResource($supplier))
            : response()->error([__('supplier.duplicate.failed')], __('supplier.duplicate.failed'));
    }
}
