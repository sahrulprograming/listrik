<?php

namespace App\Models;

use App\Traits\AuditedBy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembayaran extends Model
{
    use HasFactory, AuditedBy, SoftDeletes;
    protected $table = 'pembayarans';
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pembayaran) {
            $pembayaran->id_users = Auth::id(); // Set user_id to the currently logged-in user's ID
        });
    }

    public function tagihan()
    {
    return $this->belongsTo(Tagihan::class, 'id_tagihans');
    }

    public static function hitungTotalBayar($biaya_tagihan, $biaya_admin)
    {
        return $biaya_tagihan + $biaya_admin;
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggans');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_users');
    }
}
