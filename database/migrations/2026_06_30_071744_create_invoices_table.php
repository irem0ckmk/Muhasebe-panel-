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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->string('client_name');
            $table->decimal('amount', 12, 2);
            $table->unsignedTinyInteger('tax_rate')->default(20);
            $table->enum('status', ['beklemede', 'odendi', 'iptal'])->default('beklemede');
            $table->date('issue_date');
            $table->date('due_date');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
