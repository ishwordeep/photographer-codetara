<?php

namespace App\Http\Controllers\API\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
   

    public function show($id)
    {
        try {
            $item = Category::findOrFail($id);
            return apiResponse([
                'status' => true,
                'message' => 'Category retrieved successfully',
                'data' => new CategoryResource($item),
            ]);
        } catch (ModelNotFoundException) {
            return apiResponse([
                'status' => false,
                'message' => 'Category not found',
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while retrieving category',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }

    public function index()
    {
        try {
            $items = Category::select('name', 'id', 'slug','image')->where('is_active', true)->get();

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
