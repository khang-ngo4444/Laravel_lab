<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Models\Order;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class CheckoutController extends Controller
{
    public function index(CartService $cart)
    {
        return view('checkout.index', [
            'cart' => $cart->getCart(),
        ]);
    }

    public function store(Request $request, CartService $cart): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'shipping_address' => 'required|string|max:255',
            'coupon_code' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
        ]);

        $current_cart = $cart->getCart();

        if ($current_cart->items->isEmpty()) {
            return redirect()->route('homepage');
        }

        $order = $current_cart->order()->create($validated);

        OrderCreated::dispatch($order);

        return redirect(URL::signedRoute('orders.complete', ['order' => $order->id]));
    }

    public function show(Request $request, Order $order)
    {
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        return view('checkout.show', compact('order'));
    }
}
