<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
protected $table = 'order';

    protected $fillable = ['user_id', 'pirkimo_data', 'bendra_suma', 'statusas'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function OrderItem()
    {
        return $this->hasMany(OrderItem::class);
    }
}
