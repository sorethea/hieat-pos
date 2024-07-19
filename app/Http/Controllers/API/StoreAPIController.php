<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStoreAPIRequest;
use App\Http\Requests\API\UpdateStoreAPIRequest;
use App\Http\Resources\StoreCollection;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use App\Repositories\StoreRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class StoreAPIController
 */
class StoreAPIController extends AppBaseController
{
    private StoreRepository $storeRepository;

    public function __construct(StoreRepository $storeRepo)
    {
        $this->storeRepository = $storeRepo;
    }

    /**
     * Display a listing of the Stores.
     * GET|HEAD /stores
     */
//    public function index(Request $request): JsonResponse
//    {
//        $stores = $this->storeRepository->all(
//            $request->except(['skip', 'limit']),
//            $request->get('skip'),
//            $request->get('limit')
//        );
//
//        return $this->sendResponse($stores->toArray(), 'Stores retrieved successfully');
//    }
    public function index(Request $request): StoreCollection
    {
        $perPage = getPageSize($request);
        $stores = $this->storeRepository->paginate($perPage);
        StoreResource::usingWithCollection();

        return new StoreCollection($stores);
    }
    /**
     * Store a newly created Store in storage.
     * POST /stores
     */
    public function store(CreateStoreAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $store = $this->storeRepository->create($input);

        return $this->sendResponse($store->toArray(), 'Store saved successfully');
    }

    /**
     * Display the specified Store.
     * GET|HEAD /stores/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Store $store */
        $store = $this->storeRepository->find($id);

        if (empty($store)) {
            return $this->sendError('Store not found');
        }

        return $this->sendResponse($store->toArray(), 'Store retrieved successfully');
    }

    /**
     * Update the specified Store in storage.
     * PUT/PATCH /stores/{id}
     */
    public function update($id, UpdateStoreAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Store $store */
        $store = $this->storeRepository->find($id);

        if (empty($store)) {
            return $this->sendError('Store not found');
        }

        $store = $this->storeRepository->update($input, $id);

        return $this->sendResponse($store->toArray(), 'Store updated successfully');
    }

    /**
     * Remove the specified Store from storage.
     * DELETE /stores/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Store $store */
        $store = $this->storeRepository->find($id);

        if (empty($store)) {
            return $this->sendError('Store not found');
        }

        $store->delete();

        return $this->sendSuccess('Store deleted successfully');
    }
}
