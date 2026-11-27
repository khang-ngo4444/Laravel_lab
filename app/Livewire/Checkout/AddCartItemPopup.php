<?php

namespace App\Livewire\Checkout;

use App\Events\BrowserEvent;
use App\Events\LivewireEvent;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class AddCartItemPopup extends Component
{
    public ?Product $product = null;

    public $quantity = CartItem::MIN_QUANTITY;

    public $notes = '';

    #[On(LivewireEvent::USER_SELECT_PRODUCT)]
    public function displayAddCartItem(CartService $cart, $product_id): void
    {
        $this->product = Product::findOrFail($product_id);

        $this->resetFormData();

        $cart_item = $cart->getCartItem($product_id);

        if ($cart_item) {
            $this->quantity = $cart_item->quantity;
            $this->notes = $cart_item->notes;
        }

        $this->dispatch(BrowserEvent::DISPLAY_OFFCANVAS);

        Log::channel('user_actions')->info('User clicked on product', ['product_id' => $product_id]);
    }

    public function increment(): void
    {
        $this->quantity++;
    }

    public function decrement(): void
    {
        if ($this->quantity > CartItem::MIN_QUANTITY) {
            $this->quantity--;
        }
    }

    public function addCartItem(CartService $cart): void
    {
        $this->validate([
            'quantity' => 'required|integer|min:'.CartItem::MIN_QUANTITY,
            'notes' => 'nullable|string|max:255',
        ]);

        $cart_item = $this->getCartItemInstance();

        $cart->addCartItem($cart_item);

        $this->dispatch(LivewireEvent::CART_UPDATED_EVENT, $cart_item);

        $this->dispatch(BrowserEvent::CLOSE_OFFCANVAS);

        Log::channel('user_actions')->info('User add product to cart', ['cart_item' => $cart_item]);
    }

    private function resetFormData(): void
    {
        $this->quantity = CartItem::MIN_QUANTITY;
        $this->notes = '';
    }

    private function getCartItemInstance(): array
    {
        return [
            'product_id' => $this->product->id,
            'quantity' => $this->quantity,
            'notes' => $this->notes,
        ];
    }

    public function render(): View
    {
        return view('livewire.checkout.add-cart-item-popup');
    }
}
