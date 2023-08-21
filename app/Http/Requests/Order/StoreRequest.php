<?php

namespace App\Http\Requests\Order;

use App\Rules\SufficientBookQuantity;
use App\Traits\JsonErrors;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    use JsonErrors;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'books' => ['required', 'array', new SufficientBookQuantity()],
            'books.*.id' => ['required', Rule::exists('books')],
            'books.*.quantity' => ['required', 'numeric']
        ];
    }
}
