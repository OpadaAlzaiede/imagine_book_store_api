<?php


namespace App\Http\Services\Orders;


use App\Http\Services\UserResolver\UserResolver;
use App\Models\Book;
use App\Models\Order;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EloquentUserOrderService implements OrderQueryService, OrderStoreService
{
    protected $userResolver;

    public function __construct(UserResolver $userResolver)
    {
        $this->userResolver = $userResolver;
    }

    public function index($perPage, $page) {

        return QueryBuilder::for($this->userResolver->get()->orders())
            ->allowedIncludes(Order::getUserAllowedIncludes())
            ->allowedFilters(Order::getUserAllowedFilters())
            ->defaultSort('-id')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function show($id) {

        $order = $this->userResolver->get()->orders()->find($id);

        if(!$order) {

            throw new NotFoundHttpException(Config::get('messages.api.orders.not_found'));
        }

        return $order->load(Order::getUserAllowedIncludes());
    }

    public function store()
    {
        if(!$this->validateCart()) throw new BadRequestException(Config::get('messages.api.orders.invalid_cart'));

        $cart = $this->userResolver->get()->cart;

        try {

            DB::beginTransaction();

            $order = Order::create();

            $books = $this->getBooksFromCart($cart);

            $totalPrice = 0;
            $updatedBooksArray = []; // This array will store books after updating it's quantity.
            $data = []; // This array will store a list of (order, book, quantity) to insert it in one shot.

            foreach ($cart as $cartBook) {

                $dbBook = $books->filter(function($book) use ($cartBook){
                    return $book->id == $cartBook['id'];
                })->first();

                $requiredQuantity = $cartBook->pivot->quantity;
                $bookPrice = $dbBook->price;

                $totalPrice += $requiredQuantity * $bookPrice;

                array_push($data, ['order_id' => $order->id, 'book_id' => $cartBook['id'], 'quantity' => $requiredQuantity, 'unit_price' => $bookPrice]);

                array_push($updatedBooksArray, ['id' => $dbBook->id, 'title' => $dbBook->title, 'author' => $dbBook->author, 'price' => $dbBook->price, 'quantity' => ($dbBook->quantity - $requiredQuantity), 'book_genre_id' => $dbBook->book_genre_id]);
            }

            DB::table('book_order')->insert($data); // insert all (order, book, quantity) records in one sql stmt.
            $order->update(['total_price' => $totalPrice]);

            $this->updateBooksQuantity($updatedBooksArray);

            $this->clearUserCart();

            DB::commit();

            return $order->load(Order::getUserAllowedIncludes());

        } catch (\Exception $e) {

            DB::rollBack();

            return false;
        }
    }

    protected function validateCart() {

        $cart = $this->userResolver->get()->cart;

        if(!$cart || $cart->count() <= 0) return false;

        foreach ($cart as $book) {

            $availableQuantity = $book->quantity;
            $requestedQuantity = $book->pivot->quantity;

            if($availableQuantity < $requestedQuantity) return false;
        }

        return true;
    }

    protected function clearUserCart() {

        $this->userResolver->get()->cart()->detach();
    }

    protected function updateBooksQuantity($books) {

        Book::upsert($books, 'id');
    }

    protected function getBooksFromCart($cart) {

        return Book::whereIn('id', $cart->pluck('id')->toArray())->get();
    }
}
