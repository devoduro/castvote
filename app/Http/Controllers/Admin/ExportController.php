<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Event;
use App\Models\Nominee;
use App\Models\Vote;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ExportController extends Controller
{
    /**
     * Export the full payments ledger as CSV.
     */
    public function paymentsCsv(Event $event): Response
    {
        $this->authorise($event);

        $payments = $event->payments()->with('vote')->orderBy('created_at')->get();

        $csv  = "Reference,Phone,Network,Amount (GHS),Status,Channel,Votes Qty,Created At,Verified At\n";
        foreach ($payments as $p) {
            $csv .= implode(',', [
                $p->provider_reference,
                $p->phone_number,
                $p->momo_network ?? 'N/A',
                number_format($p->amount_pesewas / 100, 2),
                $p->status,
                $p->vote?->channel ?? 'N/A',
                $p->vote?->quantity ?? 0,
                $p->created_at->toDateTimeString(),
                $p->verified_at?->toDateTimeString() ?? '',
            ]) . "\n";
        }

        AuditLog::record('export.payments_csv', $event);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$event->slug}-payments.csv\"",
        ]);
    }

    /**
     * Export the final vote tally as a signed PDF certificate.
     */
    public function resultsPdf(Event $event): \Illuminate\Http\Response
    {
        $this->authorise($event);

        $categories = $event->categories()->with('nominees')->get();

        $results = $categories->map(function ($cat) {
            $nominees = $cat->nominees->map(function ($nom) {
                $nom->total_votes = Vote::where('nominee_id', $nom->id)->sum('quantity');
                return $nom;
            })->sortByDesc('total_votes')->values();

            return ['category' => $cat, 'nominees' => $nominees, 'total' => $nominees->sum('total_votes')];
        });

        $totalVotes   = $results->sum('total');
        $totalRevenue = $event->payments()->where('status', 'success')->sum('amount_pesewas');

        AuditLog::record('export.results_pdf', $event);

        $pdf = Pdf::loadView('admin.exports.results-pdf', compact('event', 'results', 'totalVotes', 'totalRevenue'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("{$event->slug}-results.pdf");
    }

    /**
     * Export the audit log as CSV.
     */
    public function auditCsv(Event $event): Response
    {
        $this->authorise($event);

        $logs = AuditLog::with('admin')
            ->where(function ($q) use ($event) {
                $q->where('subject_type', Event::class)->where('subject_id', $event->id)
                  ->orWhereIn('subject_type', [
                      \App\Models\Category::class,
                      \App\Models\Nominee::class,
                      \App\Models\Payment::class,
                      \App\Models\Vote::class,
                  ]);
            })
            ->orderBy('created_at')
            ->get();

        $csv = "Timestamp,Admin,Action,Subject,IP\n";
        foreach ($logs as $log) {
            $csv .= implode(',', [
                $log->created_at->toDateTimeString(),
                '"' . ($log->admin?->name ?? 'System') . '"',
                $log->action,
                ($log->subject_type ? class_basename($log->subject_type) . '#' . $log->subject_id : ''),
                $log->ip_address ?? '',
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$event->slug}-audit.csv\"",
        ]);
    }

    private function authorise(Event $event): void
    {
        $admin = auth('admin')->user();
        abort_if($admin->organization_id !== $event->organization_id, 403);
    }
}
