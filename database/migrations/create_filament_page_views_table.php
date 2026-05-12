<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('filament-analitik.table_name', 'filament_page_views'), function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('url')->index();
            $blueprint->string('path')->index();
            $blueprint->string('method');
            $blueprint->string('ip')->nullable();
            $blueprint->text('user_agent')->nullable();
            $blueprint->string('city')->nullable();
            $blueprint->string('state')->nullable();
            $blueprint->string('country')->nullable();
            $blueprint->string('project_id')->nullable()->index();
            $blueprint->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('filament-analitik.table_name', 'filament_page_views'));
    }
};
