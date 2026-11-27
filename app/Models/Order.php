<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'code',
        'full_name',
        'phone_number',
        'email',
        'shipping_address',
        'coupon_code',
        'notes',
    ];

    protected static function booted(): void
    {
        // order code shown to the customer, e.g. OR-4F7K2Q9XA
        static::creating(function (Order $order) {
            $order->code ??= 'OR-'.Str::upper(Str::random(9));
        });
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function getCheckoutConfirmtionPath(): string
    {
        return URL::signedRoute('checkout.show', ['order' => $this->id]);
    }
}
