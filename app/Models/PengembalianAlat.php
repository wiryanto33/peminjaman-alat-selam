<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengembalianAlat extends Model
{
    use HasFactory;

    protected $fillable = [
        'peminjaman_id',
        'peminjaman_item_id',
        'user_id',
        'tanggal_kembali',
        'jumlah_dikembalikan',
        'status_kondisi',
        'approval',        // pending|approved|rejected
        'keterangan',
    ];

    protected $casts = [
        'tanggal_kembali' => 'date',
    ];

    /** Pengembali (User) */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Header peminjaman */
    public function peminjaman()
    {
        return $this->belongsTo(PeminjamanAlat::class, 'peminjaman_id');
    }

    /** Item peminjaman tertentu yang dikembalikan */
    public function peminjamanItem()
    {
        return $this->belongsTo(PeminjamanItem::class, 'peminjaman_item_id');
    }
}
