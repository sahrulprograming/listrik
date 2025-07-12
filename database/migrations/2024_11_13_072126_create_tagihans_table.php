<?php

use App\Traits\BaseModel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use BaseModel;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tagihans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_penggunaans');
            $table->foreign('id_penggunaans')->references('id')->on('penggunaans')->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->unsignedBigInteger('id_pelanggans');
            $table->foreign('id_pelanggans')->references('id')->on('pelanggans')->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->string('bulan');
            $table->year('tahun');
            $table->integer('jumlah_meter');
            $table->enum('status', ['belum_bayar', 'lunas']);
            $this->base($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihans');
    }
};
