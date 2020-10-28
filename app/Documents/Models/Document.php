<?php

namespace App\Documents\Models;

use App\Models\Companies\Company;
use App\Portal\Models\PortalModel;
use App\Portal\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property int $id
 * @property string $filename
 * @property string $extension
 * @property number $size
 * @property boolean $visible
 * @property Company $company
 * @property string $path
 * @property string $type
 * @property boolean $manually_uploaded
 * @property mixed $documentable
 * @property number $documentable_id
 * @property string $documentable_type
 */
class Document extends PortalModel
{
    const SUPPLIER_INVOICE = 1;

    const SIGNED_CONTRACT = 2;

    const SINGLE_LEASE = 3;

    const TAKEOVER_CERTIFICATE = 4;

    const CREDIT_NOTE = 5;

    const INFORMATIVE = 6;

    protected $fillable = [
        'filename',
        'extension',
        'size',
        'visible',
        'company_id',
        'uploader_id',
        'document_id',
        'document_type',
        'path',
        'manually_uploaded',
        'type',
    ];

    protected $casts = [
        'visible' => 'boolean',
        'manually_uploaded' => 'boolean',
    ];

    public function uploader()
    {
        return $this->morphTo('uploader');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function leasingDocument()
    {
        return $this->morphTo('leasing_document');
    }

    public function documentable()
    {
        return $this->morphTo('documentable');
    }
}
