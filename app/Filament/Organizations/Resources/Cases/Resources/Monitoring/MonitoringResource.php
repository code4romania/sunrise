<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Resources\Monitoring;

use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Resources\Monitoring\Pages\CreateMonitoring;
use App\Filament\Organizations\Resources\Cases\Resources\Monitoring\Pages\ViewMonitoring;
use App\Models\Monitoring;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MonitoringResource extends Resource
{
    protected static ?string $model = Monitoring::class;

    protected static ?string $slug = 'monitoring';

    protected static bool $shouldRegisterNavigation = false;

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return CaseResource::asParent()
            ->relationship('monitoring')
            ->inverseRelationship('beneficiary');
    }

    public static function getModelLabel(): string
    {
        return __('monitoring.titles.create');
    }

    public static function getPluralModelLabel(): string
    {
        return __('monitoring.titles.list');
    }

    public static function getRecordTitle(?Model $record): ?string
    {
        return $record instanceof Monitoring
            ? __('monitoring.breadcrumbs.file', ['file_number' => $record->number ?? (string) $record->id])
            : null;
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->with(['children', 'specialistsTeam.user', 'specialistsTeam.roleForDisplay']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateMonitoring::route('/create'),
            'view' => ViewMonitoring::route('/{record}'),
        ];
    }
}
