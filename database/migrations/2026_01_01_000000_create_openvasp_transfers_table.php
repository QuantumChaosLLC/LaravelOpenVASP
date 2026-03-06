<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('openvasp_transfers', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('inquiry_id', 128)->unique();
            $table->string('status', 32)->index();
            $table->json('inquiry_payload');
            $table->json('resolution_payload')->nullable();
            $table->json('confirmation_payload')->nullable();
            $table->string('payment_address')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('txid', 255)->nullable();
            $table->text('canceled_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('openvasp_transfers');
    }
};
