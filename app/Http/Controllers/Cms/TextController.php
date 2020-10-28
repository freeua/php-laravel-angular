<?php

namespace App\Http\Controllers\Cms;

use App\Models\Text;
use App\Portal\Http\Controllers\Controller;

use App\Http\Resources\Cms\ListCollection\TextListCollection;
use App\Http\Resources\Cms\TextResource;
use App\Repositories\TextRepository;
use App\Services\TextService;
use App\Http\Requests\DefaultListRequest;
use App\Http\Requests\Cms\UpdateTextRequest;

/**
 * Class TextController
 *
 * @package App\Http\Controllers\Cms
 */
class TextController extends Controller
{
    /** @var TextService */
    private $textService;
    /** @var TextRepository */
    private $textRepository;

    public function __construct(
        TextService $textService,
        TextRepository $textRepository
    ) {
        parent::__construct();
        $this->textService = $textService;
        $this->textRepository = $textRepository;
    }

    /**
     *
     * @param DefaultListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(DefaultListRequest $request)
    {
        $texts = $this->textRepository->list($request->validated());
        return response()->json(new TextListCollection($texts));
    }

    /**
     * Update text
     *
     * @param Text $text
     * @param UpdateTextRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Text $text, UpdateTextRequest $request)
    {
        $text = $this->textService->update($text, $request);
        return response()->json(new TextResource($text));
    }

    /**
     * Delete text
     *
     * @param Text $text
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Text $text)
    {
        $defaultText = $this->textService->delete($text);
        return response()->json(new TextResource($defaultText));
    }
}
