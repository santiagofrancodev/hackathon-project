<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'nit' => 'nullable|string|max:50',
            'sector' => 'nullable|string|max:255',
            'size' => 'nullable|string|in:small,medium,large',
        ];
    }
}
