<?php

namespace App\Services;

use App\Models\Reference\ReportStatus;
use App\Models\Report;

class ReportService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function createReport(array $data): Report
    {
        $status = ReportStatus::where('code', 'submitted')->firstOrFail();

        return Report::create([
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

    public function validateReport(Report $report, int $validatorId): Report
    {
        $status = ReportStatus::where('code', 'validated')->firstOrFail();

        $report->update([
            'report_status_id' => $status->id,
            'valide_par'       => $validatorId,
            'motif_rejet'      => null,
        ]);

        return $report->fresh('status');
    }

    public function requestRevision(Report $report, int $validatorId, ?string $reason): Report
    {
        $status = ReportStatus::where('code', 'revision')->firstOrFail();

        $report->update([
            'report_status_id' => $status->id,
            'valide_par'       => $validatorId,
            'motif_rejet'      => $reason,
        ]);

        return $report->fresh('status');
    }

    public function rejectReport(Report $report, int $validatorId, ?string $reason): Report
    {
        $status = ReportStatus::where('code', 'rejected')->firstOrFail();

        $report->update([
            'report_status_id' => $status->id,
            'valide_par'       => $validatorId,
            'motif_rejet'      => $reason,
        ]);

        return $report->fresh('status');
    }
}
