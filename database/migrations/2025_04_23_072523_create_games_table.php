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
		Schema::create('games', function (Blueprint $table) {
			$table->id();
			$table->uuid('slug')->unique();
			$table->string('title');
			$table->text('description');
			$table->string('thumbnail');
			$table->json('media')->nullable();
			$table->foreignId('genre_id')->nullable()->constrained();
			$table->dateTime('released_at');
			$table->foreignId('user_id')->nullable()->constrained();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('games');
	}
};
