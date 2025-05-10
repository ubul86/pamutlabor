<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\SyncProductTagsRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use App\Traits\FormatsMeta;
use App\Traits\HandleJsonResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /** @use FormatsMeta<Product> */
    use FormatsMeta;

    use HandleJsonResponse;

    public function __construct(
        protected ProductService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $models = $this->service->all($request->all());

            return $this->successResponse([
                'items' => $models->items(),
                'meta' => $this->formatMeta($models),
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $model = $this->service->create($request->validated());
            return $this->successResponse($model, 201);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $model = $this->service->find($id);
            return $this->successResponse($model);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $model = $this->service->update($id, $request->validated());
            return $this->successResponse($model);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);
            return $this->successResponse(['message' => 'Deleted successfully']);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function syncTags(SyncProductTagsRequest $request, Product $product): JsonResponse
    {
        try {
            $model = $this->service->syncTags($product, $request->tag_ids);
            return $this->successResponse($model, 201);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }
}
