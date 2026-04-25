<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreatePollOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('poll_options', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->foreignId('poll_id')
                ->constrained('polls')
                ->cascadeOnDelete();
            $table->string('option_text', 500);
            $table->unsignedSmallInteger('display_order')->default(0); // ordering shown to voters; lower = earlier
            $table->unsignedBigInteger('votes_count')->default(0);     // denormalised tally to avoid COUNT() per render
            $table->timestamps();

            $table->index(['poll_id', 'display_order'], 'poll_options_order_idx');

            $table->comment('Selectable answer options for a poll, with denormalised vote counters.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('poll_options');
    }
}
