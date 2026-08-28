<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ensure `referral_mode` is always a JSON array for AsEnumCollection.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $rows = DB::table('flow_presentations')
                ->whereNotNull('referral_mode')
                ->get(['id', 'referral_mode']);

            foreach ($rows as $row) {
                $decoded = json_decode((string) $row->referral_mode, true);
                if (is_string($decoded)) {
                    DB::table('flow_presentations')->where('id', $row->id)->update([
                        'referral_mode' => json_encode([$decoded]),
                    ]);
                }
            }

            return;
        }

        DB::table('flow_presentations')
            ->whereNotNull('referral_mode')
            ->orderBy('id')
            ->chunkById(100, function ($rows): void {
                foreach ($rows as $row) {
                    $decoded = json_decode((string) $row->referral_mode, true);
                    if (is_string($decoded)) {
                        DB::table('flow_presentations')->where('id', $row->id)->update([
                            'referral_mode' => json_encode([$decoded]),
                        ]);
                    }
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $rows = DB::table('flow_presentations')
            ->whereNotNull('referral_mode')
            ->get(['id', 'referral_mode']);

        foreach ($rows as $row) {
            $decoded = json_decode((string) $row->referral_mode, true);
            if (is_array($decoded) && count($decoded) === 1 && is_string($decoded[0])) {
                DB::table('flow_presentations')->where('id', $row->id)->update([
                    'referral_mode' => json_encode($decoded[0]),
                ]);
            }
        }
    }
};
