<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return request()->user()->can(
            'update',
            request()->route('project')
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $method = request()->method();

        switch ($method) {
            case 'PUT':
                return [
                    'title' => ['required', 'string'],
                    'description' => ['nullable', 'string'],
                ];

            case 'PATCH':
            default:
                return [
                    'title' => ['sometimes', 'required', 'string'],
                    'description' => ['nullable', 'string'],
                ];
        }
    }
}
