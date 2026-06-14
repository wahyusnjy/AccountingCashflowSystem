<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED (Primary Key)
            $table->date('date');
            $table->text('description');
            $table->string('reference_type'); // e.g. 'spp_reguler', 'spp_intensif', 'pengeluaran'
            $table->string('student_name')->nullable();
            $table->string('period_month')->nullable(); // e.g. 'September', 'Oktober'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
