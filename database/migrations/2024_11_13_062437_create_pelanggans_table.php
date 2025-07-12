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
        Schema::create('pelanggans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kwh');
            $table->text('alamat');
            $table->unsignedBigInteger('id_tarifs');
            $table->foreign('id_tarifs')->references('id')->on('tarifs')->onDelete("CASCADE")->onUpdate("CASCADE");
            $this->base($table);;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggans');
    }
};
