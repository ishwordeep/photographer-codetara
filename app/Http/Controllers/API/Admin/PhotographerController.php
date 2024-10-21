<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PhotographerResource;
use App\Models\Photographer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PhotographerController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = $request->only([
                'name',
                'email',
                'phone',
                'address',
                'description',
                'facebook',
                'instagram',
                'youtube',
            ]);

            $photographer = Photographer::create($data);
            return apiResponse([
                'status' => true,
                'message' => 'Photographer created successfully',
                'data' => new PhotographerResource($photographer),
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while creating photographer',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }

    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $sortBy = $request->input('sort_by', 'created_at'); // Default sorting by 'created_at'
            $sortOrder = $request->input('sort_order', 'desc'); // Default sort order is 'desc',other option is 'asc'

            $query = Photographer::query();
            $items = Photographer::paginate($perPage);
            $query->orderBy($sortBy, $sortOrder);
            $items = $query->paginate($perPage);

            return apiResponse([
                'status' => true,
                'message' => 'Photographers retrieved successfully',
                'data' => [
                    'count' => $items->count(),
                    'rows' => PhotographerResource::collection($items),
                    'pagination' =>  $items->count() > 0 ? paginate($items) : null
                ]
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while retrieving photographers',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }

    public function show(string $id)
    {
        try {
            $photographer = Photographer::findOrFail($id);
            return apiResponse([
                'status' => true,
                'message' => 'Photographer retrieved successfully',
                'data' => new PhotographerResource($photographer),
            ]);
        } catch (ModelNotFoundException) {
            return apiResponse([
                'status' => false,
                'message' => 'Photographer not found',
                'statusCode' => Response::HTTP_NOT_FOUND,
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while retrieving photographer',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }

    public function update(Request $request)
    {
        try {
            $photographer = Photographer::findOrFail($request->id);
            $data = $request->only([
                'name',
                'email',
                'phone',
                'address',
                'description',
                'facebook',
                'instagram',
                'youtube',
            ]);

            $photographer->update($data);
            return apiResponse([
                'status' => true,
                'message' => 'Photographer updated successfully',
                'data' => new PhotographerResource($photographer),
            ]);
        } catch (ModelNotFoundException) {
            return apiResponse([
                'status' => false,
                'message' => 'Photographer not found',
                'statusCode' => Response::HTTP_NOT_FOUND,
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while updating photographer',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $photographer = Photographer::findOrFail($id);
            $photographer->delete();
            return apiResponse([
                'status' => true,
                'message' => 'Photographer deleted successfully',
            ]);
        } catch (ModelNotFoundException) {
            return apiResponse([
                'status' => false,
                'message' => 'Photographer not found',
                'statusCode' => Response::HTTP_NOT_FOUND,
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while deleting photographer',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }
}
