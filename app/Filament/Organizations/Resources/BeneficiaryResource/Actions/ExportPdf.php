<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Actions;

use Filament\Actions\Action;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPdf\Facades\Pdf;

class ExportPdf extends Action
{
    protected array | \Closure | null $schema = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action(function () {
            $infolist = Infolist::make()
                ->record($this->getRecord())
                ->schema($this->schema);

            $html = view('exports.pdf-page', ['infolist' => $infolist])->render();
//            dd($html);

            return response()->streamDownload(function () use ($infolist) {
                echo \Barryvdh\DomPDF\Facade\Pdf::loadHtml(
                    Blade::render('exports.pdf-page', ['infolist' => $infolist])
                )
//                    ->setOptions(['defaultFont' => filament()->getFontFamily()], true)
//                    ->setOptions(['defaultFont' => 'identity'], true)
                    ->stream();
            }, 'test' . '.pdf');

//            return \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.pdf-page', ['infolist' => $infolist])
//                ->download('dasda.pdf')
//                ->save(storage_path('app/public/exports.pdf'))
//            ;
//                ->download();
//            return Pdf::html($html)->download('close_file.pdf');
//            Pdf::view('exports.pdf-page', ['infolist' => $infolist])
//                ->name('close_file.pdf')
//                ->download();
//                ->save(storage_path('app/public/exports.pdf'));
//            return dd();
//                ->download();

        });
    }

    public function schema(array|\Closure|null $schema): self
    {
        $this->schema = $schema;

        return $this;
    }

    public function getName(): ?string
    {
        return 'export_pdf';
    }
}
