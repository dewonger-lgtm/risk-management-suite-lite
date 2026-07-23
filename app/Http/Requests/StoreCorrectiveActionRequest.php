<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreCorrectiveActionRequest
 * 
 * Validation untuk create corrective action.
 */
class StoreCorrectiveActionRequest extends FormRequest
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
            'incident_id' => 'nullable|uuid|exists:incidents,id',
            'risk_id' => 'nullable|uuid|exists:risks,id',
            'assigned_to' => 'required|uuid|exists:users,id',
            'code' => 'required|string|unique:corrective_actions,code',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string|in:corrective,preventive',
            'priority' => 'required|string|in:critical,high,medium,low',
            'status' => 'required|string|in:open,in_progress,completed,verified,closed',
            'due_date' => 'required|date|after:today',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'assigned_to.required' => 'Assignee is required',
            'assigned_to.exists' => 'Selected assignee does not exist',
            'code.unique' => 'CA code must be unique',
            'due_date.after' => 'Due date must be in the future',
        ];
    }
}
