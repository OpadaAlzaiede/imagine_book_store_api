<?php

namespace App\Http\Requests\Order;

use App\Rules\SufficientBookQuantity;
use App\Traits\JsonErrors;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
            'books' => ['array'],
            'books.*.id' => ['required', Rule::exists('books')->where('quantitiy', '>', 0)],
            'books.*.quantity' => ['required', new SufficientBookQuantity()],
        ];
    }
}
