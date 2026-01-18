<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'permition',
        'icon',
        'sorting',
        'is_primary_menu',
        'show_on_navbar',
        'is_section',
        'parent_id',
        'backend_route',
        'frontend_route',
    ];

    public function roleDetails()
    {
        return $this->hasMany(UserRoleDetails::class, 'menu_id');
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }   
}
