<?php

namespace App\Models;

use App\Models\Tagihan;
use App\Traits\AuditedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penggunaan extends Model
{
    use HasFactory, AuditedBy, SoftDeletes;
    protected $table = 'penggunaans';
    protected $guarded = ['id'];

    public function hitungJumlahMeter()
    {
        return $this->meter_akhir - $this->meter_awal;
    }

    public function tagihan()
    {
    return $this->hasMany(Tagihan::class);
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggans');
    }
}
