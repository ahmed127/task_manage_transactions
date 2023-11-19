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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->decimal('amount', 8, 2);
            $table->decimal('paid', 8, 2)->default(0.00);
            $table->decimal('unpaid', 8, 2)->default(0.00);
            $table->date('due_at');
            $table->unsignedTinyInteger('vat');
            $table->boolean('is_vat_inclusive');
            $table->unsignedTinyInteger('status')->default(1)->comment('1 => outstanding, 2 => overdue, 3 => paid');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
