<?php

declare(strict_types=1);

namespace App\Infolists\Components;

use BackedEnum;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\TextEntry;

class HistoryLine extends Component
{
    protected string $view = 'filament-forms::components.grid';

    protected string | null $fieldLabel = null;

    protected string | int | array | bool | BackedEnum | null  $oldValue = null;

    protected string | int | array | bool | BackedEnum | null  $newValue = null;

    protected string | int | array | bool | BackedEnum | null  $oldDescription = null;

    protected string | int | array | bool | BackedEnum | null  $newDescription = null;

    protected string | null $section = null;

    final public function __construct(string | null $id)
    {
        $this->id($id);
    }

    public static function make(string | null $id = null): static
    {
        $static = app(static::class, ['id' => $id]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpan('full');

        $this->columns();
    }

    public function oldValue(string | int | array | bool | BackedEnum | null $value): static
    {
        $this->oldValue = $value;

        return $this;
    }

    public function newValue(string | int | array | bool | BackedEnum | null $value): static
    {
        $this->newValue = $value;

        return $this;
    }

    protected function getOldValue(): mixed
    {
        return $this->oldValue;
    }

    protected function getNewValue(): mixed
    {
        return $this->newValue;
    }

    public function oldDescription(string | int | array | bool | BackedEnum | null $description): static
    {
        $this->oldDescription = $description;

        return $this;
    }

    public function newDescription(string | int | array | bool | BackedEnum | null $description): static
    {
        $this->newDescription = $description;

        return $this;
    }

    protected function getOldDescription(): mixed
    {
        return $this->oldDescription;
    }

    protected function getNewDescription(): mixed
    {
        return $this->newDescription;
    }

    public function section(?string $value): static
    {
        $this->section = $value;

        return $this;
    }

    public function fieldLabel(string | null $value): static
    {
        $this->fieldLabel = $value;

        return $this;
    }

    protected function getFieldLabel(): string
    {
        return $this->fieldLabel ?? $this->id;
    }

    protected function getFieldHeading(): string
    {
        $translatePaths = [
            'field',
            'beneficiary.section.identity.labels',
            'beneficiary.section.personal_information.label',
            'beneficiary.section.initial_evaluation.labels',
            'beneficiary.section.detailed_evaluation.labels',
            'beneficiary.section.specialists.labels',
            'beneficiary.section.documents.labels',
        ];

        $filedWithoutID = str_replace('_id', '', $this->getFieldLabel());
        $fieldWithSection = $this->section ? $this->section . '_' . $this->getFieldLabel() : null;

        foreach ($translatePaths as $path) {
            $fieldPath = implode('.', [$path, $this->getFieldLabel()]);

            if ($fieldWithSection) {
                $fieldWithSectionPath = implode('.', [$path, $fieldWithSection]);

                if (__($fieldWithSectionPath) !== $fieldWithSectionPath) {
                    return __($fieldWithSectionPath);
                }
            }

            if (__($fieldPath) !== $fieldPath) {
                return __($fieldPath);
            }

            $fieldPathWithoutID = implode('.', [$path, $filedWithoutID]);

            if (__($fieldPathWithoutID) !== $fieldPathWithoutID) {
                return __($fieldPathWithoutID);
            }
        }

        return $this->getFieldLabel();
    }

    protected function getHeadingId(): string
    {
        return \sprintf('%s_heading', $this->id);
    }

    protected function getOldValueId(): string
    {
        return \sprintf('%s_old', $this->id);
    }

    protected function getNewValueId(): string
    {
        return \sprintf('%s_new', $this->id);
    }

    protected function getOldDescriptionId(): string
    {
        return \sprintf('%s_old_description', $this->id);
    }

    protected function getNewDescriptionId(): string
    {
        return \sprintf('%s_new_description', $this->id);
    }

    public function getChildComponents(): array
    {
        $hiddenDescriptions = blank($this->getOldDescription()) && blank($this->getNewDescription());

        return [
            TextEntry::make($this->getHeadingId())
                ->hiddenLabel()
                ->state($this->getFieldHeading())
                ->columnSpanFull()
                ->size(TextEntry\TextEntrySize::Medium),

            TextEntry::make($this->getOldValueId())
                ->hiddenLabel()
                ->state($this->getOldValue()),

            TextEntry::make($this->getNewValueId())
                ->hiddenLabel()
                ->state($this->getNewValue()),

            TextEntry::make($this->getOldDescriptionId())
                ->hiddenLabel()
                ->hidden($hiddenDescriptions)
                ->state($this->getOldDescription()),

            TextEntry::make($this->getNewDescriptionId())
                ->hiddenLabel()
                ->hidden($hiddenDescriptions)
                ->state($this->getNewDescription()),

        ];
    }
}
