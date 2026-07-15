<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ExportsListPdf;

class GenericListExportController extends Controller
{
    use ExportsListPdf;

    public function export(string $key)
    {
        $config = config("list-exports.{$key}");
        if (! $config) {
            abort(404, 'Export PDF non configuré pour cette liste.');
        }

        $query = ($config['model'])::query();
        if (! empty($config['with'])) {
            $query->with($config['with']);
        }
        if (! empty($config['scope'])) {
            $query->{$config['scope']}();
        }
        if (! empty($config['order'])) {
            [$column, $direction] = $config['order'];
            $query->orderBy($column, $direction);
        }

        $items = $query->get();
        $rows = $this->mapRowsForPdf($items, $config['columns']);

        return $this->streamListPdf(
            $config['title'],
            $config['headers'],
            $rows,
            'liste-'.$key
        );
    }
}
