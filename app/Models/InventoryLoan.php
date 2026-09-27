<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLoan extends Model
{
    use HasFactory;

    protected $table = 'inventory_loans';

    protected $fillable = [
        'kode_peminjaman',
        'user_id',
        'nama_peminjam',
        'nomor_hp',
        'barang_id',
        'jumlah',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali_actual',
        'keperluan',
        'kondisi_sebelum',
        'kondisi_sesudah',
        'status',
        'catatan',
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
