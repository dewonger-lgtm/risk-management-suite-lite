<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class RiskResource
 * 
 * API Resource untuk Risk.
 */
class RiskResource extends JsonResource
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
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ],
            'owner' => [
                'id' => $this->owner?->id,
                'name' => $this->owner?->name,
                'email' => $this->owner?->email,
            ],
            'likelihood' => $this->likelihood,
            'impact' => $this->impact,
            'inherent_risk_score' => $this->inherent_risk_score,
            'mitigation_strategy' => $this->mitigation_strategy,
            'residual_risk_score' => $this->residual_risk_score,
            'status' => $this->status,
            'risk_date' => $this->risk_date?->toDateString(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
