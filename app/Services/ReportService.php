<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Report;
use App\Models\Reference\ReportStatus;

class ReportService
{
    public function __construct(
        protected IdGeneratorService $idGenerator,
        protected NotificationService $notificationService
    ) {}

    public function createReport(array $data): Report
    {
        $status = ReportStatus::where('code', 'submitted')->firstOrFail();

        return Report::create([
            'id'               => $this->idGenerator->generateReportId(),
            'client_id'        => $data['client_id'],
            'superviseur_id'   => $data['superviseur_id'] ?? null,
            'report_type_id'   => $data['report_type_id'],
            'report_status_id' => $status->id,
            'summary'          => $data['summary'] ?? null,
            'value_numeric'    => $data['value_numeric'] ?? null,
            'value_unit'       => $data['value_unit'] ?? null,
            'value_text'       => $data['value_text'] ?? null,
            'details'          => $data['details'] ?? null,
            'date_rapport'     => $data['date_rapport'],
        ]);
    }

    public function validateReport(Report $report, string $validatorId): Report
    {
        if ($report->status?->code === 'validated') {
            throw new BusinessException('Ce rapport est déjà validé.', 422);
        }

        $status = ReportStatus::where('code', 'validated')->firstOrFail();

        $report->update([
            'report_status_id' => $status->id,
            'valide_par'       => $validatorId,
            'motif_rejet'      => null,
        ]);

        $this->notificationService->create([
            'user_id'  => $report->client_id,
            'category' => 'app',
            'type'     => 'success',
            'title'    => 'Rapport validé',
            'body'     => 'Votre rapport a été validé.',
        ]);

        return $report->fresh(['status', 'type', 'client']);
    }

    public function requestRevision(Report $report, string $validatorId, ?string $reason): Report
    {
        $status = ReportStatus::where('code', 'revision')->firstOrFail();

        $report->update([
            'report_status_id' => $status->id,
            'valide_par'       => $validatorId,
            'motif_rejet'      => $reason,
        ]);

        $this->notificationService->create([
            'user_id'  => $report->client_id,
            'category' => 'app',
            'type'     => 'info',
            'title'    => 'Rapport à corriger',
            'body'     => 'Votre rapport nécessite une révision.',
        ]);

        return $report->fresh(['status', 'type', 'client']);
    }

    public function rejectReport(Report $report, string $validatorId, ?string $reason): Report
    {
        $status = ReportStatus::where('code', 'rejected')->firstOrFail();

        $report->update([
            'report_status_id' => $status->id,
            'valide_par'       => $validatorId,
            'motif_rejet'      => $reason,
        ]);

        $this->notificationService->create([
            'user_id'  => $report->client_id,
            'category' => 'app',
            'type'     => 'alert',
            'title'    => 'Rapport rejeté',
            'body'     => 'Votre rapport a été rejeté.',
        ]);

        return $report->fresh(['status', 'type', 'client']);
    }
}
