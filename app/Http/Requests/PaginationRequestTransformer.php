<?php


namespace App\Http\Requests;

abstract class PaginationRequestTransformer extends RequestTransformer
{
    public $pageSize = 0;
    public $page = 0;
    public $order = '';
    public $orderBy = '';
    public $filterColumn = '';
    public $filterValue = '';
    public $search = '';

    public function __construct(ApiRequest $request)
    {
        parent::__construct($request);
        $this->pageSize = $request->get('pageSize');
        $this->page = $request->get('page');
        $this->order = $request->get('order');
        $this->orderBy = $request->get('orderBy');
        $this->filterColumn = $request->get('filterColumn');
        $this->filterValue = $request->get('filterValue');
        $this->filterValue = $request->get('search');
    }
}
