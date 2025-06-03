<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
                        'user_id' => 'required|integer|exists:users,id',
            'status' => 'required|string|max:50', // Pode ajustar se o status inicial é sempre 'aberto'
            'titulo' => 'required|string|max:255',
        ];
    }
}
