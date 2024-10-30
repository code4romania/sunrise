<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\CloseFile;

use App\Concerns\RedirectToCloseFile;
use App\Enums\Role;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\Select;
use App\Models\Beneficiary;
use App\Models\CaseTeam;
use App\Models\CloseFile;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditCloseFileDetails extends EditRecord
{
    use RedirectToCloseFile;

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

            TextInput::make('number')
                ->label(__('beneficiary.section.close_file.labels.number'))
                ->required(),

            DatePicker::make('admittance_date')
                ->label(__('beneficiary.section.close_file.labels.admittance_date'))
                ->required(),

            DatePicker::make('exit_date')
                ->label(__('beneficiary.section.close_file.labels.exit_date'))
                ->required(),

            Select::make('case_team_id')
                ->label(__('beneficiary.section.close_file.labels.case_manager'))
                ->columnSpanFull()
                ->options(
                    function (?CloseFile $record) use ($recordParam) {
                        $team = $record ? $record->beneficiary->team : $recordParam->team;

                        return $team
                            ->map(fn (CaseTeam $item) => ['id' => $item->id, 'full_name' => $item->user->getFilamentName()])
                            ->pluck('full_name', 'id');
                    }
                )
                ->required(),

        ];
    }
}
