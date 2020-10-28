<?php

namespace App\Services;

use App\Helpers\PortalHelper;
use App\Portal\Helpers\AuthHelper;
use App\Http\Requests\Cms\UpdateTextRequest;
use App\Repositories\TextRepository;
use App\Models\Text;
use App\Models\Portal;
use Carbon\Carbon;

class TextService
{
    /** TextRepository */
    private $textRepository;

    public function __construct(TextRepository $textRepository)
    {
        $this->textRepository = $textRepository;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->textRepository->list([], []);
    }

    public function update(Text $text, UpdateTextRequest $request)
    {
        $userId = AuthHelper::id() ?? null;
        $data = $this->requestToArray($request);
        $portalId = PortalHelper::id();
        if ($portalId && $text->portal_id !== $portalId) {
            $text = $text->replicate();
            $text->portal_id = $portalId;
        }
        $text->fill($data);
        $text->updated_by = $userId;
        $text->saveOrFail();
        $this->resetCache();
        return $text;
    }

    public function delete(Text $text)
    {
        $key = $text->data['key'];
        $text->delete();
        $this->resetCache();
        $defaultText = Text::where('data->key', $key)->first();
        return $defaultText;
    }

    private function resetCache()
    {
        $portalIds = Portal::all()->pluck('id');
        Text::resetCaches($portalIds);
    }

    private function requestToArray(UpdateTextRequest $request): Array
    {
        return [
            'id' => $request->input('id'),
            'portalId' => $request->input('portalId'),
            'data' => [
                'title'       => $request->input('title'),
                'subtitle'    => $request->input('subtitle'),
                'key'         => $request->input('key'),
                'description' => $request->input('description'),
            ]
        ];
    }
}
