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

    protected $fillable = ['user_id', 'pirkimo_data', 'bendra_suma', 'statusas',  
    'payment_provider',
    'payment_reference',
    'payment_intent_id',
    'shipping_address',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'pirkimo_data' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

   public function orderItem()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
