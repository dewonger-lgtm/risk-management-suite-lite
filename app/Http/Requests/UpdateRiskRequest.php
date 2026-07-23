<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateRiskRequest
 * 
 * Validation untuk update risk.
 */
class UpdateRiskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'category_id' => 'sometimes|required|uuid|exists:risk_categories,id',
            'owner_id' => 'sometimes|required|uuid|exists:users,id',
            'code' => [
                'sometimes',
                'required',
                'string',
                Rule::unique('risks', 'code')->ignore($this->route('risk')),
            ],
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'likelihood' => 'sometimes|required|integer|min:1|max:5',
            'impact' => 'sometimes|required|integer|min:1|max:5',
            'mitigation_strategy' => 'nullable|string',
            'residual_risk_score' => 'nullable|integer|min:1|max:25',
            'status' => 'sometimes|required|string|in:open,mitigating,closed',
            'risk_date' => 'sometimes|required|date',
        ];
    }
}
