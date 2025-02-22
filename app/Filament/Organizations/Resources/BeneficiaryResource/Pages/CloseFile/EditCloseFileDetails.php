<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\CloseFile;

use App\Concerns\PreventSubmitFormOnEnter;
use App\Concerns\RedirectToCloseFile;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\DatePicker;
use App\Forms\Components\Select;
use App\Models\Beneficiary;
use App\Models\CloseFile;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class EditCloseFileDetails extends EditRecord
{
    use RedirectToCloseFile;
    use PreventSubmitFormOnEnter;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.section.close_file.titles.edit_details');
    }

    public function getBreadcrumbs(): array
    {
        return array_merge(
            BeneficiaryBreadcrumb::make($this->getRecord())
                ->getBreadcrumbs('view_close_file'),
            [__('beneficiary.section.close_file.headings.file_details')]
        );
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.section.close_file.headings.file_details'));
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->maxWidth('3xl')
                ->columns()
                ->relationship('closeFile')
                ->schema($this->getSchema()),
        ]);
    }

    public static function getSchema(?Beneficiary $recordParam = null): array
    {
        return [
            DatePicker::make('date')
                ->label(__('beneficiary.section.close_file.labels.date'))
                ->required(),

            DatePicker::make('admittance_date')
                ->label(__('beneficiary.section.close_file.labels.admittance_date'))
                ->required(),

            DatePicker::make('exit_date')
                ->label(__('beneficiary.section.close_file.labels.exit_date'))
                ->required(),

            Select::make('specialist_id')
                ->label(__('beneficiary.section.close_file.labels.case_manager'))
                ->columnSpanFull()
                ->options(
                    fn (?CloseFile $record) => Cache::driver('array')
                        ->rememberForever('close-file-specialists', function () use ($record, $recordParam) {
                            $specialists = $record
                                ? $record->beneficiary->specialistsTeam
                                : $recordParam->specialistsTeam;

                            return $specialists
                                ->loadMissing([
                                    'user:id,first_name,last_name',
                                    'role:id,name',
                                ])
                                ->pluck('name_role', 'id');
                        })
                )
                ->required(),

        ];
    }
}
