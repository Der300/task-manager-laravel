<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $slugRule = Rule::unique('projects', 'slug'); //Rule unique làm query thẳng trên bảng, không lọc soft delete

        if ($this->project?->id) {
            $slugRule->ignore($this->project->id);
        }
        return [
            'name' => ['required', 'string', 'max:50'],
            'slug' => ['required', 'string', 'max:100', $slugRule],
            'description' => ['nullable', 'string'],

            'status_id' => ['nullable', 'exists:statuses,id'],
            'issue_type_id' => ['nullable', 'exists:issue_types,id'],

            'created_by' => ['nullable', 'exists:users,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'client_id' => ['nullable', 'exists:users,id'],

            'start_date' => ['nullable', 'date', 'before_or_equal:due_date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
