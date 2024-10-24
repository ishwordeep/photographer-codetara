<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AvailabilityResource;
use App\Models\Availability;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AvailabilityController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $sortBy = $request->input('sort_by', 'created_at'); // Default sorting by 'created_at'
            $sortOrder = $request->input('sort_order', 'desc'); // Default sort order is 'desc',other option is 'asc'
            $month = $request->input('month', null);
            $year = $request->input('year', null);

            $query = Availability::query();

            if ($year && $month) {
                $query->whereYear('date', $year)->whereMonth('date', $month);
            }

            $items = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

            return apiResponse([
                'status' => true,
                'message' => 'Availabilities retrieved successfully',
                'data' => [
                    'count' => $items->count(),
                    'rows' => AvailabilityResource::collection($items),
                    'pagination' =>  $items->count() > 0 ? paginate($items) : null
                ]
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while retrieving availabilities',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->only(['date']);
            $availability = Availability::create($data);

            return apiResponse([
                'status' => true,
                'message' => 'Availability created successfully',
                'data' => new AvailabilityResource($availability),
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while creating availability',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $available = Availability::findOrFail($id);
            $available->delete();

            return apiResponse([
                'status' => true,
                'message' => 'Availability deleted successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            return apiResponse([
                'status' => false,
                'message' => 'Availability not found',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_NOT_FOUND,
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while deleting availability',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }
}
