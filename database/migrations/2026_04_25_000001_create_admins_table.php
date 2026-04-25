<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('email', 150)->unique();
            $table->string('password', 255);
            $table->rememberToken()->nullable(); // Laravel session "remember me" token
            $table->timestamps();
            $table->softDeletes();

            $table->comment('Administrative users authorised to create and manage polls.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
}
