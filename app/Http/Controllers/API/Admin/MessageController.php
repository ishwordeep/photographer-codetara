<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $sortBy = $request->input('sort_by', 'created_at'); // Default sorting by 'created_at'
            $sortOrder = $request->input('sort_order', 'desc'); // Default sort order is 'desc',other option is 'asc'

            $items = Message::orderBy($sortBy, $sortOrder)->paginate($perPage);


            return apiResponse([
                'status' => true,
                'message' => 'Messages retrieved successfully',
                'data' => [
                    'count' => $items->count(),
                    'rows' => MessageResource::collection($items),
                    'pagination' =>  $items->count() > 0 ? paginate($items) : null
                ]
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while retrieving messages',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }

    public function show($id)
    {
        try {
            $item = Message::findOrFail($id);
            return apiResponse([
                'status' => true,
                'message' => 'Message retrieved successfully',
                'data' => new MessageResource($item),
            ]);
        } catch (ModelNotFoundException) {
            return apiResponse([
                'status' => false,
                'message' => 'Message not found',
                'statusCode' => Response::HTTP_NOT_FOUND,
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while retrieving message',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }
}
