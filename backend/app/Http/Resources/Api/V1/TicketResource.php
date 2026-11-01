<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
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
            'ticket_number' => $this->ticket_number,
            'subject' => $this->subject,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'resolution_notes' => $this->resolution_notes,
            'resolved_at' => $this->resolved_at?->toISOString(),
            'closed_at' => $this->closed_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relations to avoid N+1 problem
            'requester' => new UserResource($this->whenLoaded('requester')),
            'assignee' => new UserResource($this->whenLoaded('assignee')),
            'category' => new CategoryResource($this->whenLoaded('category')),
        ];
    }
}
