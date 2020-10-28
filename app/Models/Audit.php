<?php
namespace App\Models;

use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Partners\Models\Partner;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\Contract;
use App\Portal\Models\Offer;
use App\Portal\Models\Order;
use App\Portal\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property float $id
 * @property string $description
 * @property string $type
 * @property integer $visibility
 * @property Offer $offer
 * @property User $user
 * @property TechnicalService $technicalService
 */
class Audit extends Model
{
    const VISIBLE_ALL = 1;
    const VISIBLE_ADMINS = 2;
    const SUPPLIER_CREATE_OFFER = 'supplier_create_offer';
    const EMPLOYEE_CREATE_OFFER = 'employee_create_offer';
    const EMPLOYEE_ACCEPT_OFFER = 'employee_accept_offer';
    const COMPANY_APPROVE_OFFER = 'company_approve_offer';
    const COMPANY_REJECT_OFFER = 'company_reject_offer';
    const EMPLOYEE_REJECT_OFFER = 'employee_reject_offer';
    const CONTRACT_CREATED = 'contract_created';
    const TECHNICAL_SERVICE_CREATED = 'technical_service_created';
    const ORDER_READY = 'order_ready';
    const ORDER_PICKED_UP = 'order_picked_up';
    const CREDIT_NOTE_READ = 'credit_note_read';

    protected $fillable = [
        'description',
        'type',
        'visibility',
        'offer_id',
        'user_id',
        'partner_id',
        'model_id',
        'model_type'
    ];

    static function offerCreatedBySupplier($offer, $user)
    {
        $audit = self::baseFromOffer($offer, $user);
        $audit->fill([
            'description' => "Lieferant „{$offer->supplierName}“ hat ein Angebot gesendet",
            'visibility' => Audit::VISIBLE_ALL,
            'type' => Audit::SUPPLIER_CREATE_OFFER,
        ]);
        return $audit->saveOrFail();
    }

    static function offerCreatedByPartner($offer, $partner)
    {
        $audit = self::baseFromOffer($offer, null);
        $audit->fill([
            'partner_id' => $partner->id,
            'description' => "Partner „{$offer->partner->name}“ hat ein Angebot gesendet",
            'visibility' => Audit::VISIBLE_ALL,
            'type' => Audit::SUPPLIER_CREATE_OFFER,
        ]);
        return $audit->saveOrFail();
    }

    static function offerCreatedByEmployee($offer, $user)
    {
        $audit = self::baseFromOffer($offer, $user);
        $audit->fill([
            'description' => "Mitarbeiter „{$offer->employeeName}“ hat ein Angebot erstellt",
            'visibility' => Audit::VISIBLE_ALL,
            'type' => Audit::EMPLOYEE_CREATE_OFFER,
        ]);
        return $audit->saveOrFail();
    }

    static function offerAccepted($offer)
    {
        $audit = self::baseFromOffer($offer, AuthHelper::user());
        $audit->fill([
            'description' => "Mitarbeiter „{$offer->employeeName}“ hat das Angebot und den Überlassungsvertrag angenommen",
            'visibility' => Audit::VISIBLE_ALL,
            'type' => Audit::EMPLOYEE_ACCEPT_OFFER,
        ]);
        return $audit->saveOrFail();
    }

    static function offerApproved($offer)
    {
        $audit = self::baseFromOffer($offer, AuthHelper::user());
        $companyAdminName = AuthHelper::user()->fullName;
        $audit->fill([
            'description' => "Administrator „{$companyAdminName}“ hat das Angebot angenommen",
            'visibility' => Audit::VISIBLE_ALL,
            'type' => Audit::COMPANY_APPROVE_OFFER,
        ]);
        return $audit->saveOrFail();
    }

    static function offerRejectedByCompany($offer, $user)
    {
        $audit = self::baseFromOffer($offer, $user);
        $companyAdminName = AuthHelper::user()->fullName;
        $audit->fill([
            'description' => "Administrator „{$companyAdminName}“ hat das Angebot abgelehnt",
            'visibility' => Audit::VISIBLE_ALL,
            'type' => Audit::COMPANY_REJECT_OFFER,
        ]);
        return $audit->saveOrFail();
    }

    static function offerRejectedByEmployee($offer, $user)
    {
        $audit = self::baseFromOffer($offer, $user);
        $audit->fill([
            'description' => "Mitarbeiter „{$offer->employeeName}“ hat das Angebot abgelehnt",
            'visibility' => Audit::VISIBLE_ALL,
            'type' => Audit::EMPLOYEE_REJECT_OFFER,
        ]);
        return $audit->saveOrFail();
    }

    static function contractCreated(Contract $contract, $user)
    {
        $audit = self::baseFromOffer($contract->order->offer, $user);
        $audit->fill([
            'description' => "Vertrag wurde erstellt",
            'visibility' => Audit::VISIBLE_ALL,
            'type' => Audit::CONTRACT_CREATED,
        ]);
        return $audit->saveOrFail();
    }

    static function contractCreatedByPartner(Contract $contract, $partner)
    {
        $audit = self::baseFromOffer($contract->order->offer, null);
        $audit->fill([
            'description' => "Vertrag wurde erstellt",
            'visibility' => Audit::VISIBLE_ALL,
            'type' => Audit::CONTRACT_CREATED,
            'partner_id' => $partner->id,
        ]);
        return $audit->saveOrFail();
    }

    static function orderReady(Order $order, $user)
    {
        $audit = self::baseFromOffer($order->offer, $user);
        $audit->fill([
            'description' => "Bestellung ist abholbereit",
            'visibility' => Audit::VISIBLE_ALL,
            'type' => Audit::ORDER_READY,
        ]);
        return $audit->saveOrFail();
    }

    static function orderReadyByPartner(Order $order, $partner)
    {
        $audit = self::baseFromOffer($order->offer, null);
        $audit->fill([
            'description' => "Bestellung ist abholbereit",
            'visibility' => Audit::VISIBLE_ALL,
            'type' => Audit::ORDER_READY,
            'partner_id' => $partner->id,
        ]);
        return $audit->saveOrFail();
    }

    static function orderPickedUp(Order $order, $user)
    {
        $audit = self::baseFromOffer($order->offer, $user);
        $audit->fill([
            'description' => "Bestellung ist abgeschlossen",
            'visibility' => Audit::VISIBLE_ALL,
            'type' => Audit::ORDER_PICKED_UP,
        ]);
        return $audit->saveOrFail();
    }

    static function userCompanySwitch(User $newCompanyUser, User $oldCompanyUser, $user)
    {
        $userId = $user->id ?? null;
        $audit = new Audit([
            'user_id' => !empty($user) ? $user->id : null,
        ]);
        $logText = $newCompanyUser->getFullNameAttribute() . ' wechsel von Firma ' . $newCompanyUser->company()->first()->name
                    . ' zu Firma ' . $oldCompanyUser->company()->first()->name . ' am Tag ' . Carbon::now()->format('d.m.Y H:i:s') . '.';
        $audit->fill([
            'description' => $logText,
            'visibility' => Audit::VISIBLE_ALL,
            'model_type' => User::class,
            'model_id' => $newCompanyUser->id,
            'created_by' => $userId
        ]);
        return $audit->saveOrFail();
    }

    static function baseFromOffer(Offer $offer, $user): Audit
    {
        $userId = AuthHelper::id() ?? null;

        return new Audit([
            'user_id' => !empty($user) ? $user->id : null,
            'offer_id' => $offer->id,
            'model_type' => Offer::class,
            'model_id' => $offer->id,
            'created_by' => $userId
        ]);
    }

    function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class)->withTrashed();
    }

    function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    function technicalService()
    {
        return $this->morphTo();
    }

    function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }
}
