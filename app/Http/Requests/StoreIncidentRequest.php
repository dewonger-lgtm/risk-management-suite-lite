<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreIncidentRequest
 * 
 * Validation untuk create incident.
 */
class StoreIncidentRequest extends FormRequest
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
            'code' => 'required|string|unique:incidents,code',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string|in:safety,quality,security,operational',
            'severity' => 'required|string|in:critical,high,medium,low',
            'status' => 'required|string|in:open,investigating,resolved,closed',
            'reported_date' => 'required|date',
            'occurred_date' => 'required|date|before_or_equal:reported_date',
            'investigation_findings' => 'nullable|string',
            'root_cause' => 'nullable|string',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'company_id.required' => 'Company is required',
            'occurred_date.before_or_equal' => 'Occurred date must be before or equal to reported date',
            'code.unique' => 'Incident code must be unique',
        ];
    }
}
