<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            
            // CRITICAL FIX: Use integer() not unsignedInteger()
            // Because invoices.invoice_id is int(11) - SIGNED
            $table->integer('invoice_id'); // ← CHANGED: Removed 'unsigned'
            
            $table->string('payment_ref_no')->unique();
            $table->date('payment_date');
            $table->decimal('payment_amount', 10, 2);
            
            // Payment method details
            $table->enum('payment_method', [
                'Cash', 
                'Bank Transfer', 
                'Check', 
                'Credit Card', 
                'GCash', 
                'PayMaya',
                'Other'
            ])->default('Cash');
            
            $table->string('bank_name')->nullable();
            $table->string('check_number')->nullable();
            $table->string('transaction_ref_no')->nullable();
            
            // Payment status
            $table->enum('payment_status', [
                'Pending',
                'Completed', 
                'Failed', 
                'Refunded',
                'Cancelled'
            ])->default('Pending');
            
            $table->text('remarks')->nullable();
            $table->string('received_by')->nullable();
            $table->timestamps();

            // Foreign Key - This will now work!
            $table->foreign('invoice_id')
                  ->references('invoice_id')
                  ->on('invoices')
                  ->onDelete('cascade');
                  
            // Indexes
            $table->index('invoice_id');
            $table->index('payment_date');
            $table->index('payment_status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};