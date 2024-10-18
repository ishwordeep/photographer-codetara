<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubcategoryResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'category' =>  new CategoryResource($this->whenLoaded('category')),
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];
        if ($this->image) {
            $data['image'] = asset('storage/' . $this->image);
        }
        // filter null values from the array
        $data = array_filter($data, function ($value) {
            return !is_null($value);
        });
        return $data;
    }
}
