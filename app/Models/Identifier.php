<?php


namespace App\Models;

use App\Traits\CamelCaseAttributes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $nextNumber
 * @property string $format
 * @property string $type
 * @property Carbon $nextReset
 * @property integer $yearIdentifier
 */
class Identifier extends Model
{
    use CamelCaseAttributes;
    const LEASING_CREDIT_NOTE = 'leasing_credit_note';
    const TECHNICAL_SERVICE_CREDIT_NOTE = 'inspection_credit_note';
    protected $attributes = [
        'nextNumber',
        'format',
        'lastValue',
        'type',
        'nextReset',
        'yearIdentifier',
    ];

    protected $dates = [
        'next_reset'
    ];

    public static function nextLeasingCreditNoteIdentifier(): string
    {
        $identifier = Identifier::query()->where('type', self::LEASING_CREDIT_NOTE)->firstOrFail();

        return self::nextIdentifier($identifier);
    }

    public static function nextTechnicalServiceCreditNoteIdentifier(): string
    {
        $identifier = Identifier::query()->where('type', self::TECHNICAL_SERVICE_CREDIT_NOTE)->firstOrFail();

        return self::nextIdentifier($identifier);
    }

    private static function nextIdentifier(self $id)
    {
        if ($id->nextReset->isBefore(Carbon::now())) {
            $id->nextNumber = 1;
            $id->yearIdentifier = $id->yearIdentifier + 1;
            $nextValue = sprintf($id->format, $id->nextNumber, $id->yearIdentifier);
            $id->lastValue = $nextValue;
        } else {
            $nextValue = sprintf($id->format, $id->nextNumber, $id->yearIdentifier);
            $id->lastValue = $nextValue;
            $id->nextNumber = $id->nextNumber + 1;
        }
        $id->saveOrFail();

        return $nextValue;
    }
}
