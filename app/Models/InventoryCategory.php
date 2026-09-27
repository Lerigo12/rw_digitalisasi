<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryCategory extends Model
{
    use HasFactory;

    protected $table = 'inventory_categories';

    protected $fillable = ['nama', 'deskripsi'];

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'kategori_id');
    }
}
