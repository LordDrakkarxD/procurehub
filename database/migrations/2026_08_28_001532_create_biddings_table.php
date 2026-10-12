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
        Schema::create('biddings', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('status')->default('draft'); // draft, open, awarded, cancelled
            $table->date('opened_at')->nullable();
            $table->date('awarded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('
            CREATE UNIQUE INDEX biddings_number_active_unique
            ON biddings (number)
            WHERE deleted_at IS NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS biddings_number_active_unique');
        Schema::dropIfExists('biddings');
    }
};
