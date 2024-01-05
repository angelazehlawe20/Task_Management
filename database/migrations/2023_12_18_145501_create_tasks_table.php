<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->enum('priority', ['high', 'medium', 'low']);
            $table->string('color')->default('#00000');
            $table->string('title');
            $table->string('description');
            $table->dateTime('due_date')->useCurrent();
            $table->enum('Repetition',['once','daily','weekly','monthly','annually']);
            $table->enum('repeat_days', ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'])->nullable();
            $table->enum('status', ['COMPLETED', 'IN_PROGRESS', 'PENDING'])->default('PENDING');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
