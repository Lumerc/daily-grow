<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            
            // Имя автора
            $table->string('author_name');
            
            // Рейтинг от 1 до 5
            $table->tinyInteger('rating')->unsigned();
            
            // Review ID (уникальный, как в примере)
            $table->string('review_id')->unique();
            
            // Текст отзыва
            $table->text('text');
            
            // Время написания отзыва (оригинальное, из источника)
            $table->timestamp('review_created_at');
            
            $table->timestamps();
            
            // Индексы для быстрого поиска
            $table->index('rating');
            $table->index('review_created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};