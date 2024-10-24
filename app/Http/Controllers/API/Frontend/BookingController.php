<?php

namespace App\Http\Controllers\API\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'name',
                'phone',
                'address',
                'message',
                'date',
                'category_id'
            ]);
            $data['status'] = 'pending'; // Default status is 'pending'
            // Generate a unique ticket number
            $data['ticket_number'] = $this->generateTicketNumber();
            // Create a new booking
            $booking = Booking::create($data);
            DB::commit();
            return apiResponse([
                'status' => true,
                'message' => 'Booking created successfully',
                'data' => new BookingResource($booking),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return apiResponse([
                'status' => false,
                'message' => 'An error occurred while creating booking',
                'errors' => $e->getMessage(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }


    public function generateTicketNumber()
    {
        $year = date('y'); // Two-digit year (00 to 99)
        $month = date('m'); // Two-digit month (01 to 12)

        $randomNumber = random_int(0, 9999); // Random number between 0 and 9999
        $ticket_number = $year . $month . str_pad($randomNumber, 4, '0', STR_PAD_LEFT);
        // Check for uniqueness and regenerate if necessary
        while (DB::table('bookings')->where('ticket_number', $ticket_number)->exists()) {
            $randomNumber = random_int(0, 9999);
            $ticket_number = $year . $month . str_pad($randomNumber, 4, '0', STR_PAD_LEFT);
        }
        return $ticket_number;
    }
}
