<?php

namespace App\Http\Controllers\Concerns;

use App\Support\PdfBranding;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

trait ExportsListPdf
{
    /**
     * @param  array<int, string>  $headers
     * @param  iterable<int, array<int, string|null>>  $rows
     */
    protected function streamListPdf(
        string $documentTitle,
        array $headers,
        iterable $rows,
        string $filenamePrefix,
        string $orientation = 'landscape'
    ): Response {
        $buId = session('selected_bu') ? (int) session('selected_bu') : null;
        $pdfBranding = PdfBranding::forBu($buId);

        $rowsCollection = $rows instanceof Collection ? $rows : collect($rows);

        $pdf = Pdf::loadView('partials.liste-export-generic', [
            'documentTitle' => $documentTitle,
            'pdfBranding' => $pdfBranding,
            'headers' => $headers,
            'rows' => $rowsCollection,
        ])
            ->setPaper('a4', $orientation)
            ->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->stream($filenamePrefix.'-'.now()->format('Y-m-d').'.pdf', ['Attachment' => false]);
    }

    /**
     * @param  iterable<int, mixed>  $items
     * @param  array<int, string>  $columns  dot notation (ex. fournisseur.nom_raison_sociale)
     */
    protected function mapRowsForPdf(iterable $items, array $columns): array
    {
        $rows = [];
        foreach ($items as $item) {
            $row = [];
            foreach ($columns as $column) {
                $value = data_get($item, $column);
                if ($value instanceof \DateTimeInterface) {
                    $value = $value->format('d/m/Y');
                }
                $row[] = filled($value) ? (string) $value : '—';
            }
            $rows[] = $row;
        }

        return $rows;
    }

    protected function requireBuOrRedirect(): ?int
    {
        $buId = session('selected_bu');
        if (! $buId) {
            redirect()->route('select.bu')
                ->withErrors(['error' => 'Veuillez sélectionner une BU avant d\'accéder à cette page.'])
                ->send();
            exit;
        }

        return (int) $buId;
    }
}
