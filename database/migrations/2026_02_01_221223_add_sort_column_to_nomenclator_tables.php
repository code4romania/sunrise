<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->integer('sort')->default(0)->after('id');
        });
        $this->setInitialSortOrder('services');

        Schema::table('benefits', function (Blueprint $table) {
            $table->integer('sort')->default(0)->after('id');
        });
        $this->setInitialSortOrder('benefits');

        Schema::table('roles', function (Blueprint $table) {
            $table->integer('sort')->default(0)->after('id');
        });
        $this->setInitialSortOrder('roles');

        Schema::table('results', function (Blueprint $table) {
            $table->integer('sort')->default(0)->after('id');
        });
        $this->setInitialSortOrder('results');
    }

    private function setInitialSortOrder(string $table): void
    {
        $records = DB::table($table)->orderBy('id')->get();
        foreach ($records as $index => $record) {
            DB::table($table)->where('id', $record->id)->update(['sort' => $index]);
        }
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('sort');
        });

        Schema::table('benefits', function (Blueprint $table) {
            $table->dropColumn('sort');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('sort');
        });

        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
    }
};
