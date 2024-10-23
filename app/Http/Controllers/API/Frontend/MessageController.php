<?php

namespace App\Http\Controllers\API\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = $request->only(['name', 'email', 'phone', 'message', 'address']);
            Message::create($data);

            return apiResponse([
                'status' => true,
                'message' => 'Message sent successfully',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while sending message',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }
}
