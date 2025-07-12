<?php

use App\Traits\BaseModel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use BaseModel;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tagihans');
            $table->foreign('id_tagihans')->references('id')->on('tagihans')->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->unsignedBigInteger('id_pelanggans');
            $table->foreign('id_pelanggans')->references('id')->on('pelanggans')->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->unsignedBigInteger('id_users');
            $table->foreign('id_users')->references('id')->on('users')->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->date('tanggal_pembayaran');
            $table->string('bulan_bayar');
            $table->double('biaya_tagihan')->default('5000');
            $table->double('biaya_admin');
            $table->double('total_bayar');
            $this->base($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
