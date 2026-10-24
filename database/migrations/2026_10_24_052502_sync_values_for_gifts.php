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
        Schema::table('gifts', function(Blueprint $table){
            $table->boolean('sf_contact_found')->nullable();
            $table->string('sf_contact_id')->nullable();
            $table->boolean('sf_opportunity_found')->nullable();
            $table->boolean('sf_account_found')->nullable();
            $table->string('sf_contact_search_type')->nullable();
            $table->string('action')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gifts', function(Blueprint $table){
            $table->dropColumn('sf_contact_found');
            $table->dropColumn('sf_contact_id');
            $table->dropColumn('sf_opportunity_found');
            $table->dropColumn('sf_account_found');
            $table->dropColumn('sf_contact_search_type');
            $table->dropColumn('action');
        });
    }
};
