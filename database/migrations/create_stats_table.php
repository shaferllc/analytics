<?php

use Illuminate\Support\Facades\Schema;
use Shaferllc\Analytics\Models\Website;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analytics_stats', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignIdFor(Website::class)->constrained()->onDelete('cascade');
            $table->string('name', 32)->index();
            $table->string('page', 1024)->nullable();
            $table->string('value', 255);
            $table->string('session_id', 255)->nullable();
            $table->unsignedBigInteger('count')->default(1);
            $table->date('date')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stats');
    }
};
