<?php


namespace App\Modules\TechnicalServices\Transformers;

use App\Http\Requests\PaginationRequestTransformer;
use App\Modules\TechnicalServices\Requests\TechnicalServicesListRequest;

class TechnicalServiceListTransformer extends PaginationRequestTransformer
{
    public $serviceModality;
    public $statusId;
    public function __construct(TechnicalServicesListRequest $request)
    {
        parent::__construct($request);
        $validated = $request->validated();
        $this->serviceModality = $validated['serviceModality'] ?? null;
        $this->statusId = $validated['serviceId'] ?? null;
    }
}
