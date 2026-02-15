<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('org_link')->nullable();
            $table->tinyInteger('progress')->default(0)->unsigned();
            $table->timestamps();
        });

        DB::table('settings')->insert([
            'org_link' => null,
            'progress' => 0,
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};