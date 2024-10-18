<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubcategoryRequest;
use App\Http\Resources\SubcategoryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $sortBy = $request->input('sort_by', 'created_at'); // Default sorting by 'created_at'
            $sortOrder = $request->input('sort_order', 'desc'); // Default sort order is 'desc',other option is 'asc'

            // $query = Subcategory::query();
            $query = Subcategory::with('category')->orderBy($sortBy, $sortOrder);
            $items = $query->paginate($perPage);

            return apiResponse([
                'status' => true,
                'message' => 'Subcategories retrieved successfully',
                'data' => [
                    'count' => $items->count(),
                    'rows' => SubcategoryResource::collection($items),
                    'pagination' =>  $items->count() > 0 ? paginate($items) : null
                ]
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while retrieving subcategories',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubcategoryRequest $request)
    {

        DB::beginTransaction();
        try {
            $data = [
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'category_id' => $request->category_id,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
            ];

            if ($request->hasFile('image')) {
                $data['image'] = storeImage($request->file('image'), 'subcategories');
            }

            $subcategory = Subcategory::create($data);
            $subcategory->load('category');
            DB::commit();

            return apiResponse([
                'status' => true,
                'message' => 'Subcategory created successfully',
                'data' => new SubcategoryResource($subcategory),
                'statusCode' => Response::HTTP_CREATED,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while creating subcategory',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $subcategory = Subcategory::with('category')->findOrFail($id);

            return apiResponse([
                'status' => true,
                'message' => 'Subcategory retrieved successfully',
                'data' => new SubcategoryResource($subcategory),
            ]);
        } catch (ModelNotFoundException $e) {
            return apiResponse([
                'status' => false,
                'message' => 'Subcategory not found',
                'statusCode' => Response::HTTP_NOT_FOUND,
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while retrieving subcategory',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SubcategoryRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $subcategory = Subcategory::findOrFail($id);
            $data = $request->only([
                'category_id' => $request->category_id,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
            ]);

            if ($request->filled('name')) {
                $data['name'] = $request->name;
                $data['slug'] = Str::slug($request->name);
            }

            if ($request->hasFile('image')) {
                $data['image'] = storeImage($request->file('image'), 'categories'); // Update with new image
            } elseif ($request->filled('remove_image')) {
                $data['image'] = null; // Remove the image
            } else {
                $data['image'] = $subcategory->image; // Keep the existing image
            }

            $subcategory->update($data);
            DB::commit();

            return apiResponse([
                'status' => true,
                'message' => 'Subcategory updated successfully',
                'data' => new SubcategoryResource($subcategory),
            ]);
        } catch (ModelNotFoundException $e) {
            return apiResponse([
                'status' => false,
                'message' => 'Subcategory not found',
                'statusCode' => Response::HTTP_NOT_FOUND,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while updating subcategory',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $subcategory = Subcategory::findOrFail($id);
            $subcategory->update(['is_active' => false]);
            $subcategory->delete();
            DB::commit();

            return apiResponse([
                'status' => true,
                'message' => 'Subcategory deleted successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return apiResponse([
                'status' => false,
                'message' => 'Subcategory not found',
                'statusCode' => Response::HTTP_NOT_FOUND,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while deleting subcategory',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }

    public function trash(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $items = Subcategory::with('category')->onlyTrashed()->orderBy('deleted_at', 'desc')->paginate($perPage);

            return apiResponse([
                'status' => true,
                'message' => 'Trashed subcategories retrieved successfully',
                'data' => [
                    'count' => $items->count(),
                    'rows' => SubcategoryResource::collection($items),
                    'pagination' =>  $items->count() > 0 ? paginate($items) : null
                ]
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while retrieving trashed subcategories',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }

    public function restore(string $id)
    {
        DB::beginTransaction();
        try {
            $subcategory = Subcategory::onlyTrashed()->findOrFail($id);
            $subcategory->update(['is_active' => true]);
            $subcategory->restore();
            DB::commit();

            return apiResponse([
                'status' => true,
                'message' => 'Subcategory restored successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return apiResponse([
                'status' => false,
                'message' => 'Subcategory not found',
                'statusCode' => Response::HTTP_NOT_FOUND,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while restoring subcategory',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }
}
