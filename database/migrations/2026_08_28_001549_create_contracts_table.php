<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->foreignId('bidding_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->constrained()->restrictOnDelete();
            $table->decimal('total_amount', 12, 2);
            $table->decimal('balance', 12, 2);
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->string('status')->default('active'); // active, closed, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('
            CREATE UNIQUE INDEX contracts_number_active_unique
            ON contracts (number)
            WHERE deleted_at IS NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS contracts_number_active_unique');
        Schema::dropIfExists('contracts');
    }
};
