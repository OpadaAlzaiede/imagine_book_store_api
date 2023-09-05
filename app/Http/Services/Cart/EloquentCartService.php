<?php

namespace App\Http\Services\Cart;


use App\Http\Services\UserResolver\UserResolver;

class EloquentCartService implements CartService {

    protected $userResolver;

    public function __construct(UserResolver $userResolver)
    {
        $this->userResolver = $userResolver;
    }

    public function get()
    {
        return $this->userResolver->get()->cart;
    }

    public function add($bookId, $quantity)
    {
        $cart = $this->userResolver->get()->cart();

        $cartBook = $cart->where('book_id', $bookId)->first();

        if($cartBook) {

            $cartBook->pivot->quantity = $quantity;
            $cartBook->pivot->save();
        }
        else {
            $cart->attach($bookId, [
                'quantity' => $quantity
            ]);
        }

        return $this->userResolver->get()->cart()->get();
    }

    public function remove($bookId)
    {
        $this->userResolver->get()->detach($bookId);

        return $this->userResolver->get()->cart;
    }
}
