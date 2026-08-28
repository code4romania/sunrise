<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Resources\Monitoring\Pages;

use App\Concerns\PreventSubmitFormOnEnter;
use App\Concerns\RedirectToMonitoring;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Resources\Monitoring\MonitoringResource;
use App\Forms\Components\DatePicker;
use App\Forms\Components\Repeater;
use App\Forms\Components\Select;
use App\Models\Monitoring;
use App\Models\User;
use App\Models\UserRole;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditDetails extends EditRecord
{
    use PreventSubmitFormOnEnter;
    use RedirectToMonitoring;

    protected static string $resource = MonitoringResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('monitoring.titles.edit_details');
    }

    public function getBreadcrumbs(): array
    {
        $parent = $this->getParentRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $parent]) => $parent?->getBreadcrumb() ?? '',
            CaseResource::getUrl('edit_case_monitoring', ['record' => $parent]) => __('monitoring.titles.list'),
            MonitoringResource::getUrl('view', ['beneficiary' => $parent, 'record' => $this->getRecord()]) => __('monitoring.titles.view', ['file_number' => $this->getRecord()->number ?? (string) $this->getRecord()->id]),
            '' => __('monitoring.titles.edit_details'),
        ];
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('monitoring.headings.details'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->maxWidth('3xl')->schema([
                Grid::make()
                    ->maxWidth('3xl')
                    ->schema([
                        DatePicker::make('date')->label(__('monitoring.labels.date')),
                        TextInput::make('number')->label(__('monitoring.labels.number'))->maxLength(100),
                        DatePicker::make('start_date')->label(__('monitoring.labels.start_date')),
                        DatePicker::make('end_date')->label(__('monitoring.labels.end_date')),
                        Repeater::make('specialistsTeam')
                            ->relationship('specialistsTeam')
                            ->minItems(1)
                            ->hiddenLabel()
                            ->columnSpanFull()
                            ->addActionLabel(__('monitoring.actions.add_specialist'))
                            ->columns(3)
                            ->itemLabel(fn (array $state): ?string => isset($state['user_id']) ? User::find($state['user_id'])?->name : null)
                            ->schema([
                                Placeholder::make('id')
                                    ->label(__('nomenclature.labels.nr'))
                                    ->content(function (): int {
                                        static $index = 1;

                                        return $index++;
                                    })
                                    ->hiddenLabel(),
                                Select::make('role_id')
                                    ->label(__('monitoring.labels.role'))
                                    ->options(UserRole::query()->with('role')->get()->pluck('role.name', 'role.id'))
                                    ->live(),
                                Select::make('user_id')
                                    ->label(__('monitoring.labels.specialist_name'))
                                    ->options(fn (Get $get) => UserRole::query()->where('role_id', $get('role_id'))->with('user')->get()->pluck('user.full_name', 'user.id')),
                                Hidden::make('specialistable_type')->default((new Monitoring)->getMorphClass()),
                            ]),
                    ]),
            ]),
        ]);
    }
}
