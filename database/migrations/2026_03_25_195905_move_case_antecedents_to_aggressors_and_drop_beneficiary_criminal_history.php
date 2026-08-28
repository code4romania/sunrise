<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('aggressors', function (Blueprint $table) {
            $table->string('has_police_reports')->nullable()->after('protection_order_notes');
            $table->unsignedSmallInteger('police_report_count')->nullable()->after('has_police_reports');
            $table->string('has_medical_reports')->nullable()->after('police_report_count');
            $table->unsignedSmallInteger('medical_report_count')->nullable()->after('has_medical_reports');
            $table->unsignedSmallInteger('hospitalization_days')->nullable()->after('medical_report_count');
            $table->text('hospitalization_observations')->nullable()->after('hospitalization_days');
        });

        $antecedentsRows = DB::table('beneficiary_antecedents')->get();

        foreach ($antecedentsRows as $row) {
            $aggressorId = DB::table('aggressors')
                ->where('beneficiary_id', $row->beneficiary_id)
                ->orderBy('id')
                ->value('id');

            if ($aggressorId === null) {
                continue;
            }

            DB::table('aggressors')
                ->where('id', $aggressorId)
                ->update([
                    'has_police_reports' => $row->has_police_reports,
                    'police_report_count' => $row->police_report_count,
                    'has_medical_reports' => $row->has_medical_reports,
                    'medical_report_count' => $row->medical_report_count,
                    'hospitalization_observations' => $row->observations ?? null,
                ]);
        }

        Schema::table('beneficiary_details', function (Blueprint $table) {
            $table->dropColumn(['criminal_history', 'criminal_history_notes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiary_details', function (Blueprint $table) {
            $table->string('criminal_history')->nullable();
            $table->string('criminal_history_notes')->nullable();
        });

        Schema::table('aggressors', function (Blueprint $table) {
            $table->dropColumn([
                'has_police_reports',
                'police_report_count',
                'has_medical_reports',
                'medical_report_count',
                'hospitalization_days',
                'hospitalization_observations',
            ]);
        });
    }
};
