<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class NomenclatorSeeder extends Seeder
{
    public function run(): void
    {
        $path = config('nomenclator.import_path', base_path('../work/Sunrise - Taxonomie Nomenclatoare SUPERADMIN (1).xlsx'));

        if (! file_exists($path)) {
            $this->seedDefaults();

            return;
        }

        $this->importFromExcel($path);
    }

    protected function importFromExcel(string $path): void
    {
        $data = Excel::toArray(null, $path);
        $sheet = $data[0] ?? [];

        $currentService = null;
        $sort = 0;

        foreach ($sheet as $i => $row) {
            if ($i < 2) {
                continue;
            }

            $cod = trim((string) ($row[0] ?? ''));
            $serviceName = trim((string) ($row[1] ?? ''));
            $interventionCode = trim((string) ($row[2] ?? ''));
            $interventionName = trim((string) ($row[3] ?? $interventionCode));

            if ($cod && $serviceName) {
                $currentService = Service::updateOrCreate(
                    ['name' => $serviceName],
                    ['name' => $serviceName, 'status' => 1]
                );
                $sort = 0;
            }

            if ($interventionName && $currentService) {
                $currentService->serviceInterventions()->firstOrCreate(
                    ['service_id' => $currentService->id, 'name' => $interventionName],
                    ['status' => 1, 'sort' => $sort++]
                );
            }
        }
    }

    protected function seedDefaults(): void
    {
        $services = [
            'Găzduire, protecție și siguranță' => [
                'Identificare nevoi găzduire, protecție și siguranță',
                'Informare',
                'Consiliere',
            ],
            'Alte măsuri pentru protecție și siguranță' => [
                'Identificare nevoi',
                'Informare',
            ],
        ];

        foreach ($services as $serviceName => $interventions) {
            $service = Service::updateOrCreate(
                ['name' => $serviceName],
                ['name' => $serviceName, 'status' => 1]
            );

            foreach ($interventions as $sort => $interventionName) {
                $service->serviceInterventions()->updateOrCreate(
                    ['name' => $interventionName, 'service_id' => $service->id],
                    ['name' => $interventionName, 'status' => 1, 'sort' => $sort]
                );
            }
        }
    }
}
