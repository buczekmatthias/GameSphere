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
		Schema::create('discussions', function (Blueprint $table) {
			$table->id();
			$table->uuid('slug')->unique();
			$table->string('title');
			$table->foreignId('user_id')->nullable()->constrained();
			$table->integer('discussable_id');
			$table->string('discussable_type');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('discussions');
	}
};
