<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;

class RecalculateArticlePurchasePrices extends Command
{
    protected $signature = 'articles:recalculate-purchase-prices {--article-id= : Recalculer uniquement cet article}';

    protected $description = 'Recalcule prix unitaire (dernier achat) et CMP pondéré à partir des lignes de réception (y compris réceptions en cours)';

    public function handle(): int
    {
        $specificId = $this->option('article-id');

        $query = Article::query()->when(! $specificId, function ($q) {
            $q->whereHas('lignesReception.reception', function ($r) {
                $r->whereIn('statut', ['en_cours', 'complete', 'partielle']);
            });
        }, function ($q) use ($specificId) {
            $q->where('id', (int) $specificId);
        });

        $count = 0;
        $query->orderBy('id')->chunkById(100, function ($articles) use (&$count) {
            foreach ($articles as $article) {
                $article->recalculerPrixAchatDepuisReceptions(5);
                $count++;
            }
        });

        $this->info("Recalcul terminé pour {$count} article(s).");

        return self::SUCCESS;
    }
}
