<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages;

use App\Concerns\PreventSubmitFormOnEnter;
use App\Concerns\RedirectToPersonalInformation;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Schemas\AggressorFormSchema;
use App\Forms\Components\Repeater;
use App\Models\Beneficiary;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditCaseAggressor extends EditRecord
{
    use PreventSubmitFormOnEnter;
    use RedirectToPersonalInformation;

    protected static string $resource = CaseResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.edit_aggressor.title');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $record]) => $record instanceof Beneficiary ? $record->getBreadcrumb() : '',
            CaseResource::getUrl('view_personal_information', ['record' => $record]) => __('beneficiary.page.personal_information.title'),
            '' => __('beneficiary.page.edit_aggressor.title'),
        ];
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.section.personal_information.section.aggressor'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->schema(self::aggressorSection()),
        ]);
    }

    /**
     * @return array<int, Repeater>
     */
    protected static function aggressorSection(): array
    {
        return [
            Repeater::make('aggressors')
                ->relationship('aggressors')
                ->maxWidth('3xl')
                ->hiddenLabel()
                ->columns()
                ->minItems(1)
                ->addAction(
                    fn (Action $action): Action => $action
                        ->label(__('beneficiary.section.personal_information.actions.add_aggressor'))
                        ->link()
                        ->color('primary')
                        ->extraAttributes(['class' => 'pull-left'])
                )
                ->deleteAction(
                    fn (Action $action) => $action
                        ->label(__('beneficiary.section.personal_information.actions.delete_aggressor'))
                        ->icon(null)
                        ->link()
                        ->color('danger')
                        ->modalHeading(__('beneficiary.section.personal_information.heading.delete_aggressor'))
                        ->modalDescription(__('beneficiary.section.personal_information.label.delete_aggressor_description'))
                        ->modalSubmitActionLabel(__('general.action.delete'))
                )
                ->itemLabel(function (Get $get) {
                    if (\count($get('aggressors') ?? []) <= 1) {
                        return null;
                    }

                    static $index = 0;

                    return __('beneficiary.section.personal_information.heading.aggressor', [
                        'number' => ++$index,
                    ]);
                })
                ->schema(AggressorFormSchema::getRepeaterItemSchema()),
        ];
    }
}
