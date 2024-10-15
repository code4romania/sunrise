<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceResource\Pages;

use App\Filament\Admin\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\View\View;

class ManageServices extends ManageRecords
{
    protected static string $resource = ServiceResource::class;

    protected static string $view = 'filament.admin.pages.nomenclature-list';



    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('service.action.create'))
                ->modalHeading(__('service.action.create'))
                ->createAnother(false),
        ];
    }

//    public function getCustomTabs(): array
//    {
//        return [
//            ['label' => 'Tab1', 'url'=> 'tab1'],
//            ['label' => 'Tab2', 'url'=> 'tab2'],
//            ['label' => 'Tab3', 'url'=> 'tab3'],
//        ];
//    }
}
