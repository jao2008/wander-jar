<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required','string','min:3','max:40'],
            'description' => ['nullable','string','max:255'],
            'privacy' => ['required','in:private,public'],
            'map_style' => ['required','in:classic,minimal,vibrant'],
        ];
    }
}
