<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Resources;

use App\Models\Monitoring;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class MonitoringResource extends Resource
{
    protected static ?string $model = Monitoring::class;

    protected static bool $shouldRegisterNavigation = false;

    public static ?string $parentResource = \App\Filament\Organizations\Resources\BeneficiaryResource::class;

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return \App\Filament\Organizations\Resources\BeneficiaryResource::asParent()
            ->relationship('monitorings')
            ->inverseRelationship('beneficiary');
    }

    public static function getRecordTitle(Model|null $record): string|null|Htmlable
    {
        return $record->number;
    }

    public static function getPages(): array
    {
        return [
            'index' => MonitoringResource\Pages\ListMonitoring::route('/'),
            'create' => MonitoringResource\Pages\CreateMonitoring::route('/create'),
            'view' => MonitoringResource\Pages\ViewMonitoring::route('/{record}'),
            'edit_details' => MonitoringResource\Pages\EditDetails::route('/{record}/editDetails'),
            'edit_children' => MonitoringResource\Pages\EditChildren::route('/{record}/editChildren'),
            'edit_general' => MonitoringResource\Pages\EditGeneral::route('/{record}/editGeneral'),
        ];
    }
}
