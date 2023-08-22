<?php


namespace App\Http\Services\Orders;


use App\Models\Book;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EloquentUserOrderService implements OrderQueryService, OrderStoreService
{
    public function index($perPage, $page) {

        $user = Auth::user();

        return QueryBuilder::for($user->orders())
            ->allowedIncludes(Order::getUserAllowedIncludes())
            ->allowedFilters(Order::getUserAllowedFilters())
            ->defaultSort('-id')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function show($id) {

        $user = Auth::user();

        $order = $user->orders()->find($id);

        if(!$order) {

            throw new NotFoundHttpException(Config::get('messages.api.orders.not_found'));
        }

        return $order->load(Order::getUserAllowedIncludes());
    }

    public function store($data)
    {
        $order = Order::create();

        foreach ($data['books'] as $book) {

            $bookModel = Book::find($book['id']);
            $requiredQuantity = $book['quantity'];

            $this->attachBookToOrder($order, $bookModel->id, $requiredQuantity, $bookModel->price);

            $order->total_price += $requiredQuantity * $bookModel->price;

            $bookModel->updateQuantity($requiredQuantity);
        }

        $order->save();

        return $order->load(Order::getUserAllowedIncludes());
    }

    protected function attachBookToOrder($order, $bookId, $quantity, $unitPrice) {

        $order->books()->attach($bookId, [
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
        ]);
    }
}
