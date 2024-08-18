<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(config('brand.tables.brand'), function (Blueprint $table) {
            $table->id();

            $table->boolean('status')->default(true)->index();
            $table->integer('ordering')->default(0)->index();

            $table->unsignedBigInteger('visits')->default(0);
            $table->unsignedBigInteger('likes')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('brand.tables.brand'));
    }
};
