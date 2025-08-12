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
    // public function cancelOrder()
    // {
    //     if ($this->status !== 'canceled') {
    //         $this->status = 'canceled';

    //         if (!$this->is_refunded && $this->payment_status === 'paid') {
    //             $refundAmount = $this->total_amount;

    //             $customer = $this->customer;
    //             $customer->balance -= $refundAmount; // یا هر منطق بازپرداخت شما
    //             $customer->save();

    //             $this->refunded_amount = $refundAmount;
    //             $this->is_refunded = true;
    //         }

    //         $this->save();
    //     }
    // }
}
