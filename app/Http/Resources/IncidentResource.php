<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class IncidentResource
 * 
 * API Resource untuk Incident.
 */
class IncidentResource extends JsonResource
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
            'severity' => $this->severity,
            'status' => $this->status,
            'reported_by' => [
                'id' => $this->reportedBy?->id,
                'name' => $this->reportedBy?->name,
                'email' => $this->reportedBy?->email,
            ],
            'reported_date' => $this->reported_date?->toDateString(),
            'occurred_date' => $this->occurred_date?->toDateString(),
            'investigated_by' => [
                'id' => $this->investigatedBy?->id,
                'name' => $this->investigatedBy?->name,
            ],
            'investigation_findings' => $this->investigation_findings,
            'root_cause' => $this->root_cause,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
