<?php

namespace App\Http\Controllers\API\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\PhotographerResource;
use App\Models\Photographer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PhotographerController extends Controller
{
    public function index()
    {
        try {
            $item = Photographer::findOrFail(1);
            return apiResponse([
                'status' => true,
                'message' => 'Photographers retrieved successfully',
                'data' => new PhotographerResource($item),
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
}
