<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateCorrectiveActionRequest
 * 
 * Validation untuk update corrective action.
 */
class UpdateCorrectiveActionRequest extends FormRequest
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
            'assigned_to' => 'sometimes|required|uuid|exists:users,id',
            'code' => [
                'sometimes',
                'required',
                'string',
                Rule::unique('corrective_actions', 'code')->ignore($this->route('correctiveAction')),
            ],
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'type' => 'sometimes|required|string|in:corrective,preventive',
            'priority' => 'sometimes|required|string|in:critical,high,medium,low',
            'status' => 'sometimes|required|string|in:open,in_progress,completed,verified,closed',
            'due_date' => 'sometimes|required|date',
            'effectiveness_notes' => 'nullable|string',
        ];
    }
}
