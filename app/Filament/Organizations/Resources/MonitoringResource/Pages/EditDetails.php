<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonitoringResource\Pages;

use App\Concerns\HasParentResource;
use App\Concerns\RedirectToMonitoring;
use App\Filament\Organizations\Resources\MonitoringResource;
use App\Forms\Components\Select;
use App\Models\Beneficiary;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditDetails extends EditRecord
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
        return __('monitoring.titles.edit_details');
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('monitoring.headings.details'));
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
        $lastFile = self::getParent()?->monitoring->sortByDesc('id')->first()?->load(['children', 'specialists']);
        $copyLastFile = (bool) request('copyLastFile');

        return [
            Grid::make()
                ->maxWidth('3xl')
                ->schema([
                    DatePicker::make('date')
                        ->label(__('monitoring.labels.date'))
                        ->default($copyLastFile && $lastFile?->date ? $lastFile->date : 'now'),

                    TextInput::make('number')
                        ->label(__('monitoring.labels.number'))
                        ->placeholder(__('monitoring.placeholders.number'))
                        ->default($copyLastFile && $lastFile?->number ? $lastFile->number : null)
                        ->maxLength(100),

                    DatePicker::make('start_date')
                        ->label(__('monitoring.labels.start_date'))
                        ->default($copyLastFile && $lastFile?->start_date ? $lastFile->start_date : null),

                    DatePicker::make('end_date')
                        ->label(__('monitoring.labels.end_date'))
                        ->default($copyLastFile && $lastFile?->end_date ? $lastFile->end_date : null),

                    Hidden::make('parent_id')
                        ->default(fn ($record) => $record?->beneficiary_id ?? request('parent'))
                        ->formatStateUsing(fn ($record, $state) => $state ?? $record->beneficiary_id),

                    Select::make('specialists')
                        ->label(__('monitoring.labels.team'))
                        ->placeholder(__('monitoring.placeholders.team'))
                        ->preload()
                        ->default(fn (Get $get) => $copyLastFile && $lastFile?->specialists ?
                            $lastFile->specialists->map(fn ($specialist) => $specialist->id)->toArray() : [
                                Beneficiary::find($get('parent_id'))
                                    ->team
                                    ->filter(fn ($item) => $item->user_id == auth()->id())
                                    ->first()
                                    ?->id,
                            ])
                        ->relationship('specialists')
                        ->multiple()
                        ->options(
                            fn (Get $get) => Beneficiary::find($get('parent_id'))
                                ->team
                                ->each(fn ($item) => $item->full_name = $item->user->getFilamentName())
                                ->pluck('full_name', 'id')
                        ),

                ]),

        ];
    }
}
