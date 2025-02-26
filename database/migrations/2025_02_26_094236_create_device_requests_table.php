<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('device_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone_number');
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->foreignId('device_version_id')->constrained('device_versions')->onDelete('cascade');
            $table->foreignId('primary_color_id')->constrained('colors')->onDelete('cascade');
            $table->foreignId('secondary_color_id')->constrained('colors')->onDelete('cascade');
            $table->foreignId('shipping_address_id')->constrained('shipping_addresses')->onDelete('cascade');
            $table->text('shipping_attention')->nullable();
            $table->text('caller_id_requested')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('device_requests');
    }
};

