<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->enum('provider', ['mpesa', 'emola', 'stripe', 'paypal', 'manual']);
            $table->enum('status', ['pending', 'authorized', 'captured', 'failed', 'canceled'])->default('pending');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('MZN');
            $table->string('transaction_ref')->nullable()->index();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
