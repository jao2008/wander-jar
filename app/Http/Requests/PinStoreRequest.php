<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PinStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title'         => ['required', 'string', 'min:3', 'max:120'],
            'content'       => ['nullable', 'string', 'max:1000'],
            'group_id'      => ['nullable', 'exists:groups,id'],

            'location_text' => ['nullable', 'string', 'max:180'],
            'lat'           => ['nullable', 'numeric', 'between:-90,90'],
            'lng'           => ['nullable', 'numeric', 'between:-180,180'],

            'image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}
