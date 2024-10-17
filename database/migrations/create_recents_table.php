<?php

use Illuminate\Support\Facades\Schema;
use ShaferLLC\Analytics\Models\Website;
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
            $table->string('browser', 64)->nullable();
            $table->string('device', 64)->nullable();
            $table->char('country', 2)->nullable();
            $table->string('city', 128)->nullable();
            $table->char('language', 2)->nullable();
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
