<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreRiskRequest
 * 
 * Validation untuk create risk.
 */
class StoreRiskRequest extends FormRequest
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
            'company_id' => 'required|uuid|exists:companies,id',
            'category_id' => 'required|uuid|exists:risk_categories,id',
            'owner_id' => 'required|uuid|exists:users,id',
            'code' => 'required|string|unique:risks,code',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'likelihood' => 'required|integer|min:1|max:5',
            'impact' => 'required|integer|min:1|max:5',
            'inherent_risk_score' => 'nullable|integer|min:1|max:25',
            'mitigation_strategy' => 'nullable|string',
            'residual_risk_score' => 'nullable|integer|min:1|max:25',
            'status' => 'required|string|in:open,mitigating,closed',
            'risk_date' => 'required|date',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'company_id.required' => 'Company is required',
            'company_id.exists' => 'Selected company does not exist',
            'category_id.required' => 'Risk category is required',
            'category_id.exists' => 'Selected category does not exist',
            'owner_id.required' => 'Risk owner is required',
            'owner_id.exists' => 'Selected owner does not exist',
            'code.unique' => 'Risk code must be unique',
            'likelihood.min' => 'Likelihood must be at least 1',
            'likelihood.max' => 'Likelihood cannot exceed 5',
            'impact.min' => 'Impact must be at least 1',
            'impact.max' => 'Impact cannot exceed 5',
        ];
    }
}
