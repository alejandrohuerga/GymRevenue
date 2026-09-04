<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
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
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'gym_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'software' => ['nullable', 'string', 'max:255'],
            'members' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'average_fee' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'inactive_members' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'monthly_cancellations' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'consent' => ['accepted'],
            'website' => ['prohibited'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'gym_name.required' => 'El nombre del gimnasio es obligatorio.',
            'contact_name.required' => 'Tu nombre es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'Introduce un email válido.',
            'email.max' => 'El email no puede superar los 255 caracteres.',
            'consent.accepted' => 'Debes aceptar el tratamiento de tus datos.',
        ];
    }
}
