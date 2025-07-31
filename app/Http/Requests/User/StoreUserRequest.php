<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class StoreUserRequest extends FormRequest
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
        $canAssignSuperAdmin = Auth::user()->hasRole('super-admin') && $this->user?->hasRole('super-admin');

        $roles = Role::when(!$canAssignSuperAdmin, fn($q) => $q->where('name', '!=', 'super-admin'))
            ->orderBy('level')
            ->pluck('name');
        $positions = array_values(config('positions') ?? []);
        $departments = array_values(config('departments') ?? []);
        $status = ['active', 'inactive'];

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:6'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'position' => ['nullable', Rule::in($positions)],
            'department' => ['nullable', Rule::in($departments)],
            'status' => ['required', Rule::in($status)],
            'role' => ['required', Rule::in($roles)],
        ];
    }
}
