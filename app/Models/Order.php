<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'order';

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'address_id',
        'pirkimo_data',
        'bendra_suma',
        'statusas',
        'payment_provider',
        'payment_reference',
        'payment_intent_id',
        'shipping_address',
        'payment_intents',
        'amount_charged_cents',
        'platform_fee_cents',
        'small_order_fee_cents',
        'shipping_total_cents',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'payment_intents' => 'array',
        'pirkimo_data' => 'datetime',
        'amount_charged_cents' => 'integer',
        'platform_fee_cents' => 'integer',
        'small_order_fee_cents' => 'integer',
        'shipping_total_cents' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItem()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function shipments()
    {
        return $this->hasMany(OrderShipment::class, 'order_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
