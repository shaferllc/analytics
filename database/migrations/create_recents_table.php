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
        Schema::create('analytics_recents', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignIdFor(Website::class)->constrained()->onDelete('cascade');
            $table->string('page')->nullable();
            $table->string('referrer')->nullable()->index();
            $table->string('os', 64)->nullable();
            $table->string('session_id', 128)->nullable();
            $table->string('browser', 64)->nullable();
            $table->string('device', 64)->nullable();
            $table->char('country', 2)->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('city', 128)->nullable();
            $table->string('timezone', 128)->nullable();
            $table->char('language', 26)->nullable();
            $table->string('user_agent', 1024)->nullable();
            $table->string('browser_version', 64)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recents');
    }
};
