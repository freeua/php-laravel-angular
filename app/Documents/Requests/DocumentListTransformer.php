<?php


namespace App\Documents\Requests;

use App\Http\Requests\PaginationRequestTransformer;

class DocumentListTransformer extends PaginationRequestTransformer
{
    public $showLegalDocuments = false;
    public function __construct(DocumentListRequest $request)
    {
        parent::__construct($request);
        $this->showLegalDocuments = $request->get('legalDocuments');
    }
}
