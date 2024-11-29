<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\User;
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
        User::query()->with('organizations')
            ->get()
            ->each(
                fn (User $user) => $user->isAdmin() ?
                    $user->userStatus()->make(['status' => $user->status]) :
                    $user->organizations->each(
                        fn (Organization $organization) => $user->userStatus()->create([
                            'organization_id' => $organization->id,
                            'status' => $user->status,
                        ])
                    )
            );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_status', function (Blueprint $table) {
            //
        });
    }
};
