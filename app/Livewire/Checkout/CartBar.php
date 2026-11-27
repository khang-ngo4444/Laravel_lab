<?php

namespace App\Livewire\Checkout;

use App\Events\LivewireEvent;
use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class CartBar extends Component
{
    public Cart $cart;

    public $display = false;

    public function mount(CartService $cart): void
    {
        $this->loadingCartData($cart);
    }

    #[On(LivewireEvent::CART_UPDATED_EVENT)]
    public function loadingCartData(CartService $cart): void
    {
        $this->cart = $cart->getCart();

        $this->display = $this->cart->items->isNotEmpty();
    }

    public function removeCartItem(CartService $cart, $product_id): void
    {
        $cart->removeCartItem($product_id);

        $this->loadingCartData($cart);

        Log::channel('user_actions')->info('User remove product out of cart', ['product_id' => $product_id]);
    }

    public function checkout()
    {
        return redirect()->route('checkout.index');
    }

    public function render(): View
    {
        return view('livewire.checkout.cart-bar');
    }
}
