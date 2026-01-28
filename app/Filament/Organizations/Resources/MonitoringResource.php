<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Models\Monitoring;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class MonitoringResource extends Resource
{
    protected static ?string $model = Monitoring::class;

    protected static bool $shouldRegisterNavigation = false;

    public static ?string $parentResource = BeneficiaryResource::class;

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return BeneficiaryResource::asParent()
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
            'index' => \App\Filament\Organizations\Resources\MonitoringResource\Pages\ListMonitoring::route('/'),
            'create' => \App\Filament\Organizations\Resources\MonitoringResource\Pages\CreateMonitoring::route('/create'),
            'view' => \App\Filament\Organizations\Resources\MonitoringResource\Pages\ViewMonitoring::route('/{record}'),
            'edit_details' => \App\Filament\Organizations\Resources\MonitoringResource\Pages\EditDetails::route('/{record}/editDetails'),
            'edit_children' => \App\Filament\Organizations\Resources\MonitoringResource\Pages\EditChildren::route('/{record}/editChildren'),
            'edit_general' => \App\Filament\Organizations\Resources\MonitoringResource\Pages\EditGeneral::route('/{record}/editGeneral'),
        ];
    }
}
