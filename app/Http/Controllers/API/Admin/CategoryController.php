<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\CategoryImage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class CategoryController extends Controller
{

    public function index(Request $request)
    {

        try {
            $perPage = $request->input('per_page', 10);
            $sortBy = $request->input('sort_by', 'created_at'); // Default sorting by 'created_at'
            $sortOrder = $request->input('sort_order', 'desc'); // Default sort order is 'desc',other option is 'asc'

            $query = Category::query();
            $items = Category::paginate($perPage);
            $query->orderBy($sortBy, $sortOrder);
            $items = $query->paginate($perPage);


            return apiResponse([
                'status' => true,
                'message' => 'Categories retrieved successfully',
                'data' => [
                    'count' => $items->count(),
                    'rows' => CategoryResource::collection($items),
                    'pagination' =>  $items->count() > 0 ? paginate($items) : null
                ]
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while retrieving categories',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }


    public function store(CategoryRequest $request)
    {

        DB::beginTransaction();
        try {
            $data = [
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
                'icon'=>$request->icon
            ];

            if ($request->hasFile('image')) {
                $data['image'] = storeImage($request->file('image'), 'categories'); // 'categories' is the folder for storing category images
            }
            $category = Category::create($data);
            if ($request->has('images')) {
                foreach ($request->images as $image) {
                    $cat_image = storeImage($image, 'categories'); // 'categories' is the folder for storing category images
                    $category->images()->create(['image' => $cat_image]);
                }
            }

            DB::commit();
            return apiResponse([
                'status' => true,
                'message' => 'Category created successfully',
                'data' => new CategoryResource($category),
                'statusCode' => Response::HTTP_CREATED,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return apiResponse([
                'status' => false,
                'message' => 'Category creation failed',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }


    public function show(string $id)
    {
        try {
            $category = Category::findOrFail($id);

            return apiResponse([
                'status' => true,
                'message' => 'Category retrieved successfully',
                'data' => new CategoryResource($category),
            ]);
        } catch (ModelNotFoundException $e) {
            // If category not found, return 404
            return apiResponse([
                'status' => false,
                'message' => 'Category not found',
                'statusCode' => Response::HTTP_NOT_FOUND,
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while retrieving the category',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }


    public function update(CategoryRequest $request, string $id)
    {

        DB::beginTransaction();
        try {
            $category = Category::findOrFail($id);

            $data = $request->only(['description', 'is_active','icon']);

            if ($request->filled('name')) {
                $data['name'] = $request->name;
                $data['slug'] = Str::slug($request->name);
            }

            if ($request->hasFile('image')) {
                $data['image'] = storeImage($request->file('image'), 'categories'); // Update with new image
            } elseif ($request->filled('remove_image')) {
                $data['image'] = null; // Remove the image
            } else {
                $data['image'] = $category->image; // Keep the existing image
            }

            // Update the category with the provided data
            $category->update($data);

            if ($request->has('images')) {
                foreach ($request->images as $image) {
                    $cat_image = storeImage($image, 'categories');
                    $category->images()->create(['image' => $cat_image]); // Insert each image
                }
            }

            if (isset($request->deleted_images)) {
                // parse
                $deletedImages= json_decode($request->deleted_images);
                foreach ($deletedImages as  $val) {
                    $categoryImage = CategoryImage::findOrFail(intval($val));
                    $categoryImage->delete();
                }
            }

            DB::commit();
            return apiResponse([
                'status' => true,
                'message' => 'Category updated successfully',
                'data' => new CategoryResource($category),
            ]);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return apiResponse([
                'status' => false,
                'message' => 'Category not found',
                'statusCode' => Response::HTTP_NOT_FOUND,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while updating the category',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }


    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $category = Category::findOrFail($id);
            $category->update(['is_active' => false]);

            $category->delete();
            DB::commit();

            // Return success response
            return apiResponse([
                'status' => true,
                'message' => 'Category deleted successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            // If category not found, return 404
            return apiResponse([
                'status' => false,
                'message' => 'Category not found',
                'statusCode' => Response::HTTP_NOT_FOUND,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // For any other exception, return internal server error
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while deleting the category',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }

    public function trash(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $items = Category::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate($perPage);

            return apiResponse([
                'status' => true,
                'message' => 'Deleted categories retrieved successfully',
                'data' => [
                    'count' => $items->count(),
                    'rows' => CategoryResource::collection($items),
                    'pagination' =>  $items->count() > 0 ? paginate($items) : null
                ]
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while retrieving deleted categories',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }
    public function restore(string $id)
    {
        DB::beginTransaction();
        try {
            $category = Category::onlyTrashed()->findOrFail($id);

            $category->update(['is_active' => true]);

            $category->restore();
            DB::commit();

            // Return success response
            return apiResponse([
                'status' => true,
                'message' => 'Category restored successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            // If category not found, return 404
            return apiResponse([
                'status' => false,
                'message' => 'Category not found',
                'statusCode' => Response::HTTP_NOT_FOUND,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // For any other exception, return internal server error
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while restoring the category',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }

    public function getCategoryList()
    {
        try {
            $items = Category::select('name', 'id')->where('is_active', true)->get();

            return apiResponse([
                'status' => true,
                'message' => 'Categories retrieved successfully',
                'data' => [
                    'count' => $items->count(),
                    'rows' => CategoryResource::collection($items),
                ]
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while retrieving categories',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }
}
