<?php

declare(strict_types=1);

use App\Enums\UserStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->ulid()->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('full_name')->virtualAs('CONCAT(first_name, " ", last_name)');
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->string('status')->default(UserStatus::PENDING);
            $table->boolean('has_access_to_all_cases')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->boolean('ngo_admin')->default(false);
            $table->timestamp('password_set_at')->nullable();
            $table->timestamp('deactivated_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }
};
