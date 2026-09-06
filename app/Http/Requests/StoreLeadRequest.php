<?php

namespace App\Http\Requests;

use App\Rules\ValidMemberCsv;
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
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'gym_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'software' => ['nullable', 'string', 'max:255'],
            'members' => ['required', 'integer', 'min:1', 'max:100000'],
            'average_fee' => ['required', 'numeric', 'gt:0', 'max:10000'],
            'inactive_members' => ['required', 'integer', 'min:0', 'max:100000', 'lte:members'],
            'monthly_cancellations' => ['required', 'integer', 'min:0', 'max:100000'],
            'csv' => ['nullable', 'file', 'mimes:csv,txt', 'max:2048', new ValidMemberCsv],
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
            'members.required' => 'Indica el número de socios.',
            'members.integer' => 'El número de socios debe ser un entero.',
            'members.min' => 'El número de socios debe ser mayor que cero.',
            'average_fee.required' => 'Indica la cuota mensual media.',
            'average_fee.gt' => 'La cuota mensual media debe ser mayor que cero.',
            'inactive_members.required' => 'Indica los socios actualmente inactivos.',
            'inactive_members.integer' => 'Los socios inactivos deben ser un entero.',
            'monthly_cancellations.required' => 'Indica las bajas mensuales aproximadas.',
            'monthly_cancellations.integer' => 'Las bajas mensuales deben ser un entero.',
            'inactive_members.lte' => 'Los socios inactivos no pueden superar el total de socios.',
            'csv.file' => 'El archivo subido no es válido.',
            'csv.mimes' => 'El archivo debe ser un CSV.',
            'csv.max' => 'El CSV no puede superar los 2 MB.',
            'consent.accepted' => 'Debes aceptar el tratamiento de tus datos.',
        ];
    }
}
