<?php

declare(strict_types=1);

use App\Enums\RecommendationService;
use App\Filament\Organizations\Resources\Cases\Pages\ViewCase;
use App\Models\Beneficiary;
use App\Models\DetailedEvaluationResult;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function Pest\Livewire\livewire;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withOrganization()->create();
    $this->organization = $this->user->organizations->first();
    $this->actingAs($this->user);
    Filament::setTenant($this->organization);
    Filament::bootCurrentPanel();
});

it('shows Romanian labels for recommendation services on case overview infolist', function () {
    $beneficiary = Beneficiary::factory()
        ->for($this->organization)
        ->create();

    DetailedEvaluationResult::factory()
        ->for($beneficiary)
        ->create([
            'recommendation_services' => [RecommendationService::LEGAL_ASSISTANCE],
        ]);

    livewire(ViewCase::class, [
        'record' => $beneficiary->getKey(),
        'tenant' => $this->organization,
    ])
        ->assertSuccessful()
        ->assertSee(RecommendationService::LEGAL_ASSISTANCE->getLabel(), false)
        ->assertDontSee('legal_assistance', false);
});
