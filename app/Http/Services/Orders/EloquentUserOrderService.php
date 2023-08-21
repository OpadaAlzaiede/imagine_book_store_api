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

        return $order;
    }

    public function store($data)
    {
        DB::beginTransaction();

        $order = new Order();

        foreach ($data['books'] as $book) {

            $book = Book::find($book['id']);

            $order->books()->attach($book['id'], [
                'quantity' => $book['quantity'],
                'unit_price' => $book->price
            ]);
        }

        DB::commit();

        return $order;
    }
}