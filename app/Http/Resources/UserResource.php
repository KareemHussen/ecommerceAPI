<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            // 'image' => $this->when($this->getFirstMediaUrl("main") != "", MediaResource::make($this->getMedia("main")->first())),
            'role' => $this->getRoleNames()[0],
            'token' => $this->when($this->token, $this->token),
            'ban' => $this->ban,
            'created_at' => $this->created_at
        ];
    }
}
