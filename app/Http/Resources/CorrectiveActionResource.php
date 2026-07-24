<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CorrectiveActionResource
 * 
 * API Resource untuk Corrective Action.
 */
class CorrectiveActionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'priority' => $this->priority,
            'status' => $this->status,
            'assigned_to' => [
                'id' => $this->assignedTo?->id,
                'name' => $this->assignedTo?->name,
                'email' => $this->assignedTo?->email,
            ],
            'incident' => [
                'id' => $this->incident?->id,
                'code' => $this->incident?->code,
            ],
            'risk' => [
                'id' => $this->risk?->id,
                'code' => $this->risk?->code,
            ],
            'due_date' => $this->due_date?->toDateString(),
            'completed_date' => $this->completed_date?->toDateString(),
            'effectiveness_notes' => $this->effectiveness_notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
