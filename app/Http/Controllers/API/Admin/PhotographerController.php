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

    
}
