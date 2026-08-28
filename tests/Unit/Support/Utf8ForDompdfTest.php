<?php

declare(strict_types=1);

use App\Support\Utf8ForDompdf;

uses(\PHPUnit\Framework\TestCase::class);

it('strips invalid utf-8 byte sequences from strings', function () {
    $dirty = "ok\xe9\xc0invalid";
    $clean = Utf8ForDompdf::scrubString($dirty);

    expect(mb_check_encoding($clean, 'UTF-8'))->toBeTrue();
});

it('scrubs string fields in report view payload', function () {
    $row = new stdClass;
    $row->label = "Label\xe0";
    $row->total_cases = 3;

    $data = Utf8ForDompdf::scrubReportStatisticsViewData([
        'title' => "T\xe0",
        'exportPeriodStart' => '2026-01-01',
        'header' => ['H1', "H2\xff"],
        'subHeader' => [],
        'verticalHeader' => [],
        'verticalSubHeader' => [],
        'reportData' => collect([$row]),
        'subHeaderKey' => null,
        'verticalHeaderKey' => null,
        'verticalSubHeaderKey' => null,
        'firstHeaderElementColSpan' => 1,
        'firstHeaderElementRowSpan' => 1,
    ]);

    expect(mb_check_encoding($data['title'], 'UTF-8'))->toBeTrue();
    expect(mb_check_encoding($data['header'][1], 'UTF-8'))->toBeTrue();
    expect(mb_check_encoding($data['reportData']->first()->label, 'UTF-8'))->toBeTrue();
    expect($data['reportData']->first()->total_cases)->toBe(3);
});
