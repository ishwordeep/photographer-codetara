<?php

namespace App\Http\Controllers\API\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\WorkResource;
use App\Models\Work;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkController extends Controller
{
    public function index()
    {
        try {
            $items = Work::where('is_active', true)->get();
            return apiResponse([
                'status' => true,
                'message' => 'Works retrieved successfully',
                'data' => [
                    'count' => $items->count(),
                    'rows' => WorkResource::collection($items),
                ]
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while retrieving works',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }

    public function show($id)
    {
        try {
            $item = Work::findOrFail($id);
            return apiResponse([
                'status' => true,
                'message' => 'Work retrieved successfully',
                'data' => new WorkResource($item),
            ]);
        } catch (ModelNotFoundException) {
            return apiResponse([
                'status' => false,
                'message' => 'Work not found',
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while retrieving work',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }
}
