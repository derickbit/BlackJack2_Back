<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportMessageStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mensagem' => 'required|string|max:1000',
            'imagem' => 'nullable|image',
        ];
    }
}
