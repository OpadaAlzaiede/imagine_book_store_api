<?php


namespace App\Http\Services\Orders;


use App\Http\Services\Auth\AuthService;
use App\Models\Book;
use App\Models\Order;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EloquentUserOrderService implements OrderQueryService, OrderStoreService
{
    protected $authenticationService;

    public function __construct(AuthService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function index($perPage, $page) {

        return QueryBuilder::for($this->authenticationService->getAuthUser()->orders())
            ->allowedIncludes(Order::getUserAllowedIncludes())
            ->allowedFilters(Order::getUserAllowedFilters())
            ->defaultSort('-id')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function show($id) {

        $order = $this->authenticationService->getAuthUser()->orders()->find($id);

        if(!$order) {

            throw new NotFoundHttpException(Config::get('messages.api.orders.not_found'));
        }

        return $order->load(Order::getUserAllowedIncludes());
    }

    public function store()
    {
        if(!$this->validateCart()) throw new BadRequestException(Config::get('messages.api.orders.invalid_cart'));

        $cart = $this->authenticationService->getAuthUser()->cart()->get();

        if($cart->count() <= 0)  {

            throw new BadRequestException(Config::get('messages.api.orders.empty'));
        }

        $order = Order::create();

        foreach ($cart as $book) {

            DB::beginTransaction();

            $bookModel = Book::lockForUpdate()->find($book->id);

            $requiredQuantity = $book->pivot->quantity;

            $this->attachBookToOrder($order, $bookModel->id, $requiredQuantity, $bookModel->price);

            $order->total_price += $requiredQuantity * $bookModel->price;

            $bookModel->updateQuantity($requiredQuantity);

            DB::commit();
        }

        $order->save();

        $this->authenticationService->getAuthUser()->cart()->detach();

        return $order->load(Order::getUserAllowedIncludes());
    }

    protected function attachBookToOrder($order, $bookId, $quantity, $unitPrice) {

        $order->books()->attach($bookId, [
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
        ]);
    }

    protected function validateCart() {

        $cart = $this->authenticationService->getAuthUser()->cart()->get();

        foreach ($cart as $book) {

            $availableQuantity = $book->quantity;
            $requestedQuantity = $book->pivot->quantity;

            if($availableQuantity < $requestedQuantity) return false;
        }

        return true;
    }
}
