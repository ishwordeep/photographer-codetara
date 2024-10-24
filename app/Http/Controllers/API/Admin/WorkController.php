<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\WorkResource;
use App\Models\Work;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $sortBy = $request->input('sort_by', 'created_at'); // Default sorting by 'created_at'
            $sortOrder = $request->input('sort_order', 'desc'); // Default sort order is 'desc',other option is 'asc'

            $items = Work::orderBy($sortBy, $sortOrder)->paginate($perPage);

            return apiResponse([
                'status' => true,
                'message' => 'Works retrieved successfully',
                'data' => [
                    'count' => $items->count(),
                    'rows' => WorkResource::collection($items),
                    'pagination' =>  $items->count() > 0 ? paginate($items) : null
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

    public function store(Request $request)
    {
        try {
            $data = $request->only(['title', 'description', 'is_active', 'date']);

            if ($request->hasFile('image')) {
                $data['image'] = storeImage($request->file('image'), 'works');
            }


            $work = Work::create($data);

            if ($request->has('images')) {
                foreach ($request->images as $image) {
                    $work->images()->create([
                        'image' => storeImage($image, 'works'),
                    ]);
                }
            }

            return apiResponse([
                'status' => true,
                'message' => 'Work created successfully',
                'data' => new WorkResource($work),
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while creating work',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }
}
