<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'order_date', 'status', 'total_amount', 'payment_status'];

    // رابطه با مشتری
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // رابطه با آیتم‌های سفارش
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
