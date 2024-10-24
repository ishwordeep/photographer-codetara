<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date,
            'is_active' => $this->is_active,
            'images' => WorkImageResource::collection($this->images),
        ];

        if ($this->image) {
            $data['image'] = asset('storage/' . $this->image);
        }



        return $data;
    }
}
