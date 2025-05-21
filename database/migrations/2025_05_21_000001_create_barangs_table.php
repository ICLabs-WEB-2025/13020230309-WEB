<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('barcode')->unique();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->foreignId('kategori_id')->constrained('categories')->onDelete('cascade');
            $table->enum('sn_type', ['SN', 'non-SN']);
            $table->integer('stock')->default(0);
            $table->foreignId('satuan_id')->constrained('units')->onDelete('cascade');
            $table->decimal('harga_umum', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
}; 