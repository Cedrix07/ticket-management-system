<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketActivityResource extends JsonResource
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
            'event_type' => $this->event_type,
            'old_value' => $this->old_value,
            'new_value' => $this->new_value,
            'description' => $this->description,
            'created_at' => $this->created_at?->toISOString(),
            'user' => UserResource::make($this->whenLoaded('user')) // load only when user is loaded 
        ];
    }
}
