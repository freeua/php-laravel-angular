<?php
namespace App\Partners\Models;

use App\Models\Portal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use LaravelFillableRelations\Eloquent\Concerns\HasFillableRelations;

/**
 * @property string id
 * @property string key
 */
class PartnerToken extends Model
{
    use HasFillableRelations;

    protected $fillable = [
        'id',
        'key',
    ];

    public static function findByKeyId($id)
    {
        return self::where('id', $id)->firstOrFail();
    }
}
