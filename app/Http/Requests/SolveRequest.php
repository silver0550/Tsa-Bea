<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SolveRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'cities' => ['required', 'array', 'min:4'],
            'cities.*.name' => ['required', 'string'],
            'cities.*.x' => ['required', 'numeric'],
            'cities.*.y' => ['required', 'numeric'],
        ];
    }
}
