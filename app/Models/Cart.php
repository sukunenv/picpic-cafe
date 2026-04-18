<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id', 'menu_id', 'variant_id', 'quantity', 'notes'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function variant()
    {
        return $this->belongsTo(MenuVariant::class, 'variant_id');
    }
}
