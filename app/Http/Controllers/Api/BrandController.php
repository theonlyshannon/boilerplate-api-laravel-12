<?php

namespace App\Http\Controllers\Api;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Http\Requests\BrandStoreRequest;
use App\Http\Requests\BrandUpdateRequest;
use Illuminate\Routing\Controller as BaseController;

class BrandController extends BaseController
{
    public function __construct()
    {
        $this->middleware('permission:brands-list')->only(['index', 'show']);
        $this->middleware('permission:brands-create')->only(['store']);
        $this->middleware('permission:brands-edit')->only(['update']);
        $this->middleware('permission:brands-delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Brand::query();

            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            $perPage = $request->get('per_page', 10);
            $brands = $query->paginate($perPage);

            return ResponseHelper::jsonResponse(
                true,
                'Success retrieve all Brand',
                BrandResource::collection($brands),
                200
            );
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Failed retrieve all Brand',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BrandStoreRequest $request)
    {
        try {
            $data = $request->validated();

            $brand = Brand::create($data);

            return ResponseHelper::jsonResponse(
                true,
                'Brand created',
                new BrandResource($brand),
                201
            );
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Brand failed to create',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $brand = Brand::find($id);

            if (!$brand) {
                return ResponseHelper::jsonResponse(
                    false,
                    'Brand not found',
                    null,
                    404
                );
            }

            return ResponseHelper::jsonResponse(
                true,
                'Success retrieve Brand',
                new BrandResource($brand),
                200
            );
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Failed retrieve Brand',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BrandUpdateRequest $request, string $id)
    {
        try {
            $data = $request->validated();

            $brand = Brand::find($id);

            if (!$brand) {
                return ResponseHelper::jsonResponse(
                    false,
                    'Brand not found',
                    null,
                    404
                );
            }

            $brand->update($data);

            return ResponseHelper::jsonResponse(
                true,
                'Brand updated',
                new BrandResource($brand),
                200
            );
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Brand failed to update',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $brand = Brand::find($id);

            if (!$brand) {
                return ResponseHelper::jsonResponse(
                    false,
                    'Brand not found',
                    null,
                    404
                );
            }

            $brand->delete();

            return ResponseHelper::jsonResponse(
                true,
                'Brand deleted',
                null,
                200
            );
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Brand failed to delete',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}