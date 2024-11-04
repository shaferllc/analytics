<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('class')->nullable();
            $table->integer('ttl');
            $table->string('target')->nullable();
            $table->integer('priority')->nullable();
            $table->string('host');
            $table->boolean('is_valid');
            $table->timestamp('last_change')->nullable();
            $table->timestamp('expiry');
            
            // SOA specific fields
            $table->string('rname')->nullable();
            $table->bigInteger('serial')->nullable();
            $table->integer('refresh')->nullable();
            $table->integer('retry')->nullable();
            $table->integer('expire')->nullable();
            $table->integer('minimum_ttl')->nullable();
            
            // SRV specific fields
            $table->integer('weight')->nullable();
            $table->integer('port')->nullable();
            $table->string('service')->nullable();
            
            // CAA specific fields
            $table->integer('flags')->nullable();
            $table->string('tag')->nullable();
            $table->text('value')->nullable();
            
            // DNSKEY specific fields
            $table->integer('protocol')->nullable();
            $table->integer('algorithm')->nullable();
            $table->text('public_key')->nullable();
            $table->integer('key_tag')->nullable();
            
            // NAPTR specific fields
            $table->integer('order')->nullable();
            $table->integer('preference')->nullable();
            $table->string('regexp')->nullable();
            $table->string('replacement')->nullable();
            
            // DS specific fields
            $table->integer('key_tag_ds')->nullable();
            $table->integer('algorithm_ds')->nullable();
            $table->integer('digest_type')->nullable();
            $table->string('digest')->nullable();
            
            // Metadata
            $table->json('additional_data')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['website_id', 'type']);
            $table->index('host');
            $table->index('expiry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dns');
    }
};
