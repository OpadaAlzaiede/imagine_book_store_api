<?php

namespace App\Rules;

use App\Models\Book;
use Illuminate\Contracts\Validation\Rule;

class SufficientBookQuantity implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $book = Book::find($value['id']);
        if (!$book) {
            return false;
        }

        return $book->quantity >= $value['quantity'];
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The book does not have a sufficient quantity.';
    }
}
