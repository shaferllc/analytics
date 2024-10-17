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
        Schema::create('analytics_stats', function (Blueprint $table) {
            $table->foreignIdFor(Website::class)->constrained()->onDelete('cascade');
            $table->string('name', 32)->index();
            $table->string('value', 255);
            $table->unsignedBigInteger('count')->default(1);
            $table->date('date')->index();
            $table->primary(['website_id', 'name', 'value', 'date']);
            $table->index(['website_id', 'name', 'date']);
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
