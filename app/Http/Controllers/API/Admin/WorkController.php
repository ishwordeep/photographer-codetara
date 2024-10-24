<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\WorkResource;
use App\Models\Work;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkController extends Controller
{

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
