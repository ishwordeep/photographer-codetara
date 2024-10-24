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
        // about photographer
        Schema::create('photographers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->text('description')->nullable();

            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->timestamps();
        });


        // category_images
        Schema::create('category_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('image');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });



        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            $table->date('date');  // Date of availability
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->date('date')->nullable();
            $table->string('ticket_number')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            // status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('message')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });

        Schema::create('booking_queries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->string('question')->nullable();
            $table->string('answer')->nullable();
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('message')->nullable();
            $table->timestamps();
        });

        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->string('title');  // Title of the work
            $table->text('description')->nullable();  // Description of the work
            $table->string('image');  // Path to the image file
            $table->timestamp('date')->nullable();  // Date when work was published
            $table->boolean('is_active')->default(true);  // Is work active or not
            $table->timestamps();  // created_at and updated_at
        });

        Schema::create('work_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_id');  // Work ID
            $table->string('image');  // Path to the image file
            $table->timestamps();  // created_at and updated_at

            $table->foreign('work_id')->references('id')->on('works')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photographers'); //
        Schema::dropIfExists('category_images'); //
        Schema::dropIfExists('availabilities');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('booking_queries');
        Schema::dropIfExists('messages'); //
    }
};
