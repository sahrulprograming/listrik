<?php

namespace App\Models;

use App\Traits\AuditedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pelanggan extends Model
{
    use HasFactory, AuditedBy, SoftDeletes;
    protected $table = 'pelanggans';
    protected $guarded = ['id'];

    public function tarif()
    {
        return $this->belongsTo(Tarif::class, 'id_tarifs');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_users');
    }
}
