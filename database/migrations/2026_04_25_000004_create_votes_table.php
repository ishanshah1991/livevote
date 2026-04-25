<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class CreateVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->foreignId('poll_id')
                ->constrained('polls')
                ->cascadeOnDelete();
            $table->foreignId('poll_option_id')
                ->constrained('poll_options')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete(); // keep historical votes if the user account is removed
            $table->string('ip_address', 45);                  // 45 chars covers IPv6 incl. mapped IPv4
            $table->string('user_agent', 500)->nullable();     // captured for fraud / analytics review
            $table->timestamp('voted_at')->useCurrent();       // single timestamp; no created/updated needed

            // Non-unique secondary indexes to keep the unique partials lean and lookups fast.
            $table->index('poll_id');
            $table->index('poll_option_id');

            $table->unique(['poll_id', 'ip_address'], 'votes_ip_poll_unique');

            $table->comment('Individual vote records for both authenticated users and guests.');
        });

        // Partial unique index: prevents the same authenticated user voting twice in a poll,
        // while still allowing many NULL user_id rows for guest votes (deduplicated by IP).
        // Uses CREATE UNIQUE INDEX ... WHERE syntax (PostgreSQL / SQLite). For MySQL fall back
        // to application-level enforcement, since MySQL does not support partial indexes.
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['pgsql', 'sqlite'], true)) {
            DB::statement(
                'CREATE UNIQUE INDEX votes_user_poll_unique ON votes (poll_id, user_id) WHERE user_id IS NOT NULL'
            );
        } else {
            // MySQL/MariaDB: a plain composite unique index suffices because (poll_id, NULL)
            // pairs are treated as distinct, so guests are unaffected.
            Schema::table('votes', function (Blueprint $table): void {
                $table->unique(['poll_id', 'user_id'], 'votes_user_poll_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
}
