<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->decimal('balance', 10, 2)->default(0.00);
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->timestamps();
        });

        Schema::create('draws', function (Blueprint $table) {
            $table->id();
            $table->json('winning_numbers')->nullable();
            $table->enum('status', ['open', 'closed', 'drawn'])->default('open');
            $table->timestamp('drawn_at')->nullable();
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('draw_id')->constrained()->onDelete('cascade');
            $table->json('numbers');
            $table->enum('status', ['active', 'won', 'lost'])->default('active');
            $table->decimal('price', 8, 2)->default(10.00);
            $table->timestamps();
        });

        Schema::create('winners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('draw_id')->constrained()->onDelete('cascade');
            $table->decimal('prize_amount', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('winners');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('draws');
        Schema::dropIfExists('users');
    }
};
