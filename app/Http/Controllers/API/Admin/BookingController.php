<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $sortBy = $request->input('sort_by', 'created_at'); // Default sorting by 'created_at'
            $sortOrder = $request->input('sort_order', 'desc'); // Default sort order is 'desc',other option is 'asc'

            $year = $request->input('year');
            $month = $request->input('month');

            $items = Booking::orderBy($sortBy, $sortOrder)
                ->when($year, function ($query, $year) {
                    if($month) {
                        return $query->whereYear('date', $year)->whereMonth('created_at', $month);
                    }
                    return $query->whereYear('date', $year);
                })
                ->paginate($perPage);

            return apiResponse([
                'status' => true,
                'message' => 'Bookings retrieved successfully',
                'data' => [
                    'count' => $items->count(),
                    'rows' => BookingResource::collection($items),
                    'pagination' =>  $items->count() > 0 ? paginate($items) : null
                ]
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while retrieving bookings',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }
    
    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $data = $request->only([
    //             'availability_id',
    //             'name',
    //             'phone',
    //             'address',
    //             'message',
    //             'status',
    //         ]);
    //         // Generate a unique ticket number
    //         $data['ticket_number'] = $this->generateTicketNumber();
    //         // Create a new booking
    //         $booking = Booking::create($data);
    //         DB::commit();
    //         return apiResponse([
    //             'status' => true,
    //             'message' => 'Booking created successfully',
    //             'data' => new BookingResource($booking),
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return apiResponse([
    //             'status' => false,
    //             'message' => 'An error occurred while creating booking',
    //             'errors' => $e->getMessage(),
    //             'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
    //         ]);
    //     }
    // }

    public function generateTicketNumber()
    {
        $year = date('y'); // Two-digit year (00 to 99)
        $month = date('m'); // Two-digit month (01 to 12)

        $randomNumber = random_int(0, 9999); // Random number between 0 and 9999
        $ticket_number = $year . $month . str_pad($randomNumber, 4, '0', STR_PAD_LEFT);
        // Check for uniqueness and regenerate if necessary
        while (DB::table('your_table_name')->where('ticket_number', $ticket_number)->exists()) {
            $randomNumber = random_int(0, 9999);
            $ticket_number = $year . $month . str_pad($randomNumber, 4, '0', STR_PAD_LEFT);
        }
        return $ticket_number;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $booking = Booking::findOrFail($id);
            return apiResponse([
                'status' => true,
                'message' => 'Booking retrieved successfully',
                'data' => new BookingResource($booking),
            ]);
        } catch (ModelNotFoundException $e) {
            return apiResponse([
                'status' => false,
                'message' => 'Booking not found',
                'statusCode' => Response::HTTP_NOT_FOUND,
            ]);
        } catch (\Exception $e) {
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while retrieving booking',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $booking = Booking::findOrFail($id);
            $booking->delete();
            DB::commit();
            return apiResponse([
                'status' => true,
                'message' => 'Booking deleted successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            return apiResponse([
                'status' => false,
                'message' => 'Booking not found',
                'statusCode' => Response::HTTP_NOT_FOUND,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while deleting booking',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }
}
