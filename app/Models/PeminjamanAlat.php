<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeminjamanAlat extends Model
{
    // Kolom inti: relasi dan meta pinjam; hapus field usang (peralatan_id, jumlah_pinjam)
    protected $fillable = [
        'user_id',
        'tanggal_pinjam',
        'approval',    // pending|approved|rejected
        'keterangan',
    ];

    protected $guarded = ['id'];

    // Relasi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PeminjamanItem::class, 'peminjaman_id');
    }

    public function pengembalians()
    {
        return $this->hasMany(PengembalianAlat::class, 'peminjaman_id');
    }

    // Accessors ringkas
    public function getTotalQuantityAttribute()
    {
        return $this->items->sum('jumlah');
    }

    public function getEquipmentNamesAttribute()
    {
        return $this->items->pluck('peralatan.name')->filter()->join(', ');
    }
}
