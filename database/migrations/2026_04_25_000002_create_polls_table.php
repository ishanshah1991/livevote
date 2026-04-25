<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreatePollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('polls', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->foreignId('admin_id')
                ->constrained('admins')
                ->cascadeOnDelete(); // remove polls automatically if owning admin is deleted
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true); // soft on/off switch independent of date window
            $table->timestamp('starts_at')->nullable(); // poll opens at this moment (null = open immediately)
            $table->timestamp('ends_at')->nullable();   // poll closes at this moment (null = open indefinitely)
            $table->timestamps();
            $table->softDeletes();

            $table->index(['admin_id', 'is_active'], 'polls_admin_active_idx');

            $table->comment('Polls created by admins; rows govern voting windows and visibility.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('polls');
    }
}
