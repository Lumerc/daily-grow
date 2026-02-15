<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {

            $table->decimal('rating', 3, 1)->nullable()->after('progress');
            $table->comment('Рейтинг компании (среднее значение)');
            
            $table->integer('reviews_count')->default(0)->unsigned()->after('rating');
            $table->comment('Общее количество отзывов');
        });

        // Устанавливаем значения по умолчанию для существующей записи
        DB::table('settings')->update([
            'rating' => 0.0,
            'reviews_count' => 0,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['rating', 'reviews_count']);
        });
    }
};