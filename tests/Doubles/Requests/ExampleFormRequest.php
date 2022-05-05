<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Worksome\RequestFactories\Concerns\HasFactory;

final class ExampleFormRequest extends FormRequest
{
    use HasFactory;

    public function rules(): array
    {
        return [];
    }
}
