<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateIncidentRequest
 * 
 * Validation untuk update incident.
 */
class UpdateIncidentRequest extends FormRequest
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
            'code' => [
                'sometimes',
                'required',
                'string',
                Rule::unique('incidents', 'code')->ignore($this->route('incident')),
            ],
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'type' => 'sometimes|required|string|in:safety,quality,security,operational',
            'severity' => 'sometimes|required|string|in:critical,high,medium,low',
            'status' => 'sometimes|required|string|in:open,investigating,resolved,closed',
            'reported_date' => 'sometimes|required|date',
            'occurred_date' => 'sometimes|required|date|before_or_equal:reported_date',
            'investigated_by' => 'nullable|uuid|exists:users,id',
            'investigation_findings' => 'nullable|string',
            'root_cause' => 'nullable|string',
        ];
    }
}
