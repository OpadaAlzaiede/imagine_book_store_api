<?php

namespace App\Http\Services\Cart;


use App\Http\Services\Auth\AuthService;

class EloquentCartService implements CartService {

    protected $authenticationService;

    public function __construct(AuthService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function get()
    {
        return $this->authenticationService->getAuthUser()->cart;
    }

    public function add($bookId, $quantity)
    {
        $cart = $this->authenticationService->getAuthUser()->cart();

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

        return $this->authenticationService->getAuthUser()->cart()->get();
    }

    public function remove($bookId)
    {
        $this->authenticationService->getAuthUser()->detach($bookId);

        return $this->authenticationService->getAuthUser()->get();
    }
}
