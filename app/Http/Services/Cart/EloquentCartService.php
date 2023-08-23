<?php

namespace App\Http\Services\Cart;


class EloquentCartService implements CartService {

    public function get()
    {

        return auth()->user()->cart;
    }

    public function add($bookId, $quantity)
    {
        $cart = auth()->user()->cart();

        $cart->attach($bookId, [
            'quantity' => $quantity
        ]);

        return $cart->get();
    }

    public function remove($bookId)
    {
        $cart = auth()->user()->cart();

        $cart->detach($bookId);

        return $cart->get();
    }
}
