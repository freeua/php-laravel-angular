<?php
namespace App\Repositories;

use App\Helpers\PortalHelper;
use App\Models\Text;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Portal\Models\Role;

/**
 * Class TextRepository
 *
 * @package App\Repositories
 *
 */
class TextRepository extends BaseRepository
{
    /**
     * TextRepository constructor.
     *
     * @param Text $text
     */
    public function __construct(Text $text)
    {
        $this->model = $text;
    }
    
    /**
     * @inheritdoc
     */
    public function list(array $params, array $relationships = [])
    {
        $texts = \Cache::rememberForever(Text::getCacheKey(), function () {
            $globalCollection = Text::where([
                ['portal_id', null],
            ])->get();
            $portalCollection = [];
            $portalId = PortalHelper::id();
            if ($portalId) {
                $portalCollection = Text::where([
                    ['portal_id', $portalId],
                ])->get();
            }
            return $globalCollection->merge($portalCollection);
        });
        return $texts;
    }
}
