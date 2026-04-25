<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class CreatePollRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'options'     => ['required', 'array', 'min:2', 'max:10'],
            'options.*'   => ['required', 'string', 'max:500', 'distinct'],
            'starts_at'   => ['nullable', 'date', 'after_or_equal:now'],
            'ends_at'     => ['nullable', 'date', 'after:starts_at'],
        ];
    }
}
