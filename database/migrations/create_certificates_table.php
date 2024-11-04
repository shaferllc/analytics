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
        Schema::create('analytics_certificates', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignIdFor(Website::class)->constrained()->onDelete('cascade');
            $table->string('issuer', 255)->nullable();
            $table->string('domain', 1024);
            $table->string('additional_domains', 1024)->nullable();
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_to')->nullable();
            $table->boolean('was_valid')->nullable();
            $table->boolean('did_expire')->nullable();
            $table->string('grade', 1)->nullable();
            $table->string('ssl_version', 1024)->nullable();
            $table->string('cipher_strength', 1024)->nullable();
            $table->string('fingerprint', 1024)->nullable();
            $table->string('signature_algorithm', 1024)->nullable();
            $table->string('serial_number', 1024)->nullable();
            $table->string('tls_version', 1024)->nullable();
            $table->dateTime('last_checked_at')->nullable();
            $table->string('status', 1024)->nullable();
            $table->json('chain')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('certificates');
    }
};
