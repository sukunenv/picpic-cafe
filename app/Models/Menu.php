<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $fillable = ['category_id', 'name', 'slug', 'description', 'ingredients', 'price', 'image', 'rating', 'is_available', 'is_featured'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function variants()
    {
        return $this->hasMany(MenuVariant::class);
    }

    public function activePromotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_menus')
            ->where('is_active', 1)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now());
    }
}
