<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\Services\TagService;
use App\Traits\FormatsMeta;
use App\Traits\HandleJsonResponse;
use Exception;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    /** @use FormatsMeta<Tag> */
    use FormatsMeta;

    use HandleJsonResponse;

    public function __construct(
        protected TagService $service
    ) {
    }

    public function index(): JsonResponse
    {
        try {
            $models = $this->service->all();

            return $this->successResponse([
                'items' => TagResource::collection($models),
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function store(StoreTagRequest $request): JsonResponse
    {
        try {
            $model = $this->service->create($request->validated());
            return $this->successResponse(new TagResource($model), 201);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }
}
