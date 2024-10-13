<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonitoringResource\Pages;

use App\Concerns\HasParentResource;
use App\Concerns\RedirectToMonitoring;
use App\Enums\ChildAggressorRelationship;
use App\Enums\MaintenanceSources;
use App\Filament\Organizations\Resources\MonitoringResource;
use App\Forms\Components\Repeater;
use App\Forms\Components\Select;
use App\Models\MonitoringChild;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditChildren extends EditRecord
{
    use HasParentResource;
    use RedirectToMonitoring;

    protected static string $resource = MonitoringResource::class;

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->parent)->getBreadcrumbsForMonitoringFileEdit($this->getRecord());
    }

    public function getTitle(): string|Htmlable
    {
        return __('monitoring.titles.edit_children');
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('monitoring.headings.child_info'));
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->maxWidth('3xl')
                ->schema(self::getSchema())]);
    }

    public static function getSchema(): array
    {
        $lastFile = self::getParent()?->monitoring->sortByDesc('id')->first()?->load('children');
        $copyLastFile = (bool) request('copyLastFile');
        $childrenFromLastFile = $lastFile?->children;
        $children = self::getParent()?->children;

        return [
            Repeater::make('children')
                ->relationship('children')
                ->defaultItems($children?->count() ?? 0)
                ->hiddenLabel()
                ->maxWidth('3xl')
                ->deletable(false)
                ->addAction(fn ($action) => $action->hidden())
                ->schema([
                    TextInput::make('name')
                        ->label(__('monitoring.labels.child_name'))
                        ->columnSpanFull()
                        ->default(
                            function () use ($children, $copyLastFile, $childrenFromLastFile) {
                                static $indexChild = 0;

                                return self::getDefaultValueForChild(
                                    $children->get($indexChild),
                                    $copyLastFile,
                                    $childrenFromLastFile?->get($indexChild++),
                                    'name'
                                );
                            }
                        ),

                    Grid::make()
                        ->schema([
                            TextInput::make('status')
                                ->label(__('monitoring.labels.status'))
                                ->default(function () use ($children, $copyLastFile, $childrenFromLastFile) {
                                    static $indexChild = 0;

                                    return self::getDefaultValueForChild(
                                        $children->get($indexChild),
                                        $copyLastFile,
                                        $childrenFromLastFile?->get($indexChild++),
                                        'status'
                                    );
                                }),

                            TextInput::make('age')
                                ->label(__('monitoring.labels.age'))
                                ->default(function () use ($children, $copyLastFile, $childrenFromLastFile) {
                                    static $indexChild = 0;

                                    return self::getDefaultValueForChild(
                                        $children->get($indexChild),
                                        $copyLastFile,
                                        $childrenFromLastFile?->get($indexChild++),
                                        'age'
                                    );
                                }),

                            DatePicker::make('birthdate')
                                ->label(__('monitoring.labels.birthdate'))
                                ->default(function () use ($children, $copyLastFile, $childrenFromLastFile) {
                                    static $indexChild = 0;

                                    return self::getDefaultValueForChild(
                                        $children->get($indexChild),
                                        $copyLastFile,
                                        $childrenFromLastFile?->get($indexChild++),
                                        'birthdate'
                                    );
                                }),

                            Select::make('aggressor_relationship')
                                ->label(__('monitoring.labels.aggressor_relationship'))
                                ->placeholder(__('monitoring.placeholders.select_an_answer'))
                                ->default(function () use ($childrenFromLastFile) {
                                    static $indexChild = 0;

                                    return $childrenFromLastFile?->get($indexChild++)?->aggressor_relationship;
                                })
                                ->options(ChildAggressorRelationship::options()),

                            Select::make('maintenance_sources')
                                ->label(__('monitoring.labels.maintenance_sources'))
                                ->placeholder(__('monitoring.placeholders.select_an_answer'))
                                ->default(function () use ($childrenFromLastFile) {
                                    static $indexChild = 0;

                                    return $childrenFromLastFile?->get($indexChild++)?->maintenance_sources;
                                })
                                ->options(MaintenanceSources::options()),

                            TextInput::make('location')
                                ->label(__('monitoring.labels.location'))
                                ->placeholder(__('monitoring.placeholders.location'))
                                ->default(function () use ($childrenFromLastFile) {
                                    static $indexChild = 0;

                                    return $childrenFromLastFile?->get($indexChild++)?->location;
                                })
                                ->maxLength(100),

                            Textarea::make('observations')
                                ->label(__('monitoring.labels.observations'))
                                ->placeholder(__('monitoring.placeholders.observations'))
                                ->default(function () use ($childrenFromLastFile) {
                                    static $indexChild = 0;

                                    return $childrenFromLastFile?->get($indexChild++)?->observations;
                                })
                                ->maxLength(500)
                                ->columnSpanFull(),
                        ]),
                ]),
        ];
    }

    private static function getDefaultValueForChild(array $child, bool $copyLastFile, ?MonitoringChild $childFromLastFile, string $field): string | int | null
    {
        return $copyLastFile && isset($childFromLastFile?->$field) ?
            $childFromLastFile->$field :
            ($child[$field] ?? null);
    }
}
