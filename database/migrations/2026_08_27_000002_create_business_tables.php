<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country', 2)->nullable();
            $table->text('notes')->nullable();
            $table->date('registered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('product_name');
            $table->string('product_url')->nullable();
            $table->string('store')->nullable();
            $table->text('description')->nullable();
            $table->string('size_color')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('discount_found', 10, 2)->nullable();
            $table->string('status')->default('new')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_request_id')->nullable()->constrained()->nullOnDelete();
            $table->string('store')->nullable();
            $table->string('original_tracking')->nullable();
            $table->date('received_at')->nullable();
            $table->decimal('weight_lb', 8, 2)->nullable();
            $table->string('photo_path')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->default('received')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('carrier')->nullable();
            $table->string('destination_country', 2)->nullable();
            $table->decimal('final_weight_lb', 8, 2)->nullable();
            $table->string('dimensions')->nullable();
            $table->string('international_tracking')->nullable();
            $table->decimal('shipping_cost', 10, 2)->nullable();
            $table->date('shipped_at')->nullable();
            $table->date('delivered_at')->nullable();
            $table->string('status')->default('draft')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('package_shipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->unique(['package_id', 'shipment_id']);
        });

        Schema::create('cost_items', function (Blueprint $table) {
            $table->id();
            $table->morphs('costable');
            $table->string('type')->index();
            $table->string('description')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('billable');
            $table->decimal('invoice_total', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('statuses_history', function (Blueprint $table) {
            $table->id();
            $table->morphs('statusable');
            $table->string('from')->nullable();
            $table->string('to');
            $table->text('note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('statuses_history');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('cost_items');
        Schema::dropIfExists('package_shipment');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('purchase_requests');
        Schema::dropIfExists('customers');
    }
};
