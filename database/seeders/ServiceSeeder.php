<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = database_path('data/services_and_interventions.csv');

        if (! File::exists($csvFile)) {
            $this->command->error('CSV file not found: '.$csvFile);

            return;
        }

        $file = fopen($csvFile, 'r');
        $header = fgetcsv($file);

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($file)) !== false) {
                $data = array_combine($header, $row);

                $service = Service::updateOrCreate(
                    [
                        'identifier' => $data['service_identifier'],
                    ],
                    [
                        'name' => $data['service_name'],
                        'status' => 1,
                    ]
                );

                $service->serviceInterventions()->updateOrCreate(
                    [
                        'identifier' => $data['identifier_intervention'],
                    ],
                    [
                        'name' => $data['intervention_name'],
                        'status' => 1,
                        'sort' => $data['intervention_sort'] ?? 0,
                    ]
                );
            }

            DB::commit();
            $this->command->info('Services and interventions imported successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error importing data: '.$e->getMessage());
        } finally {
            fclose($file);
        }
    }
}
