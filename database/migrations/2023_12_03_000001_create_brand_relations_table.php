<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(config('brand.tables.brand_relation'), function (Blueprint $table) {
            $table->foreignId('brand_id')->index()->constrained(config('brand.tables.brand'))->cascadeOnUpdate()->cascadeOnDelete();

            $table->morphs('brandable');
            /**
             * brandable to: any model
             */

            $table->dateTime('created_at')->index()->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->unique([
                'brand_id',
                'brandable_type',
                'brandable_id'
            ], 'BRAND_RELATION_UNIQUE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('brand.tables.brand_relation'));
    }
};
