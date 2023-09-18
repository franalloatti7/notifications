<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'tag' => ['required', 'string'],
            'message' => ['required', 'string'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            "tag.required" => "El tag es requerido",
            "tag.string" => "El tag debe ser una cadena de texto",
            "message.required" => "El mensaje es requerido",
            "message.string" => "El mensaje debe ser una cadena de texto",
        ];
    }
}
