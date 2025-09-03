<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAtualizacaoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Apenas usuários autenticados e admins podem criar atualizações
        return Auth::check() && Auth::user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'conteudo' => 'required|string',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Máximo 2MB
            'versao' => 'nullable|string|max:50',
            'tipo' => 'required|in:feature,bugfix,improvement,breaking',
            'ativo' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'O título é obrigatório',
            'titulo.max' => 'O título não pode exceder 255 caracteres',
            'conteudo.required' => 'O conteúdo é obrigatório',
            'imagem.image' => 'O arquivo deve ser uma imagem',
            'imagem.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg, gif ou webp',
            'imagem.max' => 'A imagem não pode exceder 2MB',
            'versao.max' => 'A versão não pode exceder 50 caracteres',
            'tipo.required' => 'O tipo de atualização é obrigatório',
            'tipo.in' => 'O tipo deve ser: feature, bugfix, improvement ou breaking',
        ];
    }
}
