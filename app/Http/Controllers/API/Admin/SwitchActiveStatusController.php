<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class SwitchActiveStatusController extends Controller
{
    public function toggleStatus($modelName, $id)
    {
        try {
            $model = "App\\Models\\" . Str::studly($modelName);;
            $model = $model::findOrFail($id);
            $model->is_active = !$model->is_active;
            $model->save();
            return apiResponse([
                'status' => true,
                'message' => 'Status updated successfully',
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while updating status',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }
}
