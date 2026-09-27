<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_categories', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->foreignId('kategori_id')->constrained('inventory_categories')->onDelete('cascade');
            $table->string('merek')->nullable();
            $table->string('tipe')->nullable();
            $table->integer('jumlah')->default(0);
            $table->string('satuan')->default('Unit');
            $table->enum('kondisi', ['Baik', 'Cukup Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang'])->default('Baik');
            $table->year('tahun_perolehan')->nullable();
            $table->string('sumber_dana')->nullable();
            $table->decimal('harga_perolehan', 15, 2)->default(0);
            $table->string('lokasi')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->string('foto')->nullable();
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['Tersedia', 'Dipinjam', 'Rusak', 'Hilang', 'Tidak Aktif'])->default('Tersedia');
            $table->timestamps();
        });

        Schema::create('inventory_loans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_peminjaman')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_peminjam');
            $table->string('nomor_hp');
            $table->foreignId('barang_id')->constrained('inventories')->onDelete('cascade');
            $table->integer('jumlah');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali_rencana');
            $table->date('tanggal_kembali_actual')->nullable();
            $table->text('keperluan')->nullable();
            $table->enum('kondisi_sebelum', ['Baik', 'Cukup Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang'])->default('Baik');
            $table->enum('kondisi_sesudah', ['Baik', 'Cukup Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang'])->nullable();
            $table->enum('status', ['Menunggu Persetujuan', 'Disetujui', 'Dipinjam', 'Dikembalikan', 'Terlambat', 'Ditolak'])->default('Menunggu Persetujuan');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('inventories')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('aktivitas');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_histories');
        Schema::dropIfExists('inventory_loans');
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('inventory_categories');
    }
};
