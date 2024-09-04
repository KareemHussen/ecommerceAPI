<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'priceBefore' => $this->priceBefore,
            'quantity' => $this->quantity,
            'image' => $this->whenLoaded("main_image") && $this->getFirstMediaUrl("main") != "" ? MediaResource::make($this->whenLoaded("main_image")[0]) : null,
            'additional_images' => MediaResource::collection($this->whenLoaded("additional_images")),
            'description' => $this->description,
            'live' => $this->live,
            'special_offer' => $this->special_offer,
            'daily_offer' => $this->daily_offer,
            'similarProducts' => ProductResource::collection($this->whenLoaded('similarProducts')),
            'ratings' => RatingResource::collection($this->whenLoaded('ratings')),
        ];
    }
}
