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
        // 1. Company Settings (Issuer Profile)
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('ssm_brn');
            $table->string('tin_number');
            $table->string('sst_number')->nullable();
            $table->string('msic_code')->default('62010');
            $table->string('email');
            $table->string('phone');
            $table->text('address');
            $table->string('bank_name');
            $table->string('bank_account_number');
            $table->string('bank_account_holder');
            $table->string('duitnow_id')->nullable();
            $table->enum('einvoice_mode', ['off', 'sandbox', 'production'])->default('off');
            $table->timestamps();
        });

        // 2. Customers (Buyers)
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('identification_type', ['BRN', 'NRIC', 'PASSPORT', 'ARMY'])->default('BRN');
            $table->string('ssm_brn')->nullable();
            $table->string('tin_number')->nullable();
            $table->string('sst_number')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->integer('payment_terms_days')->default(30);
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Invoices (AR)
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->date('issue_date');
            $table->date('due_date');
            $table->string('currency', 3)->default('MYR');
            $table->string('po_number')->nullable();

            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('discount_total', 15, 2)->default(0.00);
            $table->decimal('tax_total', 15, 2)->default(0.00);
            $table->decimal('grand_total', 15, 2)->default(0.00);
            $table->decimal('paid_amount', 15, 2)->default(0.00);

            $table->enum('status', ['draft', 'issued', 'partially_paid', 'paid', 'cancelled'])->default('draft');
            $table->enum('einvoice_mode', ['off', 'sandbox', 'production'])->default('off');
            $table->string('lhdn_uuid')->nullable()->index();
            $table->enum('lhdn_status', ['not_submitted', 'submitted', 'valid', 'invalid', 'cancelled'])->default('not_submitted');
            $table->timestamp('lhdn_validated_at')->nullable();
            $table->text('lhdn_validation_url')->nullable();

            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Invoice Line Items
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->string('classification_code')->default('001');
            $table->decimal('quantity', 12, 4)->default(1);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->decimal('sst_rate', 5, 2)->default(8.00);
            $table->decimal('sst_amount', 15, 2)->default(0.00);
            $table->decimal('net_amount', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();
        });

        // 5. Vendors (Suppliers)
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ssm_brn')->nullable();
            $table->string('tin_number')->nullable();
            $table->string('sst_number')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 6. Bills (AP)
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number');
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->string('po_number')->nullable()->index();
            $table->date('bill_date');
            $table->date('due_date');

            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('tax_total', 15, 2)->default(0.00);
            $table->decimal('grand_total', 15, 2)->default(0.00);

            $table->enum('match_status', ['unmatched', 'matched', 'variance_flagged'])->default('unmatched');
            $table->decimal('matching_variance', 15, 2)->default(0.00);
            $table->enum('approval_status', ['draft', 'pending_approval', 'approved', 'rejected'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_remarks')->nullable();

            $table->string('file_attachment_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['vendor_id', 'bill_number']);
        });

        // 7. Bill Line Items
        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->decimal('quantity', 12, 4)->default(1);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('sst_rate', 5, 2)->default(8.00);
            $table->decimal('sst_amount', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_items');
        Schema::dropIfExists('bills');
        Schema::dropIfExists('vendors');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('company_settings');
    }
};
