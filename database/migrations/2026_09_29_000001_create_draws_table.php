<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('draws', function (Blueprint $table) {
            $table->id();
            $table->date('draw_date')->unique();
            // pending: งวดที่ยังไม่ออก, partial: ผลสดยังออกไม่ครบ, official: GLO ครบทุกรางวัล, verified: แอดมินยืนยันแล้ว
            $table->string('status', 16)->default('pending')->index();
            $table->string('source', 32)->default('glo');
            $table->string('pdf_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->json('raw')->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('draw_prizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draw_id')->constrained()->cascadeOnDelete();
            $table->string('tier', 16);
            $table->string('number', 6);
            $table->unsignedInteger('amount');
            $table->unique(['draw_id', 'tier', 'number']);
            $table->index('number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('draw_prizes');
        Schema::dropIfExists('draws');
    }
};
