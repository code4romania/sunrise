<?php

declare(strict_types=1);

namespace App\Services\CaseExports\Composers;

use App\Enums\AdmittanceReason;
use App\Enums\CloseMethod;
use App\Models\Beneficiary;
use App\Models\CloseFile;
use App\Services\CaseExports\Support\ExportDataFormatter;
use Illuminate\Support\Collection;

class CloseFilePdfComposer
{
    /**
     * @var list<AdmittanceReason>
     */
    private const ADMITTANCE_REASON_ORDER = [
        AdmittanceReason::SECURITY,
        AdmittanceReason::EVICTION_FROM_HOME,
        AdmittanceReason::DIVORCE,
        AdmittanceReason::CRISIS_SITUATION,
        AdmittanceReason::OTHER,
    ];

    /**
     * @var list<CloseMethod>
     */
    private const CLOSE_METHOD_ORDER = [
        CloseMethod::ACCORDING_TO_INTERVENTIONAL_PROGRAM,
        CloseMethod::TRANSFER_TO,
        CloseMethod::CONTRACT_EXPIRED,
        CloseMethod::DEREGISTRATION,
        CloseMethod::RETURN_TO_RELATIONSHIP_WITH_AGGRESSOR,
        CloseMethod::BENEFICIARY_REQUEST,
        CloseMethod::OTHER,
    ];

    public function __construct(
        private readonly ExportDataFormatter $formatter,
    ) {}

    /**
     * @return array{title: string, type: string, data: array<string, mixed>}
     */
    public function composeMainSection(CloseFile $closeFile, ?Beneficiary $beneficiary): array
    {
        $fullName = '—';
        $cnpDigits = array_fill(0, 13, ' ');

        if ($beneficiary instanceof Beneficiary) {
            $fullName = trim((string) $beneficiary->full_name) !== '' ? (string) $beneficiary->full_name : '—';
            $digits = preg_replace('/\D+/', '', (string) ($beneficiary->cnp ?? '')) ?? '';
            $digits = substr($digits, 0, 13);
            $padded = str_pad($digits, 13, ' ', STR_PAD_RIGHT);
            $cnpDigits = preg_split('//u', $padded, -1, PREG_SPLIT_NO_EMPTY) ?: $cnpDigits;
            if (count($cnpDigits) < 13) {
                $cnpDigits = array_pad($cnpDigits, 13, ' ');
            }
        }

        $selectedReasons = [];
        $reasonCollection = $closeFile->admittance_reason;
        if ($reasonCollection instanceof Collection) {
            foreach ($reasonCollection as $reason) {
                if ($reason instanceof AdmittanceReason) {
                    $selectedReasons[$reason->value] = true;
                }
            }
        }

        $admittanceReasonRows = [];
        foreach (self::ADMITTANCE_REASON_ORDER as $reason) {
            $admittanceReasonRows[] = [
                'value' => $reason->value,
                'label' => $reason->getLabel(),
                'checked' => isset($selectedReasons[$reason->value]),
            ];
        }

        $selectedMethod = $closeFile->close_method instanceof CloseMethod ? $closeFile->close_method->value : null;

        $closeMethodRows = [];
        foreach (self::CLOSE_METHOD_ORDER as $method) {
            $closeMethodRows[] = [
                'value' => $method->value,
                'label' => $method->getLabel(),
                'checked' => $selectedMethod === $method->value,
            ];
        }

        return [
            'title' => '',
            'type' => 'close_file_form',
            'data' => [
                'beneficiary_full_name' => $fullName,
                'cnp_digits' => $cnpDigits,
                'admittance_date' => $this->formatter->toPrintableValue($closeFile->admittance_date),
                'exit_date' => $this->formatter->toPrintableValue($closeFile->exit_date),
                'admittance_reason_rows' => $admittanceReasonRows,
                'admittance_details' => $this->formatter->toPrintableValue($closeFile->admittance_details),
                'close_method_rows' => $closeMethodRows,
                'close_method_selected' => $selectedMethod,
                'institution_name' => $this->formatter->toPrintableValue($closeFile->institution_name),
                'beneficiary_request' => $this->formatter->toPrintableValue($closeFile->beneficiary_request),
                'other_details' => $this->formatter->toPrintableValue($closeFile->other_details),
                'close_situation' => $this->formatter->toPrintableValue($closeFile->close_situation),
                'closure_date' => $this->formatter->toPrintableValue($closeFile->date),
            ],
        ];
    }
}
