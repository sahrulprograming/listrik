<?php

namespace App\Models;

use App\Traits\AuditedBy;
use App\Models\Penggunaan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tagihan extends Model
{
    use HasFactory, AuditedBy, SoftDeletes;
    protected $table = 'tagihans';
    protected $guarded = ['id'];

    public function penggunaan()
    {
        return $this->belongsTo(Penggunaan::class, 'id_penggunaans');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggans');
    }

    public function hitungTarif()
    {
        if ($this->pelanggan && $this->pelanggan->id_tarifs) {
            return $this->pelanggan->id_tarifs * $this->jumlah_meter;
        }
        return 0;
    }

    public function hitungJumlahMeter()
    {
    $penggunaan = $this->penggunaan;

    return $penggunaan ? ($penggunaan->meter_akhir - $penggunaan->meter_awal) : 0;
    }


    public static function hitungTotalBayar($biaya_tagihan, $biaya_admin)
    {
        return $biaya_tagihan + $biaya_admin;
    }
    
    const STATUS_OPTIONS = [
        'belum_bayar' => 'Belum Bayar',
        'lunas' => 'Lunas',
    ];
}
