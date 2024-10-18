<?php

use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

if (!function_exists('storeImage')) {
    function storeImage(UploadedFile $image, string $directory): string
    {
        $hashedName = md5($image->getClientOriginalName() . time()) . '.' . $image->extension();
        return $image->storeAs('uploads/' . $directory, $hashedName, 'public');
    }
}


function apiResponse(array $options = [])
{
    // Set default values for success, message, data, and statusCode
    $response = [
        'status' => $options['status'] ?? true,
        'message' => $options['message'] ?? '',
        'data' => $options['data'] ?? null,
        'errors' => $options['errors'] ?? null
    ];

    // Filter out null attributes (if you don't want to return empty fields)
    $response = array_filter($response, fn($value) => !is_null($value));

    $statusCode = $options['statusCode'] ?? Response::HTTP_OK;

    return response()->json($response, $statusCode);
}

function paginate($items)
{
    return [
        'total' => $items->total(),
        'per_page' => $items->perPage(),
        'current_page' => $items->currentPage(),
        'last_page' => $items->lastPage(),
        'next_page_url' => $items->nextPageUrl(),
        'prev_page_url' => $items->previousPageUrl(),
        'last_page_url' => $items->url($items->lastPage()),
        'first_page_url' => $items->url(1),
    ];
}
