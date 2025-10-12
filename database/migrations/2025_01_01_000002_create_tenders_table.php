<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('department')->nullable();
            $table->date('last_date')->nullable();
            $table->string('document_path')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_number')->nullable();
            $table->string('contact_email')->nullable();
            $table->enum('status', ['pending', 'approved', 'disapproved'])->default('pending');
            $table->unsignedBigInteger('created_by')->default(1); // dummy user id
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenders');
    }
};
