<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'menu_id', 'variant_id', 'quantity', 'price', 'subtotal', 'notes'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class)->withTrashed();
    }

    public function variant()
    {
        return $this->belongsTo(MenuVariant::class, 'variant_id');
    }
}
