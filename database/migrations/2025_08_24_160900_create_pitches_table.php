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
        Schema::create('pitches', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->text('problem');
            $table->text('solution');
            $table->text('market');
            $table->text('product');
            $table->text('business_model');
            $table->text('competition');
            $table->text('market_strategy');
            $table->text('results');
            $table->text('team_info');
            $table->text('financials_investment');
            $table->enum("status",['draft', 'submitted', 'scored']);
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pitches');
    }
};
