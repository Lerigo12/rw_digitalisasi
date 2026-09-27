<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    use HasFactory;

    protected $table = 'inventory_histories';

    protected $fillable = [
        'barang_id',
        'user_id',
        'aktivitas',
        'keterangan',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'barang_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
