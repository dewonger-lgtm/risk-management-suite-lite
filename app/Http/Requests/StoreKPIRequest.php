<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreKPIRequest
 * 
 * Validation untuk create KPI.
 */
class StoreKPIRequest extends FormRequest
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
            'department_id' => 'required|uuid|exists:departments,id',
            'owner_id' => 'required|uuid|exists:users,id',
            'code' => 'required|string|unique:kpis,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'formula' => 'nullable|string',
            'target_value' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'frequency' => 'required|string|in:daily,weekly,monthly,quarterly,yearly',
            'status' => 'required|string|in:active,inactive',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'owner_id.required' => 'KPI owner is required',
            'code.unique' => 'KPI code must be unique',
            'target_value.required' => 'Target value is required',
        ];
    }
}
