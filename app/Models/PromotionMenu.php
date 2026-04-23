<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionMenu extends Model
{
    protected $table = 'promotion_menus';
    protected $fillable = ['promotion_id', 'menu_id'];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
