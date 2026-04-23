<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = ['name', 'type', 'value', 'start_date', 'end_date', 'is_active'];

    public function promotionMenus()
    {
        return $this->hasMany(PromotionMenu::class);
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'promotion_menus');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now());
    }
}
