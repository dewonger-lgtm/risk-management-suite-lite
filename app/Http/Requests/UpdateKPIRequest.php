<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateKPIRequest
 * 
 * Validation untuk update KPI.
 */
class UpdateKPIRequest extends FormRequest
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
            'owner_id' => 'sometimes|required|uuid|exists:users,id',
            'code' => [
                'sometimes',
                'required',
                'string',
                Rule::unique('kpis', 'code')->ignore($this->route('kpi')),
            ],
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'formula' => 'nullable|string',
            'target_value' => 'sometimes|required|numeric|min:0',
            'unit' => 'sometimes|required|string|max:50',
            'frequency' => 'sometimes|required|string|in:daily,weekly,monthly,quarterly,yearly',
            'status' => 'sometimes|required|string|in:active,inactive',
        ];
    }
}
