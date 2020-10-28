<?php

namespace App\Repositories;

use App\Helpers\PortalHelper;
use App\Models\Email;
use App\Models\Portal;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class EmailRepository
 *
 * @package App\System\Repositories
 * @method Order find(int $id, array $relations = [])
 */
class EmailRepository extends BaseRepository
{
    /** @var array */
    protected $filterWhereColumns = [
        'supplier' => 's.name',
        'company'  => 'orders.company_name',
        'product'  => 'orders.product_name'
    ];
    /** @var array */
    protected $searchWhereColumns = [
        's.name',
        'p.name',
        'username',
        'product_name'
    ];

    /** @var Email */
    protected $model;

    /**
     * EmailRepository constructor.
     *
     * @param Email $email
     */
    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    /**
     * @inheritdoc
     */
    public function getByKey(string $key)
    {
        return $this->email->where('key', '=', $key)->firstOrFail();
    }
}
