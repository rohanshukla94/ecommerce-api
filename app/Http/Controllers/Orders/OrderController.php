<?php

namespace App\Http\Controllers\Orders;

use App\Cart\Cart;
use App\Events\Order\OrderCreated;
use App\Http\Requests\Orders\OrderStoreRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //
    protected $cart;

    public function __construct()
    {
        $this->middleware(['auth:api']);
        $this->middleware(['cart.sync', 'cart.notempty'])->only('store');
    }

    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->with(['products', 'products.stock', 'products.type', 'products.product', 'products.product.variations', 'products.product.variations.stock', 'address'])
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders);
    }



    public function store(OrderStoreRequest $request, Cart $cart)
    {
        # code...

        if ($cart->isEmpty()) {
            return response(null, 400);
        }

        $order = $this->createOrder($request, $cart);

        $order->products()->sync($cart->products()->forSyncing());

        //fire an event to process the  payment  and then empty   the cart
        event(new OrderCreated($order));

        return new OrderResource($order);
    }

    protected function createOrder(Request $request, Cart $cart)
    {
        return $request->user()->orders()->create(
            array_merge($request->only(['address_id', 'shipping_method_id', 'payment_method_id']), [
                'subtotal' => $cart->subtotal()->amount()
            ])
        );
    }
}
