<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'quantity', 'unit_price', 'total_price'];

    // رابطه با سفارش
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // رابطه با محصول
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    protected static function booted()
    {
        static::creating(function ($orderItem) {
            // مقدار unit_price و quantity ممکن است از فرم آمده باشد
            $product = \App\Models\Product::find($orderItem->product_id);

            // اگر محصول موجود است، cost_price را از محصول بردار
            $orderItem->cost_price = $product->cost_price ?? 0;

            // اگر unit_price مقدار ندارد، از قیمت محصول استفاده کن
            if (empty($orderItem->unit_price) && $product) {
                $orderItem->unit_price = $product->price;
            }

            // محاسبه total_price اگر مقدار نداشت
            $orderItem->total_price = $orderItem->unit_price * ($orderItem->quantity ?? 1);

            // محاسبه سود این آیتم
            $orderItem->profit = ($orderItem->unit_price - $orderItem->cost_price) * ($orderItem->quantity ?? 1);
        });

        // در صورت ویرایش هم می‌توانیم سود را به‌روز کنیم
        static::saving(function ($orderItem) {
            $orderItem->total_price = $orderItem->unit_price * ($orderItem->quantity ?? 1);
            $orderItem->profit = ($orderItem->unit_price - ($orderItem->cost_price ?? 0)) * ($orderItem->quantity ?? 1);
        });

        // اگر آیتم حذف شد و می‌خواهی: (مثلاً بازگرداندن موجودی) آن‌را مدیریت کن
    }
}
