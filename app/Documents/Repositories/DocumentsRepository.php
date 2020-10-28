<?php


namespace App\Documents\Repositories;

use App\Documents\Exceptions\WrongUserProfileException;
use App\Documents\Models\Document;
use App\Documents\Requests\DocumentListTransformer;
use App\Helpers\PaginationHelper;
use App\Helpers\PortalHelper;
use App\Http\Requests\ApiRequest;
use App\Models\Companies\Company;
use App\Models\Portal;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\Offer;
use App\Portal\Models\Order;
use App\Portal\Models\Supplier;
use App\Portal\Models\User;
use App\System\Models\User as SystemUser;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class DocumentsRepository
{
    public static function listDocuments(DocumentListTransformer $request): LengthAwarePaginator
    {
        $user = auth()->user();
        if ($user instanceof User) {
            switch (true) {
                case $user->isAdmin():
                    return self::getPortalDocuments($request);
                case $user->isSupplier():
                    return self::getSupplierDocuments($request);
                case $user->isCompanyAdmin() && $request->getRequest()->getModule() == ApiRequest::COMPANY_ADMIN:
                    return self::getCompanyAdminDocuments($request);
                case $user->isEmployee() && $request->getRequest()->getModule() == ApiRequest::EMPLOYEE:
                    return self::getEmployeeDocuments($request);
            }
        } elseif ($user instanceof SystemUser) {
            return self::getSystemDocuments($request);
        }
        throw new WrongUserProfileException();
    }

    private static function getPortalInformativeDocumentsQuery(Builder $query)
    {
        return $query
            ->where(function (Builder $q) {
                $q->whereHasMorph('documentable', [Portal::class], function (Builder $q) {
                    $q->where('id', PortalHelper::id());
                })
                    ->orwhere(function (Builder $query) {
                        $query->whereNull('documentable_type')
                            ->where('visible', true);
                    });
            })
            ->where('type', Document::INFORMATIVE);
    }

    public static function getPortalLegalDocumentsQuery(Builder $query)
    {
        return $query
            ->whereIn('type', [Document::CREDIT_NOTE, Document::SIGNED_CONTRACT, Document::TAKEOVER_CERTIFICATE, Document::SINGLE_LEASE])
            ->whereHasMorph('documentable', [Company::class, Portal::class], function (Builder $q, $type) {
                if ($type == Company::class) {
                    $q->where('portal_id', PortalHelper::id());
                }
                if ($type == Portal::class) {
                    $q->where('id', PortalHelper::id());
                }
            });
    }

    public static function getCompanyAdminLegalDocumentsQuery(Builder $query)
    {
        return $query->whereHasMorph('documentable', [Company::class, Portal::class], function (Builder $q, $type) {
            if ($type == Company::class) {
                $q->where('id', AuthHelper::companyId());
            }
            if ($type == Portal::class) {
                $q->where('id', PortalHelper::id());
            }
        })
            ->whereIn('type', [Document::SIGNED_CONTRACT, Document::TAKEOVER_CERTIFICATE, Document::SINGLE_LEASE]);
    }

    public static function getCompanyAdminDocuments(DocumentListTransformer $request)
    {
        $query = Document::query()->orderBy('documents.created_at', 'desc');
        if ($request->showLegalDocuments) {
            $query = self::getCompanyAdminLegalDocumentsQuery($query);
        } else {
            $query = self::getCompanyInformativeDocumentsQuery($query);
        }

        return PaginationHelper::processList($query, $request);
    }

    public static function getSupplierDocuments(DocumentListTransformer $request)
    {
        $query = Document::query()
            ->whereHasMorph('documentable', [Supplier::class], function (Builder $q) {
                $q->where('id', AuthHelper::supplierId());
            })
            ->orderBy('documents.created_at', 'desc');

        if ($request->showLegalDocuments) {
            $query->where('type', Document::SUPPLIER_INVOICE);
        } else {
            $query->where('type', Document::INFORMATIVE);
        }

        return PaginationHelper::processList($query, $request);
    }

    public static function getEmployeeDocuments(DocumentListTransformer $request)
    {
        $query = Document::query()->orderBy('documents.created_at', 'desc');
        if ($request->showLegalDocuments) {
            $query = self::getEmployeeLegalDocumentsQuery($query);
        } else {
            $query = self::getCompanyInformativeDocumentsQuery($query)
                ->where('visible', true);
        }

        return PaginationHelper::processList($query, $request);
    }

    public static function getEmployeeLegalDocumentsQuery(Builder $query)
    {
        return $query->whereHasMorph('documentable', [Company::class], function (Builder $q) {
            $q->where('portal_id', PortalHelper::id());
        })
            ->whereHasMorph('leasingDocument', [Offer::class, Order::class], function (Builder $q) {
                $q->where('company_id', AuthHelper::companyId())
                    ->where('user_id', AuthHelper::id());
            })
            ->whereIn('type', [Document::SIGNED_CONTRACT, Document::TAKEOVER_CERTIFICATE]);
    }

    public static function getCompanyInformativeDocumentsQuery(Builder $query)
    {
        return $query
            ->where(function (Builder $query) {
                $query->whereHasMorph('documentable', [Company::class, Portal::class], function (Builder $q, $type) {
                    if ($type == Company::class) {
                        $q->where('portal_id', PortalHelper::id());
                    }
                    if ($type == Portal::class) {
                        $q->where('id', PortalHelper::id())
                            ->where('visible', true);
                    }
                })
                    ->orwhere(function (Builder $query) {
                        $query->whereNull('documentable_type')
                            ->where('visible', true);
                    });
            })
            ->where('type', Document::INFORMATIVE);
    }

    public static function getSystemDocuments(DocumentListTransformer $request)
    {
        $query = Document::query()->orderBy('documents.created_at', 'desc');
        if ($request->showLegalDocuments) {
            $query->where('type', Document::SUPPLIER_INVOICE);
        } else {
            $query->where('type', Document::INFORMATIVE)
                ->whereNull('documentable_type');
        }

        return PaginationHelper::processList($query, $request);
    }

    public static function getPortalDocuments(DocumentListTransformer $request)
    {
        $query = Document::query()->orderBy('documents.created_at', 'desc');

        if ($request->showLegalDocuments) {
            $query = self::getPortalLegalDocumentsQuery($query);
        } else {
            $query = self::getPortalInformativeDocumentsQuery($query);
        }

        return PaginationHelper::processList($query, $request);
    }
}
