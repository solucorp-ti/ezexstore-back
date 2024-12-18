<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('product_serial');
            $table->string('product_name');
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->enum('status', ['active', 'inactive', 'discontinued']);
            $table->enum('unit_of_measure', ['piece', 'kg', 'liter', 'meter']);
            $table->string('sku')->unique()->nullable();
            $table->string('part_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->enum('part_condition', ['new', 'used', 'discontinued', 'damaged', 'refurbished'])->nullable();
            $table->string('brand')->nullable();
            $table->string('family')->nullable();
            $table->string('line')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('tenant_id');
            $table->index('product_serial');
            $table->index('status');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
