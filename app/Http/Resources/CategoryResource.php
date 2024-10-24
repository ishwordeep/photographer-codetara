<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data= [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'images'=>CategoryImageResource::collection($this->images),
            'is_active' => $this->is_active,
            'icon' => $this->icon,
        ];
        if($this->image){
            $data['image'] = asset('storage/'.$this->image);
        }
        
        // filter null values from the array
        $data = array_filter($data, function ($value) {
            return !is_null($value);
        });
        return $data;
    }
}
