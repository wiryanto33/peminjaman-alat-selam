<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peralatan extends Model
{
    protected $fillable = [
        'name',
        'image',
        'description',
        'jumlah', // kalau dipakai untuk total awal
        'stock',  // stok berjalan
    ];

    protected $guarded = ['id'];

    public function peminjamanItems()
    {
        return $this->hasMany(PeminjamanItem::class);
    }
}
