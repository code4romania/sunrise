<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Actions;

use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Infolists\Infolist;
use Spatie\LaravelPdf\Facades\Pdf;

class ExportPdf2 extends ExportAction
{
    protected array | \Closure | null $schema = null;

    protected function setUp(): void
    {
        $this->exporter(\App\Exports\Pdf::class);
        parent::setUp();

//        $this->action(function () {
//            $infolist = Infolist::make()
//                ->record($this->getRecord())
//                ->schema($this->schema);
//
//            dd(Pdf::view('exports.pdf-page', ['infolist' => $infolist]));
////                ->save(storage_path('app/public/exports.pdf'));
//            return dd();
////                ->download();
//
//        });
    }

    public function schema(array|\Closure|null $schema): self
    {
        $this->schema = $schema;

        return $this;
    }

    public function getName(): ?string
    {
        return 'export_pdf_2';
    }
}
