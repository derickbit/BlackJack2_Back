<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartidaStoreRequest extends FormRequest
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
            'jogador' => 'required|integer|exists:users,id',
            'jogo' => 'required|string|max:255',
            'pontuacao' => 'required|integer|min:0',
        ];
    }
}
