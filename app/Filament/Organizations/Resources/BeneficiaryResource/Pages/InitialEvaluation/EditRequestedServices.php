<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\InitialEvaluation;

use App\Concerns\PreventSubmitFormOnEnter;
use App\Concerns\RedirectToInitialEvaluation;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditRequestedServices extends EditRecord
{
    use RedirectToInitialEvaluation;
    use PreventSubmitFormOnEnter;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.edit_requested_services.title');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbs('view_initial_evaluation');
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.wizard.requested_services.label'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(
            $this->getFormSchema()
        );
    }

    protected function getFormSchema(): array
    {
        return [
            Group::make()
                ->relationship('requestedServices')
                ->schema([
                    Section::make(__('beneficiary.section.initial_evaluation.heading.types_of_requested_services'))
                        ->schema(self::getRequestedServicesSchema()),
                ]),
        ];
    }

    public static function getRequestedServicesSchema(): array
    {
        return [
            CheckboxList::make('requested_services')
                ->hiddenLabel()
                ->options(RecommendationService::options()),

            Textarea::make('other_services_description')
                ->hiddenLabel()
                ->placeholder(__('beneficiary.placeholder.other_services'))
                ->maxLength(100),
        ];
    }
}
