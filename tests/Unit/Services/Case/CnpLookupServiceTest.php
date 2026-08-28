<?php

declare(strict_types=1);

use App\Enums\Gender;
use App\Models\Beneficiary;
use App\Models\Institution;
use App\Models\Organization;
use App\Models\Specialist;
use App\Models\User;
use App\Services\Case\CnpLookupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->withOrganization()->create();
    $this->user->organizations()->sync([$this->organization->id]);
});

it('returns no beneficiary in tenant when cnp is not in database', function () {
    $service = app(CnpLookupService::class);
    $beneficiaryWithCnp = Beneficiary::factory()->withCNP()->make();
    $cnp = $beneficiaryWithCnp->cnp;

    $result = $service->lookup($cnp, $this->organization, $this->user);

    expect($result->existsInTenant())->toBeFalse()
        ->and($result->canProceedToRegister())->toBeTrue()
        ->and($result->shouldRedirectToView())->toBeFalse();
});

it('redirects to view when cnp exists in tenant and user has access', function () {
    $beneficiary = Beneficiary::factory()
        ->withCNP()
        ->for($this->organization)
        ->create(['first_name' => 'Test', 'last_name' => 'User']);

    Specialist::create([
        'user_id' => $this->user->id,
        'specialistable_id' => $beneficiary->id,
        'specialistable_type' => Beneficiary::class,
    ]);

    $service = app(CnpLookupService::class);
    $result = $service->lookup($beneficiary->cnp, $this->organization, $this->user);

    expect($result->existsInTenant())->toBeTrue()
        ->and($result->userHasAccessToTenantBeneficiary)->toBeTrue()
        ->and($result->shouldRedirectToView())->toBeTrue();
});

it('shows no access when cnp exists in tenant but user has no access', function () {
    $beneficiary = Beneficiary::factory()
        ->withCNP()
        ->for($this->organization)
        ->create(['first_name' => 'Test', 'last_name' => 'User']);

    $otherUser = User::factory()->withOrganization()->create();
    $otherUser->organizations()->sync([$this->organization->id]);

    $service = app(CnpLookupService::class);
    $result = $service->lookup($beneficiary->cnp, $this->organization, $otherUser);

    expect($result->existsInTenant())->toBeTrue()
        ->and($result->userHasAccessToTenantBeneficiary)->toBeFalse()
        ->and($result->showNoAccessMessage())->toBeTrue();
});

it('offers copy from another tenant when user belongs to both organizations and cnp matches', function () {
    $institution = Institution::factory()->create();
    $orgA = Organization::factory()->for($institution)->create();
    $orgB = Organization::factory()->for($institution)->create();

    $user = User::factory()->create();
    $user->organizations()->sync([$orgA->id, $orgB->id]);

    $beneficiaryInB = Beneficiary::factory()
        ->state([
            'birthdate' => '15.03.1990',
            'gender' => Gender::FEMALE,
        ])
        ->withCNP()
        ->for($orgB)
        ->create();

    $service = app(CnpLookupService::class);
    $result = $service->lookup((string) $beneficiaryInB->cnp, $orgA, $user);

    expect($result->existsInTenant())->toBeFalse()
        ->and($result->existsInUserOtherTenant())->toBeTrue()
        ->and($result->canCopyFromOtherCenter())->toBeTrue()
        ->and($result->beneficiaryToCopyFrom()?->is($beneficiaryInB))->toBeTrue();
});
