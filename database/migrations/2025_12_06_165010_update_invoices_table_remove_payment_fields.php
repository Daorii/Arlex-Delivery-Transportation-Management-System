<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Remove payment-related fields
            $table->dropColumn(['payment_method', 'payment_status']);
            
            // Add invoice_status (if it doesn't exist)
            $table->enum('invoice_status', [
                'Draft', 
                'Sent', 
                'Partially Paid', 
                'Fully Paid', 
                'Overdue', 
                'Cancelled'
            ])->default('Draft')->after('net_total');
            
            // Add due_date for tracking overdue invoices
            $table->date('due_date')->nullable()->after('invoice_date');
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->nullable();
            $table->dropColumn(['invoice_status', 'due_date']);
        });
    }
};