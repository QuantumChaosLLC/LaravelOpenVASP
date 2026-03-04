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
            $table->string('message_id', 64)->unique();
            $table->string('originator_lei', 20);
            $table->string('beneficiary_lei', 20);
            $table->string('asset_symbol', 20);
            $table->decimal('asset_amount', 30, 12);
            $table->string('status', 20)->index();
            $table->json('payload');
            $table->json('decision_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('openvasp_transfers');
    }
};
