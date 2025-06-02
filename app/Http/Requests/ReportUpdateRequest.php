<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DenunciaUpdateRequest extends FormRequest
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
        return [
            'denunciante_id' => 'required|integer|exists:users,id', // Verifica se o ID do denunciante existe na tabela users
            'denunciado_id' => 'required|integer|exists:users,id',   // Verifica se o ID do denunciado existe na tabela users
            'descricao' => 'required|string|max:255',
        ];
    }

}
