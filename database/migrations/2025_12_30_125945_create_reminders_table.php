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
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('field_key'); // The date field key in the document data
            $table->timestamp('reminder_at'); // When to send the reminder
            $table->timestamp('sent_at')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->text('email_to')->nullable(); // Comma-separated emails
            $table->text('message')->nullable();
            $table->timestamps();

            $table->index(['reminder_at', 'is_sent']);
            $table->index('document_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
