<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'availability_id' => $this->availability_id,
            'ticket_number' => $this->ticket_number,
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'message' => $this->message,
            'status' => $this->status,
            'booked_at' => $this->created_at,
            'date' => $this->date,
            'category' => CategoryResource::collection($this->category),

        ];
        return $data;
    }
}
