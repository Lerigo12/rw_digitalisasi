<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventories';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori_id',
        'merek',
        'tipe',
        'jumlah',
        'satuan',
        'kondisi',
        'tahun_perolehan',
        'sumber_dana',
        'harga_perolehan',
        'lokasi',
        'penanggung_jawab',
        'foto',
        'deskripsi',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(InventoryCategory::class, 'kategori_id');
    }

    public function loans()
    {
        return $this->hasMany(InventoryLoan::class, 'barang_id');
    }

    public function histories()
    {
        return $this->hasMany(InventoryHistory::class, 'barang_id');
    }

    public function getDipinjamCountAttribute()
    {
        return $this->loans()->whereIn('status', ['Disetujui', 'Dipinjam'])->sum('jumlah');
    }

    public function getTersediaCountAttribute()
    {
        $tersedia = $this->jumlah - $this->dipinjam_count;

        return max(0, $tersedia);
    }
}
