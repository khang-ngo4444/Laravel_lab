<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;

class CartService
{
    /**
     * Add new or update exists cart item
     */
    public function addCartItem(array $item): bool
    {
        $cart = $this->getCart();

        $query_condition = [
            'cart_id' => $cart->id, 'product_id' => $item['product_id'],
        ];

        CartItem::updateOrCreate($query_condition, $item);

        return true;
    }

    /**
     * Remove exists cart item
     */
    public function removeCartItem($product_id): bool
    {
        $cart_item = $this->getCartItem($product_id);

        if ($cart_item) {
            $cart_item->delete();

            return true;
        }

        return false;
    }

    public function getCartItem($product_id): ?CartItem
    {
        return $this->getCart()->items()->where('product_id', $product_id)->first();
    }

    /**
     * Get the open cart (no order yet) of the current session user, creating it if needed
     */
    public function getCart(): Cart
    {
        $user_id = UserService::getUserIdFromSession();

        return Cart::query()->with('items.product')
            ->where('user_id', $user_id)
            ->doesntHave('order')
            ->firstOrCreate(['user_id' => $user_id]);
    }
}
