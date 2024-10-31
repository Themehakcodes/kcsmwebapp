<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SidebarItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sidebar_items';

    protected $fillable = [
        'menu_item',
        'url',
        'icon',
        'parent_id',
        'permission_id',
        'order',
    ];

    // Define the relationship for nested items
    public function parent()
    {
        return $this->belongsTo(SidebarItem::class, 'parent_id');
    }

    // Define the relationship with permissions
    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    // Define the relationship for child items
    public function children()
    {
        return $this->hasMany(SidebarItem::class, 'parent_id');
    }
}
