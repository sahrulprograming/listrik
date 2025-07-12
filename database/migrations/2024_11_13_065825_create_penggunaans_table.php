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
        Schema::create('penggunaans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pelanggans');
            $table->foreign('id_pelanggans')->references('id')->on('pelanggans')->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->string('bulan');
            $table->year('tahun');
            $table->integer('meter_awal');
            $table->integer('meter_akhir');
            $this->base($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penggunaans');
    }
};
