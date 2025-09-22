<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanItem extends Model
{
    use HasFactory;

    protected $fillable = ['peminjaman_id', 'peralatan_id', 'jumlah'];
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali' => 'date',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(PeminjamanAlat::class, 'peminjaman_id');
    }

    public function peralatan()
    {
        return $this->belongsTo(Peralatan::class);
    }
}
