<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('reviews', function (Blueprint $table) {
			$table->id();
			$table->uuid('slug')->unique();
			$table->text('content');
			$table->json('ratings');
			$table->boolean('is_verified')->default(false);
			$table->foreignId('user_id')->nullable()->constrained();
			$table->foreignId('game_id')->constrained()->cascadeOnDelete();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('reviews');
	}
};
