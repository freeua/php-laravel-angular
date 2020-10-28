<?php

declare(strict_types=1);

namespace App\Portal\Models\Type;

use App\Portal\Models\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @inheritdoc
 */
class Boolean extends \Rinvex\Attributes\Models\Type\Boolean
{

    /**
     * Relationship to the attribute entity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id', 'id');
    }
}
