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
        Schema::create('gifts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('givecampus_id')->unique();
            $table->uuid('request_id')->nullable()->index();
            $table->string('sf_opportunity_id')->nullable();
            $table->string('sf_account_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('payer_name')->nullable();
            $table->string('payer_email')->nullable();
            $table->string('paid_name')->nullable();
            $table->string('paid_email')->nullable();
            $table->string('phone_number')->nullable();
            $table->boolean('anonymous')->default(false);
            $table->decimal('value', 14, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->decimal('value_usd', 14, 2)->nullable();
            $table->string('donation_type')->nullable();
            $table->string('state')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('card_type')->nullable();
            $table->string('checkout_id')->nullable()->index();
            $table->string('refund_status')->nullable();
            $table->unsignedBigInteger('project_id')->nullable()->index();
            $table->string('project_type')->nullable();
            $table->string('project_name')->nullable();
            $table->timestamp('gift_created_at')->nullable()->index();
            $table->timestamp('gift_updated_at')->nullable()->index();
            $table->timestamp('captured_at')->nullable()->index();
            $table->timestamp('checkout_at')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->timestamp('date_exported_to_salesforce')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gifts');
    }
};
