<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // protected $fillable = ['category_id', 'name', 'description', 'price', 'stock_quantity', 'sku', 'image_url'];
    protected $fillable = ['category_id', 'name', 'description', 'price', 'cost_price', 'stock_quantity', 'sku', 'image_url'];

    // رابطه با دسته‌بندی
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // رابطه با آیتم‌های سفارش
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
