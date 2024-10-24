<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\WorkResource;
use App\Models\Work;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        DB::beginTransaction();
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

            DB::commit();

            return apiResponse([
                'status' => true,
                'message' => 'Work created successfully',
                'data' => new WorkResource($work),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while creating work',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }

    public function show(string $id)
    {
        try {
            $work = Work::findOrFail($id);

            return apiResponse([
                'status' => true,
                'message' => 'Work retrieved successfully',
                'data' => new WorkResource($work),
            ]);
        } catch (ModelNotFoundException) {
            return apiResponse([
                'status' => false,
                'message' => 'Work not found',
                'statusCode' => Response::HTTP_NOT_FOUND,
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

    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $work = Work::findOrFail($id);

            $data = $request->only(['title', 'description', 'is_active', 'date']);

            if ($request->hasFile('image')) {
                $data['image'] = storeImage($request->file('image'), 'works');
            }elseif ($request->filled('remove_image')) {
                $data['image'] = null;
            }else{
                $data['image'] = $work->image;
            }

            

            $work->update($data);

            if ($request->has('images')) {
                $work->images()->delete();
                foreach ($request->images as $image) {
                    $work->images()->create([
                        'image' => storeImage($image, 'works'),
                    ]);
                }
            }

            if(isset($request->deleted_images)){
                $deletedImages = json_decode($request->deleted_images);
                foreach ($deletedImages as $deletedImage) {
                    $work->images()->where('id', $deletedImage)->delete();
                }
            }

            DB::commit();

            return apiResponse([
                'status' => true,
                'message' => 'Work updated successfully',
                'data' => new WorkResource($work),
            ]);
        } catch (ModelNotFoundException) {
            DB::rollBack();
            return apiResponse([
                'status' => false,
                'message' => 'Work not found',
                'statusCode' => Response::HTTP_NOT_FOUND,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while updating work',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }

    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $work = Work::findOrFail($id);

            $work->delete();

            DB::commit();

            return apiResponse([
                'status' => true,
                'message' => 'Work deleted successfully',
            ]);
        } catch (ModelNotFoundException) {
            DB::rollBack();
            return apiResponse([
                'status' => false,
                'message' => 'Work not found',
                'statusCode' => Response::HTTP_NOT_FOUND,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while deleting work',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }
}
