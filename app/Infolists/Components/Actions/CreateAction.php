<?php

declare(strict_types=1);

namespace App\Infolists\Components\Actions;

use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Actions\Concerns\InteractsWithRecord;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Form;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class CreateAction extends \Filament\Actions\Action
{
    use CanCustomizeProcess;
    use InteractsWithRecord;

    protected ?Closure $getRelationshipUsing = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action(function (array $arguments, Schema $schema): void {
            $model = $this->getModel();

            $record = $this->process(function (array $data, HasActions $livewire) use ($model): Model {
                if ($translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver()) {
                    $record = $translatableContentDriver->makeRecord($model, $data);
                } else {
                    $record = new $model;
                    $record->fill($data);
                }

                if ($relationship = $this->getRelationship()) {
                    /* @phpstan-ignore-next-line */
                    $relationship->save($record);

                    return $record;
                }

                $record->save();

                return $record;
            });

            $this->record($record);
            $schema->model($record)->saveRelationships();

            if ($arguments['another'] ?? false) {
                $this->callAfter();
                $this->sendSuccessNotification();

                $this->record(null);

                // Ensure that the form record is anonymized so that relationships aren't loaded.
                $schema->model($model);

                $schema->fill();

                $this->halt();

                return;
            }

            $this->success();
        });
    }

    public function relationship(?Closure $relationship): static
    {
        $this->getRelationshipUsing = $relationship;

        return $this;
    }

    public function getRelationship(): Relation | Builder | null
    {
        return $this->evaluate($this->getRelationshipUsing);
    }
}
